<?php
require_once dirname(__FILE__) . "/base_cli.php";

/**
 * Class Sync_game_records
 *
 * General behaviors include :
 *
 * * Get/check running file for game platform
 * * Create/stop running file for game platform
 * * Merge game logs and total stats
 * * Sync game result logs
 *
 */
class Sync_game_results extends Base_cli {

	const LOG_TITLE = '[sync_game_result_records_controller]';

	public $oghome = null;

	/**
	 * overview : Sync_game_records constructor.
	 */
	public function __construct() {
		parent::__construct();
		// load game platform
		// $this->load->library(array('lib_gearman', 'utils'));
		$this->config->set_item('app_debug_log', APPPATH . 'logs/game_api.log');

		$this->config->set_item('print_log_to_console', true);

		$this->oghome = realpath(dirname(__FILE__) . "/../../../");

		// register_shutdown_function(array($this, 'clean_shutdown'));
	}

	/**
	 * overview : get running file for game platform
	 *
	 * @param  int	$gamePlatformId
	 * @return string
	 */
	private function get_runing_file_for_game_platform($gamePlatformId) {
		return $this->oghome . '/application/logs/sync_game_result_records_runing_' . $gamePlatformId . '.pid';
	}

	/**
	 * overview : check if game platform is running
	 * @param $gamePlatformId
	 * @return bool
	 */
	public function is_runing_for_game_platform($gamePlatformId) {
		$runingFile = $this->get_runing_file_for_game_platform($gamePlatformId);
		$runing = file_exists($runingFile);
		if ($runing) {
			# check if it's stale
			$lockingPID = trim(file_get_contents($runingFile));

			# Get all active PIDs.
			$pids = explode("\n", trim(`ps -e | awk '{print $1}'`));

			# If PID is still active, return true
			$runing = in_array($lockingPID, $pids);
		}

		// echo "runing state:" . $runing . "\n";
		$this->utils->debug_log("runing state " . $gamePlatformId, $runing);

		return $runing;
	}

	/**
	 * overview : create running for game platform
	 *
	 * @param int	$gamePlatformId
	 */
	public function create_runing_for_game_platform($gamePlatformId) {
		$runingFile = $this->get_runing_file_for_game_platform($gamePlatformId);
		// unlink($runingFile);
		$this->utils->debug_log("create runing file", $runingFile);
		file_put_contents($runingFile, getmypid());
	}

	/**
	 * overview : stop game platform
	 *
	 * @param int	$gamePlatformId
	 */
	public function stop_runing_for_game_platform($gamePlatformId) {
		$runingFile = $this->get_runing_file_for_game_platform($gamePlatformId);
		$this->utils->debug_log("remove runing file", $runingFile);
		unlink($runingFile);
	}

	/**
	 * overview : sync service start
	 *
	 * @param string $dateTimeFromStr
	 */
	public function sync_service_start($mode='with_totals', $dateTimeFromStr = '-60 minutes') {
		//never stop
		set_time_limit(0);
		// if (!$this->isRuning()) {
		// from -> now
		$dateTimeFromStr = (new \DateTime($dateTimeFromStr))->format('Y-m-d H:i:s');
		$dateTimeToStr = date('Y-m-d H:i:s');
		// $this->remove_stop_flag();

		// $this->install_sig_handler();
		$playerName=null;

		$this->utils->debug_log('Ag gresult runing... started from:' . $dateTimeFromStr . 'to:' . $dateTimeToStr);
		// echo "runing... started from " . $dateTimeFrom->format(\DateTime::ISO8601) . "\n";

		$mark = 'benchSyncGameRecords';
		//run sync
		while (true) {
			//check stop flag first
			// if ($this->exists_stop_flag()) {
			// 	break;
			// }
			$syncSleepTime = $this->config->item('sync_sleep_seconds');

			$this->utils->markProfilerStart($mark);
			//create from to
			//run on thread
			$this->run_sync_shell($dateTimeFromStr, $dateTimeToStr, $playerName, $mode);
			// $this->forkShell($dateTimeFromStr, $dateTimeToStr);
			// $this->monitor($dateTimeFromStr, $dateTimeToStr);
			$this->utils->markProfilerEndAndPrint($mark);

			$this->utils->debug_log('sleep...', $syncSleepTime, 'from', $dateTimeFromStr, 'to', $dateTimeToStr);
			// echo "sleep...\n";
			for ($i = 0; $i < $syncSleepTime; $i++) {
				// if ($this->exists_stop_flag()) {
				// 	break;
				// }
				sleep(1);
			}

			//set next
			$dateTimeFromStr = $dateTimeToStr;
			$dateTimeToStr = date('Y-m-d H:i:s');

			$this->utils->debug_log('from', $dateTimeFromStr, 'to', $dateTimeToStr);
		}

		// $this->remove_stop_flag();
		// $this->remove_runing_file();

		$this->utils->debug_log(self::LOG_TITLE, 'stopped');
		// echo "stopped\n";
		// exit();
		// } else {
		// 	$this->utils->debug_log(self::LOG_TITLE, 'still runing... stopped');
		// 	// echo "still runing... stopped\n";
		// }
	}

