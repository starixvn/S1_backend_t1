<?php
require_once dirname(__FILE__) . '/game_api_interface.php';
require_once dirname(__FILE__) . '/../abstract_external_system_manager.php';

class Game_platform_manager extends Abstract_external_system_manager implements Game_api_interface {

	protected $systemType = SYSTEM_GAME_API;

	// const API_MAPS = array(
	// 	PT_API => "game_api_pt",
	// 	AG_API => "game_api_ag",
	// 	AG_FTP => "game_api_ag_ftp",
	// );

	// private $API;
	// public $platformCode;

	public function __construct($params = null) {
		parent::__construct($params);

		$this->utils=$this->CI->utils;
		//load all class
		// $this->CI = &get_instance();

		// log_message("error", var_export(self::API_MAPS, true));

		// if ($params && !empty($params["platform_code"]) && $params["platform_code"]) {
		// 	$this->initApi($params["platform_code"], $params);
		// }

	}

	// public function initApi($platformCode = null, $params = null) {
	// 	log_message("error", 'platformCode : ' . $platformCode);
	// 	if (!empty($platformCode)) {
	// 		$this->platformCode = $platformCode;
	// 		$cls = self::API_MAPS[$platformCode];
	// 		log_message("error", 'class : ' . $cls);
	// 		$this->CI->load->library('game_platform/' . $cls, $params);
	// 		$this->API = $this->CI->$cls;
	// 	}
	// 	return $this->API;
	// }

	// public function getApi($platform_code = null, $params = null) {
	// 	if (!empty($platformCode)) {
	// 		//reinit
	// 		$this->initApi($platformCode, $params);
	// 	}

	// 	log_message("error", 'API : ' . ($this->API == null));
	// 	return $this->API;
	// }

	//========================================================================================

	public function asyncCallOnAllPlatforms($funcName, $params, $callerType = null, $caller = null, $state = null) {
		$this->loadAllApiByType($this->systemType);
		$apis = $this->API_ARRAY[$this->systemType];
		$result = array();

		foreach ($apis as $api) {
			$result[$api->getPlatformCode()] = $api->asyncCall($funcName, $params, $callerType, $caller, $state);
		}

		return $result;
	}

	public function createPlayerOnAllPlatforms($playerName, $playerId, $password, $email = null, $extra = null) {
		$this->loadAllApiByType($this->systemType);
		$apis = $this->API_ARRAY[$this->systemType];
		$result = array();
		$this->CI->load->model(array('external_system'));
		$disabled_gameplatform = $extra['disabled_gameplatform'];
		foreach ($apis as $key => $api) {
			//disabled Gameplatform autocreate
			if(in_array($key,$disabled_gameplatform)){
				continue;
			}

			if (!$this->CI->external_system->isGameApiActive($api->getSystemInfo('id'))) {
				continue;
			}
			//check disable
			if ($api->isDisabled()) {
				continue;
			}

			$player = $api->createPlayer($playerName, $playerId, $password, $email, $extra);

			if ($player['success']) {
				$api->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE);

				$result[$api->getPlatformCode()] = array(
					'success' => $player['success'],
				);
			// } else {
			// 	$api->updateRegisterFlag($playerId, Abstract_game_api::FLAG_FALSE);
			}
		}

