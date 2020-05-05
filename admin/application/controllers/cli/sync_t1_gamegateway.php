<?php
require_once dirname(__FILE__) . "/base_cli.php";

/**
 * Class Sync_t1_gamegateway
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
 * @version 3.30.10
 * @copyright 2013-2016 TripleOneTech
 */
class Sync_t1_gamegateway extends Base_cli {

	public $oghome = null;

	/**
	 * overview : Sync_one_api constructor.
	 */
	public function __construct() {
		parent::__construct();
		// load game platform
		// $this->load->library(array('lib_gearman', 'utils'));
//		$this->config->set_item('app_debug_log', APPPATH . 'logs/game_api.log');

		$this->config->set_item('print_log_to_console', true);

		$this->oghome = realpath(dirname(__FILE__) . "/../../../");

		// register_shutdown_function(array($this, 'clean_shutdown'));
	}

	private function clearTmpFile($file_list){
		if(!empty($file_list)){
			foreach ($file_list as $f) {
				$this->utils->debug_log('delete file: '.$f);
				@unlink($f);
			}
		}
	}

	/**
	 * overview : sync service start
	 *
	 * @param string $dateTimeFromStr
	 */
	public function sync_service_start($dateTimeFromStr = '-10 minutes') {
		//never stop
		set_time_limit(0);
		// from -> now
		$dateTimeFromStr = (new \DateTime($dateTimeFromStr))->format('Y-m-d H:i:s');
		$dateTimeToStr = date('Y-m-d H:i:s');

		$this->utils->debug_log('runing... started from:' . $dateTimeFromStr . 'to:' . $dateTimeToStr);
		// echo "runing... started from " . $dateTimeFrom->format(\DateTime::ISO8601) . "\n";

		$syncSleepTime = $this->utils->getConfig('sync_t1_sleep_seconds');
		if(empty($syncSleepTime)){
			$syncSleepTime = $this->utils->getConfig('sync_sleep_seconds');
		}
		$enabled_sync_t1_gamegateway_stream=$this->utils->getConfig('enabled_sync_t1_gamegateway_stream');
		$gamegateway_stream_query_max_limit_seconds=$this->utils->getConfig('gamegateway_stream_query_max_limit_seconds');
		// $mark = 'benchSyncOneApi';
		//run sync
		while (true) {

			$lastDateTimeFileName=$this->utils->createTempFileName();
			$file_list=[];
			if($enabled_sync_t1_gamegateway_stream){
				$cmd = $this->getCommandLine('sync_stream', [$dateTimeFromStr, $lastDateTimeFileName], $file_list);
			}else{
				$cmd = $this->getCommandLine('sync', array($dateTimeFromStr, $dateTimeToStr), $file_list);
			}
			$this->utils->debug_log('start sync------------------', $cmd);
			$str = shell_exec($cmd);
			$this->utils->debug_log('end sync--------------------', strlen($str), 'delete files', count($file_list));
			$this->clearTmpFile($file_list);
			unset($file_list);

			$this->utils->debug_log('sleep...', $syncSleepTime, 'from', $dateTimeFromStr, 'to', $dateTimeToStr);
			for ($i = 0; $i < $syncSleepTime; $i++) {
				sleep(1);
			}

			//set next
			if($enabled_sync_t1_gamegateway_stream){
				$jsonResult=file_get_contents($this->utils->createTempDirPath().'/'.$lastDateTimeFileName);
				$jsonObj=json_decode($jsonResult, true);
				$this->utils->debug_log('get result', $jsonObj);
				if($jsonObj['success']){
					if(isset($jsonObj['normalResult']['next_datetime']) &&
							!empty($jsonObj['normalResult']['next_datetime'])){
						//next date time
						$dateTimeFromStr=$jsonObj['normalResult']['next_datetime'];
					}else{
						//empty next_datetime means no data, then try last to date
						$dateTimeFromStr=$dateTimeToStr;
					}
				}else{
					//keep $dateTimeFromStr
					$this->utils->error_log('sync failed', $jsonObj, 'keep dateTimeFromStr', $dateTimeFromStr);
				}
				$now=new DateTime();
				$now->modify('-'.$gamegateway_stream_query_max_limit_seconds.' seconds');
				//if $dateTimeFromStr>$now,then use $now
				//check max limit
				if($dateTimeFromStr>$now->format('Y-m-d H:i:s')){
					$this->utils->debug_log('exceed max time', $dateTimeFromStr, $now);
					$dateTimeFromStr=$now->format('Y-m-d H:i:s');
				}
			}else{
				//no stream mode
				$dateTimeFromStr = $dateTimeToStr;
				$dateTimeToStr = date('Y-m-d H:i:s');
			}

			$this->utils->debug_log('from', $dateTimeFromStr, 'to', $dateTimeToStr);

			gc_collect_cycles();
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
				if($val===null || $val===''){
					$val='_null';
				}
				$argStr .= ' "' . $val . '"';
			}
		}

