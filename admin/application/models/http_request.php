<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * http://ogdev.ddns.net:8090/display/OG/PaymentAccount
 *
 *
 */
class Http_request extends BaseModel {

	protected $tableName = 'http_request';

	protected $idField = 'id';

	const TYPE_REGISTRATION = 1;
	const TYPE_LAST_LOGIN = 2;
	const TYPE_DEPOSIT = 3;
	const TYPE_WITHDRAWAL = 4;
	const TYPE_MAIN_WALLET_TO_SUB_WALLET = 5;
	const TYPE_SUB_WALLET_TO_MAIN_WALLET = 6;
	const TYPE_AFFILIATE_BANNER=7;
	const TYPE_AFFILIATE_SOURCE_CODE=8;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * get HTTP Request by playerId and type
	 *
	 * @param	array
	 * @return	array
	 */
	// public function getHttpRequestByData($data) {
	// 	$this->db->select('*');
	// 	$this->db->from('http_request');
	// 	$this->db->where('playerId', $data['playerId']);
	// 	$this->db->where('ip', $data['ip']);
	// 	$this->db->where('referrer', $data['referrer']);
	// 	$this->db->where('user_agent', $data['user_agent']);
	// 	$this->db->where('os', $data['os']);
	// 	$this->db->where('device', $data['device']);
	// 	$this->db->where('is_mobile', $data['is_mobile']);
	// 	$this->db->where('type', $data['type']);

	// 	$query = $this->db->get();

	// 	return $query->result_array();
	// }

	/**
	 * insert HTTP Request
	 *
	 * @param	array
	 * @return	void
	 */
	public function insertHttpRequest($data) {
		return $this->insertData($this->tableName, $data);
	}

	public function getHttpRequst($playerId, $type) {
		return $this->db->get_where($this->tableName, array('playerId' => $playerId, 'type' => $type), 1);
	}

	// public function existingHttpRequest($data) {
	// 	$result = $this->getHttpRequestByData($data);

	// 	if (empty($result)) {
	// 		return true;
	// 	}

	// 	return false;
	// }

	public function syncLastDevice($player_id, $device, $datetime, $requestId) {
		$this->utils->debug_log('player_id', $player_id, 'device', $device);
		//player_device_last_request
		$this->db->from('player_device_last_request')->where('player_id', $player_id);//->where('device', $device);
		$row = $this->runOneRow();

		if (!empty($row)) {
			$this->utils->debug_log('update', 'player_id', $player_id, 'device', $device);
			$this->db->set('last_datetime', $datetime)->set('http_request_id', $requestId)->where('id', $row->id);
			return $this->runAnyUpdate('player_device_last_request');
		} else {
			$this->utils->debug_log('insert', 'player_id', $player_id, 'device', $device);
			$data = array('last_datetime' => $datetime,
				'player_id' => $player_id,
				'device' => $device,
				'http_request_id' => $requestId);
			return $this->insertData('player_device_last_request', $data);
		}
	}

	public function syncLastIP($player_id, $ip, $datetime, $requestId) {
		//player_ip_last_request
		$this->db->from('player_ip_last_request')->where('player_id', $player_id);//->where('ip', $ip);
		$row = $this->runOneRow();
		if (!empty($row)) {
			$this->db->set('last_datetime', $datetime)->set('http_request_id', $requestId)->where('id', $row->id);
			return $this->runAnyUpdate('player_ip_last_request');
		} else {
			$data = array('last_datetime' => $datetime,
				'player_id' => $player_id,
				'ip' => $ip,
				'http_request_id' => $requestId);
			return $this->insertData('player_ip_last_request', $data);
		}
	}

	public function existsIp($ip, $playerId, $type) {
		//check player id in request by type
		$this->db->from($this->tableName)
			->where('playerId !=', $playerId)
			->where('ip', $ip);

		if (!empty($type)) {
			$this->db->where('type', $type);
		}
		$this->limitOneRow();
		return $this->runExistsResult();
	}

	public function getRegistrationIp($playerId) {
		return $this->getIpByType($playerId, self::TYPE_REGISTRATION);
	}

	public function getIpByType($playerId, $type) {
		$this->db->from($this->tableName)->where('playerId', $playerId);

		if (!empty($type)) {
			$this->db->where('type', $type);
		}
		$this->db->order_by('createdat desc');
		$row = $this->runOneRowArray();
		// $this->utils->printLastSQL();

		$ip = null;
		if (!empty($row)) {
			$ip = $row['ip'];
		}
		return $ip;
	}

