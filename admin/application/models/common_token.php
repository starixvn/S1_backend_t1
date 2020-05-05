<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * player_id
 * admin_user_id
 * affiliate_id
 * token
 * timout
 * created_at
 * updated_at
 * status
 *
 */
class Common_token extends BaseModel {

	function __construct() {
		parent::__construct();
		$this->load->helper('string');
	}

	protected $tableName = "common_tokens";

	const BACKEND_USER_ID=-1;

	/**
	 * get player id token
	 *
	 * @param 	int playerId
	 * @return 	int last insert id
	 */
	public function getPlayerToken($playerId) {
		if(empty($playerId)){
			return null;
		}
		//check available token
		// $token = $this->getAvailableToken($playerId, 'player_id');
		// if (empty($token)) {
		// 	$token = $this->createTokenBy($playerId, 'player_id');
		// }

		list($token, $sign_key) = $this->getAvailableTokenWithSignKey($playerId, 'player_id');
		if (empty($token) || empty($sign_key)) {
			list($token, $sign_key) = $this->createTokenWithSignKeyBy($playerId, 'player_id');
		}

		// $this->utils->debug_log('get player token ', $playerId, $token);
		$this->utils->debug_log('player token/sign_key', [ 'playerId' => $playerId, 'token' => $token, 'sign_key' => $sign_key ]);
		return $token;
	}

////////////////////////////////////////////////
	public function getValidPlayerToken($playerId) {
		//check available token
		$token = $this->getAvailableToken($playerId, 'player_id');

		return $token;
	}

	public function isTokenValid($playerId, $token){

		$this->db->select('COUNT(id) as count');
		$this->db->from($this->tableName);
		$this->db->where('player_id', $playerId);
		$this->db->where('token', $token);
		$this->db->where('status', self::STATUS_NORMAL);
		$this->db->where('timeout_at >=', $this->getNowForMysql());
		$count = $this->runOneRowOneField('count');
		$result = $count > 0;

		// $this->utils->printLastSQL();

		if($result){
			$this->updatePlayerToken($playerId, $token);
		}

		return $result;
	}

	public function updatePlayerToken($playerId, $token, $timeout = null){
		$timeout = $timeout ?: $this->utils->getConfig('token_timeout');
		$d = new DateTime();
		$d->modify('+' . $timeout . ' seconds');

		$update_data = array(
			'updated_at' => $this->getNowForMysql(),
			'timeout_at' => $this->utils->formatDateTimeForMysql($d),
		);

		$this->db->where('player_id', $playerId);
		$this->db->where('token', $token);
//		$this->db->where('timeout_at >=', $this->getNowForMysql());

		$this->db->update($this->tableName, $update_data);

		return $this->db->affected_rows() > 0;
	}

////////////////////////////////////////////////

	public function getAdminUserToken($adminUserId, $db=null) {
		if(empty($adminUserId)){
			return null;
		}
		$field = 'admin_user_id';
		//check available token
		//getAvailableToken($id, $field, &$timeout_datetime=null, $db=null)
		$timeout_datetime=null;
		$token = $this->getAvailableToken($adminUserId, 'admin_user_id', $timeout_datetime, $db);
		if (empty($token)) {
			//createTokenBy($id, $field, $timeout = null, &$timeout_datetime=null, $db=null) {
			$token = $this->createTokenBy($adminUserId, 'admin_user_id', null, $timeout_datetime, $db);
		}
		return $token;
	}

	public function getAffiliateToken($affiliateId) {
		if(empty($affiliateId)){
			return null;
		}
		$field = 'affiliate_id';
		//check available token
		$token = $this->getAvailableToken($affiliateId, 'affiliate_id');
		if (empty($token)) {
			$token = $this->createTokenBy($affiliateId, 'affiliate_id');
		}
		return $token;
	}

	public function getPlayerIdByToken($token) {
		return $this->getIdByAvailableToken('player_id', $token);
	}

	/**
	 * get player info by token
	 * @param  string $token
	 * @return array player info from table
	 */
	public function getPlayerInfoByToken($token) {
		$playerInfo=null;
		$playerId=$this->getPlayerIdByToken($token);
		if(!empty($playerId)){
			$this->load->model(['player_model']);
			$playerInfo=$this->player_model->getPlayerArrayById($playerId);
		}
		return $playerInfo;
	}