		$cmd = $php_str . ' ' . $og_home . '/shell/ci_cli.php cli/sync_t1_gamegateway/' . $func . $argStr;

		return $this->utils->generateCommonLine($cmd, true, $func, $file_list);

//		return $cmd;
	}

	/**
	 * call from shell
	 * @param string $dateTimeFromStr
	 * @param string $dateTimeToStr
	 * @param string $playerName
	 * @param string $queue_token
	 * @param string $use_bet_time
	 */
	public function sync($dateTimeFromStr, $dateTimeToStr, $playerName = '_null', $queue_token='_null', $use_bet_time='false'){

		$use_bet_time=$use_bet_time=='true';
		if($playerName=='_null'){
			$playerName=null;
		}
		if($queue_token=='_null'){
			$queue_token=null;
		}

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$this->load->model(['game_description_model', 'queue_result', 'external_system']);

		// $this->alwaysEnableQueue=$this->utils->getConfig('alwaysEnableQueue');
		// if($this->alwaysEnableQueue && empty($queue_token)){
		// 	//create one
		// 	$this->load->library(['language_function']);
		// 	// $this->load->model(['queue_result']);
		// 	$lang=$this->language_function->getCurrentLanguage();
		// 	$systemId = Queue_result::SYSTEM_UNKNOWN;
		// 	$params = [
		// 		'dateTimeFromStr' => $dateTimeFromStr,
		// 		'dateTimeToStr' => $dateTimeToStr,
		// 		'playerName' => $playerName,
		// 	];
		// 	$funcName='sync_t1_gamegateway';
		// 	$caller=0;
		// 	$callerType=Queue_result::CALLER_TYPE_SYSTEM;
		// 	$state=null;
		// 	$queue_token = $this->queue_result->newResult($systemId,
		// 		$funcName, $params, $callerType, $caller, $state, $lang);

		// }

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('=========start Sync_t1_gamegateway============================');

		$this->load->model(array('sync_status_model', 'queue_result'));

		$adjust_datetime_str='-'.$this->utils->getConfig('adjust_datetime_minutes_sync_t1_gamegateway').' minutes';

		if(!empty($queue_token)){
			$this->utils->debug_log('append result ', _REQUEST_ID, $queue_token);
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'request_id'=>_REQUEST_ID, 'func'=>'sync_t1_gamegateway', 'dateTimeFromStr'=>$dateTimeFromStr,
				'dateTimeToStr'=>$dateTimeToStr, 'playerName'=>$playerName, 'use_bet_time'=>$use_bet_time,
				'adjust_datetime_str'=>$adjust_datetime_str]);
		}

		$success=false;

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

		$error_message=null;
		$rlt=null;
		//load all active t1 games
		$apiList=$this->external_system->getAllActiveT1GameApiForSync();
		if(!empty($apiList)){
			//get min api id
			$apiId=-1;
			$api=null;
			//original code to current code
			$multiplePlatformIdMap=[];
			foreach ($apiList as $key => $value) {
				$tmpApi=$this->utils->loadExternalSystemLibObject($key);
				$multiplePlatformIdMap[$tmpApi->getOriginalPlatformCode()]=$tmpApi->getPlatformCode();
				if(empty($api) || $key<$apiId){
					$apiId=$key;
					$api=$tmpApi;
				}
			}
			//get unknown game from t1 platform
			$unknownGameTypeMap=$this->game_description_model->getMultipleUnknownGameMap(array_values($multiplePlatformIdMap));
			// $api=$this->utils->loadExternalSystemLibObject($apiId);
			if(!empty($api)){

				$gameName=null;
				$sync_id=null;
				$token = random_string('unique');
				$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $sync_id,
					['multiplePlatformIdMap'=>$multiplePlatformIdMap,
					'unknownGameTypeMap'=>$unknownGameTypeMap,
					'adjust_datetime_str'=>$adjust_datetime_str,
					'use_bet_time'=>$use_bet_time,
				]);

				$this->utils->info_log('multiplePlatformIdMap', $multiplePlatformIdMap, 'unknownGameTypeMap', $unknownGameTypeMap);

				$rlt=$api->syncDirectlyAllT1GameLogs($token);

				$this->utils->debug_log('print syncDirectlyAllT1GameLogs', $rlt);

				$api->clearSyncInfo($token);

				$success=$rlt['success'];
				$error_message=isset($rlt['error_message']) ? $rlt['error_message'] : null;
			}else{
				$success=false;
				$error_message='load t1 common game api failed';
				$this->utils->error_log($error_message);
			}
		}else{
			$error_message='no any available api';
		}

		if(!empty($queue_token)){
			$done=false;
			$is_error=!$success;
			if($success){
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'sync_t1_gamegateway', 'success'=> $success,
					'result'=>$rlt], $done, $is_error);
			}else{
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'sync_t1_gamegateway', 'success'=> $success, 'error_message'=>$error_message,
					'result'=>$rlt] , $done, $is_error);
			}
		}

		$this->utils->debug_log('=========end Sync_t1_gamegateway============================');

		if(!$success){
			//any non-zore is error
			exit(1);
		}

		return $success;
	}

	public function sync_stream($startDateTime, $lastDateTimeFileName, $playerName = '_null', $queue_token='_null'){

		if($playerName=='_null'){
			$playerName=null;
		}
		if($queue_token=='_null'){
			$queue_token=null;
		}

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

		$this->load->model(['game_description_model', 'sync_status_model', 'queue_result', 'external_system']);

		if($playerName=='_null'){
			$playerName=null;
		}

		$this->utils->debug_log('=========start sync_stream============================');

		$adjust_datetime_str='-'.$this->utils->getConfig('adjust_datetime_minutes_sync_t1_gamegateway_stream').' minutes';

		if(!empty($queue_token)){
			$this->utils->debug_log('append result ', _REQUEST_ID, $queue_token);
			//update queue_results
			$this->queue_result->appendResult($queue_token, [
				'request_id'=>_REQUEST_ID, 'func'=>'sync_t1_gamegateway_stream', 'startDateTime'=>$startDateTime,
				'playerName'=>$playerName, 'adjust_datetime_str'=>$adjust_datetime_str]);
		}

		$success=false;

		$dateTimeFrom = new \DateTime($startDateTime);
		$dateTimeTo = null; //new \DateTime($dateTimeToStr);

		$error_message=null;
		$rlt=null;
		//load all active t1 games
		$apiList=$this->external_system->getAllActiveT1GameApiForSync();
		if(!empty($apiList)){
			//get min api id
			$apiId=-1;
			$api=null;
			//original code to current code
			$multiplePlatformIdMap=[];
			foreach ($apiList as $key => $value) {
				$tmpApi=$this->utils->loadExternalSystemLibObject($key);
				$multiplePlatformIdMap[$tmpApi->getOriginalPlatformCode()]=$tmpApi->getPlatformCode();
				if(empty($api) || $key<$apiId){
					$apiId=$key;
					$api=$tmpApi;
				}
			}
			//get unknown game from t1 platform
			$unknownGameTypeMap=$this->game_description_model->getMultipleUnknownGameMap(array_values($multiplePlatformIdMap));
			// $api=$this->utils->loadExternalSystemLibObject($apiId);
			if(!empty($api)){

				$gameName=null;
				$sync_id=null;
				$token = random_string('unique');
				$api->saveSyncInfoByToken($token, $dateTimeFrom, $dateTimeTo, $playerName, $gameName, $sync_id,
					['multiplePlatformIdMap'=>$multiplePlatformIdMap,
					'unknownGameTypeMap'=>$unknownGameTypeMap,
					'adjust_datetime_str'=>$adjust_datetime_str,
				]);

				$this->utils->info_log('multiplePlatformIdMap', $multiplePlatformIdMap, 'unknownGameTypeMap', $unknownGameTypeMap);

				$rlt=$api->syncDirectlyT1GameLogsStream($token);
				file_put_contents($this->utils->createTempDirPath().'/'.$lastDateTimeFileName, json_encode($rlt, JSON_PRETTY_PRINT));

				$this->utils->debug_log('print syncDirectlyT1GameLogsStream', $rlt);

				$api->clearSyncInfo($token);

				$success=$rlt['success'];
				$error_message=isset($rlt['error_message']) ? $rlt['error_message'] : null;
			}else{
				$success=false;
				$error_message='load t1 common game api failed';
				$this->utils->error_log($error_message);
			}
		}else{
			$error_message='no any available api';
		}

		if(!empty($queue_token)){
			$done=false;
			$is_error=!$success;
			if($success){
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'sync_t1_gamegateway_stream', 'success'=> $success,
					'result'=>$rlt], $done, $is_error);
			}else{
				$this->queue_result->appendResult($queue_token, [
					'request_id'=>_REQUEST_ID, 'func'=>'sync_t1_gamegateway_stream', 'success'=> $success, 'error_message'=>$error_message,
					'result'=>$rlt] , $done, $is_error);
			}
		}

		$this->utils->debug_log('=========end sync_stream============================');

		if(!$success){
			//any non-zore is error
			exit(1);
		}

		return $success;
	}

}

/// END OF FILE//////////////
