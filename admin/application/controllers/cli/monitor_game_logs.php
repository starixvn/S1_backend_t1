<?php
require_once dirname(__FILE__) . "/base_cli.php";

/**
 * Monitors game logs table for empty entries. Triggers re-sync of game logs.
 *
 * Find missing logs only: ~/Code/og$ php admin/shell/ci_cli.php cli/monitor_game_logs/find
 * Run once: ~/Code/og$ php admin/shell/ci_cli.php cli/monitor_game_logs/run
 * Run as service: ~/Code/og$ php admin/shell/ci_cli.php cli/monitor_game_logs/serviceStart
 *
 * @category Command line
 * @version 1.8.10
 * @copyright 2013-2017 TripleOneTech
 */
class Monitor_game_logs extends Base_cli {
    const ADMIN_USER_ID = 1; # userId in adminuser table
    const DELAY_MINUTES = 60; # By default check game log records that's more than 1 hour ago. Configurable using $config['monitor_game_logs_delay_minutes']
    const RUN_PERIOD_MINUTES = 60; # By default run the monitor every hour. Configurable using $config['monitor_game_logs_run_period_minutes']
    const SLEEP_BETWEEN_SYNC_SECONDS = 120; # By default sleep 2 minutes between sync requests. Configurable per API using $config['monitor_game_logs_sleep_between_sync_seconds'][$gamePlatformId]
    const DAYS_BACK = 0; # By default check records for the same day only. Configurable using $config['monitor_game_logs_days_back']
    const PERIOD_SECONDS = 3600; # By default check in 1 hour interval. Configurable per API using $config['monitor_game_logs_period_seconds'][$gamePlatformId]
    const LOG_TITLE = '[Monitor game logs]';
    const MAX_SERVICE_ITERATIONS = 10; # Max number of iterations when running as service. Terminates normally after this number of run().

    const RESYNC_STAGE_IMMEDIATE = 0;
    const RESYNC_STAGE_3HR = 1;
    const RESYNC_STAGE_12HR = 2;
    const RESYNC_STAGE_1DAY = 3;

    private $delayMinutes = 0;
    private $runPeriodMinutes = 0;
    private $numDaysBack = 0;
    private $periodInSeconds = 0;
    private $sleepBetweenSync = 0;
    private $apis = array();
    private $stages = array();

    public function __construct() {
        parent::__construct();

        $this->load->model(array('game_logs', 'game_logs_missing'));
        $this->load->library('lib_queue');
        $this->config->set_item('app_debug_log', APPPATH . 'logs/Monitor_game_logs.log');

        $this->delayMinutes = $this->config->item('monitor_game_logs_delay_minutes') ?: self::DELAY_MINUTES;
        $this->runPeriodMinutes = $this->config->item('monitor_game_logs_run_period_minutes') ?: self::RUN_PERIOD_MINUTES;
        $this->numDaysBack = $this->config->item('monitor_game_logs_days_back') ?: self::DAYS_BACK;
        $this->periodInSeconds = $this->config->item('monitor_game_logs_period_seconds') ?: array();
        $this->sleepBetweenSync = $this->config->item('monitor_game_logs_sleep_between_sync_seconds') ?: array();

        $this->apis = $this->utils->getAllCurrentGameSystemList();
        $this->stages = array(
            self::RESYNC_STAGE_IMMEDIATE => '',
            self::RESYNC_STAGE_3HR => ' +3 hours',
            self::RESYNC_STAGE_12HR => ' +12 hours',
            self::RESYNC_STAGE_1DAY => ' +1 day',
        );
    }

    public function serviceStart() {
        set_time_limit(86400);
        $this->utils->debug_log("Running game logs monitor, triggering every [$this->runPeriodMinutes] minutes...");
        $this->init();
        $iteration = 0;
        while ($iteration++ < self::MAX_SERVICE_ITERATIONS || self::MAX_SERVICE_ITERATIONS == 0) {
            $this->run();
            $this->utils->debug_log("Game logs monitor iteration [$iteration] done, sleeping for [$this->runPeriodMinutes] minutes");
            for ($i = 0; $i < $this->runPeriodMinutes; $i++) {
                sleep(60);
            }
        }
    }

    public function run() {
        # Go thru all APIs to find missing game logs
        $this->findMissingLogs();

        # Go thru all found missing logs and resync accordingly.
        $this->resyncMissingLogs();
    }

    # Only execute find logic, for debugging
    public function find() {
        $this->findMissingLogs();
    }