	public function disableToken($token) {
		$this->db->set('status', self::STATUS_DISABLED);
		$this->db->where('token', $token);
		$this->db->update($this->tableName);
		return $this->db->affected_rows() > 0;
	}

	public function getAdminUserIdByToken($token) {
		return $this->getIdByAvailableToken('admin_user_id', $token);
	}

	public function getAffiliateIdByToken($token) {
		return $this->getIdByAvailableToken('affiliate_id', $token);
	}

	public function getMerchantIdByToken($token) {
		return $this->getIdByAvailableToken('merchant_id', $token);
	}

    public function getAgentIdByToken($token) {
        return $this->getIdByAvailableToken('agent_id', $token);
    }

	public function getIdByAvailableToken($field, $token) {
		$this->db->from($this->tableName)->where('token', $token)
			->where('timeout_at >=', $this->getNowForMysql())
			->where('status', self::STATUS_NORMAL);
		$oneRowField = $this->runOneRowOneField($field);
// $this->utils->debug_log('getIdByAvailableToken oneRowField', $oneRowField);
		if( empty($oneRowField)
			&& $this->utils->isEnabledMDB()
		){
			$readonly = true;
			$excludeCurrencyDB=$this->utils->getActiveCurrencyKeyOnMDB();
			$foreachMultipleDB = $this->foreachMultipleDBWithoutSourceDB($excludeCurrencyDB, function($db, &$result) use($field, $token, &$oneRowField){
				$db->from($this->tableName)->where('token', $token)
					->where('timeout_at >=', $this->getNowForMysql())
					->where('status', self::STATUS_NORMAL);
				$result = $this->runOneRowOneField($field, $db);
				if( ! empty($result) ){ // fount it, always runs even if $success=false
					$success = true;
					$oneRowField = $result; // keep result and pass var.
				}else{
					$success = false;
				}
				return $success;
			}, $readonly);
			// $this->utils->debug_log('getIdByAvailableToken foreachMultipleDB', $field, $token, $foreachMultipleDB, $oneRowField);
		}

		return $oneRowField;
	}

	public function getAvailableToken($id, $field, &$timeout_datetime=null, $db=null) {
		if(empty($db)){
			$db=$this->db;
		}
		// at least one minute available
		$dt=new DateTime('-1 minute');

		$db->select('timeout_at, token')->from($this->tableName)->where($field, $id)
			->where('timeout_at >=', $this->utils->formatDateTimeForMysql($dt))
			->where('status', self::STATUS_NORMAL)
			->order_by('id', 'desc')
			->limit(1);

		$row=$this->runOneRowArray($db);

		if(!empty($row)){
			$timeout_datetime=$row['timeout_at'];
			return $row['token'];
		}

		return null;

	}

	public function createTokenBy($id, $field, $timeout = null, &$timeout_datetime=null, $db=null) {
		if(empty($db)){
			$db=$this->db;
		}
		$token = random_string('unique');
		$timeout = $timeout ?: $this->utils->getConfig('token_timeout');
		$d = new DateTime();
		$d->modify('+' . $timeout . ' seconds');

		$timeout_datetime=$this->utils->formatDateTimeForMysql($d);

		$data=[$field => $id,
			"token" => $token,
			"created_at" => $this->getNowForMysql(),
			'updated_at' => $this->getNowForMysql(),
			'timeout_at' => $timeout_datetime,
			'timeout' => $timeout,
			'status' => self::STATUS_NORMAL,];
		$id=$this->runInsertData($this->tableName, $data, $db);

		if(empty($id)){
			$this->utils->error_log('create token failed', $this->tableName, $data);
			return null;
		}else{
			return $token;
		}
	}

	public function getPlayerIdByOldToken($token) {
		return $this->getIdByOldToken('player_id', $token);
	}

	public function getIdByOldToken($field, $token){
		$this->db->from($this->tableName)->where('token', $token)
			->where('status', self::STATUS_NORMAL);
		return $this->runOneRowOneField($field);
	}

	public function getPlayerInfoByOldToken($token){
		$playerInfo=null;
		$playerId=$this->getPlayerIdByOldToken($token);
		if(!empty($playerId)){
			$this->load->model(['player_model']);
			$playerInfo=$this->player_model->getPlayerArrayById($playerId);
		}
		return $playerInfo;
	}