		return $result;
	}

	public function concurrentQueryBalanceOnAllPlatformsByPlayerId($playerId) {
		$this->CI->load->model(array('player_model'));
		$playerName = $this->CI->player_model->getUsernameById($playerId);
		return $this->concurrentQueryBalanceOnAllPlatforms($playerName, $playerId);
	}

	public function concurrentQueryBalanceOnAllPlatforms($playerName, $playerId=null) {

		$this->CI->load->model(array('game_provider_auth', 'player_model', 'wallet_model', 'external_system'));

		if(empty($playerId)){
			$playerId = $this->CI->player_model->getPlayerIdByUsername($playerName);
		}

		// $this->loadAllApiByType($this->systemType);
		$apis = $this->utils->getAllCurrentGameSystemList();
		$result = array();
		$api_str=implode(' ', $apis);

		$bigWallet=$this->CI->wallet_model->getBigWalletByPlayerId($playerId);

		//try run shell
		$og_admin_home = realpath(dirname(__FILE__) . "/../../../");

		$gameAccounts=$this->CI->game_provider_auth->getAllGameAccountsByPlayerId($playerId);

		$gameAccountMap=[];
		if(!empty($gameAccounts)){
			foreach ($gameAccounts as $row) {
				if($row['register']){
					$gameAccountMap[]=intval($row['game_provider_id']);
				}
			}
		}

		$php_str=$this->utils->find_out_php();
		$params=[];
		foreach ($apis as $apiId) {
			if(in_array($apiId, $gameAccountMap)){
				$balance=floatval($bigWallet['sub'][$apiId]['total']);
				$params[]='/'.$apiId.'/'.$playerName.'/'.$playerId.'/'.$balance;
			}
		}

		$param_str=implode(' ', $params);

		$noroot_command_shell=<<<EOD
#!/bin/bash

echo "start `date`"

echo "{$param_str}"

echo "{$playerName}"

for param in {$param_str} ; do {

	# sleep 1
	echo "{$php_str} {$og_admin_home}/shell/ci_cli.php cli/command/refresh_player_balance_one_api\$param"

	{$php_str} {$og_admin_home}/shell/ci_cli.php cli/command/refresh_player_balance_one_api\$param

} & done

wait

echo "done `date`"
EOD;

		// $cmd = 'bash '.$og_admin_home . '/shell/noroot_sync_game_logs.sh "' . implode(' ', $apis) . '" "' . $dateTimeFromStr . '" "' . $dateTimeToStr . '" "' . $playerName . '"';

		$uniqueid=random_string('md5');
		//app log
		$tmp_dir='/tmp/'.$this->CI->utils->getAppPrefix();
		if(!file_exists($tmp_dir)){
			@mkdir($tmp_dir, 0777 , true);
		}

		$tmp_shell=$tmp_dir.'/refresh_player_balance_'.$uniqueid.'.sh';
		file_put_contents($tmp_shell, $noroot_command_shell);

		$main_cmd=$tmp_shell;
		$is_blocked=true;
		// $cmd=$this->CI->utils->generateCommonLine($main_cmd, $is_blocked, 'refresh_player_balance_one_api');
		$cmd='bash '.$tmp_shell;
		$output='';
		$return_var=0;
		$rlt=exec($cmd, $output, $return_var);

		$this->CI->utils->debug_log('concurrentQueryBalanceOnAllPlatforms', $output, $return_var);

		//query big wallet, reload
		$bigWallet=$this->CI->wallet_model->getBigWalletByPlayerId($playerId);

		foreach ($apis as $apiId) {
			$result[$apiId] = array(
				'success' => true,
				'balance' => floatval($bigWallet['sub'][$apiId]['total_nofrozen']),
			);
		}

		return $result;
	}

	public function queryBalanceOnAllPlatformsByPlayerId($playerId) {
		$this->CI->load->model(array('player_model'));
		$playerName = $this->CI->player_model->getUsernameById($playerId);
		return $this->queryBalanceOnAllPlatforms($playerName);
	}

	public function queryBalanceOnAllPlatforms($playerName) {
		$this->CI->load->model(array('game_provider_auth', 'player_model', 'external_system'));
		$this->loadAllApiByType($this->systemType);
		$apis = $this->API_ARRAY[$this->systemType];
		$result = array();

		$playerId = $this->CI->player_model->getPlayerIdByUsername($playerName);
		$isRegisteredFlag = false;
		foreach ($apis as $api) {
			if (!$this->CI->external_system->isGameApiActive($api->getPlatformCode())) {
				continue;
			}

			if (!empty($playerId)) {
				$isRegisteredFlag = $this->CI->game_provider_auth->isRegisterd($playerId, $api->getPlatformCode());
				if ($isRegisteredFlag) {
					$balance = $api->queryPlayerBalance($playerName);
					if($balance['success'] && !isset($balance['balance'])){
						$this->CI->utils->error_log('return success=true, but no balance', $api->getPlatformCode());
					}
					if ($balance['success'] && isset($balance['balance']) && $balance['balance']!==null) {
						$result[$api->getPlatformCode()] = array(
							'success' => $balance['success'],
							'balance' => $this->CI->utils->floorCurrencyForShow($balance['balance']),
						);
					} else {
						$result[$api->getPlatformCode()] = array(
							'success' => $balance['success'],
							'balance' => 0,
						);
					}
				}
			}
		}

		return $result;
	}

	public function batchQueryPlayerBalanceOnAll($playerNames, $force = true, $syncId = null) {
		$this->CI->load->model(array('game_provider_auth', 'player_model', 'external_system'));
		$this->loadAllApiByType($this->systemType);
		$apis = $this->API_ARRAY[$this->systemType];
		$result = array();

		foreach ($apis as $api) {
			if (!$this->CI->external_system->isGameApiActive($api->getPlatformCode())) {
				continue;
			}

			$rlt = $api->batchQueryPlayerBalance($playerNames, $syncId);
			//TODO try get it from game logs

			// if ($force && isset($rlt['unimplemented']) && $rlt['unimplemented']) {
			// 	//run again
			// 	$rlt = $api->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);
			// }
			$result[$api->getPlatformCode()] = $rlt;
		}
		return $result;
	}

	public function batchQueryPlayerBalanceOnOne($gamePlatformId, $playerNames, $force = true, $syncId = null) {
		$this->CI->load->model(array('game_provider_auth', 'player_model', 'external_system'));

		$api = $this->CI->utils->loadExternalSystemLibObject($gamePlatformId);

		$result = array('success' => false);

		if ($this->CI->external_system->isGameApiActive($api->getPlatformCode())) {

			$rlt = $api->batchQueryPlayerBalance($playerNames, $syncId);
			if ($force && isset($rlt['unimplemented']) && $rlt['unimplemented']) {
				//run again
				$rlt = $api->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);

			}

			$result = $rlt;
		}

		return $result;
	}

	public function syncOriginalGameRecordsOnOnePlatform($gamePlatformId, \DateTime $dateTimeFrom, \DateTime $dateTimeTo, $playerName = null, $gameName = null) {
		$this->CI->benchmark->mark('sync_game_logs_one_start');

		$this->CI->utils->debug_log('sync game records', $gamePlatformId, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
		// $this->loadAllApiByType($this->systemType);
		// $apis = $this->API_ARRAY[$this->systemType];
		$api = $this->CI->utils->loadExternalSystemLibObject($gamePlatformId);
		$token = random_string('unique');
		$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);

		//sync
		$this->CI->utils->debug_log('syncOriginalGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
		$api->syncOriginalGameLogs($token);
		$this->CI->utils->debug_log('syncLostAndFound', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
		$api->syncLostAndFound($token);

		$this->CI->utils->debug_log('syncConvertResultToDB', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
		$api->syncConvertResultToDB($token);

		$this->CI->utils->debug_log('syncMergeToGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
		$api->syncMergeToGameLogs($token);

		// if ($abstractApi) {
		// 	$this->CI->utils->debug_log('syncTotalStats abstractApi');
		// 	$abstractApi->syncTotalStats($token);
		// }
		$api->clearSyncInfo($token);

		$this->CI->benchmark->mark('sync_game_logs_one_stop');

		$this->CI->utils->debug_log('sync_game_logs_one_bench', $this->CI->benchmark->elapsed_time('sync_game_logs_one_start', 'sync_game_logs_one_stop'));
	}

	/***
	 *
	 * Sync game result on one platfrom
	 *
	 */
	public function syncGameResultRecordsOnOnePlatform($gamePlatformId, \DateTime $dateTimeFrom, \DateTime $dateTimeTo,
														$playerName = null, $gameName = null, $sync_id = null) {
		$succ = true;
		$msg = null;
		try {
			$this->CI->utils->debug_log('sync game result_records ', $gamePlatformId, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
			$this->CI->load->model(array('external_system'));
			if (!$this->CI->external_system->isGameApiActive($gamePlatformId)) {
				$this->CI->utils->debug_log($gamePlatformId . ' is stopped');
				return array('success' => $succ, 'message' => $msg);
			}
			$api = $this->CI->utils->loadExternalSystemLibObject($gamePlatformId);
			$token = random_string('unique');
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $sync_id);

			$this->CI->utils->debug_log('syncOriginalGameResult', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchsyncOriginalGameResult.' . $gamePlatformId;
			$this->CI->utils->markProfilerStart($mark);
			$rlt = $api->syncOriginalGameResult($token);
			$this->CI->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];
			$api->clearSyncInfo($token);
		} catch (Exception $e) {
			$succ = false;
			$msg = $e->__toString();
		}

		return array('success' => $succ, 'message' => $msg);
	}

	/***
	 *
	 * Sync game after balance on one platfrom
	 *
	 */
	public function syncGameAfterBalanceOnOnePlatform($gamePlatformId, \DateTime $dateTimeFrom, \DateTime $dateTimeTo,
														$playerName = null, $gameName = null, $sync_id = null) {
		$succ = true;
		$msg = null;
		try {
			$this->CI->utils->debug_log('sync game result_records ', $gamePlatformId, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
			$this->CI->load->model(array('external_system'));
			if (!$this->CI->external_system->isGameApiActive($gamePlatformId)) {
				$this->CI->utils->debug_log($gamePlatformId . ' is stopped');
				return array('success' => $succ, 'message' => $msg);
			}
			$api = $this->CI->utils->loadExternalSystemLibObject($gamePlatformId);
			$token = random_string('unique');
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $sync_id);

			$this->CI->utils->debug_log('syncOriginalGameResult', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchsyncAfterBalance.' . $gamePlatformId;
			$this->CI->utils->markProfilerStart($mark);
			$rlt = $api->syncAfterBalance($token);
			$this->CI->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];
			$api->clearSyncInfo($token);
		} catch (Exception $e) {
			$succ = false;
			$msg = $e->__toString();
		}

		return array('success' => $succ, 'message' => $msg);
	}

	/**
	 *
	 * no merge
	 *
	 */
	public function syncGameRecordsNoMergeOnOnePlatform($gamePlatformId, \DateTime $dateTimeFrom, \DateTime $dateTimeTo,
														$playerName = null, $gameName = null, $sync_id = null) {
		// $this->CI->benchmark->mark('sync_game_logs_one_no_merge_start');

		$succ = true;
		$msg = null;
		try {

			$this->CI->utils->debug_log('sync game records _no_merge', $gamePlatformId, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
			$this->CI->load->model(array('external_system'));
			if (!$this->CI->external_system->isGameApiActive($gamePlatformId)) {
				$this->CI->utils->debug_log($gamePlatformId . ' is stopped');
				return array('success' => $succ, 'message' => $msg);
			}

			// $this->loadAllApiByType($this->systemType);
			// $apis = $this->API_ARRAY[$this->systemType];
			$api = $this->CI->utils->loadExternalSystemLibObject($gamePlatformId);
			$token = random_string('unique');
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $sync_id);

			//sync
			$this->CI->utils->debug_log('syncOriginalGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncOriginalGameLogs.' . $gamePlatformId;
			$this->CI->utils->markProfilerStart($mark);
			$rlt = $rlt = $api->syncOriginalGameLogs($token);
			$this->CI->utils->markProfilerEndAndPrint($mark);

			$succ = $succ && $rlt && $rlt['success'];

			$this->CI->utils->debug_log('syncLostAndFound', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncLostAndFound.' . $gamePlatformId;
			$this->CI->utils->markProfilerStart($mark);
			$rlt = $api->syncLostAndFound($token);
			$this->CI->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];

			$this->CI->utils->debug_log('syncConvertResultToDB', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncConvertResultToDB.' . $gamePlatformId;
			$this->CI->utils->markProfilerStart($mark);
			$rlt = $api->syncConvertResultToDB($token);
			$this->CI->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];

			// $this->CI->utils->debug_log('syncMergeToGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			// $api->syncMergeToGameLogs($token);

			// if ($abstractApi) {
			// 	$this->CI->utils->debug_log('syncTotalStats abstractApi');
			// 	$abstractApi->syncTotalStats($token);
			// }
			$api->clearSyncInfo($token);

			// $this->CI->benchmark->mark('sync_game_logs_one_no_merge_stop');

			// $this->CI->utils->debug_log('sync_game_logs_one_no_merge_bench', $this->CI->benchmark->elapsed_time('sync_game_logs_no_merge_one_start', 'sync_game_logs_no_merge_one_stop'));

		} catch (Exception $e) {
			$succ = false;
			$msg = $e->__toString();
		}

		return array('success' => $succ, 'message' => $msg);
	}

	public function mergeGameLogs(\DateTime $dateTimeFrom, \DateTime $dateTimeTo,
												  $playerName = null, $gameName = null, $syncId = null) {
		$succ = true;
		$msg = null;

		try {

			$this->CI->utils->debug_log('mergeGameLogsAndTotalStatsAll', $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
			$this->loadAllApiByType($this->systemType);
			$apis = $this->API_ARRAY[$this->systemType];

			$ignore_apis = $this->CI->config->item('og_sync_ignore_api');
			if(!empty($ignore_apis)) {
				$this->utils->debug_log("mergeGameLogs ignoring the following APIs: ", $ignore_apis);
			} else {
				$ignore_apis = array();
			}

			$abstractApi = null;
			foreach ($apis as $api) {
				$abstractApi = $api;
				break;
			}
			// $result = array();
			$token = random_string('unique');
			foreach ($apis as $api) {
				if($api) {
					$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $syncId);
				}
			}

			foreach ($apis as $api) {
				if(in_array($api->getPlatformCode(), $ignore_apis)) {
					$this->utils->debug_log("mergeGameLogs ignoring API ", $api->getPlatformCode());
					continue;
				}

				$mark = 'benchmarkSyncMergeToGameLogs' . $api->getPlatformCode();
				$this->CI->utils->markProfilerStart($mark);
				if (!$api->isDisabled()) {
					$api->syncMergeToGameLogs($token);
				}
				$this->CI->utils->markProfilerEndAndPrint($mark);
			}

//			if ($abstractApi) {
//				// $this->CI->utils->debug_log('syncTotalStats abstractApi');
//				$mark = 'benchmarkSyncTotalStats';
//				$this->CI->utils->markProfilerStart($mark);
//				$abstractApi->syncTotalStats($token);
//				$this->CI->utils->markProfilerEndAndPrint($mark);
//			}
			foreach ($apis as $api) {
				$api->clearSyncInfo($token);
			}

		} catch (Exception $e) {
			$succ = false;
			$msg = $e->__toString();
		}

		return array('success' => $succ, 'message' => $msg);
	}

	public function mergeGameLogsAndTotalStatsAll(\DateTime $dateTimeFrom, \DateTime $dateTimeTo,
		$playerName = null, $gameName = null, $syncId = null) {
		$succ = true;
		$msg = null;

		try {

			$this->CI->utils->debug_log('mergeGameLogsAndTotalStatsAll', $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
			$this->loadAllApiByType($this->systemType);
			$apis = $this->API_ARRAY[$this->systemType];

			$ignore_apis = $this->CI->config->item('og_sync_ignore_api');
			if(!empty($ignore_apis)) {
				$this->utils->debug_log("mergeGameLogsAndTotalStatsAll ignoring the following APIs: ", $ignore_apis);
			} else {
				$ignore_apis = array();
			}

			$abstractApi = null;
			foreach ($apis as $api) {
				$abstractApi = $api;
				break;
			}
			// $result = array();
			$token = random_string('unique');
			foreach ($apis as $api) {
				$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $syncId);
			}

			foreach ($apis as $api) {
				if(in_array($api->getPlatformCode(), $ignore_apis)) {
					$this->utils->debug_log("mergeGameLogsAndTotalStatsAll ignoring API ", $api->getPlatformCode());
					continue;
				}

				$mark = 'benchmarkSyncMergeToGameLogs' . $api->getPlatformCode();
				$this->CI->utils->markProfilerStart($mark);
				if (!$api->isDisabled()) {
					$api->syncMergeToGameLogs($token);
				}
				$this->CI->utils->markProfilerEndAndPrint($mark);
			}

			if ($abstractApi) {
				// $this->CI->utils->debug_log('syncTotalStats abstractApi');
				$mark = 'benchmarkSyncTotalStats';
				$this->CI->utils->markProfilerStart($mark);
				$abstractApi->syncTotalStats($token);
				$this->CI->utils->markProfilerEndAndPrint($mark);
			}
			foreach ($apis as $api) {
				$api->clearSyncInfo($token);
			}

		} catch (Exception $e) {
			$succ = false;
			$msg = $e->__toString();
		}

		return array('success' => $succ, 'message' => $msg);
	}

	/**
	 * @param $gamePlatformId
	 * @param DateTime $dateTimeFrom
	 * @param DateTime $dateTimeTo
	 * @param string $playerName
	 * @param string $gameName
	 * @param string $sync_id
	 * @return array ['success'=>, 'message'=>]
	 */
	public function syncGameRecordsWithMergeOnOnePlatform($gamePlatformId, \DateTime $dateTimeFrom, \DateTime $dateTimeTo,
														  $playerName = null, $gameName = null, $sync_id = null) {
		// $this->CI->benchmark->mark('sync_game_logs_one_no_merge_start');

		$succ = true;
		$msg = null;
		try {

			$this->CI->utils->debug_log('sync game records _no_merge', $gamePlatformId, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
			$this->CI->load->model(array('external_system'));
			if (!$this->CI->external_system->isGameApiActive($gamePlatformId)) {
				$this->CI->utils->debug_log($gamePlatformId . ' is stopped');
				return array('success' => $succ, 'message' => $msg);
			}

			// $this->loadAllApiByType($this->systemType);
			// $apis = $this->API_ARRAY[$this->systemType];
			$api = $this->CI->utils->loadExternalSystemLibObject($gamePlatformId);
			$token = random_string('unique');
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $sync_id);

			//sync
			$this->CI->utils->debug_log('syncOriginalGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncOriginalGameLogs.' . $gamePlatformId;
			$this->CI->utils->markProfilerStart($mark);
			$rlt = $rlt = $api->syncOriginalGameLogs($token);
			$this->CI->utils->markProfilerEndAndPrint($mark);

			$succ = $succ && $rlt && $rlt['success'];
			$msg.= @$rlt['message'];

			$this->CI->utils->debug_log('syncLostAndFound', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncLostAndFound.' . $gamePlatformId;
			$this->CI->utils->markProfilerStart($mark);
			$rlt = $api->syncLostAndFound($token);
			$this->CI->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];
			$msg.= @$rlt['message'];

			$this->CI->utils->debug_log('syncConvertResultToDB', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncConvertResultToDB.' . $gamePlatformId;
			$this->CI->utils->markProfilerStart($mark);
			$rlt = $api->syncConvertResultToDB($token);
			$this->CI->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];
			$msg.= @$rlt['message'];

			$this->CI->utils->debug_log('syncMergeToGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncMergeToGameLogs.' . $gamePlatformId;
			$this->CI->utils->markProfilerStart($mark);
			$rlt = $api->syncMergeToGameLogs($token);
			$this->CI->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];
			$msg.= @$rlt['message'];

			// if ($abstractApi) {
			// 	$this->CI->utils->debug_log('syncTotalStats abstractApi');
			// 	$abstractApi->syncTotalStats($token);
			// }
			$api->clearSyncInfo($token);

			// $this->CI->benchmark->mark('sync_game_logs_one_no_merge_stop');

			// $this->CI->utils->debug_log('sync_game_logs_one_no_merge_bench', $this->CI->benchmark->elapsed_time('sync_game_logs_no_merge_one_start', 'sync_game_logs_no_merge_one_stop'));

		} catch (Exception $e) {
			$succ = false;
			$msg = $e->__toString();
			$this->CI->utils->error_log('sync with exception', $e);
		}

		return array('success' => $succ, 'message' => $msg);
	}

	public function syncLongTotalStatsAll(\DateTime $dateTimeFrom, \DateTime $dateTimeTo, $playerName = null, $gameName = null) {
		$this->CI->utils->debug_log('syncTotalStatsAll', $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
		$this->loadAllApiByType($this->systemType);
		$apis = $this->API_ARRAY[$this->systemType];
		$abstractApi = null;
		foreach ($apis as $api) {
			$abstractApi = $api;
			break;
		}
		// $result = array();
		$token = random_string('unique');
//		foreach ($apis as $api) {
//			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
//		}
		$abstractApi->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);

		if ($abstractApi) {
			$this->CI->utils->debug_log('syncTotalStats abstractApi');
			$abstractApi->syncLongTotalStats($token);
		}
//		foreach ($apis as $api) {
//			$api->clearSyncInfo($token);
//		}
		$abstractApi->clearSyncInfo($token);

		return array('success' => true);
	}

	/**
	 * @param DateTime $dateTimeFrom
	 * @param DateTime $dateTimeTo
	 * @param string $playerName
	 * @param string $gameName
	 * @return array
	 */
	public function syncTotalStatsAll(\DateTime $dateTimeFrom, \DateTime $dateTimeTo, $playerName = null, $gameName = null) {
		$this->CI->utils->debug_log('syncTotalStatsAll', $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
		$this->loadAllApiByType($this->systemType);
		$apis = $this->API_ARRAY[$this->systemType];
		$abstractApi = null;
		foreach ($apis as $api) {
			$abstractApi = $api;
			break;
		}
		// $result = array();
		$token = random_string('unique');
//		foreach ($apis as $api) {
//			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
//		}
		$abstractApi->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName);

		if ($abstractApi) {
			$this->CI->utils->debug_log('syncTotalStats abstractApi');
			$abstractApi->syncTotalStats($token);
		}
//		foreach ($apis as $api) {
//			$api->clearSyncInfo($token);
//		}
		$abstractApi->clearSyncInfo($token);

		return array('success' => true);
	}

	public function syncGameRecordsOnAllPlatforms(\DateTime $dateTimeFrom, \DateTime $dateTimeTo, $playerName = null, $gameName = null, $ignore_public_sync = false, $sync_total=true) {
		$this->CI->benchmark->mark('sync_game_logs_start');

		$this->CI->utils->debug_log('[syncGameRecordsOnAllPlatforms]', 'sync game records', $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
		$this->loadAllApiByType($this->systemType);
		$apis = $this->API_ARRAY[$this->systemType];
		$abstractApi = null;
		foreach ($apis as $api) {
			$abstractApi = $api;
			break;
		}
		// $result = array();
		$token = random_string('unique');
		foreach ($apis as $api) {
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, null,
				array('ignore_public_sync' => $ignore_public_sync));
		}

		$sync_result['syncOriginalGameLogs']=[];

		//sync
		foreach ($apis as $api) {
			$this->CI->utils->debug_log('syncOriginalGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$sync_result['syncOriginalGameLogs'][$api->getPlatformCode()]=$api->syncOriginalGameLogs($token);
		}
		foreach ($apis as $api) {
			$this->CI->utils->debug_log('syncLostAndFound', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$api->syncLostAndFound($token);
		}

		foreach ($apis as $api) {
			$this->CI->utils->debug_log('syncConvertResultToDB', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$api->syncConvertResultToDB($token);
		}

		$sync_result['syncMergeToGameLogs']=[];

		foreach ($apis as $api) {
			$this->CI->utils->debug_log('syncMergeToGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$sync_result['syncMergeToGameLogs'][$api->getPlatformCode()]=$api->syncMergeToGameLogs($token);
		}

		// foreach ($apis as $api) {
		// 	$this->CI->utils->debug_log('syncTotalStats', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'));
		// 	$api->syncTotalStats($token);
		// }
		if ($abstractApi && $sync_total) {
			$this->CI->utils->debug_log('syncTotalStats abstractApi');
			$abstractApi->syncTotalStats($token);
		}
		if(!$sync_total){
			$this->CI->utils->info_log('ignore syncTotalStats', $sync_total);
		}
		foreach ($apis as $api) {
			$api->clearSyncInfo($token);
		}

		$this->CI->benchmark->mark('sync_game_logs_stop');

		$this->CI->utils->debug_log('sync_game_logs_bench', $this->CI->benchmark->elapsed_time('sync_game_logs_start', 'sync_game_logs_stop'));

		return array('success' => true, 'sync_result'=>$sync_result);
	}

	public function syncOneGameRecords($gamePlatformId, \DateTime $dateTimeFrom, \DateTime $dateTimeTo, $playerName = null, $gameName = null, $ignore_public_sync = false, $sync_total=true) {
		$this->CI->benchmark->mark('sync_game_logs_start');

		$this->CI->utils->debug_log('[syncOneGameRecords] '.$gamePlatformId, 'sync game records', $dateTimeFrom, $dateTimeTo, $playerName, $gameName);

		$rowsCount=0;
		$api=$this->CI->utils->loadExternalSystemLibObject($gamePlatformId);
		$abstractApi = $api;

		$token = random_string('unique');
		$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, null,
			array('ignore_public_sync' => $ignore_public_sync));

		//sync
		$this->CI->utils->debug_log('syncOriginalGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
		$rlt=$api->syncOriginalGameLogs($token);
		if(!$rlt['success']){
			$this->CI->utils->error_log('sync original failed');
			return $rlt;
		}
		if(isset($rlt['rows_count'])){
			$rowsCount=$rlt['rows_count'];
		}
		$this->CI->utils->debug_log('syncOriginalGameLogs result', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'), $rlt);

		$this->CI->utils->debug_log('syncLostAndFound', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
		$rlt=$api->syncLostAndFound($token);
		if(!$rlt['success']){
			$this->CI->utils->error_log('syncLostAndFound failed');
			return $rlt;
		}

		$this->CI->utils->debug_log('syncConvertResultToDB', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
		$rlt=$api->syncConvertResultToDB($token);
		if(!$rlt['success']){
			$this->CI->utils->error_log('syncConvertResultToDB failed');
			return $rlt;
		}

		$this->CI->utils->debug_log('syncMergeToGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
		$rlt=$api->syncMergeToGameLogs($token);
		if(!$rlt['success']){
			$this->CI->utils->error_log('syncMergeToGameLogs failed');
			return $rlt;
		}

		if ($abstractApi && $sync_total) {
			$this->CI->utils->debug_log('syncTotalStats abstractApi');
			$rlt=$abstractApi->syncTotalStats($token);
			if(!$rlt['success']){
				$this->CI->utils->error_log('syncTotalStats failed');
				return $rlt;
			}
		}

		$api->clearSyncInfo($token);

		$this->CI->benchmark->mark('sync_game_logs_stop');

		$this->CI->utils->debug_log('sync_game_logs_bench', $this->CI->benchmark->elapsed_time('sync_game_logs_start', 'sync_game_logs_stop'));

		return array('success' => true, 'rows_count'=>$rowsCount);
	}

	public function syncOneLostAndFound($gamePlatformId, \DateTime $dateTimeFrom, \DateTime $dateTimeTo, $playerName = null, $gameName = null, $ignore_public_sync = false) {
		$this->CI->benchmark->mark('sync_game_logs_start');

		$this->CI->utils->debug_log('[syncOneLostAndFound] '.$gamePlatformId, 'sync game records', $dateTimeFrom, $dateTimeTo, $playerName, $gameName);
		// $this->loadAllApiByType($this->systemType);
		// $apis = $this->API_ARRAY[$this->systemType];
		// $abstractApi = null;

		$api=$this->CI->utils->loadExternalSystemLibObject($gamePlatformId);
		$abstractApi = $api;

		// foreach ($apis as $api) {
		// 	$abstractApi = $api;
		// 	break;
		// }
		// $result = array();
		$token = random_string('unique');
		// foreach ($apis as $api) {
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, null,
				array('ignore_public_sync' => $ignore_public_sync));
		// }

		//sync
		// foreach ($apis as $api) {
			// $this->CI->utils->debug_log('syncOriginalGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			// $api->syncOriginalGameLogs($token);
		// }
		// foreach ($apis as $api) {
			$this->CI->utils->debug_log('syncLostAndFound', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$api->syncLostAndFound($token);
		// }

		// foreach ($apis as $api) {
			// $this->CI->utils->debug_log('syncConvertResultToDB', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			// $api->syncConvertResultToDB($token);
		// }

		// foreach ($apis as $api) {
			// $this->CI->utils->debug_log('syncMergeToGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			// $api->syncMergeToGameLogs($token);
		// }

		// foreach ($apis as $api) {
		// 	$this->CI->utils->debug_log('syncTotalStats', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'));
		// 	$api->syncTotalStats($token);
		// }
		// if ($abstractApi) {
		// 	$this->CI->utils->debug_log('syncTotalStats abstractApi');
		// 	$abstractApi->syncTotalStats($token);
		// }
		// foreach ($apis as $api) {
			$api->clearSyncInfo($token);
		// }

		$this->CI->benchmark->mark('sync_game_logs_stop');

		$this->CI->utils->debug_log('sync_game_logs_bench', $this->CI->benchmark->elapsed_time('sync_game_logs_start', 'sync_game_logs_stop'));

		return array('success' => true);
	}

	public function syncBalanceOnAllPlatforms($dateTimeFrom, $dateTimeTo, $playerName = null) {

	}

	//========================================================================================
	// public $syncInfo = array();

	/**
	 * only for sync function
	 *
	 */
	public function putValueToSyncInfo($token, $key, $value) {
		// $this->syncInfo[$token][$key] = $value;
		return $this->API->putValueToSyncInfo($token, $key, $value);
	}
	/**
	 * only for sync function
	 *
	 */
	public function getValueFromSyncInfo($token, $key) {
		// return $this->syncInfo[$token][$key];
		return $this->API->getValueFromSyncInfo($token, $key);
	}

	public function saveSyncInfoByToken($token, $dateTimeFrom = null, $dateTimeTo = null, $playerName = null, $gameName = null, $syncId = null, $extra = null) {
		// $this->syncInfo = array();
		// $this->syncInfo[$token] = array("dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo, "playerName" => $playerName, "gameName" => $gameName);
		return $this->API->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $syncId, $extra);
	}

	public function clearSyncInfo($token) {
		// $this->syncInfo[$token] = null;
		// $this->syncInfo = null;
		return $this->API->clearSyncInfo($token);
	}

	public function checkIfPlayerExistOnAllPlatforms($playerName) {
		$this->loadAllApiByType($this->systemType);
		$apis = $this->API_ARRAY[$this->systemType];

		$result = array();

		foreach ($apis as $api) {
			$result[$api->getPlatformCode()] = $api->isPlayerExist($playerName);
		}

		return $result;
	}

	//====implements Game_api_interface start===================================
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = null) {
		return $this->API->createPlayer($playerName, $playerId, $password, $email, $extra);
	}
	public function isPlayerExist($playerName) {
		return $this->API->isPlayerExist($playerName);
	}
	public function isBlocked($playerUsername) {
		return $this->API->isBlocked($playerUsername);
	}
	public function checkLoginToken($playerName, $token) {
		return $this->API->checkLoginToken($playerName, $token);
	}
	public function queryPlayerInfo($playerName) {
		return $this->API->queryPlayerInfo($playerName);
	}
	public function getPassword($playerName) {
		return $this->API->getPassword($playerName);
	}
	public function changePassword($playerName, $oldPassword, $newPassword) {
		return $this->API->changePassword($playerName, $oldPassword, $newPassword);
	}
	public function blockPlayer($playerName) {
		return $this->API->blockPlayer($playerName);
	}
	public function unblockPlayer($playerName) {
		return $this->API->unblockPlayer($playerName);
	}
	public function depositToGame($playerName, $amount, $transfer_secure_id=null) {
		return $this->API->depositToGame($playerName, $amount, $transfer_secure_id);
	}
	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null) {
		return $this->API->withdrawFromGame($playerName, $amount, $transfer_secure_id);
	}
	public function login($playerName, $password = null) {
		return $this->API->login($playerName, $password);
	}
	public function logout($playerName, $password = null) {
		return $this->API->logout($playerName, $password);
	}
	public function updatePlayerInfo($playerName, $infos) {
		return $this->API->updatePlayerInfo($playerName, $infos);
	}
	public function queryPlayerBalance($playerName) {
		return $this->API->queryPlayerBalance($playerName);
	}
	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null) {
		return $this->API->queryPlayerDailyBalance($playerName, $playerId, $dateFrom, $dateTo);
	}
	public function queryGameRecords($dateFrom, $dateTo, $playerName = null) {
		return $this->API->queryGameRecords($dateFrom, $dateTo, $playerName);
	}
	public function checkLoginStatus($playerName) {
		return $this->API->checkLoginStatus($playerName);
	}
	public function totalBettingAmount($playerName, $dateFrom, $dateTo) {
		return $this->API->totalBettingAmount($playerName, $dateFrom, $dateTo);
	}
	public function queryTransaction($transactionId, $extra) {
		return $this->API->queryTransaction($transactionId, $extra);
	}
	public function queryForwardGame($playerName, $extra) {
		return $this->API->queryForwardGame($playerName, $extra);
	}
	public function syncGameRecords($dateTimeFrom = null, $dateTimeTo = null, $playerName = null, $gameName = null) {
		return $this->API->syncGameRecords($dateTimeFrom, $dateTimeTo, $playerName, $gameName);
	}
	public function syncOriginalGameLogs($token) {
		return $this->API->syncOriginalGameLogs($token);
	}
	public function syncLostAndFound($token) {
		return $this->API->syncLostAndFound($token);
	}
	public function syncConvertResultToDB($token) {
		return $this->API->syncConvertResultToDB($token);
	}
	public function syncMergeToGameLogs($token) {
		return $this->API->syncMergeToGameLogs($token);
	}
	public function syncTotalStats($token) {
		return $this->API->syncTotalStats($token);
	}
	public function syncBalance($dateTimeFrom, $dateTimeTo, $playerName = null, $gameName = null) {
		return $this->API->syncBalance($dateTimeFrom, $dateTimeTo, $playerName, $gameName);
	}
	// public function syncLostAndFound($dateTimeFrom = null, $dateTimeTo = null, $playerName = null, $gameName = null) {
	// 	return $this->API->syncLostAndFound($dateTimeFrom, $dateTimeTo, $playerName, $gameName);
	// }
	public function callApi($apiName, $params) {
		return $this->API->callApi($apiName, $params);
	}
	public function batchCreatePlayer($playerInfos) {
		return $this->API->batchCreatePlayer($playerInfos);
	}
	public function batchQueryPlayerInfo($playerNames) {
		return $this->API->batchQueryPlayerInfo($playerNames);
	}
	public function batchBlockPlayer($playerNames) {
		return $this->API->batchBlockPlayer($playerNames);
	}
	public function batchUnblockPlayer($playerNames) {
		return $this->API->batchUnblockPlayer($playerNames);
	}
	public function batchDepositToGame($playerDepositInfos) {
		return $this->API->batchDepositToGame($playerDepositInfos);
	}
	public function batchWithdrawFromGame($playerWithdrawInfos) {
		return $this->API->batchWithdrawFromGame($playerWithdrawInfos);
	}
	public function batchLogin($playerNames) {
		return $this->API->batchLogin($playerNames);
	}
	public function batchLogout($playerNames) {
		return $this->API->batchLogout($playerNames);
	}
	public function batchUpdatePlayerInfo($playerInfos) {
		return $this->API->batchUpdatePlayerInfo($playerInfos);
	}
	public function batchQueryPlayerBalance($playerNames, $syncId = null) {
		return $this->API->batchQueryPlayerBalance($playerNames, $syncId);
	}
	public function batchTotalBettingAmount($playerNames) {
		return $this->API->batchTotalBettingAmount($playerNames);
	}
	public function batchQueryTransaction($transactionIds) {
		return $this->API->batchQueryTransaction($transactionIds);
	}

	public function initCustom($platformCode, $params = null) {
	}

	public function resetPlayer($playerName) {
		return $this->API->resetPlayer($playerNames);
	}

	//====implements Game_api_interface end===================================

    const INCOMPLETE_TYPE_LIST=['hb'];

    /**
     *
     * @param  string $username
     * @param  string $type     incomplete type
     * @return array $result['success'=>, 'incomplete_game'=>['game_unique_code'=>, 'game_platform_id'=>]]
     */
    public function searchLastIncompleteGameInDB($username, $type=null){

        $result=['success'=>true, 'incomplete_game'=>null];
        $typeList=self::INCOMPLETE_TYPE_LIST;
        if(!empty($type)){
            $typeList=[$type];
        }
        //only pick up one
        foreach ($typeList as $t) {
            if($t=='hb'){
                $rlt=$this->searchLastIncompleteGameInHB($username);
                if(!empty($rlt) && !empty($rlt['game_unique_code']) && !empty($rlt['game_platform_id'])){
                    $result['incomplete_game']=$rlt;
                    break;
                }
            }
        }
        return $result;
    }

    public function searchLastIncompleteGameInHB($playerUsername){
        $result=['game_unique_code'=>null, 'game_platform_id'=>null];
    	$this->CI->load->model(['game_provider_auth', 'original_game_logs_model']);
        $hb_common_apis=$this->CI->utils->getConfig('hb_common_apis');
        $gameUsernameList = $this->CI->game_provider_auth->getMultipleGameUsernameBy($playerUsername, $hb_common_apis);
        if(empty($gameUsernameList)){
        	return $result;
        }
        $usernameKeys=[];
        foreach ($gameUsernameList as $row) {
        	$usernameKeys[]=$row['game_provider_id'].'-'.$row['login_name'];
        }
        $this->CI->utils->debug_log('searchLastIncompleteGameInHB usernameKeys', $usernameKeys);

        $row=$this->CI->original_game_logs_model->queryHBIncompleteLastGame($usernameKeys);

        $this->CI->utils->debug_log('queryHBIncompleteLastGame row', $row);

        if(!empty($row)){
            $result['game_unique_code']=$row['game_key_name'];
            $result['game_platform_id']=$row['game_platform_id'];
        }

        return $result;
    }

    public function forwardToIncompleteGameLink($username, $extra){
        $result=['success'=>true, 'url'=>null];
        //search in db
        $rlt=$this->searchLastIncompleteGameInDB($username);
        if($rlt['success'] && !empty($rlt['incomplete_game'])){
	    	$incompleteGame=$rlt['incomplete_game'];
	        if(!empty($incompleteGame['game_platform_id'])){
	        	//load api then generate launcher url
	        	$api=$this->CI->utils->loadExternalSystemLibObject($incompleteGame['game_platform_id']);
	        	if(!empty($api)){
		        	//['success'=>$success, 'url'=> $forward_url]
		        	$launcherSettings=[
		        		'game_unique_code'=>$incompleteGame['game_unique_code'],
		        		'mode'=>$extra['mode'],
		        		'language'=>$extra['language'],
		        		'redirection'=>isset($extra['redirection']) ? $extra['redirection'] : null,
		        		'platform'=>null,
		        		'game_type'=>null,
		        	];
		        	$launcherRlt=$api->getGotoUrl($username, $launcherSettings);
		            if($launcherRlt['success'] && !empty($launcherRlt['url'])){
		                $result['url']=$launcherRlt['url'];
		            }
	        	}
	        }
        }
        return $result;
    }

}

/*end of file*/