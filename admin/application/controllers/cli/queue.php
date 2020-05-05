<?php

use Monolog\Logger;

/**
 * only cli
 *
 *
 *
 */
class Queue extends CI_Controller {

	private static $HALT_FLAG = false;

	public function __construct() {
		parent::__construct();
		// load gearman library
		$this->load->library(array('lib_gearman', 'utils'));
		//only cli
		if (!$this->input->is_cli_request()) {
			//quit
			// echo 'Not allowed';
			show_error('Not allowed', 405);
			exit;
		}

		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
		set_time_limit($default_sync_game_logs_max_time_second);

	}

	// public static function doTestJob($job) {
	// 	$data = unserialize($job->workload());
	// 	print_r($data);
	// 	sleep(10);

	// 	$CI = &get_instance();
	// 	$CI->load->library(array('utils'));
	// 	$d = $CI->utils->getNowForMysql();
	// 	echo "get date from utils:" . $d . "\n";
	// 	echo "Test job is done really.\n\n";
	// }

	// public function test_client() {
	// 	$this->lib_gearman->gearman_client();

	// 	$emailData = array(
	// 		'name' => 'web',
	// 		'email' => 'member@example.com',
	// 	);

	// 	$this->lib_gearman->do_job_background('testJob', serialize($emailData));

	// 	echo "testJob is done.\n";
	// }

	public static function do_sync_job($job) {
		list($data, $CI) = Queue::initJobData($job);
		raw_debug_log('debug', var_export($data, true));
		sleep(3);
		raw_debug_log('debug', "do_sync_job is done.");
	}

	// public static function sendToWebSocketServer($CI, $msg) {
	// 	try {

	// 		// require_once APPPATH . "/libraries/vendor/autoload.php";
	// 		$client = new WebSocket\Client("ws://127.0.0.1:10080/");
	// 		$client->send(json_encode($msg));
	// 		//maybe don't get receive
	// 		raw_debug_log('debug', $client->receive()); // Will output 'Hello WebSocket.org!'
	// 		$client->close();
	// 	} catch (Exception $e) {
	// 		raw_debug_log('[ERROR] [do_api_job] send to websocket failed, token:' . $token);
	// 	}
	// }

	/**
	 *
	 *
	 * @param GearmanJob job {"system_id":int,"":}
	 *
	 */
	public static function do_api_job($job) {
		list($data, $CI) = Queue::initJobData($job);
		raw_debug_log('debug','[do_api_job] data'.json_encode($data));
		$token = $data['token'];
		//validate token first

		$CI->load->model(array('queue_result'));
		$CI->queue_result->reconnectDB();

		$api = $CI->utils->loadExternalSystemLibObject($data['system_id']);
		$rlt=false;
		// $CI->utils->debug_log('loaded:' . $loaded . ' managerName:' . $managerName);
		if ($api) {
			$funcName = $data['func_name'];
			$params = $data['params'];
			//check function name first
			$rlt = call_user_func_array(array($api, $funcName), $params);
			if ($rlt) {
				//save result
				if (!$CI->queue_result->updateResult($token, $rlt)) {
					raw_debug_log('[ERROR] [do_api_job] save result failed, token:' . $token . ' result:' . var_export($rlt, true));
					// log_message('error', 'save result failed, token:' . $token . ' result:' . var_export($rlt, true));
				}
				//push result to target
				// raw_debug_log('debug', "[do_api_job] sendToWebSocketServer");
				// self::sendToWebSocketServer($CI, array('type' => 'system:push_queue_result', 'job_token' => $token));
			} else {
				//set status
				$CI->queue_result->failedResult($token, $rlt);
				raw_debug_log("[ERROR] [do_api_job] call " . $funcName . ", token:" . $token . " failed: " . var_export($params, true));
			}
		} else {
			$CI->queue_result->failedResult($token, 'load system manager failed:' . $data['system_id']);
			raw_debug_log("[ERROR] [do_api_job] load system manager failed: " . var_export($data, true));
		}
		// sleep(3);
		raw_debug_log('debug', "[do_api_job] is done.");
		return $rlt;
	}