    private function init() {
        foreach($this->apis as $gamePlatformId) {
            $this->utils->debug_log("Initializing logs record for [$gamePlatformId] up to [$this->numDaysBack] days back...");
            $this->initMissingLogsRecord($gamePlatformId);
        }
    }

    private function findMissingLogs() {
        # Scan all configured APIs for missing logs
        foreach($this->apis as $gamePlatformId) {
            $this->utils->debug_log("Scanning logs for [$gamePlatformId] up to [$this->numDaysBack] days back...");
            $this->findMissingLogsFor($gamePlatformId);
        }
    }

    # This function will be run once upon service start
    # It marks all missing logs found as re-synced, so we won't have a burst of
    # resync requests at beginning
    private function initMissingLogsRecord($gamePlatformId) {
        $this->utils->debug_log("Initializing... marking start-up missing game logs as re-synced");
        $dateTo = $startDate = strtotime(date("Y-m-d")." - $this->numDaysBack days");
        while($dateTo < strtotime(date("Y-m-d H:i:s")." - $this->delayMinutes minutes")) {
            $dateFrom = $dateTo;
            $dateTo = strtotime(date("Y-m-d H:i:s", $dateFrom)." + ".$this->getPeriodInSeconds($gamePlatformId)." seconds");
            $gameLogCount = $this->game_logs->countGameLogsByTime($gamePlatformId, $dateFrom, $dateTo);
            $transactionCount = $this->game_logs->countGameLogsByTime($gamePlatformId, $dateFrom, $dateTo, Game_logs::FLAG_TRANSACTION);
            $this->utils->debug_log("Game log count for [$gamePlatformId] from [".date("Y-m-d H:i:s", $dateFrom)."] to [".date("Y-m-d H:i:s", $dateTo)."] is [$gameLogCount]; transaction count is [$transactionCount]");
            if($gameLogCount == 0 && $transactionCount > 0) { # Found a period without gamelogs, but with wallet transfer record
                $recorded = $this->game_logs_missing->recordMissingGameLog($gamePlatformId, $dateFrom, $this->getPeriodInSeconds($gamePlatformId));
                if($recorded) {
                    $this->game_logs_missing->markGameLogFound($gamePlatformId, $dateFrom, $this->getPeriodInSeconds($gamePlatformId));
                    $this->utils->debug_log("Missing game log recorded and marked as resynced");
                }
            }
        }
    }

    private function findMissingLogsFor($gamePlatformId) {
        $this->utils->debug_log("Finding missing logs for [$gamePlatformId] using period = ".$this->getPeriodInSeconds($gamePlatformId));
        $dateTo = $startDate = strtotime(date("Y-m-d")." - $this->numDaysBack days");
        while($dateTo < strtotime(date("Y-m-d H:i:s")." - $this->delayMinutes minutes")) {
            $dateFrom = $dateTo;
            $dateTo = strtotime(date("Y-m-d H:i:s", $dateFrom)." + ".$this->getPeriodInSeconds($gamePlatformId)." seconds");
            $gameLogCount = $this->game_logs->countGameLogsByTime($gamePlatformId, $dateFrom, $dateTo);
            $transactionCount = $this->game_logs->countGameLogsByTime($gamePlatformId, $dateFrom, $dateTo, Game_logs::FLAG_TRANSACTION);
            $this->utils->debug_log("Game log count for [$gamePlatformId] from [".date("Y-m-d H:i:s", $dateFrom)."] to [".date("Y-m-d H:i:s", $dateTo)."] is [$gameLogCount]; transaction count is [$transactionCount]");
            if($gameLogCount == 0 && $transactionCount > 0) { # Found a period without gamelogs, but with wallet transfer record
                $recorded = $this->game_logs_missing->recordMissingGameLog($gamePlatformId, $dateFrom, $this->getPeriodInSeconds($gamePlatformId));
                if($recorded) {
                    $this->utils->debug_log("Missing game log recorded.");
                }
            } else { # This period has game logs
                $marked = $this->game_logs_missing->markGameLogFound($gamePlatformId, $dateFrom, $this->getPeriodInSeconds($gamePlatformId));
                if($marked) {
                    $this->utils->debug_log("Missing game log record marked as done.");
                }
            }
        }
    }