	public function stringTypeToRequestType($type) {
		// const TYPE_REGISTRATION = 1;
		// const TYPE_LAST_LOGIN = 2;
		// const TYPE_DEPOSIT = 3;
		// const TYPE_WITHDRAWAL = 4;
		// const TYPE_MAIN_WALLET_TO_SUB_WALLET = 5;
		// const TYPE_SUB_WALLET_TO_MAIN_WALLET = 6;

		$reqType = null;

		if ($type == 'registration') {
			$reqType = Http_request::TYPE_REGISTRATION;
		} else if ($type == 'last_login') {
			$reqType = Http_request::TYPE_LAST_LOGIN;
		} else if ($type == 'deposit') {
			$reqType = Http_request::TYPE_DEPOSIT;
		} else if ($type == 'withdrawal') {
			$reqType = Http_request::TYPE_WITHDRAWAL;
		} else if ($type == 'main_wallet_to_sub_wallet') {
			$reqType = Http_request::TYPE_MAIN_WALLET_TO_SUB_WALLET;
		} else if ($type == 'sub_wallet_to_main_wallet') {
			$reqType = Http_request::TYPE_SUB_WALLET_TO_MAIN_WALLET;
		}

		return $reqType;
	}

	public function existsIpWithPromotion($ip, $playerId, $promorulesId, $type = null) {
		//check player id in request by type
		$this->db->distinct()->select('playerId')->from($this->tableName)
			->where('playerId !=', $playerId)
			->where('ip', $ip);

		if (!empty($type)) {
			$this->db->where('type', $type);
		}
		// $this->limitOneRow();
		// return $this->runExistsResult();
		$rows=$this->runMultipleRowArray();
		$playerIdArr=[];
		if(!empty($rows)){
			foreach ($rows as $row) {
				$playerIdArr[]=$row['playerId'];
			}
		}
		$this->utils->printLastSQL();

		if(!empty($playerIdArr)){
			$this->load->model(['player_promo']);
			return $this->player_promo->existsPromotion($playerIdArr, $promorulesId);
		}

		return false;
	}

	public function getLastLoginRequest($playerId){
		$this->db->from($this->tableName)->where('playerId',$playerId)
			->where('type',self::TYPE_LAST_LOGIN)
			->order_by('createdat desc');

		$this->limitOneRow();

		return $this->runOneRowArray();
	}

	public function recordVisitBanner($bannerId, $trackingCode, $trackingSourceCode){
		$type=self::TYPE_AFFILIATE_BANNER;

		$this->load->model(['affiliatemodel']);
		$this->load->library(array('user_agent','session'));

		if(empty($trackingCode)){
			//load from session
			$trackingCode = $this->utils->getTrackingCodeFromSession(); //$this->session->userdata('tracking_code');
			// if(empty($trackingCode)){
				// $this->load->helper('cookie');
				// $trackingCode=get_cookie('_og_tracking_code');
			// }
		}
		$affId=null;
		if(!empty($trackingCode)){
			$affId=$this->affiliatemodel->getAffiliateIdByTrackingCode($trackingCode);
		}
		if(empty($trackingCode)){
			$trackingCode='';
		}
		$bannerName='';
		$bannerUrl='';
		if(!empty($bannerId)){
			$this->db->select('bannerId, bannerName, bannerURL')->from('banner')->where('bannerId', $bannerId)->limit(1);
			$row=$this->runOneRowArray();
			$bannerName=$row['bannerName'];
			$bannerUrl=$row['bannerURL'];
		}

		$headers = $this->input->request_headers();
		$device = ($this->agent->is_mobile() == TRUE) ? $this->agent->mobile() : $this->agent->browser() . " " . $this->agent->version();
		$ip = $this->input->ip_address();
		$now = $this->getNowForMysql();
		$player_id=$this->session->userdata('player_id');

		$data = array(
			"player_id" => $player_id,
			"ip" => $ip,
			"cookie" => isset($headers['Cookie']) ? $headers['Cookie'] : null,
			"referrer" => ($this->agent->is_referral() == TRUE) ? $this->agent->referrer() : ' ',
			"user_agent" => isset($headers['User-agent']) ? $headers['User-agent'] : '',
			"os" => $this->agent->platform(),
			"device" => $device,
			"is_mobile" => ($this->agent->is_mobile() == TRUE) ? 1 : 0,
			"type" => $type,
			"created_at" => $now,
			"affiliate_id"=> $affId,
			"tracking_code"=> $trackingCode,
			"tracking_source_code"=> $trackingSourceCode,
			"banner_id"=>$bannerId,
			"banner_name"=>$bannerName,
			"banner_url"=>$bannerUrl,
		);
		return $this->insertData('affiliate_traffic_stats', $data);

	}