	public static function do_halt_job($job) {
		list($data, $CI) = Queue::initJobData($job);
		// log_message('debug', var_export($data, true));
		if ($data && $data['queue_secret'] == $CI->config->item('queue_secret')) {
			raw_debug_log("[ERROR] do_halt_job, try halt...");
			//set flag
			Queue::$HALT_FLAG = true;
			//send empty job to make sure halt
			$CI->load->library(array('lib_gearman'));
			$CI->lib_gearman->gearman_client();
			$CI->lib_gearman->do_job_background('empty_job', serialize(''), 'high');
		}
		// sleep(3);
		// log_message('debug', "do_halt_job is done.");
	}

	public static function do_email_job($job) {
		list($data, $CI) = Queue::initJobData($job);

		// $CI->utils->debug_log('workload data', $data);
		$CI->load->model(array('queue_result'));
		$CI->queue_result->reconnectDB();

		$token = $data['token'];
		$params = $data['params'];
		$to = $params['to'];
		$from = $params['from'];
		$fromName = $params['fromName'];
		$subject = $params['subject'];
		$body = $params['body'];
		$emailData = array(
			'from' => $from,
			'from_name' => $fromName,
			'subject' => $subject,
			'body' => $body,
		);

		raw_debug_log('debug', $token. ' send mail: '.$to.' subject:'.$emailData['subject']);

		if($CI->utils->isSmtpApiEnabled() && $CI->utils->getOperatorSetting('use_smtp_api') == 'true')
        {
            $smtp_api = $CI->utils->getConfig('current_smtp_api');
            $CI->load->library('smtp/'.$smtp_api);
            $CI->load->model('operatorglobalsettings');

            $smtp_api = strtolower($smtp_api);
            $api = $CI->$smtp_api;

            $from_email = isset($data['from']) && !empty($data['from']) ? $data['from'] : $CI->operatorglobalsettings->getSettingValue('smtp_api_mail_from_email') ?: $CI->operatorglobalsettings->getSettingValue('mail_from_email');
            $from_name = isset($data['from_name']) && !empty($data['from_name']) ? $data['from_name'] : $CI->operatorglobalsettings->getSettingValue('smtp_api_mail_from_name') ?: $CI->operatorglobalsettings->getSettingValue('mail_from');

            $SMTP_API_RESULT = $api->sendEmail($to, $from_email, $from_name, $subject, $body);

            $rlt = $api->isSuccess($SMTP_API_RESULT);

            $CI->utils->debug_log("SMTP API RESPONSE: " . var_export($rlt, true));

            if(!$rlt) $CI->utils->debug_log("SMTP API ERROR RESPONSE: " . var_export($api->getErrorMessages($SMTP_API_RESULT), true));

        }
		else
		{
			$CI->load->library(['email_setting', 'email_manager']);
            if (isset($data['new_email']) && $data['new_email']) {
                $rlt = $CI->email_manager->sendEmail($to, $params);
            } else {
                $rlt = $CI->email_setting->sendEmail($to, $emailData);
            }
		}


		if ($rlt === true) {
			if (!$CI->queue_result->updateResult($token, $rlt)) {
				raw_debug_log("[ERROR] [do_email_job] to " . $to . ", token:" . $token . " failed: " . var_export($params, true));
			}
		} else {
			$CI->queue_result->failedResult($token, $rlt);
			raw_debug_log("[ERROR] [do_email_job] to " . $to . ", token:" . $token . " failed: " . var_export($params, true));
		}

		raw_debug_log('debug', "[do_email_job] is done: ".$rlt);
		return $rlt;
	}

	public static $transport=null;
	public static $publisher=null;