    private function resyncMissingLogs() {
        foreach($this->stages as $stageIndex => $dateModifier) {
            foreach($this->apis as $gamePlatformId) {
                $missingGameLogs = $this->game_logs_missing->findMissingGameLogsByStage($gamePlatformId, $stageIndex, $this->numDaysBack);
                $this->utils->debug_log("Find missing game logs record on API [$gamePlatformId] for stage [$stageIndex] with [$this->numDaysBack] days back, record count: ", count($missingGameLogs));
                foreach($missingGameLogs as $aMissingLog){
                    $resyncResult = $this->resyncOneMissingLog($aMissingLog, $dateModifier);
                    if(!empty($resyncResult)) {
                        $this->game_logs_missing->updateMissingGameLogsStage($aMissingLog['id'], $stageIndex+1, $resyncResult);

                        # Upon resync, add in delay
                        $sleepSeconds = $this->getSleepBetweenSync($aMissingLog['game_platform_id']);
                        $this->utils->debug_log("Process will now sleep for [$sleepSeconds] seconds.");
                        sleep($sleepSeconds);
                    }
                }
            }
        }
    }

    private function resyncOneMissingLog($log, $dateModifier) {
        $logDateStr = $log['start_time'];
        $period = $log['period'];
        if(strtotime($logDateStr . $dateModifier) > now()) {
            # Skip this log if the designated time has not arrived
            return array();
        }

        $dateFrom = strtotime($logDateStr);
        $dateTo = strtotime($logDateStr. " + $period seconds");
        #return $this->submitResyncRequest($log['game_platform_id'], $dateFrom, $dateTo);
        return $this->mergeGameLogs($log['game_platform_id'], $dateFrom, $dateTo);
    }

    private function mergeGameLogs($gamePlatformId, $dateFrom, $dateTo) {
        $dateFromStr = date('Y-m-d H:i:s', $dateFrom);
        $dateToStr = date('Y-m-d H:i:s', $dateTo);
        $this->utils->debug_log("Re-merge for game [$gamePlatformId] from [$dateFromStr] to [$dateToStr]");

        $from = new DateTime();
        $from->setTimestamp($dateFrom);
        $to = new DateTime();
        $to->setTimestamp($dateTo);

        $api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
        $rlt = array('success' => false, 'msg' => '');
        if ($api) {
            $token = random_string('unique');
            $api->saveSyncInfoByToken($token, $from, $to, null);
            $rlt = $api->syncMergeToGameLogs($token);
        }

        $this->utils->debug_log("Re-merge for game [$gamePlatformId] from [$dateFromStr] to [$dateToStr]: ", $rlt);
        return $rlt;
    }

    # Deprecated
    private function submitResyncRequest($gamePlatformId, $dateFrom, $dateTo) {
        $dateFromStr = date('Y-m-d H:i:s', $dateFrom);
        $dateToStr = date('Y-m-d H:i:s', $dateTo);
        $this->utils->debug_log("Starting resync for game [$gamePlatformId] from [$dateFromStr] to [$dateToStr]");

        $from = new DateTime();
        $from->setTimestamp($dateFrom);
        $to = new DateTime();
        $to->setTimestamp($dateTo);

        $manager = $this->utils->loadGameManager();
        $rlt = $manager->syncGameRecordsNoMergeOnOnePlatform($gamePlatformId, $from, $to);
        $manager->mergeGameLogsAndTotalStatsAll($from, $to);

        $this->utils->debug_log("Resync result for game [$gamePlatformId] from [$dateFromStr] to [$dateToStr]: ", $rlt);
        return $rlt;
    }

    private function getPeriodInSeconds($gamePlatformId) {
        if(!is_array($this->periodInSeconds)) {
            return $this->periodInSeconds; # Config given in single number
        }
        if(!array_key_exists($gamePlatformId, $this->periodInSeconds)) {
            if(array_key_exists(0, $this->periodInSeconds)) {
                return $this->periodInSeconds[0]; # Index 0 gives default value
            }
            return self::PERIOD_SECONDS; # No default value in config, return hardcoded default
        }
        return $this->periodInSeconds[$gamePlatformId]; # Return configured value
    }

    private function getSleepBetweenSync($gamePlatformId) {
        if(!is_array($this->sleepBetweenSync)) {
            return $this->sleepBetweenSync; # Config given in single number
        }
        if(!array_key_exists($gamePlatformId, $this->sleepBetweenSync)) {
            if(array_key_exists(0, $this->sleepBetweenSync)) {
                return $this->sleepBetweenSync[0]; # Index 0 gives default value
            }
            return self::SLEEP_BETWEEN_SYNC_SECONDS; # No default value in config, return hardcoded default
        }
        return $this->sleepBetweenSync[$gamePlatformId]; # Return configured value
    }
}
