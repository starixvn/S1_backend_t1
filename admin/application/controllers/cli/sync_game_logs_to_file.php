<?php
require_once dirname(__FILE__) . "/base_cli.php";

/**
 * only cli
 *
 *
 *
 */
class Sync_game_logs_to_file extends Base_cli {

	const LOG_TITLE = '[init_game_logs]';

	public function __construct() {
		parent::__construct();
	}

	// private $platformCode = MG_API;

	public function index() {
		set_time_limit(0);

		// $token = 'useless';
		// $dateTimeFrom = new DateTime('2015-04-01');
		// $dateTimeTo = new DateTime('2015-10-30');
		// $playerName = 'actfmg1';

		// $api->syncInfo[$token] = array("playerName" => null, "dateTimeFrom" => $dateTimeFrom, "dateTimeTo" => $dateTimeTo);

		$this->syncMG();

		// $jsonStr = file_get_contents('/home/vagrant/Code/result_of_mg.txt');

		// var_export(json_decode($jsonStr));

		// echo "\n" . json_last_error_msg();
		//
		//
	}

	private $platformCode = null;

	protected function loadApi() {
		$this->load->library('game_platform/game_platform_manager', array("platform_code" => $this->platformCode));
		return $this->game_platform_manager->initApi($this->platformCode);
	}

	public function syncMG() {
		$this->platformCode = MG_API;

		$api = $this->loadApi();

		$rlt = $api->convertGameRecordsToFile();

		//get last row id from db
		// $this->load->model(array('external_system'));
		// $sys = $this->external_system->getSystemById($this->platformCode);
		// $lastRowId = $sys->last_sync_id;

		// if (empty($lastRowId)) {
		// 	$lastRowId = 0;
		// }

		// $apiName = 'GetSpinBySpinData';
		// $params = array('LastRowId' => $lastRowId);

		// $url = $this->getConfig('mg_web_api_url') . '/' . $apiName;

		// list($header, $resultText, $statusCode, $statusText, $errCode, $error, $resultObj) =
		// $this->httpCallApiLongTime($url, $params,
		// 	function ($ch) {
		// 		//no ssl
		// 	},
		// 	function ($params) {
		// 		//getHttpHeaders
		// 		$loginName = $this->getConfig('mg_login_name');
		// 		$pinCode = $this->getConfig('mg_pin_code');

		// 		$postJson = json_encode($params);

		// 		return array(
		// 			'Authorization' => 'Basic ' . base64_encode($loginName . ':' . $pinCode),
		// 			'Content-Length' => strlen($postJson),
		// 			'Content-Type' => 'application/json',
		// 		);

		// 	},
		// 	function ($ch, $params) {
		// 		//customHttpCall
		// 		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		// 		$postJson = json_encode($params);

		// 		curl_setopt($ch, CURLOPT_POST, 1);
		// 		curl_setopt($ch, CURLOPT_POSTFIELDS, $postJson);
		// 	},
		// 	3600);

		// $success = !$this->isErrorCode($apiName, $params, $statusCode, $errCode, $error);
		// // $this->CI->utils->debug_log('success', $success);
		// $responseResultId = $this->saveResponseResult($success, $apiName, $params, $resultText, $statusCode, $statusText, $header);
		// if ($success) {
		// 	$rltJson = json_decode($resultText);
		// 	$this->outputInfo(var_export($rltJson, true));

		// 	$success = !$rltJson->Status->ErrorCode;
		// 	if ($success) {
		// 		$this->outputInfo('success');
		// 		//split result by hour
		// 	}
		// }

		// $this->utils->debug_log('success', $success);

		// var_dump($rlt);

	}

	protected function outputInfo($msg) {
		echo $msg . "\n";
	}

	// protected function getConfig($key) {
	// 	return $this->config->item($key);
	// }

	// protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
	// 	// $statusCode = intval($statusCode, 10);
	// 	return $errCode || intval($statusCode, 10) >= 400;
	// }

	// protected function saveResponseResult($success, $apiName, $params, $resultText, $statusCode, $statusText = null, $extra = null, $field = null) {
	// 	//save to db
	// 	$this->load->model("response_result");
	// 	$flag = $success ? Response_result::FLAG_NORMAL : Response_result::FLAG_ERROR;
	// 	return $this->response_result->saveResponseResult($this->platformCode, $flag, $apiName, json_encode($params), $resultText, $statusCode, $statusText, $extra, $field);
	// }

	// protected function httpCallApiLongTime($url, $params, $initSSL, $getHttpHeaders, $customHttpCall, $timeout = 3600) {
	// 	//write to temp file then process file
	// 	// $responseFile = tempnam(sys_get_temp_dir(), 'sync_game_');
	// 	$this->utils->debug_log("url", $url, "params", $params);

	// 	//call http
	// 	$content = null;
	// 	$header = null;
	// 	$statusCode = null;
	// 	$statusText = '';
	// 	$ch = null;
	// 	try {
	// 		$ch = curl_init();

	// 		// $fp = fopen($responseFile, 'w');

	// 		curl_setopt($ch, CURLOPT_URL, $url);
	// 		curl_setopt($ch, CURLOPT_HEADER, true);
	// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// 		//set timeout
	// 		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	// 		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

	// 		$initSSL($ch);

	// 		$headers = $this->utils->convertArrayToHeaders($getHttpHeaders($params));
	// 		if (!empty($headers)) {
	// 			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// 		}
	// 		//write to file
	// 		// curl_setopt($ch, CURLOPT_FILE, $fp);

	// 		//process post
	// 		$customHttpCall($ch, $params);

	// 		$response = curl_exec($ch);
	// 		$errCode = curl_errno($ch);
	// 		$error = curl_error($ch);

	// 		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	// 		$header = substr($response, 0, $header_size);
	// 		$content = substr($response, $header_size);

	// 		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	// 		$last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

	// 		$statusText = $errCode . ':' . $error;

	// 		curl_close($ch);
	// 		// fclose($fp);

	// 		// $processResponse($responseFile);
	// 		//delete temp file
	// 		// unlink($responseFile);

	// 	} catch (Exception $e) {
	// 		$this->processError($e);
	// 	}
	// 	return array($header, $content, $statusCode, $statusText, $errCode, $error, null);

	// }

}

/// END OF FILE//////////////