	public static function do_publish_log_job($job) {
		list($data, $CI) = Queue::initJobData($job);

		// $CI->utils->debug_log('workload data', $data);
		$CI->load->model(array('queue_result'));

		$token = $data['token'];
		$params = $data['params'];
		$level=$params['level'];
		// $psr_level=\Psr\Log\LogLevel::DEBUG;
		// if($level=='error'){
		// 	$psr_level=\Psr\Log\LogLevel::ERROR;
		// }
		$msg = $params['msg'];
		$host = $params['host'];

		if(!empty($token)){
			$CI->queue_result->reconnectDB();
		}

        // raw_debug_log('call sys log: '.$msg);
		$_log = &load_class('Log');
		// $_log->send_logentries($level, $msg, $host);
		$success=$_log->send_loggly($level, $msg, $host);
		if(!$success){
			raw_debug_log('[ERROR] call loggly failed');
		}

		// try{
		// 	//use logentries

		// 	raw_debug_log('get msg: '.$msg);

		// 	require APPPATH.'/libraries/logentries.php';
		// 	$logentries_settings=$CI->utils->getConfig('logentries_settings');
		// 	$log = Logentries::getLogger($logentries_settings['token'], $logentries_settings['persistent'],
		// 		$logentries_settings['ssl'], $logentries_settings['severity'],
		// 		$logentries_settings['DATAHUB_ENABLED'], $logentries_settings['DATAHUB_IP_ADDRESS'], $logentries_settings['DATAHUB_PORT'],
		// 		$host, $logentries_settings['HOST_NAME'], $logentries_settings['HOST_NAME_ENABLED'],
		// 		$logentries_settings['ADD_LOCAL_TIMESTAMP']);

		// 	if($level==Logger::DEBUG){
		// 		$log->Debug($msg);
		// 	}elseif($level==Logger::INFO){
		// 		$log->Info($msg);

		// 	}elseif($level==Logger::WARNING){
		// 		$log->Warning($msg);

		// 	}elseif($level==Logger::ERROR){
		// 		$log->Error($msg);

		// 	}

		// 	// if(empty(self::$transport)){
		// 	// 	$log_server_address=$CI->utils->getConfig('log_server_address');
		// 	// 	$log_server_port=$CI->utils->getConfig('log_server_port');
		// 	// 	self::$transport = new Gelf\Transport\TcpTransport($log_server_address, $log_server_port);
		// 	// 	self::$publisher = new Gelf\Publisher();
		// 	// 	self::$publisher->addTransport(self::$transport);
		// 	// }

		// 	// $message = new Gelf\Message();
		// 	// $message->setShortMessage($CI->utils->truncateWith($msg,30))
		//  //        ->setLevel($level)
		//  //        ->setFullMessage($msg)
		//  //        ->setFacility($host);

		//  //    self::$publisher->publish($message);
		// }catch(Exception $e){
		// 	$CI->utils->writeQueueErrorLog($e->getMessage());
		// 	$CI->utils->writeQueueErrorLog($e->getTraceAsString());
		// }

		// if ($rlt === true) {
		$rlt=true;
		if(!empty($token)){
			if (!$CI->queue_result->updateResult($token, $rlt)) {
				raw_debug_log("[ERROR] [do_publish_log_job] to " . $to . ", token:" . $token . " failed: " . var_export($params, true));
			}
		}
		// } else {
		// 	$CI->queue_result->failedResult($token, $rlt);
		// 	$CI->utils->writeQueueErrorLog("[do_email_job] to " . $to . ", token:" . $token . " failed: " . var_export($params, true));
		// }

		// $CI->utils->writeQueueErrorLog("[do_publish_log_job] is done.");
		//
		return $rlt;
	}

	public static function do_export_excel_job($job) {
		list($data, $CI) = Queue::initJobData($job);

		// $CI->utils->debug_log('workload data', $data);
		$CI->load->model(array('queue_result'));
		$CI->queue_result->reconnectDB();

		$token = $data['token'];
		$params = $data['params'];
		$funcName = $data['func_name'];
		$rlt=['success'=>false, 'filename'=>null];
		if(!empty($funcName)){
			try{

				raw_debug_log('call '.$funcName, 'params', $params);

				$CI->load->model(['report_model']);
				$result = call_user_func_array(array($CI->report_model, $funcName), $params);
				// $result = $this->report_model->traffic_statistics_aff($affId, $request, $is_export);

				$d = new DateTime();
				$filename=$funcName.'_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999);
				$link = $CI->utils->create_excel($result, $filename, TRUE);

				$rlt=['success'=>true, 'filename'=>$filename.'.xls'];

				raw_debug_log('result '.$funcName, $rlt);

			}catch(Exception $e){
				raw_debug_log('[ERROR] do_export_excel_job error', $e);
				// $CI->utils->writeQueueErrorLog($e->getTraceAsString());
			}
		}else{
			raw_debug_log('[ERROR] do_export_excel_job error: canot call empty function');
		}

		// if ($rlt === true) {
		// $rlt=true;

		if (!$CI->queue_result->updateResult($token, $rlt)) {
			raw_debug_log("[ERROR] [do_export_excel_job] to " , $to , "token", $token, 'funcName', $funcName,
				"failed", $params);
		}

		// } else {
		// 	$CI->queue_result->failedResult($token, $rlt);
		// 	$CI->utils->writeQueueErrorLog("[do_email_job] to " . $to . ", token:" . $token . " failed: " . var_export($params, true));
		// }

		// $CI->utils->writeQueueErrorLog("[do_publish_log_job] is done.");
		//

		$CI->queue_result->closeDB();

		return $CI->utils->encodeJson($rlt);
	}

