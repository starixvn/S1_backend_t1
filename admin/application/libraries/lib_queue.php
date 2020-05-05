<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Lib_queue {

	private $CI;

	private $_app_prefix;

	const CHANNEL_SUFFIX='-remote-queue';

	public function __construct() {
		$this->CI = &get_instance();
		// $this->CI->load->library(array('lib_gearman'));
		$this->CI->load->helper('string');
		$this->CI->load->model(array('queue_result'));
		$this->utils=$this->CI->utils;

		// $is_staging=config_item('RUNTIME_ENVIRONMENT')=='staging';

        $this->_app_prefix=try_get_prefix();

        // $default_db=config_item('db.default.database');
        // if($default_db!='og'){
        //     $this->_app_prefix=$default_db;
        // }else{
        //     static $_log;
        //     $_log = &load_class('Log');

        //     $this->_app_prefix=$_log->getHostname();
        // }

        // if($is_staging && strpos($this->_app_prefix, 'staging')===false){
        // 	//try append staging
        // 	$this->_app_prefix.='_staging';
        // }

        // $this->redisClient=new Redis();

        // $redis_server_config=$this->CI->utils->getConfig('queue_redis_server');
        // $this->redis_host=isset($redis_server_config['server']) ? $redis_server_config['server'] : null;
        // $this->redis_port=isset($redis_server_config['port']) ? $redis_server_config['port'] : null;

        // $this->redis_timeout=isset($redis_server_config['timeout']) ? $redis_server_config['timeout'] : 1;
        // $this->redis_retry_timeout=isset($redis_server_config['retry_timeout']) ? $redis_server_config['retry_timeout'] : 10;
        // $this->redis_password=isset($redis_server_config['password']) ? $redis_server_config['password'] : 10;

        // if(isset($redis_server_config['database'])){
        // 	$this->redis_database=$redis_server_config['database'];
        // }

        // $this->redis_channel_key=$this->_app_prefix.self::CHANNEL_SUFFIX;

        $rabbitmq_server_config=$this->CI->utils->getConfig('rabbitmq_server');
        $this->rabbitmq_host=isset($rabbitmq_server_config['host']) ? $rabbitmq_server_config['host'] : null;
        $this->rabbitmq_port=isset($rabbitmq_server_config['port']) ? $rabbitmq_server_config['port'] : null;
        $this->rabbitmq_username=isset($rabbitmq_server_config['username']) ? $rabbitmq_server_config['username'] : null;
        $this->rabbitmq_password=isset($rabbitmq_server_config['password']) ? $rabbitmq_server_config['password'] : null;
        $this->rabbitmq_queue_key=$this->_app_prefix.self::CHANNEL_SUFFIX;

	}

    private $online=false;
    private $tried=false;
	// private $hostname;
	// private $redis_host;
	// private $redis_port;
	// private $redis_timeout;
	// private $redis_retry_timeout;
	// private $redis_password;
	// private $redis_database=null;

	// private $redis_channel_key;

	// public function getRedisChannelKey(){
	// 	return $this->redis_channel_key;
	// }

 //    protected function activeRedis(){
 //        $this->tried=true;
 //        if(!$this->online && !empty($this->redis_host)){
 //            try{
 //                $this->online=!!$this->redisClient->connect($this->redis_host, $this->redis_port, $this->redis_timeout, null, $this->redis_retry_timeout);
 //                if(!empty($this->redis_password)){
 //                    $this->redisClient->auth($this->redis_password);
 //                }
 //                if($this->redis_database!==null){
 //                	$this->redisClient->select($this->redis_database);
 //                }
 //            }catch(Exception $e){
 //                $this->CI->utils->error_log('active redis failed', $e);
 //                $this->online=false;
 //            }
 //        }
 //    }

	// public function addJobToRedisChannel($token){

 //        if(!$this->tried){
 //            $this->activeRedis();
 //        }

 //        if(!$this->online){
 //        	$this->CI->utils->error_log('addJobToRedisChannel failed cannot connect to redis');
 //        	$this->CI->queue_result->appendResult($token, ['error_message'=>'add queue failed'], false, true);
 //            return;
 //        }

 //        $rlt=$this->redisClient->lPush($this->redis_channel_key, $token);

 //        return $rlt;
	// }

	public function getRabbitmqQueueKey(){
		return $this->rabbitmq_queue_key;
	}

	public function activeRabbitMQ(){
        $this->tried=true;
        if(!$this->online && !empty($this->rabbitmq_host)){
            try{

				$this->rabbitmq_connection = new AMQPStreamConnection($this->rabbitmq_host, $this->rabbitmq_port, $this->rabbitmq_username, $this->rabbitmq_password);
				$this->rabbitmq_channel = $this->rabbitmq_connection->channel();

                $this->online=!empty($this->rabbitmq_connection);

            }catch(Exception $e){
                $this->CI->utils->error_log('active rabbit mq failed', $e);
                $this->online=false;
                $this->rabbitmq_connection=null;
	        	$this->rabbitmq_channel=null;
            }
        }

	}

	public function closeRabbitMQ(){
        if($this->online && !empty($this->rabbitmq_connection)){
        	try{
	        	$this->online=false;
	        	$this->tried=false;
	        	if(!empty($this->rabbitmq_channel)){
		        	$this->rabbitmq_channel->close();
		        	$this->rabbitmq_channel=null;
	        	}
	        	$this->rabbitmq_connection->close();
	        	$this->rabbitmq_connection=null;
	        }catch(Exception $e){
	        	$this->CI->utils->error_log('closeRabbitMQ failed', $this->CI->utils->getConfig('rabbitmq_server'), $e);
	        }
        }
	}

	public function addJobToRabbitMQ($token){

		$success=false;

        if(!$this->tried){
            $this->activeRabbitMQ();
        }

        if(!$this->online || empty($this->rabbitmq_connection) || empty($this->rabbitmq_channel)){
        	$this->CI->utils->error_log('addJobToRabbitMQ failed cannot connect to rabbitmq', $this->CI->utils->getConfig('rabbitmq_server'));
        	$this->CI->queue_result->appendResult($token, ['error_message'=>'add queue failed'], false, true);
            return $success;
        }

        try{
        	$msg = new AMQPMessage($token);
        	$this->rabbitmq_channel->basic_publish($msg, null, $this->rabbitmq_queue_key);
        	$success=true;
        }catch(Exception $e){
        	$this->CI->utils->error_log('addJobToRabbitMQ failed', $this->CI->utils->getConfig('rabbitmq_server'), $e);
        	$success=false;
        }
        //close it
        $this->closeRabbitMQ();

        return $success;
	}

	/**
	 * @param int systemId
	 * @param string funcName
	 * @param array params
	 * @param string callerType
	 * @param string caller
	 * @param string state it will be sent back to UI
	 *
	 */
	public function addApiJob($systemId, $funcName, $params,
		$callerType, $caller, $state) {
		//check params
		// $token = random_string('unique');
		// $token = $this->CI->queue_result->newResult($systemId, $funcName, $params, $callerType, $caller, $state);

		// $this->CI->lib_gearman->gearman_client();
		// $data = array(
		// 	'system_id' => $systemId,
		// 	'func_name' => $funcName,
		// 	'params' => $params,
		// 	'token' => $token,
		// );
		// $this->CI->lib_gearman->do_job_background('api_job', json_encode($data));
		// return $token;
	}

	public function addEmailJob($to, $from, $fromName, $emailSubject, $emailBody,
		$callerType, $caller, $state, $lang=null) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'email';
		$params = array('to' => $to,
			'from' => $from,
			'fromName' => $fromName,
			'subject' => $emailSubject,
			'body' => $emailBody);
		$token = $this->CI->queue_result->newResult($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

		$is_blocked=false;
		$cmd=$this->CI->utils->generateCommandLine('do_email_job', [$token], $is_blocked);

        $this->runCmd($cmd);
		return $token;
	}

    public function addNewEmailJob($to, $emailSubject, $emailContent, $emailPlainContent, $mode, $callerType, $caller, $state, $lang=null)
    {
        $systemId = Queue_result::SYSTEM_UNKNOWN;
        $funcName = 'email';
        $params = [
            'to'   => $to,
            'from' => '',
            'fromName' => '',
            'subject'   => $emailSubject,
            'body'      => $emailContent,
            'body_text' => $emailPlainContent,
            'new_email' => true,
            'body_mode' => $mode # text or html
        ];
        $token = $this->CI->queue_result->newResult($systemId, $funcName, $params, $callerType, $caller, $state, $lang);

        $is_blocked=false;
        $cmd=$this->CI->utils->generateCommandLine('do_email_job', [$token], $is_blocked);

        $this->runCmd($cmd);
        return $token;
    }

	public function addRemoteSMSJob($mobileNum, $content,
		$callerType, $caller, $state, $lang=null, $isGroup = false) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'remote_sms';

		$params['content'] = $content;
		$params['isGroup'] = $isGroup;

		$mobileList[] = [$mobileNum];

		if ($isGroup && is_array($mobileNum)) {
			$mobileList = $this->arrayScale($mobileNum);
		}

		foreach ($mobileList as $list) {
			$params['mobileNum'] = $list;
			$token = $this->CI->queue_result->newResult($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

			$this->addJobToRabbitMQ($token);
		}
	}

	private function arrayScale($array, $scale = 1000) {

		$rlt = [];
		if (is_array($array)) {
			$sliceArr = [];
			$count = count($array);
			$length = (int) ($count/$scale);

			foreach ($array as $list) {
				$sliceArr[] = $list['contactNumber'];
			}

			for ($i = 0; $i <= $length; $i++ ) {
				$rlt[$i] = array_slice($sliceArr, $i * ($scale - 1), $scale);
			}
		}

		return $rlt;
	}

	public function addLogJob($level, $msg, $host, $callerType, $caller, $state) {

		// $systemId = Queue_result::SYSTEM_UNKNOWN;
		// $funcName = 'publish_log';
		// $params = array(
		// 	'level' => $level,
		// 	'msg' => $msg,
		// 	'host' => $host,
		// );
		// $token = $this->CI->queue_result->newResult($systemId,
		// 	$funcName, $params, $callerType, $caller, $state);

		// $this->CI->lib_gearman->gearman_client();
		// $data = array(
		// 	'system_id' => $systemId,
		// 	'func_name' => $funcName,
		// 	'params' => $params,
		// 	'token' => $token,
		// );
		// $this->CI->lib_gearman->do_job_background('publish_log_job', json_encode($data));
		// return $token;
	}

	public function addExportExcelJob($funcName, $params, $callerType, $caller, $state, $lang=null) {

		// $systemId = Queue_result::SYSTEM_UNKNOWN;
		// // $funcName = 'export_excel';
		// // $params = array(
		// // 	'msg' => $msg,
		// // 	'host' => $host,
		// // );
		// $token = $this->CI->queue_result->newResult($systemId,
		// 	$funcName, $params, $callerType, $caller, $state, $lang);

		// $this->CI->lib_gearman->gearman_client();
		// $data = array(
		// 	'system_id' => $systemId,
		// 	'func_name' => $funcName,
		// 	'params' => $params,
		// 	'token' => $token,
		// );
		// $this->CI->lib_gearman->do_job_background('export_excel_job', json_encode($data));
		// return $token;
	}

	public function generateCommandLine($token, $func){

		$cmd= dirname(__FILE__).'/../../shell/noroot_command.sh';
		$this->CI->utils->debug_log($cmd);

		//app log
		$tmp_dir='/tmp/'.$this->_app_prefix;
		if(!file_exists($tmp_dir)){
			@mkdir($tmp_dir, 0777 , true);
		}
		//convert to realpath
		$cmd='nohup /bin/bash '.realpath($cmd).' '.$func.' '.$token.' 2>&1 > '.$tmp_dir.'/job_'.$func.'_'.$token.'.log &';
		$this->CI->utils->debug_log('full cmd', $cmd);
		return $cmd;
	}

	public function addExportCsvJob($funcName, $params, $callerType, $caller, $state, $lang=null) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		// $funcName = 'export_excel';
		// $params = array(
		// 	'msg' => $msg,
		// 	'host' => $host,
		// );
		//$token = $this->CI->queue_result->newResult($systemId,
		//	$funcName, $params, $callerType, $caller, $state, $lang);

		// if($this->CI->utils->isEnabledFeature('exporting_on_queue')){

		// 	$this->CI->lib_gearman->gearman_client();
		// 	$data = array(
		// 		'system_id' => $systemId,
		// 		'func_name' => $funcName,
		// 		'params' => $params,
		// 		'token' => $token,
		// 	);
		// 	$this->CI->lib_gearman->do_job_background('export_csv_job', json_encode($data));
		// }else{
			//run
			//$cmd=$this->generateCommandLine($token, 'do_export_csv_job');

           // $this->runCmd($cmd);

		// }
		//return $token;
		$is_remote_export = false;
		$export_data_remote_functions=$this->utils->getConfig('export_data_remote_functions');
		$use_export_csv_with_progress_template =$this->utils->getConfig('use_export_csv_with_progress');


		if($this->utils->isAgencySubProject()) {

			if(in_array($funcName, $export_data_remote_functions['agency'])){
				$is_remote_export =  true;
			}


		}else if($this->utils->isAdminSubProject()) {
			if(in_array($funcName, $export_data_remote_functions['admin'])){
				$is_remote_export =  true;
			}
			//aff
		}else{
			if(in_array($funcName, $export_data_remote_functions['aff'])){
				$is_remote_export =  true;
			}
		}


		if($is_remote_export && $this->utils->getConfig('dt_use_fetch_all_on_csv_export')){
			//$params['target_func_name'] = $funcName;
			$csvCommonRemoteExportFunc = 'export_csv';
			//$params[0]['target_func_name'] = $funcName;

			return $this->commonAddRemoteJob($systemId,
				$csvCommonRemoteExportFunc, $params, $callerType, $caller, $state, $lang);

		}else{
			$token = $this->CI->queue_result->newResult($systemId,
				$funcName, $params, $callerType, $caller, $state, $lang);
			//run
			$cmd=$this->generateCommandLine($token, 'do_export_csv_job',true);

			$this->runCmd($cmd);
		}
		return $token;

	}

	public function stopRemoteQueueJob($funcName, $params, $callerType, $caller, $state, $lang=null) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		return $this->commonAddRemoteJob($systemId,
				$funcName, $params, $callerType, $caller, $state, $lang);

	}


	public function runCmd($cmd){
        $this->CI->utils->debug_log(pclose(popen($cmd, 'r')));
	}

	public function addSyncGameLogsJob($fromDateTimeStr, $endDateTimeStr, $game_api_id,
		$callerType, $caller, $state, $lang=null, $playerName = '', $dry_run='false', $timelimit = 30, $merge_only=false) {

		return false;

		// $systemId = Queue_result::SYSTEM_UNKNOWN;
		// $funcName = 'sync_game_logs';
		// $params = ['endDateTimeStr' => $endDateTimeStr,
		// 	'fromDateTimeStr' => $fromDateTimeStr,
		// 	'game_api_id' => $game_api_id,
		// 	'timelimit' => $timelimit,
		// 	'playerName' => $playerName,
		// 	'dry_run'=>$dry_run,
		// 	'merge_only'=>$merge_only];
		// $token = $this->CI->queue_result->newResult($systemId,
		// 	$funcName, $params, $callerType, $caller, $state, $lang);

		// $is_blocked=false;
		// $cmd=$this->CI->utils->generateCommandLine('do_sync_game_logs_job', [$token], $is_blocked);

  //       $this->runCmd($cmd);

		// // $this->CI->lib_gearman->gearman_client();
		// // $data = array(
		// // 	'system_id' => $systemId,
		// // 	'func_name' => $funcName,
		// // 	'params' => $params,
		// // 	'token' => $token,
		// // );
		// // $this->CI->lib_gearman->do_job_background('email_job', json_encode($data));
		// return $token;
	}
	public function addCalculateAffEarnings($startDate, $endDate, $username, $callerType, $caller, $state, $lang = null, $dry_run = 'false') {
		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'calculate_aff_earnings';
		$params = ['startDate' => $startDate,
			'endDate' => $endDate,
			'username' => $username,
			'dry_run'=>$dry_run];
		$token = $this->CI->queue_result->newResult($systemId, $funcName, $params, $callerType, $caller, $state, $lang);
		$is_blocked = false;
		$cmd = $this->CI->utils->generateCommandLine('do_calculate_aff_earnings_job', [$token], $is_blocked);

        $this->runCmd($cmd);

		return $token;
	}

	public function addBroadcastMessageJob($systemId,$funcName, $params, $callerType, $caller, $state, $lang=null) {

		//return $this->addAnyJob('do_boardcast_message_job', $funcName, $params, $callerType, $caller, $state, $lang);
		return $this->commonAddRemoteJob($systemId,$funcName, $params, $callerType, $caller, $state, $lang);

	}

	public function addAnyJob($commandFunc, $funcName, $params, $callerType, $caller, $state, $lang=null, $systemId=Queue_result::SYSTEM_UNKNOWN) {

		$token = $this->CI->queue_result->newResult($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

		//run
		// $cmd=$this->generateCommandLine($token, $commandFunc);
		$is_blocked = false;
		$cmd = $this->CI->utils->generateCommandLine($commandFunc, [$token], $is_blocked);

        $this->runCmd($cmd);

		return $token;

	}

	// public function addCallCFJob($domainList,
	// 	$callerType, $caller, $state, $lang=null) {

	// 	$funcName = 'call_cf';
	// 	$params = array('domainList' => $domainList);

	// 	return $this->addAnyJob('do_call_cf_job', $funcName, $params, $callerType, $caller, $state, $lang);

	// }

	public function addRemoteEmailJob($to, $from, $fromName, $emailSubject, $emailBody,
		$callerType, $caller, $state, $lang=null) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'send_email';
		$params = array('to' => $to,
			'from' => $from,
			'fromName' => $fromName,
			'subject' => $emailSubject,
			'body' => $emailBody);

		return $this->commonAddRemoteJob($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

		// $systemId = Queue_result::SYSTEM_UNKNOWN;
		// $funcName = 'remote_email';
		// $params = array('to' => $to,
		// 	'from' => $from,
		// 	'fromName' => $fromName,
		// 	'subject' => $emailSubject,
		// 	'body' => $emailBody);
		// $token = $this->CI->queue_result->newResult($systemId,
		// 	$funcName, $params, $callerType, $caller, $state, $lang);

		// $this->addJobToRedisChannel($token);

		// $is_blocked=false;
		// $cmd=$this->CI->utils->generateCommandLine('do_email_job', [$token], $is_blocked);

  //       $this->runCmd($cmd);

		// $this->CI->lib_gearman->gearman_client();
		// $data = array(
		// 	'system_id' => $systemId,
		// 	'func_name' => $funcName,
		// 	'params' => $params,
		// 	'token' => $token,
		// );
		// $this->CI->lib_gearman->do_job_background('email_job', json_encode($data));
		// return $token;
	}

	public function addRemoteSyncGameLogsJob($fromDateTimeStr, $toDateTimeStr, $game_api_id,
		$callerType, $caller, $state, $lang=null, $playerName = '', $dry_run='false', $timelimit = 30,
		$merge_only=false, $only_original=false) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'sync_game_logs';
		$params = [
			'fromDateTimeStr' => $fromDateTimeStr,
			'toDateTimeStr' => $toDateTimeStr,
			'game_api_id' => $game_api_id,
			'timelimit' => $timelimit,
			'merge_only'=>$merge_only,
			'only_original'=>$only_original,
			'playerName' => $playerName,
			'dry_run'=>$dry_run,
		];
		// $token = $this->CI->queue_result->newResult($systemId,
		// 	$funcName, $params, $callerType, $caller, $state, $lang);

		// $this->addJobToRedisChannel($token);

		return $this->commonAddRemoteJob($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

		// $is_blocked=false;
		// $cmd=$this->CI->utils->generateCommandLine('do_sync_game_logs_job', [$token], $is_blocked);
        // $this->runCmd($cmd);

		// $this->CI->lib_gearman->gearman_client();
		// $data = array(
		// 	'system_id' => $systemId,
		// 	'func_name' => $funcName,
		// 	'params' => $params,
		// 	'token' => $token,
		// );
		// $this->CI->lib_gearman->do_job_background('email_job', json_encode($data));
		// return $token;
	}

	public function commonAddRemoteJob($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang){

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'remote_'.$funcName;
		$token = $this->CI->queue_result->newResult($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

		if(!empty($token)){
			if(!$this->addJobToRabbitMQ($token)){
				//failed
				$this->CI->queue_result->failedResult($token, ['error'=>'add job to mq failed']);
			}
		}else{
			$token=null;
		}

		return $token;
	}

	public function addRemoteDebugQueue($callerType, $caller, $state, $lang=null) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'debug_queue';
		$params = ['trigger_time'=>$this->utils->getNowForMysql()];

		return $this->commonAddRemoteJob($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

	}

	public function addRemoteImportPlayers($files, $importer_formatter, $callerType, $caller, $state, $lang=null) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'import_players';
		$params = ['files'=>$files, 'importer_formatter'=>$importer_formatter];

		return $this->commonAddRemoteJob($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

	}

	public function addRemotePayCashbackDaily($dateStr, $callerType, $caller, $state, $lang=null) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'pay_cashback_daily';
		$params = ['date'=>$dateStr];

		return $this->commonAddRemoteJob($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

	}

	public function addRemoteBatchAddCashbackBonus($file,$adminUserId,$adminUsername,$reason,$callerType, $caller, $state, $lang=null) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'manually_batch_add_cashback_bonus';
		$params = [
			'file'=>$file,
			'adminUserId' => $adminUserId,
			'adminUsername' => $adminUsername,
			'reason' => $reason,
		];

		return $this->commonAddRemoteJob($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

	}

	public function addRemoteBatchSyncBalanceByJob($systemId, $funcName, $params, $callerType, $caller, $state = null, $lang = null) {

		$systemId = $systemId ?: Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'batch_sync_balance_by';

		// -- add default value to params if empty
		if(empty($params)){
			$params = array(
				'mode' 	 		=> 'last_one_hour',
				'dry_run' 	 	=> 'true',
				'max_number' 	=> '10',
				'apiId' 	 	=> '',
			);
		}

		return $this->commonAddRemoteJob($systemId, $funcName, $params, $callerType, $caller, $state, $lang);

	}

	//===remote events=======================================
	public function triggerAsyncRemoteEvent($systemId, $params, $callerType, $caller, $state = null, $lang = null) {
		if(!$this->CI->utils->getConfig('enabled_remote_async_event')){
			return null;
		}

		$systemId = $systemId ?: Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'trigger_event';

		return $this->commonAddRemoteJob($systemId, $funcName, $params, $callerType, $caller, $state, $lang);
	}

	public function triggerAsyncRemoteDebugEvent($callerType, $caller, $state = null, $lang = null) {
		$params=[
			'event'=>[
				'name'=>Queue_result::EVENT_DEBUG,
				'class'=>'DebugEvent',
				'data'=>[
					'now'=>$this->utils->getNowForMysql(),
					'caller'=>$caller,
				]
			]
		];
		return $this->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params, $callerType, $caller, $state, $lang);
	}

	public function triggerAsyncRemoteDepositEvent($eventName, $playerId, $saleOrderId, $transId, $paymentAccountId,
			$callerType, $caller, $state = null, $lang = null) {
		$params=[
			'event'=>[
				'name'=>$eventName,
				'class'=>'DepositEvent',
				'data'=>[
					'sale_order_id'=>$saleOrderId,
					'transaction_id'=>$transId,
					'player_id'=>$playerId,
					'payment_account_id'=>$paymentAccountId
				]
			]
		];
		return $this->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params, $callerType, $caller, $state, $lang);
	}

	public function triggerAsyncRemoteWithdrawalEvent($eventName, $playerId, $walletAccountId, $transId,
			$callerType, $caller, $state = null, $lang = null) {
		$params=[
			'event'=>[
				'name'=>$eventName,
				'class'=>'WithdrawalEvent',
				'data'=>[
					'wallet_account_id'=>$walletAccountId,
					'transaction_id'=>$transId,
					'player_id'=>$playerId,
				]
			]
		];
		return $this->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params, $callerType, $caller, $state, $lang);
	}

	public function triggerAsyncRemoteRegisterEvent($eventName, $playerId, $callerType, $caller, $state = null, $lang = null) {
		$params=[
			'event'=>[
				'name'=>$eventName,
				'class'=>'RegisterEvent',
				'data'=>[
					'player_id'=>$playerId,
				]
			]
		];
		return $this->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params, $callerType, $caller, $state, $lang);
	}

	public function triggerAsyncRemoteGenerateCommandEvent($eventName, $command, $command_params=null, $is_blocked=false, $callerType, $caller, $state = null, $lang = null) {
		$params=[
			'event'=>[
				'name'=>$eventName,
				'class'=>'GenerateCommandEvent',
				'data'=>[
					'command' => $command,
					'command_params' => $command_params,
					'is_blocked' =>$is_blocked
				]
			]
		];
		return $this->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params, $callerType, $caller, $state, $lang);
	}

	public function triggerAsyncRemoteWithdrawConditionEvent($eventName, $playerId, $withdrawConditionId, $transId,
			$callerType, $caller, $state = null, $lang = null) {
		$params=[
			'event'=>[
				'name'=>$eventName,
				'class'=>'WithdrawConditionEvent',
				'data'=>[
					'withdraw_condition_id'=>$withdrawConditionId,
					'transaction_id'=>$transId,
					'player_id'=>$playerId,
				]
			]
		];
		return $this->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params, $callerType, $caller, $state, $lang);
	}

	public function triggerAsyncRemoteTransferEvent($eventName, $playerId, $transferRequestId, $transId,
			$callerType, $caller, $state = null, $lang = null) {
		$params=[
			'event'=>[
				'name'=>$eventName,
				'class'=>'TransferEvent',
				'data'=>[
					'transfer_request_id'=>$transferRequestId,
					'transaction_id'=>$transId,
					'player_id'=>$playerId,
				]
			]
		];
		return $this->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params, $callerType, $caller, $state, $lang);
	}

	public function triggerAsyncRemoteSyncMDBEvent(array $targetIdArr, $callerType, $caller, $state = null, $lang = null, &$message=null) {
		if(!$this->CI->utils->isEnabledMDB()){
			$message=lang('MDB is disabled');
			return false;
		}

		$params=[
			'event'=>[
				'name'=>Queue_result::EVENT_SYNC_MDB,
				'class'=>'SyncMDBEvent',
				'data'=>[
					'agent_id'=>$this->safeGetValueFromArray($targetIdArr, 'agent_id'),
					'affiliate_id'=>$this->safeGetValueFromArray($targetIdArr, 'affiliate_id'),
					'admin_user_id'=>$this->safeGetValueFromArray($targetIdArr, 'admin_user_id'),
					'role_id'=>$this->safeGetValueFromArray($targetIdArr, 'role_id'),
					'player_id'=>$this->safeGetValueFromArray($targetIdArr, 'player_id'),
					'reg_setting_type'=>$this->safeGetValueFromArray($targetIdArr, 'reg_setting_type'),

					'agent_lock_unique_name'=>$this->safeGetValueFromArray($targetIdArr, 'agent_lock_unique_name'),
					'affiliate_lock_unique_name'=>$this->safeGetValueFromArray($targetIdArr, 'affiliate_lock_unique_name'),
					'admin_user_lock_unique_name'=>$this->safeGetValueFromArray($targetIdArr, 'admin_user_lock_unique_name'),
					'role_lock_unique_name'=>$this->safeGetValueFromArray($targetIdArr, 'role_lock_unique_name'),
					'player_lock_unique_name'=>$this->safeGetValueFromArray($targetIdArr, 'player_lock_unique_name'),
					'reg_setting_lock_unique_name'=>$this->safeGetValueFromArray($targetIdArr, 'reg_setting_lock_unique_name'),
				]
			]
		];
		return $this->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params, $callerType, $caller, $state, $lang);
	}

	private function safeGetValueFromArray($arr, $fieldName){
		return isset($arr[$fieldName]) ? $arr[$fieldName] : null;
	}

	public function triggerAsyncRemoteMonitorHeartBeatEvent($callerType, $caller, $state = null, $lang = null) {
		$params=[
			'event'=>[
				'name'=>Queue_result::EVENT_MONITOR_HEART_BEAT,
				'class'=>'MonitorEvent',
				'data'=>[
					'trigger_time'=>$this->utils->getNowForMysql(),
				]
			]
		];
		return $this->triggerAsyncRemoteEvent(Queue_result::SYSTEM_UNKNOWN, $params, $callerType, $caller, $state, $lang);
	}
	//===remote events=======================================


	public function addSendPlayerPrivateIpMmAlertJob($systemId, $funcName, $params, $callerType, $caller = null, $state = null, $lang = null) {

		if(empty($params) || !is_array($params) || !isset($params['player_id'],$params['http_request_type'],$params['ip_address'],$params['app_prefix'])){
			$this->utils->error_log('LIB QUEUE > addSendPlayerPrivateIpMmAlertJob: Empty/Incomplete params provided. Job will not be processed. PARAMS:', $params);
			return false;
		}

		$systemId 	= $systemId ?: Queue_result::SYSTEM_UNKNOWN;
		$funcName 	= 'send_player_private_ip_mm_alert';
		$callerType = $callerType ?: Queue_result::CALLER_TYPE_ADMIN;

		return $this->commonAddRemoteJob($systemId, $funcName, $params, $callerType, $caller, $state, $lang);

	}

	public function addRemoteBatchAddBonusJob($csv_file, $promoCmsSettingId,
		$release_date, $status, $reason, $show_in_front_end, $callerType, $caller, $state, $lang=null) {

		$systemId = Queue_result::SYSTEM_UNKNOWN;
		$funcName = 'batch_add_bonus';

		$params = [
			'csv_full_path'=>$csv_file,
			'promo_cms_setting_id' => $promoCmsSettingId,
			'release_date' => $release_date,
			'status' => $status,
			'reason' => $reason,
			'show_in_front_end' => $show_in_front_end
		];

		return $this->commonAddRemoteJob($systemId,
			$funcName, $params, $callerType, $caller, $state, $lang);

	}


}

///END OF FILE/////