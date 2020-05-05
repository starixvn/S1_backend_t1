<?php
require_once dirname(__FILE__) . "/base_cli.php";

/**
 * General behaviors include :
 *
 * * Running monitor
 * * Monitor -> call each api start_sync_balance -> php sync_one
 * * Set sync log file
 * * Sync service start
 * * Get/create/stop running file for game platform
 * * Start sync balance
 * * Sync one
 * * Monitor player
 *
 * @category Command line
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Sync_balance extends Base_cli {

	const LOG_TITLE = '[sync balance]';

	public $oghome = null;

	/**
	 * overview : Sync_balance constructor
	 */
	public function __construct() {
		parent::__construct();
		// load game platform
		// $this->load->library(array('lib_gearman', 'utils'));
		//
		$this->setSyncLogFile();

		$this->oghome = realpath(dirname(__FILE__) . "/../../../");

		// register_shutdown_function(array($this, 'clean_shutdown'));

	}

	/**
	 * overview : set sync log file
	 */
	public function setSyncLogFile() {
		$this->config->set_item('app_debug_log', APPPATH . 'logs/sync_balance.log');
	}

	/**
	 * overview : sync service start
	 *
	 * @param string $playerName
	 */
	public function sync_service_start($playerName = null) {
		// $this->remove_stop_flag();
		set_time_limit(0);

		while (true) {
			$syncSleepTime = $this->config->item('sync_balance_sleep_seconds');
			$this->monitor($playerName);
			$this->utils->debug_log('sleep... ' . $syncSleepTime);
			for ($i = 0; $i < $syncSleepTime; $i++) {
				// if ($this->exists_stop_flag()) {
				// 	break;
				// }
				sleep(1);
			}
		}
	}

	/**
	 * overview : get running file for game platform
	 *
	 * @param  int	$gamePlatformId
	 * @return string
	 */
	private function get_runing_file_for_game_platform($gamePlatformId) {
		return APPPATH . 'logs/sync_balance_runing_' . $gamePlatformId . '.pid';
	}

	/**
	 * overview : check if running for game platform
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
		file_put_contents($runingFile, getmypid());
	}

	/**
	 * overview : stop running for game platform
	 *
	 * @param int $gamePlatformId
	 */
	public function stop_runing_for_game_platform($gamePlatformId) {
		$runingFile = $this->get_runing_file_for_game_platform($gamePlatformId);
		unlink($runingFile);
	}

	/**
	 * @param $gamePlatformId
	 * @param string $playerName
	 */
	public function start_sync_balance($gamePlatformId, $playerName = null) {

		$runing = $this->is_runing_for_game_platform($gamePlatformId);

		if (!$runing) {
			$og_home = realpath(APPPATH . '../');
			//load api
			$api = $this->loadApi($gamePlatformId);
			$this->setSyncLogFile();
			if ($api->useQueryAvailableBalance()) {
				$shell = 'sync_available_balance_for_one.sh';
			} else {
				$shell = 'sync_balance_once.sh';
			}
			// $PHP = '/usr/bin/php';
			// echo "try start\n";
			$cmd = 'bash ' . $og_home . '/shell/' . $shell . ' ' . $og_home . ' ' . $gamePlatformId . ' ' . $playerName . ' > /dev/null 2>&1 & ';
			$this->utils->debug_log("try start", $gamePlatformId, $playerName, $og_home, $cmd);
			//start it
			exec($cmd);
			$this->utils->debug_log("started", $gamePlatformId, $playerName, $og_home);
		} else {
			$this->utils->debug_log("runing already", $gamePlatformId, $playerName);
		}

	}

	/**
	 * overview : sync once
	 *
	 * @param $gamePlatformId
	 * @param null $playerName
	 */
	public function sync_once($gamePlatformId, $playerName = null) {

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');

		set_time_limit($default_sync_game_logs_max_time_second);

		$runing = $this->is_runing_for_game_platform($gamePlatformId);

		if (!$runing) {
			//create running file
			$this->create_runing_for_game_platform($gamePlatformId);

			$this->benchmark->mark('sync_balance_start');
			$this->utils->debug_log('=========start sync_balance ' . $gamePlatformId . ' playerName:' . $playerName . '===============================');
			$api = $this->loadApi($gamePlatformId);
			$this->setSyncLogFile();

			// $this->load->model(array('sync_status_model'));
			$note = 'pid:' . getmypid();
			$func = 'batchQueryPlayerBalance';
			// $syncId = $this->sync_status_model->startSync($gamePlatformId, $func, null, null, $playerName, null, null, $note);
			$syncId=null;
			$this->syncId = $syncId;
			$this->writeSyncId($func . '_' . $gamePlatformId, $syncId);

			$rlt = array('success' => false);
			if ($api) {
				$playerNames = null;
				if (!empty($playerName)) {
					$playerNames = array($playerName);
				}
				$rlt = $api->batchQueryPlayerBalance($playerNames, $syncId);
			} else {
				$this->utils->debug_log('you disabled ' . $gamePlatformId);
			}

			if ($rlt['success']) {
				if(!empty($rlt['balances'])){
					$this->load->model(['player_model', 'wallet_model']);
					foreach ($rlt['balances'] as $playerId => $bal) {
						if(!empty($playerId) && $bal!==null){
//							$playerId=$this->player_model->getPlayerIdByUsername($username);
//							if(!empty($playerId)){
								$this->lockAndTransForPlayerBalance($playerId, function ()
										use ($playerId, $bal, $gamePlatformId) {

									$this->utils->debug_log('update wallet on sync balance', $playerId, $gamePlatformId, $bal);

									return $this->wallet_model->refreshSubWalletOnBigWallet($playerId, $gamePlatformId, $bal);

								});
//							}
						}
					}
				}
				// $this->sync_status_model->successfullySync($syncId);
			} else {
				if (array_key_exists('message', $rlt) && !empty(@$rlt['message'])) {
					$note .= ' error: ' . $rlt['message'];
				}
				// $this->sync_status_model->failedSync($syncId, null, null, $note);
			}

			// $this->syncNTBalance($playerName);
			$this->benchmark->mark('sync_balance_stop');
			$this->utils->debug_log('=========end sync_balance ' . $gamePlatformId . ' playerName:' . $playerName . '=================================');
			$this->utils->debug_log('sync_balance_bench', $gamePlatformId, $this->benchmark->elapsed_time('sync_balance_start', 'sync_balance_stop'));

			$this->stop_runing_for_game_platform($gamePlatformId);

			// echo $syncId;
		} else {
			$this->utils->debug_log("runing already", $gamePlatformId, $playerName);
		}
	}

	/**
	 * overview : monitor player
	 *
	 * @param string $playerName
	 * @return string
	 */
	public function monitor($playerName = null) {
		$this->load->model('external_system');
		//run all
		$apiList = $this->utils->getAllCurrentGameSystemList();

		$og_home = $this->oghome;

		$cmd = $og_home . '/shell/sync_balance_all.sh "' . implode(' ', $apiList) . '" ' . $playerName;
		$this->utils->debug_log("try start", $cmd);
		//start it
		$str = shell_exec($cmd);

		$this->utils->debug_log("sync_balance_all done", $str);

		// foreach ($apiList as $apiId) {
		// 	//check game active
		// 	if (!$this->external_system->isGameApiActive($apiId)) {
		// 		continue;
		// 	}
		// 	$this->start_sync_balance($apiId, $playerName);
		// }

		return $str;
	}

	/**
	 * overview : sync available once
	 *
	 * @param $gamePlatformId
	 */
	public function sync_available_once($gamePlatformId) {

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');

		set_time_limit($default_sync_game_logs_max_time_second);

		$runing = $this->is_runing_for_game_platform($gamePlatformId);

		$playerName = null;
		if (!$runing) {
			//create running file
			$this->create_runing_for_game_platform($gamePlatformId);

			$this->benchmark->mark('sync_balance_start');
			$this->utils->debug_log('=========start sync_available_once ' . $gamePlatformId . ' playerName:' . $playerName . '===============================');
			$api = $this->loadApi($gamePlatformId);
			$this->setSyncLogFile();

			// $this->load->model(array('sync_status_model'));
			$note = 'pid:' . getmypid();
			$func = 'batchQueryPlayerBalance';
			// $syncId = $this->sync_status_model->startSync($gamePlatformId, $func, null, null, $playerName, null, null, $note);
			$syncId=null;
			$this->syncId = $syncId;
			$this->writeSyncId($func . '_' . $gamePlatformId, $syncId);

			$rlt = array('success' => false);
			if ($api) {
				// $playerNames = null;
				// if (!empty($playerName)) {
				// 	$playerNames = array($playerName);
				// }
				$rlt = $api->batchQueryPlayerBalanceOnlyAvailable($syncId);
			} else {
				$this->utils->debug_log('you disabled ' . $gamePlatformId);
			}

			if ($rlt['success']) {
				// $this->sync_status_model->successfullySync($syncId);
			} else {
				if (array_key_exists('message', $rlt) && !empty(@$rlt['message'])) {
					$note .= ' error: ' . $rlt['message'];
				}
				// $this->sync_status_model->failedSync($syncId, null, null, $note);
			}

			// $this->syncNTBalance($playerName);
			$this->benchmark->mark('sync_balance_stop');
			$this->utils->debug_log('=========end sync_available_once ' . $gamePlatformId . ' playerName:' . $playerName . '=================================');
			$this->utils->debug_log('sync_balance_bench', $gamePlatformId, $this->benchmark->elapsed_time('sync_balance_start', 'sync_balance_stop'));

			$this->stop_runing_for_game_platform($gamePlatformId);

			// echo $syncId;
		} else {
			$this->utils->debug_log("runing already", $gamePlatformId, $playerName);
		}
	}

}

/// END OF FILE//////////////