	/***
	 * overview : run sync shell
	 *
	 * @param string 	 $dateTimeFromStr
	 * @param string	 $dateTimeToStr
	 * @param string $playerName
	 */
	public function run_sync_shell($dateTimeFromStr, $dateTimeToStr, $playerName = null, $mode='with_totals') {
		$this->utils->debug_log("try start", $dateTimeFromStr, $dateTimeToStr, $playerName);

		// $og_home = $this->oghome;
		// $PHP = '/usr/bin/php';
		// $this->load->model(array('external_system'));
		// $this->external_system->activeConnection();

		//create pool by all game apis
		$apis = $this->utils->getAllCurrentGameSystemList();

		$og_home = $this->oghome;

		$cmd = 'bash '.$og_home . '/shell/sync_game_result_records.sh "' . implode(' ', $apis) . '" "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '"';

		//run merge
		$this->utils->debug_log('start sync', $cmd);

		// $cmd = $this->getCommandLine('merge_game_logs_and_total_stats', array($dateTimeFromStr, $dateTimeToStr, $playerName));
		// $PHP . ' ' . $og_home . '/shell/ci_cli.php cli/sync_game_records/merge_game_logs_and_total_stats "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '" ';

		shell_exec($cmd);

		$this->utils->debug_log("sync_game_records_all done");

		//all done
		// $this->returnText($str);
	}

	/**
	 * overview : sync game logs result
	 * @param int	$gamePlatformId
	 * @param string  $dateTimeFromStr
	 * @param string  $dateTimeToStr
	 * @param string $playerName
	 */
	public function sync_game_log_results($gamePlatformId = null, $dateTimeFromStr = null, $dateTimeToStr = null,
			$playerName = null, $queue_token=null) {
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('========= start sync_game_log_results ============================', $gamePlatformId);
		$this->utils->debug_log('========= start sync_game_log_results ============================ date', $dateTimeFromStr ,'-' ,$dateTimeToStr);

		$this->load->model(array('sync_status_model', 'queue_result'));

		if(!empty($queue_token)){
			$this->utils->debug_log('append result ', _REQUEST_ID, $queue_token);
			//update queue_results
			$this->queue_result->appendResult($queue_token, ['game_api'=>$gamePlatformId,
				'request_id'=>_REQUEST_ID, 'func'=>'sync_game_log_results']);
		}

		$dateTimeFrom = new \DateTime($dateTimeFromStr);
		$dateTimeTo = new \DateTime($dateTimeToStr);
		list($todayFrom, $todayTo) = $this->utils->getTodayDateTimeRange();
		//set default datetime from to
		if (empty($dateTimeFromStr)) {
			$dateTimeFrom = $todayFrom;
		}
		if (empty($dateTimeToStr)) {
			$dateTimeTo = $todayTo;
		}

		$note = 'pid:' . getmypid();
		$func = 'sync_game_log_results ';

		$manager = $this->utils->loadGameManager();

		$rlt = $manager->syncGameResultRecordsOnOnePlatform($gamePlatformId, $dateTimeFrom, $dateTimeTo,
			$playerName, null, null);

		$this->utils->debug_log('========= end syncGameResultRecordsOnOnePlatform ============================', $gamePlatformId);
	}
}

/// END OF FILE//////////////