	public static function generateCommandLine($token, $func){

		$cmd= dirname(__FILE__).'/../../../shell/command.sh';
		raw_debug_log($cmd);

		$log_dir=BASEPATH.'/../application/logs/tmp_shell'; //.$this->_app_prefix;
		// $log_dir='/tmp/'.$this->_app_prefix;
		if(!file_exists($log_dir)){
			@mkdir($log_dir, 0777 , true);
		}
		//convert to realpath
		$cmd='nohup /bin/bash '.realpath($cmd).' '.$func.' '.$token.' 2>&1 > '.$log_dir.'/job_'.$func.'_'.$token.'.log &';
		raw_debug_log('full cmd', $cmd);
		return $cmd;
	}

	public static function do_export_csv_job($job) {
		list($data, $CI) = Queue::initJobData($job);

		// $CI->utils->debug_log('workload data', $data);
		$CI->load->model(array('queue_result'));
		$CI->queue_result->reconnectDB();

		$token = $data['token'];

		//try run command
		$cmd=Queue::generateCommandLine($token, 'do_export_csv_job');

		exec($cmd);

		$rlt=['success'=>true];

		return $CI->utils->encodeJson($rlt);
	}

	public static function do_empty_job($job) {
		//empty
	}

	private static function initJobData($job) {
		$data = null;
		if ($job) {
			$json = $job->workload();
			if (!empty($json)) {
				// log_message('debug', 'start do_halt_job: ' . $json);
				$data = json_decode($json, true);
			}
		}
		$CI = &get_instance();
		$CI->load->library(array('lib_gearman', 'utils'));
		return array($data, $CI);
	}

	private function initJob() {
		$this->lib_gearman->add_worker_function('sync_job', 'Queue::do_sync_job');
		$this->lib_gearman->add_worker_function('api_job', 'Queue::do_api_job');
		$this->lib_gearman->add_worker_function('email_job', 'Queue::do_email_job');
		$this->lib_gearman->add_worker_function('halt_job', 'Queue::do_halt_job');
		$this->lib_gearman->add_worker_function('empty_job', 'Queue::do_empty_job');
		$this->lib_gearman->add_worker_function('publish_log_job', 'Queue::do_publish_log_job');
		$this->lib_gearman->add_worker_function('export_excel_job', 'Queue::do_export_excel_job');
		$this->lib_gearman->add_worker_function('export_csv_job', 'Queue::do_export_csv_job');
	}

	public function worker() {
		Queue::$HALT_FLAG = false;
		$worker = $this->lib_gearman->gearman_worker();
		$this->initJob();

		while ($this->lib_gearman->work()) {
			if (!$worker->returnCode()) {
				// $this->utils->writeQueueErrorLog("worker done successfully");
				// echo "worker done successfully \n";
				raw_debug_log('debug', "worker done successfully");
			}
			if ($worker->returnCode() != GEARMAN_SUCCESS) {
				raw_debug_log("[ERROR] return_code: " . $this->lib_gearman->current('worker')->returnCode());
				// echo "return_code: " . $this->lib_gearman->current('worker')->returnCode() . "\n";
				// break;
			}

			if (Queue::$HALT_FLAG) {
				raw_debug_log("[ERROR] get halt flag, safe halt now");
				break;
			}
		}
	}

}
///END OF FILE////