	public function recordPlayerRegistration($player_id,$trackingCode,$trackingSourceCode=null){


 	    $type=self::TYPE_AFFILIATE_SOURCE_CODE;

		$this->load->model(['affiliatemodel']);
		$this->load->library(array('user_agent','session'));

		$affId=null;
		if(!empty($trackingCode)){
			$affId=$this->affiliatemodel->getAffiliateIdByTrackingCode($trackingCode);
		}

		$headers = $this->input->request_headers();
		$device = ($this->agent->is_mobile() == TRUE) ? $this->agent->mobile() : $this->agent->browser() . " " . $this->agent->version();
		$ip = $this->input->ip_address();
		$now = $this->getNowForMysql();
		//$player_id=$this->session->userdata('player_id');

		$data = array(
			"player_id" => $player_id,
			"ip" => $ip,
			"cookie" => isset($headers['Cookie']) ? $headers['Cookie'] : null,
			"referrer" => ($this->agent->is_referral() == TRUE) ? $this->agent->referrer() : ' ',
			"user_agent" => isset($headers['User-agent']) ? $headers['User-agent'] : '',
			"os" => $this->agent->platform(),
			"device" => $device,
			"is_mobile" => ($this->agent->is_mobile() == TRUE) ? 1 : 0,
			"type" => $type,
			"created_at" => $now,
			"affiliate_id"=> $affId,
			"tracking_code"=> $trackingCode,
			"sign_up_player_id" => $player_id,
		    "tracking_source_code"=> $trackingSourceCode,
			);


	   return $this->insertData('affiliate_traffic_stats', $data);

	}



	public function updateVisitRecordSignUp($visit_record_id, $playerId){

		if(!empty($visit_record_id)){

			$this->db->set('sign_up_player_id', $playerId)->set('player_id', $playerId)->where('id', $visit_record_id);

			return $this->runAnyUpdate('affiliate_traffic_stats');
		}

		return true;
	}

	// public function get_summary(){

	// 	$select = 'MAX(id) as id, playerId, ip, cookie, referrer, user_agent, os, device, is_mobile, type, createdat, source_site';

	// 	$qobj = $this->db->select($select)
	// 					 ->group_by(array('type', 'ip', 'playerId'))
	// 					 ->get($this->tableName);

	// 	return $qobj->result_array();

	// }

	public function get_summary_from_last_id($last_id){

		$this->db->select('id, playerId, ip, cookie, referrer, user_agent, os, device, is_mobile, type, max(createdat) createdat, source_site')->where('id >', $last_id)->group_by(array('type', 'ip', 'playerId'));

		return $this->runMultipleRowArray();

	}

	public function merge_summary_once(){

		$sql=<<<EOD
truncate table http_request_summary
EOD;

		$this->runRawUpdateInsertSQL($sql);

// 		$sql[]=<<<EOD
// drop table if exists tmp_http_request_summary
// EOD;

// 		$sql[]=<<<EOD
// create table tmp_http_request_summary
// select max(createdat) createdat, `type`, ip, playerId
// from http_request
// group by `type`, ip, playerId
// EOD;


		$sql=<<<EOD
insert into http_request_summary(playerId, ip, cookie, referrer, user_agent, os, device, is_mobile, `type`, createdat, source_site)
select playerId, ip, cookie, referrer, user_agent, os, device, is_mobile, `type`, max(createdat), source_site
from http_request
group by `type`, ip, playerId
EOD;

		$this->runRawUpdateInsertSQL($sql);


	}

	public function getLoginCount($player_id, $date_from, $date_to) {
		$this->db
			->select('count(*)', 'LoginCount')
			->from($this->tableName)
			->where('type', self::TYPE_LAST_LOGIN)
			->where('playerId', $player_id);
		return $this->runOneRowOneField('LoginCount');
	}

	public function getPlayerLastLogin($player_id) {
		$this->db
			->from($this->tableName)
			->where('type', self::TYPE_LAST_LOGIN)
			->where('playerId', $player_id)
			->order_by('id', 'desc')
			->limit(1)
		;

		$res = $this->runOneRowArray();

		$this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

		return $res;
	}

}

///END OF FILE/////////