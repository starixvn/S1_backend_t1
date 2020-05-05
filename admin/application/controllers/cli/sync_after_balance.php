<?php
require_once dirname(__FILE__) . "/base_cli.php";

class Sync_after_balance extends Base_cli {

	const LOG_TITLE = '[sync_game_records_controller]';

	public $oghome = null;

	/**
	 * overview : Sync_game_records constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->config->set_item('print_log_to_console', true);
		$this->oghome = realpath(dirname(__FILE__) . "/../../../");
	}

	/**
	 * overview : sync service start
	 *
	 * @param string $dateTimeFromStr
	 */
	public function start_sync_after_balance($dateTimeFromStr = '-10 minutes') {
		//never stop
		set_time_limit(0);
		$dateTimeFromStr = (new \DateTime($dateTimeFromStr))->format('Y-m-d H:i:s');
		$dateTimeToStr = date('Y-m-d H:i:s');
		$this->utils->debug_log('runing... started from:' . $dateTimeFromStr . 'to:' . $dateTimeToStr);

		$mark = 'benchSyncAfterBalance';
		//run sync
	    while (true) {
	    	# reset db
	        $this->resetDb($this->db);
	        
			$this->utils->markProfilerStart($mark);
			$this->run_sync_shell($dateTimeFromStr, $dateTimeToStr);
			$this->utils->markProfilerEndAndPrint($mark);

			$syncSleepTime = $this->config->item('sync_sleep_seconds');
			$this->utils->debug_log('sleep...', $syncSleepTime, 'from', $dateTimeFromStr, 'to', $dateTimeToStr);
			sleep($syncSleepTime);

			//set next
			$dateTimeFromStr = $dateTimeToStr;
			$dateTimeToStr = date('Y-m-d H:i:s');

			$this->utils->debug_log('from', $dateTimeFromStr, 'to', $dateTimeToStr);

			gc_collect_cycles();
		}

		$this->utils->debug_log(self::LOG_TITLE, 'stopped');
	}

	/**
	 * overview : run sync shell
	 *
	 * @param string 	 $dateTimeFromStr
	 * @param string	 $dateTimeToStr
	 * @param string $playerName
	 */
	public function run_sync_shell($dateTimeFromStr, $dateTimeToStr) {
		//create pool by all game apis
		$apis = $this->config->item('enable_sync_afterbalance_for_gameplatform');

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
        $pauseSync_apis = array_column($this->external_system->getGameApiMaintenanceOrPauseSyncing('pause_sync'), 'id');
        if(!empty($pauseSync_apis)){
            $this->utils->debug_log("Sync game logs ignoring the following under pause syncing APIs: ", $pauseSync_apis);
            $apis = array_diff($apis, $pauseSync_apis);
        }

		$og_home = $this->oghome;

		$cmd = 'bash '.$og_home . '/shell/sync_after_balance.sh "' . implode(' ', $apis) . '" "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '"';
		

		//run merge
		$this->utils->debug_log('start sync', $cmd);

		// $cmd = $this->getCommandLine('merge_game_logs_and_total_stats', array($dateTimeFromStr, $dateTimeToStr, $playerName));
		// $PHP . ' ' . $og_home . '/shell/ci_cli.php cli/sync_game_records/merge_game_logs_and_total_stats "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '" ';

		shell_exec($cmd);

		$this->utils->debug_log("sync_game_records_all done");

		//all done
		// $this->returnText($str);
	}

	public function sync_game_logs_afterbalance($gamePlatformId, $dateTimeFromStr = null, $dateTimeToStr = null){
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$this->utils->debug_log('========= start sync_game_log_results ============================', $gamePlatformId);
		$this->utils->debug_log('========= start sync_game_log_results ============================ date', $dateTimeFromStr ,'-' ,$dateTimeToStr);

		$this->load->model(array('sync_status_model', 'queue_result'));

		if(!empty($queue_token)){
			$this->utils->debug_log('append result ', _REQUEST_ID, $queue_token);
			//update queue_results
			$this->queue_result->appendResult($queue_token, ['game_api'=>$gamePlatformId,
				'request_id'=>_REQUEST_ID, 'func'=>'sync_game_logs_afterbalance']);
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
		$func = 'sync_game_logs_afterbalance ';

		$manager = $this->utils->loadGameManager();

		$rlt = $manager->syncGameAfterBalanceOnOnePlatform($gamePlatformId, $dateTimeFrom, $dateTimeTo,null, null, null);

		$this->utils->debug_log('========= end syncGameResultRecordsOnOnePlatform ============================', $gamePlatformId);
	}

}

/// END OF FILE//////////////
