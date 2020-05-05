<?php
if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * SMS Verification
 */
class Sms_verification extends BaseModel {
	protected $tableName = 'sms_verification';
	private $sms_valid_duration = 86400;
	private $smsValidTime;

	private $restrictArea = [
		1, // player center password verification
	];

	const USAGE_DEFAULT						= 'default';
	const USAGE_COMAPI_MOBILE_REG			= 'comapi_mobile_reg';
	const USAGE_COMAPI_SMS_VALIDATE 		= 'comapi_sms_validate';
	const USAGE_COMAPI_PASSWORD_RECOVERY	= 'comapi_password_recovery';

	const SESSION_ID_DEFAULT		= '0badface';

	const DAYS_OLD_SMS_EXPIRY		= 30;

	function __construct() {
		parent::__construct();
		// $this->smsValidTime = strtotime('-'.$this->config->item('sms_valid_time').' minutes');
		$sms_valid_duration_config = (int) $this->config->item('sms_valid_time');
		$this->sms_valid_duration = empty($sms_valid_duration_config) ? $this->sms_valid_duration : $sms_valid_duration_config;
		$this->smsValidTime = time() - $this->sms_valid_duration;
	}

	# Returns the number of verification codes generated during the past minute
	public function getVerificationCodeCountPastMinute() {
		$this->db->where('create_time > ', date('Y-m-d H:i:s', time() - 60));
		$query = $this->db->get($this->tableName);
		$this->utils->debug_log($this->db->last_query());
		return $query->num_rows();
	}

	// public function getVerificationCode($playerId, $sessionId, $mobileNumber, $restrictArea = null) {
	// 	$code = mt_rand(100000, 999999);	# a random 6-digit number
	// 	$entry = array(
	// 		'session_id' => $sessionId,
	// 		'mobile_number' => $mobileNumber,
	// 		'code' => $code,
	// 		'verified' => 0,
	// 		'ip' => $this->utils->getIP(),
	// 		'playerId' => ($playerId) ? $playerId : null
	// 	);

	// 	if (!empty($restrictArea) && $this->isRestrictArea($restrictArea)) {
	// 		$entry['restrict_area'] = $restrictArea;
	// 	}

	// 	$this->db->set('create_time', 'NOW()', FALSE);
	// 	$this->db->insert($this->tableName, $entry);
	// 	return $code;
	// }

	# Returns true if the validation is successful, and code is validated
	// public function validateVerificationCode($playerId, $sessionId, $mobileNumber, $code, $restrictArea = null) {
	// 	$this->utils->debug_log("========= smsValidTime ============",date('Y-m-d H:i:s', $this->smsValidTime));

	// 	$recode = $this->gettLastVcode($playerId, $sessionId, $mobileNumber, $restrictArea);

	// 	if ($recode && ($code == $recode['code'])) {
	// 		$this->db->where('id', $recode['id']);
	// 		$this->db->update($this->tableName, array('verified' => 1));
	// 		return $this->db->affected_rows() > 0;
	// 	}
	// 	return false;
	// }

