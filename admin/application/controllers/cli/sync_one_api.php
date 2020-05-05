<?php
require_once dirname(__FILE__) . "/base_cli.php";

/**
 * Class Sync_one_api
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
class Sync_one_api extends Base_cli {

	public $oghome = null;

	/**
	 * overview : Sync_one_api constructor.
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
	public function sync_one_api_start($apiId, $dateTimeFromStr = '-10 minutes') {
		//never stop
		set_time_limit(0);
		// from -> now
		$dateTimeFromStr = (new \DateTime($dateTimeFromStr))->format('Y-m-d H:i:s');
		$dateTimeToStr = date('Y-m-d H:i:s');

		$this->utils->debug_log('runing... started from:' . $dateTimeFromStr . 'to:' . $dateTimeToStr);

		$mark = 'benchSyncOneApi';
		$playerName='_null';
		//run sync
		while (true) {

			$syncSleepTime = $this->config->item('sync_sleep_seconds');

			$this->utils->markProfilerStart($mark);

			$responseFileName=$this->utils->createTempFileName();

			$cmd = $this->getCommandLine('sync', [$apiId, $dateTimeFromStr, $dateTimeToStr, $responseFileName], $file_list);
			$this->utils->debug_log('start sync', $cmd, $responseFileName, $file_list);
			$returnVar=0;
			passthru($cmd, $returnVar);
			$success=$returnVar==0;
			$resp=$this->utils->decodeJson(file_get_contents($responseFileName));
			$this->utils->debug_log('end sync', $returnVar, $success, $resp);

			$file_list[]=$responseFileName;
			$this->clearTmpFile($file_list);
			unset($file_list);

			$this->utils->markProfilerEndAndPrint($mark);

			$this->utils->debug_log('sleep...', $syncSleepTime, 'from', $dateTimeFromStr, 'to', $dateTimeToStr);
			for ($i = 0; $i < $syncSleepTime; $i++) {
				sleep(1);
			}

			if($success){
				//set next
				$dateTimeFromStr = $dateTimeToStr;
				$dateTimeToStr = date('Y-m-d H:i:s');
			}else{
				//send to mm
				$this->utils->error_log('return failed, keep date time');
			}

			$this->utils->debug_log('from', $dateTimeFromStr, 'to', $dateTimeToStr);
		}


		$this->utils->debug_log('stopped');

	}

	/**
	 * overview : get command line
	 *
	 * @param string $func
	 * @param array $args
	 * @param $file_list
	 * @return string
	 */
	protected function getCommandLine($func, $args, &$file_list) {
		$og_home = $this->oghome;
		$php_str=$this->utils->find_out_php();
		// echo "try start\n";

		$argStr = '';
		if (!empty($args)) {
			foreach ($args as $val) {
				$argStr .= ' "' . $val . '"';
			}
		}

		$cmd = $php_str . ' ' . $og_home . '/shell/ci_cli.php cli/sync_one_api/' . $func . $argStr;

		return $this->utils->generateCommonLine($cmd, true, $func, $file_list);
	}

	private function returnErrorAndExit($errorMessage, $result, $responseFileName=null){
		if(!empty($responseFileName)){
			$result['success']=false;
			file_put_contents($responseFileName, $this->utils->encodeJson($result));
		}
		$this->utils->error_log($errorMessage);
		exit(1);
	}

	/**
	 * real function to sync
	 * @param string $gamePlatformId
	 * @param string $dateTimeFromStr
	 * @param string $dateTimeToStr
	 * @param string $playerName
	 */
	public function sync($gamePlatformId, $dateTimeFromStr = null, $dateTimeToStr = null,
			$responseFileName = null){

		if(empty($gamePlatformId) || $gamePlatformId=='_null'){
			$this->utils->error_log('empty game platform id', $gamePlatformId, $dateTimeFromStr, $dateTimeToStr);
			exit(1);
		}

		$default_sync_game_logs_max_time_second = $this->utils->getConfig('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		if($responseFileName=='_null'){
			$responseFileName=null;
		}
		if($dateTimeFromStr=='_null'){
			$dateTimeFromStr=null;
		}
		if($dateTimeToStr=='_null'){
			$dateTimeToStr=null;
		}

		$this->utils->debug_log('=========start sync_one_api============================', $gamePlatformId);

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

		// $manager = $this->utils->loadGameManager();

		// $rlt = $manager->syncGameRecordsWithMergeOnOnePlatform($gamePlatformId, $dateTimeFrom, $dateTimeTo,
		// 	$playerName, null, null);

		$succ=true;
		$result=['success'=>$succ];
		try {

			$this->utils->debug_log('sync game records _no_merge', $gamePlatformId, $dateTimeFrom, $dateTimeTo);
			$this->load->model(array('external_system'));
			if (!$this->external_system->isGameApiActive($gamePlatformId)) {
				$errorMessage=$gamePlatformId . ' is stopped';
				$result['gamePlatformId']=$gamePlatformId;
				return $this->returnErrorAndExit($errorMessage, $result, $responseFileName);
			}

			$api = $this->utils->loadExternalSystemLibObject($gamePlatformId);
			if(empty($api)){
				$errorMessage='cannot load '.$gamePlatformId;
				$result['gamePlatformId']=$gamePlatformId;
				return $this->returnErrorAndExit($errorMessage, $result, $responseFileName);
			}
			$token = random_string('unique');
			$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo);

			//sync
			$this->utils->debug_log('syncOriginalGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncOriginalGameLogs.' . $gamePlatformId;
			$this->utils->markProfilerStart($mark);
			$rlt = $rlt = $api->syncOriginalGameLogs($token);
			$this->utils->markProfilerEndAndPrint($mark);

			$succ = $succ && $rlt && $rlt['success'];
			$result['syncOriginalGameLogs']=$rlt;
			if(!$succ){
				$errorMessage='run syncOriginalGameLogs failed';
				return $this->returnErrorAndExit($errorMessage, $result, $responseFileName);
			}

			$this->utils->debug_log('syncLostAndFound', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncLostAndFound.' . $gamePlatformId;
			$this->utils->markProfilerStart($mark);
			$rlt = $api->syncLostAndFound($token);
			$this->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];
			$result['syncLostAndFound']=$rlt;
			if(!$succ){
				$errorMessage='run syncLostAndFound failed';
				return $this->returnErrorAndExit($errorMessage, $result, $responseFileName);
			}

			$this->utils->debug_log('syncConvertResultToDB', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncConvertResultToDB.' . $gamePlatformId;
			$this->utils->markProfilerStart($mark);
			$rlt = $api->syncConvertResultToDB($token);
			$this->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];
			$result['syncConvertResultToDB']=$rlt;
			if(!$succ){
				$errorMessage='run syncConvertResultToDB failed';
				return $this->returnErrorAndExit($errorMessage, $result, $responseFileName);
			}

			$this->utils->debug_log('syncMergeToGameLogs', $api->getPlatformCode(), $api->getValueFromSyncInfo($token, 'dateTimeFrom'), $api->getValueFromSyncInfo($token, 'dateTimeTo'));
			$mark = 'benchSyncMergeToGameLogs.' . $gamePlatformId;
			$this->utils->markProfilerStart($mark);
			$rlt = $api->syncMergeToGameLogs($token);
			$this->utils->markProfilerEndAndPrint($mark);
			$succ = $succ && $rlt && $rlt['success'];
			$result['syncMergeToGameLogs']=$rlt;
			if(!$succ){
				$errorMessage='run syncMergeToGameLogs failed';
				return $this->returnErrorAndExit($errorMessage, $result, $responseFileName);
			}

			$api->clearSyncInfo($token);

		} catch (Exception $e) {
			$this->utils->error_log('sync with exception', $e);
			$errorMessage='sync with exception';
			$result['exception']=$e->getMessage();
			return $this->returnErrorAndExit($errorMessage, $result, $responseFileName);
		}

		$result=['success'=>$succ];
		if(!empty($responseFileName)){
			file_put_contents($responseFileName, $this->utils->encodeJson($result));
		}
		$this->utils->debug_log('=========end sync_one_api============================', $gamePlatformId, $result);

	}

	private function clearTmpFile($file_list){
		if(!empty($file_list)){
			foreach ($file_list as $f) {
				$this->utils->debug_log('delete file: '.$f);
				@unlink($f);
			}
		}
	}

}

/// END OF FILE//////////////