	public function getSignKeyByCode($merchant_code){
		if(empty($merchant_code)){
			return null;
		}

		return $this->getSignKeyFrom($this->getMerchantInfoByCode($merchant_code));
	}

	public function getSignKeyFrom($merchantInfo){
		if(!empty($merchantInfo)){
			if($merchantInfo['live_mode']){
				return $merchantInfo['live_sign_key'];
			}else{
				return $merchantInfo['staging_sign_key'];
			}
		}

		return null;
	}

	public function getSecureKeyFrom($merchantInfo){
		if(!empty($merchantInfo)){
			if($merchantInfo['live_mode']){
				return $merchantInfo['live_secure_key'];
			}else{
				return $merchantInfo['staging_secure_key'];
			}
		}

		return null;
	}

	public function generateSign($fields, $signKey, $except=['sign']){

		$signString=$this->getSignString($fields, $except);
		if(empty($signString)){
			return ['',''];
		}

		$sign=strtolower(sha1($signString.$signKey));

		return [$sign, $signString];
	}

	public function getSignString($fields, $except=['sign']){
		$params=[];
		foreach ($fields as $key => $value) {
			if( in_array($key, $except) || is_array($value)){
				continue;
			}
			$params[$key]=$value;
		}

		if(empty($params)){
			return '';
		}

		ksort($params);

		return implode('', array_values($params));
	}

	public function isValidSecureKey($agent, $secure_key){
		return $this->getSecureKeyFrom($agent)==$secure_key;
	}

	public function isValidAuthToken($agent, $auth_token){
		//query token
        $agent_id=$this->getAgentIdByToken($auth_token);
		return $agent_id==$agent['agent_id'];
	}

	public function generateAuthKeyBySecureKey($agent, $secure_key, $always_new=false){
		if($this->isValidSecureKey($agent, $secure_key)){

			$timeout_datetime=null;
			$token = $always_new ? null : $this->getAvailableToken($agent['agent_id'], 'agent_id', $timeout_datetime);
			if (empty($token)) {
				$token = $this->createTokenBy($agent['agent_id'], 'agent_id', null, $timeout_datetime);
			}

			return [$token, $timeout_datetime];

		}

		return [null, null];
	}

	public function deleteTimeoutTokens(\DateTime $timeout_datetime){

		//will delete timeout tokens
		$this->db->where('timeout_at <', $this->utils->formatDateTimeForMysql($timeout_datetime));

		return $this->runRealDelete('common_tokens');

	}

	public function getPlayerIdFromAuthToken($auth_token){
		//query player token
        return $this->getPlayerIdByToken($auth_token);
	}

	public function generateAuthKeyByPlayerUsernamePassword($username, $password,
			$always_new=false, $allow_clear_session=true, $updateOnlineStatus=true, $sendNotification=false){
		$this->load->model(['player_model']);
		list($playerId, $errorCode)=$this->player_model->loginBy($username, $password,
			$allow_clear_session, $updateOnlineStatus, $sendNotification);
		if(!empty($playerId)){
			//clear token
			if($allow_clear_session && $always_new){
				//clear token
				$this->kickoutByPlayerId($playerId);
			}
			$timeout_datetime=null;
			$token=null;
			$signKey=null;
			if(!$always_new){
				list($token, $signKey) = $this->getAvailableTokenWithSignKey($playerId, 'player_id', $timeout_datetime);
			}
			if (empty($token)) {
				list($token, $signKey) = $this->createTokenWithSignKeyBy($playerId, 'player_id', null, $timeout_datetime);
			}
			return [$playerId, $token, $signKey, $timeout_datetime, $errorCode];
		}
		return [null, null, null, null, $errorCode];
	}

	public function kickoutByPlayerId($playerId){
		$this->db->where('player_id', $playerId);
		return $this->runRealDelete('common_tokens');
	}

