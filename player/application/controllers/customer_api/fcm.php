<?php
/**
 * Application API
 * Content : [Firebase Cloud Messaging | Firebase]
 * 
 * @author		Bryson
 * @copyright	Tripleonetech 2018
 */

require_once dirname(__FILE__) . '/../BaseController.php';

class Fcm extends BaseController
{
	private $t1tAppMethod;
	private $t1tAppPostData;
	private $t1tAppValidMethod = [
		'setVPNAppFCMToken',
		'setSecureAppFCMToken'
	];
	private $t1tAppRulesMethod = [
		'setVPNAppFCMToken'    => ['notificationToken', 'deviceType', 'signature'],
		'setSecureAppFCMToken' => ['userToken', 'notificationToken', 'deviceType', 'signature']
	];

	public function app($method=null)
	{
		# check method
		if (!in_array($method, $this->t1tAppValidMethod)) {
			return $this->t1tAppResponse(1);
		} else {
			$this->t1tAppMethod = $method;
		}

		# check params
		if (!$this->t1tAppCheckParams()) {
			return $this->t1tAppResponse(2);
		}

		# check signature
		if (!$this->t1tAppCheckSignature()) {
			return $this->t1tAppResponse(3);
		}

		return $this->$method();
	}

	public function t1t_app_get_data()
	{
		$qry = $this->db->get('firebase_cloud_messaging_token');
		$rlt = $qry->result_array();

		return $this->returnJsonResult($rlt);
	}

	private function setVPNAppFCMToken()
	{
		$this->load->model('fcm_model', 'fcm');

		$postData['ip'] = $this->getClientIP();
		$postData['app_type'] = fcm_model::T1T_API_VPNAPP;
		$postData['update_time'] = date('Y-m-d H:i:s');
		$postData['device_type'] = $this->t1tAppPostData['deviceType'];
		$postData['notification_token'] = $this->t1tAppPostData['notificationToken'];

		$this->fcm->addFcmData($postData);

		return $this->t1tAppResponse(200, true);
	}

	private function setSecureAppFCMToken()
	{
		# check user token
		if (!$this->t1tAppCheckUserToken()) {
			return $this->t1tAppResponse(4);
		}

		$this->load->model('fcm_model', 'fcm');

		$postData['ip'] = $this->getClientIP();
		$postData['app_type'] = fcm_model::T1T_API_SECUREAPP;
		$postData['update_time'] = date('Y-m-d H:i:s');
		$postData['player_id']   = $this->t1tAppPostData['player_id'];
		$postData['device_type'] = $this->t1tAppPostData['deviceType'];
		$postData['notification_token'] = $this->t1tAppPostData['notificationToken'];

		if ($this->fcm->checkExiseID($postData['app_type'], $postData['player_id'])) {
			$this->fcm->updateFcmData($postData, $postData['player_id']);
		} else {
			$this->fcm->addFcmData($postData);
		}

		return $this->t1tAppResponse(200, true);
	}

	private function t1tAppCheckParams()
	{
		$rules = $this->t1tAppRulesMethod[$this->t1tAppMethod];

		foreach ($rules as $colName) {
			if (empty($this->input->post($colName))) {
				return false;
			}
		}

		return true;
	}

	private function t1tAppGetPostData()
	{
		$rules = $this->t1tAppRulesMethod[$this->t1tAppMethod];
	
		$postData = [];
		foreach ($rules as $colName) {
			$postData[$colName] = $this->input->post($colName);
		}

		return $this->t1tAppPostData = $postData;
	}

	private function t1tAppCheckSignature()
	{
		$method = $this->t1tAppMethod;
		$postData = $this->t1tAppGetPostData();
		$localSignature = $this->t1tAppGenerateSignature($method, $postData);

		return ($postData['signature'] == $localSignature) ? true : false;
	}

	private function t1tAppCheckUserToken()
	{
		$this->load->model(['common_token']);
		$player_id = $this->common_token->getPlayerIdByToken($this->t1tAppPostData['userToken']);
		$this->t1tAppPostData['player_id'] = $player_id;
		return ($player_id) ? true : false;
	}

	private function t1tAppGenerateSignature($method, $postData)
	{
		unset($postData['signature']);
		$postData['apiKey'] = $this->config->item('app_api_key');

		return md5(urldecode(http_build_query($postData)));
	}

	private function t1tAppResponse($code, $is_success = false)
	{
		$data['success'] = $is_success;
		$data['message'] = $this->t1tAppGetResponseMsg($code);
		$data['code']    = $code;

		$this->returnJsonResult($data);
	}

	private function t1tAppGetResponseMsg($code)
	{
		$codeList = [
			'1' => 'method unknown',
			'2' => 'params incorrect',
			'3' => 'signature invalid',
			'4' => 'token invalid',
			'200' => 'execute success'
		];

		return $codeList[$code];
	}

	private function getClientIP()
	{
		if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$clientIP = $_SERVER['HTTP_CLIENT_IP'];
		} else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$clientIP= $_SERVER['REMOTE_ADDR'];
		}
		return $clientIP;
	}
}