	/**
	 * Get first unvalidated verification code for given session and number.  If not available, generate one.
	 * Vulnerability: order for the query is not given.  But not sure about the usage scenario.
	 * @param	string	$sessionId		The session ID
	 * @param	numeric	$mobileNumber	The phone number
	 * @param	int		$usage 			Usage, see defined USAGE_* constants above
	 * @return	string	The verification in effect now
	 */
	public function getVerificationCode($playerId, $sessionId, $mobileNumber, $usage = self::USAGE_DEFAULT) {
		# Query whether there is an un-verified verification code for this session created within an hour
		if (!empty($playerId)) { $this->db->where('playerId', $playerId); }
		$this->db->where('session_id', $sessionId);
		$this->db->where('mobile_number', $mobileNumber);
		$this->db->where('usage', $usage);
		$this->db->where('create_time > ', date('Y-m-d H:i:s', $this->smsValidTime));
		$this->db->where('verified', 0);
		$query = $this->db->get($this->tableName);

		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->code;
		}
		else {
			return $this->createVerificationCode($sessionId, $mobileNumber, $playerId, $usage);
		}
	}


	/**
	 * Attempt to verify the code by given code/session/mobile number.
	 * Sets verified = 1 for the row that matches given code/session/mobile number tuple, or do nothing if no row matches.
	 * @param	string	$sessionId	The session ID
	 * @param	numeric	$mobileNumber	The phone number
	 * @param	string	$code		The validation code
	 * @param	int		$playerId	== player.playerId
	 * @param	int		$usage 		Usage, see defined USAGE_* constants above.
	 *
	 * @return	bool	true if one or more matching codes are verified, false if no successful verification.
	 */
	public function validateVerificationCode($playerId = null, $sessionId, $mobileNumber, $code, $usage = self::USAGE_DEFAULT) {
		if (!empty($playerId)) { $this->db->where('playerId', $playerId); }
		if (empty($sessionId)) { $sessionId = self::SESSION_ID_DEFAULT; }
		if($sessionId != self::SESSION_ID_DEFAULT) {
			$this->db->where('session_id', $sessionId);
		}
		$this->db->where('mobile_number', $mobileNumber);
		$this->db->where('usage', $usage);
		$this->db->where('create_time > ', date('Y-m-d H:i:s', $this->smsValidTime));
		$this->db->where('code', $code);
		$this->db->update($this->tableName, array('verified' => 1));
		$this->utils->debug_log(__METHOD__, 'sql', $this->db->last_query());

		return $this->db->affected_rows() > 0;
	}

	/**
	 * Returns number of un-validated codes for given session/mobile number/player
	 * @param	string	$sessionId	The session ID
	 * @param	int		$playerId	== player.playerId
	 * @param	numeric	$mobileNumber	The phone number
	 * @param	int		$usage 		Usage, see defined USAGE_* constants above.
	 *
	 * @return	int		Count of pending validation codes
	 */
	public function playerValidationStatus($sessionId, $mobileNumber, $playerId = null, $usage = self::USAGE_DEFAULT) {
		if (!empty($playerId)) { $this->db->where('playerId', $playerId); }
		if (empty($sessionId)) { $sessionId = self::SESSION_ID_DEFAULT; }
		$this->db	//->from($this->table)
			->where('session_id', $sessionId)
			->where('mobile_number', $mobileNumber)
			->where('usage', $usage)
			->where('create_time > ', date('Y-m-d H:i:s', $this->smsValidTime))
			->where('verified', 0)
		;

		$res = $this->db->count_all_results($this->tableName);

		$this->utils->debug_log(__METHOD__, 'valid sql', $this->db->last_query());

		$this->utils->debug_log(__METHOD__, 'result', $res);

		return intval($res);
	}

	/**
	 * Creates verification code for given session/mobile number tuple.
	 * @param	string	$sessionId	The session ID
	 * @param	numeric	$mobileNumber	The phone number
	 * @param	int		$usage 		Usage, see defined USAGE_* constants above.
	 * @return	stromg	The just-generated validation code.
	 */
	private function createVerificationCode($sessionId, $mobileNumber, $playerId = null, $usage = self::USAGE_DEFAULT) {
		$code = mt_rand(100000, 999999);	# a random 6-digit number
		$entry = array(
			'session_id'	=> $sessionId,
			'playerId'		=> $playerId,
			'mobile_number'	=> $mobileNumber,
			'code'			=> $code,
			'verified'		=> 0,
			'ip'			=> $this->utils->getIP(),
			'usage'			=> $usage
		);

		$this->db->set('create_time', $this->utils->getNowForMysql());
		$this->db->insert($this->tableName, $entry);
		return $code;
	}

	private function gettLastVcode($playerId, $sessionId, $mobileNumber, $restrictArea = null) {
		$condition = [
			"session_id" => $sessionId,
			"mobile_number" => $mobileNumber,
			"create_time >" => date('Y-m-d H:i:s', $this->smsValidTime),
			"playerId" => ($playerId) ? $playerId : null
		];

		$this->db->order_by("id", "desc");
		$qry = $this->db->get_where($this->tableName, $condition);
		$res = $qry->row_array();

		return $res;
	}

	# Returns a list of verification codes sent, joined with the player info
	# This join is 1-1 if player contactNumber is unique
	public function listVerificationCodes($request,$is_export = false) {
		$this->load->library('data_tables');
		$i = 0;
		$columns = array(
			array(
				'alias' => 'username',
				'select' => 'player.username',
				'name' => lang('Username'),
				'dt' => $i++,
			),
			array(
				'alias' => 'session_id',
				'select' => $this->tableName.".session_id",
				'name' => lang('Session'),
				'dt' => $i++,
			),
			array(
				'alias' => 'mobile_number',
				'select' => $this->tableName.".mobile_number",
				'name' => lang('Mobile Number'),
				'dt' => $i++,
				'formatter'=> function($d){
                    if($this->utils->isEnabledFeature('display_all_numbers_of_mobile')){
                        return $d;
                    }
                        return $this->utils->keepOnlyString($d, 6);
                }
			),
			array(
				'alias' => 'code',
				'select' => $this->tableName.".code",
				'name' => lang('Verification Code'),
				'dt' => $i++,
			),
			array(
				'alias' => 'verified',
				'select' => $this->tableName.".verified",
				'name' => lang('Verified'),
				'dt' => $i++,
			),
			array(
				'alias' => 'create_time',
				'select' => $this->tableName.".create_time",
				'name' => lang('Created'),
				'dt' => $i++,
			),
			array(
				'alias' => 'timeout',
				'select' => 'DATE_ADD('.$this->tableName.'.create_time, INTERVAL '.$this->config->item('sms_valid_time').' second)',
				'name' => lang('Timeout'),
				'dt' => $i++,
			)
		);
		$input = $this->data_tables->extra_search($request);
		$where = array("mobile_number <> ''");
		$values = array();
		$joins = array();
		$joins['playerdetails'] = 'BINARY '.$this->tableName.'.mobile_number = BINARY playerdetails.contactNumber';
		$joins['player'] = 'playerdetails.playerId = player.playerId';
		$group_by = array();
		$having = array();

		if (isset($input['date_from'], $input['date_to'])) {
			$where[] = "create_time BETWEEN ? AND ?";
			$values[] = $input['date_from'];
			$values[] = $input['date_to'];
		}

		if (isset($input['username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['username'] . '%';
		}

		if (isset($input['mobileNumber'])) {
			$where[] = $this->tableName.'.mobile_number LIKE ?';
			$values[] = '%' . $input['mobileNumber'] . '%';
		}

		return $this->data_tables->get_data($request,$columns,$this->tableName,$where,$values,$joins,$group_by,$having,false);
	}

	public function isRestrictArea($num) {
		return in_array($num, $this->restrictArea);
	}

	public function sendNumDaily($playerId, $restrictArea) {
		$condition = [
			'playerId' => $playerId,
			'create_time >=' => date("Y-m-d 00:00:00"),
			'create_time <=' => date("Y-m-d 23:59:59"),
			'restrict_area' => $restrictArea
		];

		$this->db->where($condition);
		$query = $this->db->get($this->tableName);
		return $query->num_rows();
	}

	public function getTodaySMSCountFor($mobileNumber) {
		$condition = [
			'create_time >=' => date("Y-m-d 00:00:00"),
			'create_time <=' => date("Y-m-d 23:59:59"),
			'mobile_number' => $mobileNumber
		];

		$this->db->where($condition);
		$query = $this->db->get($this->tableName);
		return $query->num_rows();
	}

	public function checkIPAndMobileLastTime($smsCooldownTime, $mobileNumber) {
		$currentIP = $this->db->escape($this->utils->getIP());
		$mobileNumber = $this->db->escape($mobileNumber);
		$this->db->where("(ip = $currentIP OR mobile_number = $mobileNumber)");
		$this->db->where('create_time > ', date('Y-m-d H:i:s', time() - $smsCooldownTime));
		$qry = $this->db->get($this->tableName);
		return ($qry->num_rows()) ? true : false;
	}

	/**
	 * Delete expired SMS message.  Expiry time determined by class constant.
	 * Used by Command::clearTimeoutSms().  OGP-12075.
	 * @see		Sms_verification::DAYS_OLD_SMS_EXPIRY
	 * @see		Command::clearTimeoutSms()
	 * @return	array 	Combined message for deletion operation
	 */
	public function deleteTimeoutSms() {
		$days_old_sms_expiry = self::DAYS_OLD_SMS_EXPIRY;
		$offset_days = "-{$days_old_sms_expiry} days";
		$date_before = date('c', strtotime( $offset_days ));
		$this->db->where('create_time <=', $date_before)
			->delete($this->tableName);
		$rows_deleted = $this->db->affected_rows();
		$mesg = "Keeping only SMS in last {$days_old_sms_expiry} days.  Deleted {$rows_deleted} rows before {$date_before} from sms_verification.";

		return $mesg;
	}
}