	public function getPlayerIdAndSignKeyByPlayerAuthToken($auth_token){
		//query token
		$this->db->select('player_id, sign_key')->from($this->tableName)->where('token', $auth_token)
			->where('timeout_at >=', $this->getNowForMysql())
			->where('status', self::STATUS_NORMAL);

		$row=$this->runOneRowArray();
		$playerId=null;
		$signKey=null;
		if(!empty($row)){
			$playerId=$row['player_id'];
			$signKey=$row['sign_key'];
		}

		return [$playerId, $signKey];
	}

	public function getAvailableTokenWithSignKey($id, $field, &$timeout_datetime=null, $db=null) {
		if(empty($db)){
			$db=$this->db;
		}
		// at least one minute available
		$dt=new DateTime('-1 minute');

		$db->select('timeout_at, token, sign_key')->from($this->tableName)->where($field, $id)
			->where('timeout_at >=', $this->utils->formatDateTimeForMysql($dt))
			->where('status', self::STATUS_NORMAL)
			->limit(1);

		$row=$this->runOneRowArray($db);

		if(!empty($row)){
			$timeout_datetime=$row['timeout_at'];
			return [$row['token'], $row['sign_key']];
		}

		return [null, null];

	}

	public function createTokenWithSignKeyBy($id, $field, $timeout = null, &$timeout_datetime=null, $db=null) {
		if(empty($db)){
			$db=$this->db;
		}
		$token = random_string('unique');
		$signKey= random_string('unique');
		$timeout = $timeout ?: $this->utils->getConfig('token_timeout');
		$d = new DateTime();
		$d->modify('+' . $timeout . ' seconds');

		$timeout_datetime=$this->utils->formatDateTimeForMysql($d);

		$data=[$field => $id,
			'token' => $token,
			'sign_key' => $signKey,
			"created_at" => $this->getNowForMysql(),
			'updated_at' => $this->getNowForMysql(),
			'timeout_at' => $timeout_datetime,
			'timeout' => $timeout,
			'status' => self::STATUS_NORMAL,];
		$id=$this->runInsertData($this->tableName, $data, $db);

		if(empty($id)){
			$this->utils->error_log('create token failed', $this->tableName, $data);
			return [null, null];
		}else{
			return [$token, $signKey];
		}
	}

	/**
	 * delete token
	 * @param  string $token
	 * @return boolean
	 */
	public function deleteToken($token) {
		$this->db->where('token', $token);
		return $this->runRealDelete($this->tableName);
	}

	public function generateBackendAuthKeyByUserId($adminUserId, $always_new=false){
		// if($backend_key_info['secure_key']!=$secure_key){
		// 	return [null, null];
		// }

		$timeout_datetime=null;
		$token = $always_new ? null : $this->getAvailableToken($adminUserId, 'admin_user_id', $timeout_datetime);
		if (empty($token)) {
			$token = $this->createTokenBy($adminUserId, 'admin_user_id', null, $timeout_datetime);
		}

		return [$token, $timeout_datetime];
	}

	public function isValidBackendAuthToken($auth_token){
		$success=false;
		if(empty($auth_token)){
			return $success;
		}
		//get id from auth token
		$arr=explode('-', $auth_token);
		if(empty($arr) || count($arr)!=2){
			return $success;
		}
		$adminUserId=$arr[0];
		$token=$arr[1];
		if(!empty($adminUserId) && !empty($token)){
			// $this->load->model(['users']);
			// $keyInfo=$this->users->getKeysByUserId($adminUserId);

			$userIdInDB=$this->getAdminUserIdByToken($token);
			$this->utils->debug_log('compare user id by token', $userIdInDB, $adminUserId, $token, $auth_token);
			if(!empty($userIdInDB) && $userIdInDB==$adminUserId){
				$success=true;
			}
		}

		return $success;
	}

	/**
	 * delete token for admin user
	 * @param  string $auth_token
	 * @return boolean
	 */
	public function deleteTokenForAdminUser($auth_token) {
		$success=false;
		if(empty($auth_token)){
			return $success;
		}
		//get id from auth token
		$arr=explode('-', $auth_token);
		if(empty($arr) || count($arr)!=2){
			return $success;
		}
		$adminUserId=$arr[0];
		$token=$arr[1];
		if(!empty($adminUserId) && !empty($token)){
			$this->db->where('token', $token)->where('admin_user_id', $adminUserId);
			return $this->runRealDelete($this->tableName);
		}

		return $success;
	}

}

///END OF FILE///////