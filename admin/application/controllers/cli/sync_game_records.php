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
 * * Sync game logs
 * * Sync cmd
 * * Sync service start
 * * Get command line tool
 * * Fork shell
 * * Sync game platform
 * * Load api
 *
 * @category Command Line
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Sync_game_records extends Base_cli {

	const LOG_TITLE = '[sync_game_records_controller]';

	public $oghome = null;

	/**
	 * overview : Sync_game_records constructor.
	 */
	public function __construct() {
		parent::__construct();
		// load game platform
		// $this->load->library(array('lib_gearman', 'utils'));
		// $this->config->set_item('app_debug_log', APPPATH . 'logs/game_api.log');

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
		return $this->oghome . '/application/logs/sync_game_records_runing_' . $gamePlatformId . '.pid';
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
	 * overview : merge game logs and total stats
	 *
	 * @param string $dateTimeFromStr
	 * @param string $dateTimeToStr
	 * @param string $playerName
	 */
	public function merge_game_logs_only($dateTimeFromStr = null, $dateTimeToStr = null, $playerName = null, $queue_token=null) {
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('=========start merge_game_logs_only============================');

		$this->load->model(array('sync_status_model', 'queue_result'));
		if(!empty($queue_token)){
			//update queue_results
			$this->queue_result->appendResult($queue_token, ['game_api'=>$gamePlatformId,
				'request_id'=>_REQUEST_ID, 'func'=>'merge_game_logs_and_total_stats']);
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
		//for all
		$gamePlatformId = 0;
		$func = 'mergeGameLogs';
		// $note = 'pid:' . getmypid();
		// $syncId = $this->sync_status_model->startSync($gamePlatformId, $func, $dateTimeFromStr, $dateTimeToStr, $playerName, null, null, $note);
		// $this->syncId = $syncId;
		// $this->writeSyncId($func . '_' . $gamePlatformId, $syncId);

		$manager = $this->utils->loadGameManager();

//		$rlt = $manager->mergeGameLogsAndTotalStatsAll($dateTimeFrom, $dateTimeTo,
//			$playerName, null, null);
		$rlt = $manager->mergeGameLogs($dateTimeFrom, $dateTimeTo,
			$playerName, null, null);

		// if ($rlt && $rlt['success']) {
		// 	$this->sync_status_model->successfullySync($syncId);
		// } else {
		// 	if ($rlt && array_key_exists('message', $rlt) && !empty(@$rlt['message'])) {
		// 		$note .= ' error: ' . $rlt['message'];
		// 	}
		// 	$this->sync_status_model->failedSync($syncId, null, null, $note);
		// }

		$this->utils->debug_log('=========end merge_game_logs_only============================');
	}

	/**
	 * overview : merge game logs and total stats
	 *
	 * @param string $dateTimeFromStr
	 * @param string $dateTimeToStr
	 * @param string $playerName
	 */
	public function merge_game_logs_and_total_stats($dateTimeFromStr = null, $dateTimeToStr = null, $playerName = null, $queue_token=null) {
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		if(!empty($queue_token)){
			//turn off
			$this->config->set_item('print_log_to_console', false);
		}

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('=========start mergeGameLogsAndTotalStatsAll============================');

		$this->load->model(array('sync_status_model', 'queue_result'));
		if(!empty($queue_token)){
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'request_id'=>_REQUEST_ID,
				'func'=>'merge_game_logs_and_total_stats',
				'log_file'=>'sync_game_records.log',
			]);
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
		//for all
		$gamePlatformId = 0;
		$func = 'mergeGameLogsAndTotalStatsAll';
		// $note = 'pid:' . getmypid();
		// $syncId = $this->sync_status_model->startSync($gamePlatformId, $func, $dateTimeFromStr, $dateTimeToStr, $playerName, null, null, $note);
		// $this->syncId = $syncId;
		// $this->writeSyncId($func . '_' . $gamePlatformId, $syncId);

		$manager = $this->utils->loadGameManager();

		$rlt = $manager->mergeGameLogsAndTotalStatsAll($dateTimeFrom, $dateTimeTo,
			$playerName, null, null);
		// $rlt = $manager->mergeGameLogs($dateTimeFrom, $dateTimeTo,
		// 	$playerName, null, null);

		// if ($rlt && $rlt['success']) {
		// 	$this->sync_status_model->successfullySync($syncId);
		// } else {
		// 	if ($rlt && array_key_exists('message', $rlt) && !empty(@$rlt['message'])) {
		// 		$note .= ' error: ' . $rlt['message'];
		// 	}
		// 	$this->sync_status_model->failedSync($syncId, null, null, $note);
		// }
		if(!empty($queue_token)){
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'request_id'=>_REQUEST_ID,
				'func'=>'merge_game_logs_and_total_stats',
				'log_file'=>'sync_game_records.log',
				'result'=>$rlt,
			]);
		}

		$this->utils->debug_log('=========end mergeGameLogsAndTotalStatsAll============================');
	}

	/**
	 * overview : sync game logs ( no merge )
	 * @param int	$gamePlatformId
	 * @param string  $dateTimeFromStr
	 * @param string  $dateTimeToStr
	 * @param string $playerName
	 */
	public function sync_game_logs_no_merge($gamePlatformId, $dateTimeFromStr = null, $dateTimeToStr = null,
			$playerName = null, $queue_token=null) {
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		if(!empty($queue_token)){
			//turn off
			$this->config->set_item('print_log_to_console', false);
		}

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('=========start syncGameRecordsNoMergeOnOnePlatform============================', $gamePlatformId);

		$this->load->model(array('sync_status_model', 'queue_result'));

		if(!empty($queue_token)){
			$this->utils->debug_log('append result ', _REQUEST_ID, $queue_token);
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'game_api'=>$gamePlatformId,
				'request_id'=>_REQUEST_ID,
				'func'=>'sync_game_logs_no_merge',
				'log_file'=>'sync_game_records.log',
			]);
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
		$func = 'syncGameRecordsNoMergeOnOnePlatform';
		// $syncId = $this->sync_status_model->startSync($gamePlatformId, $func, $dateTimeFromStr, $dateTimeToStr, $playerName, null, null, $note);
		// $this->syncId = $syncId;
		// $this->writeSyncId($func . '_' . $gamePlatformId, $syncId);

		$manager = $this->utils->loadGameManager();

		$rlt = $manager->syncGameRecordsNoMergeOnOnePlatform($gamePlatformId, $dateTimeFrom, $dateTimeTo,
			$playerName, null, null);

		if(!empty($queue_token)){
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'game_api'=>$gamePlatformId,
				'request_id'=>_REQUEST_ID,
				'func'=>'sync_game_logs_no_merge',
				'log_file'=>'sync_game_records.log',
				'result'=>$rlt,
			]);
		}

		// if ($rlt['success']) {
			// $this->sync_status_model->successfullySync($syncId);
		// } else {
			// if (array_key_exists('message', $rlt) && !empty(@$rlt['message'])) {
			// 	$note .= ' error: ' . $rlt['message'];
			// }
			// $this->sync_status_model->failedSync($syncId, null, null, $note);
		// }

		$this->utils->debug_log('=========end syncGameRecordsNoMergeOnOnePlatform============================', $gamePlatformId);

		// echo $syncId;
	}

	public function sync_game_logs_with_total($gamePlatformId, $dateTimeFromStr = null, $dateTimeToStr = null,
			$playerName = null, $queue_token=null) {
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		if(!empty($queue_token)){
			//turn off, print log and write it to queue
			$this->config->set_item('print_log_to_console', false);
		}

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('=========start sync_game_logs_with_total============================', $gamePlatformId);

		$this->load->model(array('sync_status_model', 'queue_result'));

		if(!empty($queue_token)){
			$this->utils->debug_log('append result ', _REQUEST_ID, $queue_token);
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'game_api'=>$gamePlatformId,
				'request_id'=>_REQUEST_ID,
				'func'=>'sync_game_logs_with_total',
				'log_file'=>'sync_game_records.log',
			]);
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

		// $note = 'pid:' . getmypid();
		// $func = 'syncGameRecordsNoMergeOnOnePlatform';
		// $syncId = $this->sync_status_model->startSync($gamePlatformId, $func, $dateTimeFromStr, $dateTimeToStr, $playerName, null, null, $note);
		// $this->syncId = $syncId;
		// $this->writeSyncId($func . '_' . $gamePlatformId, $syncId);

		$manager = $this->utils->loadGameManager();
		$ignore_public_sync=true;
		$rlt=$manager->syncOneGameRecords($gamePlatformId, $dateTimeFrom, $dateTimeTo,
			$playerName, null, $ignore_public_sync);

		// $rlt = $manager->syncGameRecordsNoMergeOnOnePlatform($gamePlatformId, $dateTimeFrom, $dateTimeTo,
		// 	$playerName, null, null);

		if(!empty($queue_token)){
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'game_api'=>$gamePlatformId,
				'request_id'=>_REQUEST_ID,
				'func'=>'sync_game_logs_with_total',
				'log_file'=>'sync_game_records.log',
				'result'=>$rlt,
			]);
		}

		// if ($rlt['success']) {
			// $this->sync_status_model->successfullySync($syncId);
		// } else {
			// if (array_key_exists('message', $rlt) && !empty(@$rlt['message'])) {
			// 	$note .= ' error: ' . $rlt['message'];
			// }
			// $this->sync_status_model->failedSync($syncId, null, null, $note);
		// }

		$this->utils->debug_log('=========end sync_game_logs_with_total============================', $gamePlatformId);

		// echo $syncId;
	}

	public function sync_merge_game_logs($gamePlatformId, $dateTimeFromStr = null, $dateTimeToStr = null,
			$playerName = null, $queue_token=null) {
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		if(!empty($queue_token)){
			//turn off, print log and write it to queue
			$this->config->set_item('print_log_to_console', false);
		}

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('=========start sync_merge_game_logs============================', $gamePlatformId);

		$this->load->model(array('sync_status_model', 'queue_result'));

		if(!empty($queue_token)){
			$this->utils->debug_log('append result ', _REQUEST_ID, $queue_token);
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'game_api'=>$gamePlatformId,
				'request_id'=>_REQUEST_ID,
				'func'=>'sync_merge_game_logs',
				'log_file'=>'sync_game_records.log',
			]);
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

		$ignore_public_sync=true;
		$api=$this->CI->utils->loadExternalSystemLibObject($gamePlatformId);
		$gameName=null;

		$token = random_string('unique');
		$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, null,
			array('ignore_public_sync' => $ignore_public_sync));

		$this->CI->utils->debug_log('syncMergeToGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
		$rlt=$api->syncMergeToGameLogs($token);
		if(!$rlt['success']){
			$this->CI->utils->error_log('syncMergeToGameLogs failed');
			return $rlt;
		}

		if(!empty($queue_token)){
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'game_api'=>$gamePlatformId,
				'request_id'=>_REQUEST_ID,
				'func'=>'sync_merge_game_logs',
				'log_file'=>'sync_game_records.log',
				'result'=>$rlt,
			]);
		}

		$this->utils->debug_log('=========end sync_merge_game_logs============================', $gamePlatformId);

	}

	/**
	 * overview : sync service start
	 *
	 * @param string $dateTimeFromStr
	 */
	public function sync_service_start($mode='with_totals', $dateTimeFromStr = '-10 minutes') {
		//never stop
		set_time_limit(0);
		// if (!$this->isRuning()) {
		// from -> now
		$dateTimeFromStr = (new \DateTime($dateTimeFromStr))->format('Y-m-d H:i:s');
		$dateTimeToStr = date('Y-m-d H:i:s');
		// $this->remove_stop_flag();

		// $this->install_sig_handler();
		$playerName=null;

		$this->utils->debug_log('runing... started from:' . $dateTimeFromStr . 'to:' . $dateTimeToStr);
		// echo "runing... started from " . $dateTimeFrom->format(\DateTime::ISO8601) . "\n";

		$mark = 'benchSyncGameRecords';
		//run sync
	    while (true) {
	    	# reset db
	        $this->resetDb($this->db);

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

			gc_collect_cycles();
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

	/**
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
		$apis = $this->utils->getAllCurrentGameSystemList(true); # Always get all game apis from db

		$ignore_apis = $this->config->item('og_sync_ignore_api');
		if(!empty($ignore_apis)) {
			$this->utils->debug_log("Sync game logs ignoring the following APIs: ", $ignore_apis);
			$apis = array_diff($apis, $ignore_apis);
		}

        $this->load->model('external_system');
        $maintenance_apis = array_column($this->external_system->getGameApiMaintenanceOrPauseSyncing('maintenance_mode'), 'id');
        if(!empty($maintenance_apis)){
            $this->utils->debug_log("Sync game logs ignoring the following under maintenance APIs: ", $maintenance_apis);
            $apis = array_diff($apis, $maintenance_apis);
        }
        $pauseSync_apis = array_column($this->external_system->getGameApiMaintenanceOrPauseSyncing('pause_sync'), 'id');
        if(!empty($pauseSync_apis)){
            $this->utils->debug_log("Sync game logs ignoring the following under pause syncing APIs: ", $pauseSync_apis);
            $apis = array_diff($apis, $pauseSync_apis);
        }

		$og_home = $this->oghome;

		if($mode=='with_totals'){
			$cmd = 'bash '.$og_home . '/shell/sync_game_records_all.sh "' . implode(' ', $apis) . '" "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '"';
		}else{
			$cmd = 'bash '.$og_home . '/shell/sync_game_records_without_totals.sh "' . implode(' ', $apis) . '" "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '"';
		}

		//run merge
		$this->utils->debug_log('start sync', $cmd);

		// $cmd = $this->getCommandLine('merge_game_logs_and_total_stats', array($dateTimeFromStr, $dateTimeToStr, $playerName));
		// $PHP . ' ' . $og_home . '/shell/ci_cli.php cli/sync_game_records/merge_game_logs_and_total_stats "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '" ';

		shell_exec($cmd);

		$this->utils->debug_log("sync_game_records_all done");

		//all done
		// $this->returnText($str);
	}

	//only one game=========================================
	/**
	 * overview : sync one game api service start
	 * only sync one game api
	 *
	 * @param string $dateTimeFromStr
	 */
	public function sync_one_only_start($game_api_id, $sleep_time, $dateTimeFromStr = '-10 minutes') {
		//never stop
		set_time_limit(0);
		$dateTimeFromStr = (new \DateTime($dateTimeFromStr))->format('Y-m-d H:i:s');
		$dateTimeToStr = date('Y-m-d H:i:s');

		$playerName=null;

		$this->utils->debug_log('runing... started from:' . $dateTimeFromStr . 'to:' . $dateTimeToStr);
		// echo "runing... started from " . $dateTimeFrom->format(\DateTime::ISO8601) . "\n";
		$mode='without_totals';
		$mark = 'benchSyncGameRecords';
		//run sync
		while (true) {

			$syncSleepTime = $sleep_time; // $this->config->item('sync_sleep_seconds');

			$this->utils->markProfilerStart($mark);
			//create from to
			//run on thread
			$this->run_sync_one_shell($game_api_id, $dateTimeFromStr, $dateTimeToStr, $playerName, $mode);
			$this->utils->markProfilerEndAndPrint($mark);

			$this->utils->debug_log('sleep...', $syncSleepTime, 'from', $dateTimeFromStr, 'to', $dateTimeToStr);
			for ($i = 0; $i < $syncSleepTime; $i++) {
				sleep(1);
			}

			//set next
			$dateTimeFromStr = $dateTimeToStr;
			$dateTimeToStr = date('Y-m-d H:i:s');

			$this->utils->debug_log('from', $dateTimeFromStr, 'to', $dateTimeToStr);
		}

		$this->utils->debug_log('stopped');

	}

	/**
	 * overview : run sync shell
	 *
	 * @param string 	 $dateTimeFromStr
	 * @param string	 $dateTimeToStr
	 * @param string $playerName
	 */
	public function run_sync_one_shell($game_api_id, $dateTimeFromStr, $dateTimeToStr, $playerName = null, $mode='with_totals') {
		$this->utils->debug_log("try start", $dateTimeFromStr, $dateTimeToStr, $playerName);

		// $og_home = $this->oghome;
		// $PHP = '/usr/bin/php';
		// $this->load->model(array('external_system'));
		// $this->external_system->activeConnection();

		//create pool by all game apis
		$apis = [$game_api_id];// $this->utils->getAllCurrentGameSystemList();

		//always run
		// $ignore_apis = $this->config->item('og_sync_ignore_api');
		// if(!empty($ignore_apis)) {
		// 	$this->utils->debug_log("Sync game logs ignoring the following APIs: ", $ignore_apis);
		// 	$apis = array_diff($apis, $ignore_apis);
		// }

        $this->load->model('external_system');
        $maintenance_apis = array_column($this->external_system->getGameApiMaintenanceOrPauseSyncing('maintenance_mode'), 'id');
        if(!empty($maintenance_apis)){
            $this->utils->debug_log("Sync game logs ignoring the following under maintenance APIs: ", $maintenance_apis);
            $apis = array_diff($apis, $maintenance_apis);
        }
        // $pauseSync_apis = array_column($this->external_system->getGameApiMaintenanceOrPauseSyncing('pause_sync'), 'id');
        // if(!empty($pauseSync_apis)){
        //     $this->utils->debug_log("Sync game logs ignoring the following under pause syncing APIs: ", $pauseSync_apis);
        //     $apis = array_diff($apis, $pauseSync_apis);
        // }

        if(empty($apis)){
        	$this->utils->debug_log('empty apis', $apis);
        	return false;
        }

		$og_home = $this->oghome;

		if($mode=='with_totals'){
			$cmd = 'bash '.$og_home . '/shell/sync_game_records_all.sh "' . implode(' ', $apis) . '" "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '"';
		}else{
			$cmd = 'bash '.$og_home . '/shell/sync_game_records_without_totals.sh "' . implode(' ', $apis) . '" "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '"';
		}

		//run merge
		$this->utils->debug_log('start sync', $cmd);

		// $cmd = $this->getCommandLine('merge_game_logs_and_total_stats', array($dateTimeFromStr, $dateTimeToStr, $playerName));
		// $PHP . ' ' . $og_home . '/shell/ci_cli.php cli/sync_game_records/merge_game_logs_and_total_stats "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '" ';

		shell_exec($cmd);

		$this->utils->debug_log("sync_game_records_all done");

		return true;

		//all done
		// $this->returnText($str);
	}

}

/// END OF FILE//////////////
