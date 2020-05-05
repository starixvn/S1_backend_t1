<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Session Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session {

	var $sess_encrypt_cookie = FALSE;
	var $sess_use_database = FALSE;
	var $sess_table_name = '';
	var $sess_expiration = 7200;
	var $sess_expire_on_close = FALSE;
	var $sess_expire_append_on_ajax_request = TRUE;
	//24hours
	var $sess_expiration_time_on_redis_when_expire_on_close=86400;
	var $sess_match_ip = FALSE;
	var $sess_match_useragent = TRUE;
	var $sess_match_hmac=true;
	var $sess_cookie_name = 'ci_session';
	var $cookie_prefix = '';
	var $cookie_path = '';
	var $cookie_domain = '';
	var $cookie_secure = FALSE;
	var $sess_time_to_update = 300;
	var $encryption_key = '';
	var $flashdata_key = 'flash';
	var $time_reference = 'time';
	var $gc_probability = 5;
	var $userdata = array();
	var $CI;
	var $now;

	var $how_many_hours_from_utc=8;

	public $cookie_userdata;
	public $prefix;

	private $added_cookies=false;

	private $session_id;
	private $_app_prefix;
	// private $redis=null;

	const TABLE_USER_DATA_ID_MAP=[
		'ci_admin_sessions'=>'user_id',
		'ci_player_sessions'=>'player_id',
		'ci_aff_sessions'=>'affiliateId',
		'ci_agency_sessions'=>'agent_id',
	];

	const TABLE_OPERATOR_SETTING_MAP=[
        'ci_admin_sessions'=>'admin_sess_expire',
        'ci_player_sessions'=>'player_sess_expire',
        'ci_aff_sessions'=>'aff_sess_expire',
        'ci_agency_sessions'=>'agency_sess_expire',
    ];


	public function getSessionId(){
		return $this->session_id;
	}

/*
json session samples:

{
    "session_id": "d44f5287324c857fc23fe8a4bf1e6038",
    "ip_address": "172.28.0.1",
    "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.3",
    "last_activity": 1569313104,
    "user_data": {
        "user_data": "",
        "login_lan": "1",
        "user_id": "31",
        "username": "superadmin",
        "status": "1",
        "sessionId": "7nHw*ddp1",
        "admin_login_token": "ae1113a6b26ce13181f33fbd94fde279",
        "current_url": "/home"
    },
    "last_update_time": "2019-09-24 16:19:19",
    "last_update_microtime": 1569313159.367806,
    "object_id": "31"
}

{
    "session_id": "26263415424be55d14a15cf0cd4cc14f",
    "ip_address": "172.28.0.1",
    "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.3",
    "last_activity": 1569313198,
    "user_data": {
        "user_data": "",
        "login_lan": "1",
        "flash:old:REDIRECT_SOURCE": "http://admin.og.local/"
    },
    "last_update_time": "2019-09-24 16:19:59",
    "last_update_microtime": 1569313199.642842,
    "object_id": null
}

*/

	/**
	 * try connect redis
	 * @return object redis
	 */
	public function getRedis(){

		return $this->CI->utils->getSessionRedisServer();

		// if(empty($this->redis)){
		// 	$this->redis=try_load_redis($this->CI);
		// 	if(!empty($this->redis)){
		// 		try{
		// 			//default db of redis
		// 			$this->redis->select(0);
	 //            }catch(Exception $e){
	 //                log_message('error', 'exception when connect redis', ['exception'=>$e]);
	 //                $this->redis=null;
	 //                return null;
	 //            }
		// 	}
		// }

		// return $this->redis;
	}
	/**
	 * try connect to redis
	 * @return boolean
	 */
	public function isEnabledRedis(){
		return !empty($this->getRedis());
	}

	public function tryCloseRedis(){
		//no need to close
		$success=true;
		// if(!empty($this->redis)){
		// 	try{
		// 		$success=$this->redis->close();
		// 		// log_message('debug', 'close redis result', ['success'=>$success]);
		// 	}catch(Exception $e){
		// 		log_message('error', 'close redis failed', ['exception'=>$e]);
		// 		$success=false;
		// 	}
		// }
		return $success;
	}

	public function readBySessionIdFromRedis($sessionId){
		$key=$this->_app_prefix.'-'.$this->sess_table_name.'-'.$sessionId;
		$redis=$this->getRedis();
		if(!empty($redis)){
			$data=$redis->get($key);
			if($data!==false){
				$arr=json_decode($data, true);
				log_message('debug', 'readBySessionIdFromRedis', ['key'=>$key, 'data'=>$data, 'arr'=>$arr]);
				return $arr;
			}
		}else{
			log_message('error', 'lost redis on session readBySessionIdFromRedis');
		}

		return null;
	}

	public function deleteBySessionIdFromRedis($sessionId){
		$success=false;
		$key=$this->_app_prefix.'-'.$this->sess_table_name.'-'.$sessionId;
		$o2sKey=$this->CI->utils->getSessionHashMapKeyForRedis($this->sess_table_name);
		$redis=$this->getRedis();
		if(!empty($redis)){
			$redis->unlink([$key]);
			$success=true;
			if(!empty($sessionId)){
				$it=null;
				$redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
				$arrKeys=$redis->hScan($o2sKey, $it, '*-'.$sessionId);
				// log_message('debug', 'scan '.$o2sKey, ['arrKeys'=>$arrKeys]);
				//delete all session id
				if(!empty($arrKeys)){
					foreach ($arrKeys as $delKey=>$delVal) {
						$rlt=$redis->hDel($o2sKey, $delKey);
						// log_message('debug', 'delete key from hash', ['delKey'=>$delKey, 'rlt'=>$rlt]);
					}
				}
			}
			log_message('debug', 'deleteBySessionIdFromRedis', ['key'=>$key, 'sessionId'=>$sessionId]);
		}else{
			log_message('error', 'lost redis on session deleteBySessionIdFromRedis');
		}

		return $success;
	}

	public function writeToRedis($sessionId, $data, $ttl=null){
		$success=false;
		$key=$this->_app_prefix.'-'.$this->sess_table_name.'-'.$sessionId;
		$o2sKey=$this->CI->utils->getSessionHashMapKeyForRedis($this->sess_table_name);
		$redis=$this->getRedis();
		if(!empty($redis)){
			if($ttl===null || $ttl<=0){

				if($this->sess_expire_on_close && !$this->check_sess_expiration_custom_setting()){
					//default long session
					$ttl=$this->sess_expiration_time_on_redis_when_expire_on_close;
				}else{
					$ttl=$this->sess_expiration;
				}
				    // $ttl=$this->sess_expiration;

			}
			if($ttl<=0 || $ttl===null){
				//fallback
				$ttl=$this->sess_expiration_time_on_redis_when_expire_on_close;
			}
			// $ttl=$ttl*1000;
			$objectId=null;
			$lastActivity=null;
			if(!empty($data)){
				$data['last_update_time']=date('Y-m-d H:i:s');
				$data['last_update_microtime']=microtime(true);
				$data['object_id']=null;
				if(!empty($data['last_activity'])){
					$lastActivity=$data['last_activity'];
				}
				//write object id, object means player/admin/aff/agent
				if(array_key_exists($this->sess_table_name, self::TABLE_USER_DATA_ID_MAP)){
					$idFld=self::TABLE_USER_DATA_ID_MAP[$this->sess_table_name];
					if(!empty($data['user_data']) && is_array($data['user_data']) && array_key_exists($idFld, $data['user_data'])){
						$data['object_id']=$data['user_data'][$idFld];
						$objectId=$data['object_id'];
					}
				}
			}
			log_message('debug', 'writeToRedis', ['key'=>$key, 'ttl'=>$ttl]);
			$success=$redis->set($key, json_encode($data), ['ex'=>$ttl]);
			//save to hash key
			// $redis->hset($s2oKey, $sessionId, $objectId);
			if(!empty($objectId)){
				$rlt=$redis->hSet($o2sKey, $objectId.'-'.$sessionId, $lastActivity);
				if($rlt===false){
					log_message('error', 'write hash to redis', ['sessionId'=>$sessionId]);
				}
				// log_message('debug', 'write to hash', ['o2sKey'=>$o2sKey, 'lastActivity'=>$lastActivity]);
			}
			if($success!==true){
				log_message('error', 'write to redis failed', ['sessionId'=>$sessionId]);
			}
		}else{
			log_message('error', 'lost redis on session writeToRedis');
		}

		return $success;
	}

	public function writeSessionIdToCookie($session_id){
		if($this->added_cookies){
			return;
		}

		// log_message('debug', 'sess_expire_append_on_ajax_request', [
		// 	'is_ajax_request'=>$this->CI->input->is_ajax_request(),
		// 	'sess_expire_append_on_ajax_request'=>$this->sess_expire_append_on_ajax_request]);

		if($this->CI->input->is_ajax_request() && !$this->sess_expire_append_on_ajax_request){
			return;
		}


		$expire_seconds = (($this->sess_expire_on_close === TRUE) && !$this->check_sess_expiration_custom_setting()) ? 0 : time()+$this->sess_expiration; //+$this->how_many_hours_from_utc*3600;
		// $expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();

		// Set the cookie
		setcookie(
			$this->sess_cookie_name,
			$session_id,
			$expire_seconds,
			$this->cookie_path,
			$this->cookie_domain,
			$this->cookie_secure
		);
		$this->added_cookies=true;

		log_message('debug', 'write session id to cookies:'.$session_id);

	}

    public function setLanguageCookie($lang){
        $expire_seconds = (($this->sess_expire_on_close === true)&& !$this->check_sess_expiration_custom_setting()) ? 0 : time()+$this->sess_expiration;

		setcookie(
			'language',
			$lang,
			$expire_seconds,
			$this->cookie_path,
			$this->cookie_domain,
			$this->cookie_secure
		);
    }

	public function deleteSessionIdFromCookie(){

		setcookie(
			$this->sess_cookie_name,
			'',
			($this->now - 31500000),
			$this->cookie_path,
			$this->cookie_domain,
			0
		);

	}

	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */
	public function __construct($params = array()) {
		// log_message('debug', "Session Class Initialized");

        //get db name , if it's not og, use it, if it's og, use hostname
        // $default_db=config_item('db.default.database');
        // if($default_db!='og'){
        //     $this->prefix=$default_db;
        // }else{
        //     static $_log;
        //     $_log = &load_class('Log');

        //     $this->prefix=$_log->getHostname();
        // }

		// Set the super object to a local variable for use throughout the class
		$this->CI = &get_instance();
        $this->_app_prefix=try_get_prefix();

		// log_message('debug', 'log session');

		// Set all the session preferences, which can either be set
		// manually via the $params array above or via the config file
		foreach (array('sess_encrypt_cookie', 'sess_use_database', 'sess_table_name', 'sess_expiration',
			'sess_expiration_time_on_redis_when_expire_on_close', 'sess_expire_on_close', 'sess_expire_append_on_ajax_request',
			'sess_match_ip', 'sess_match_useragent', 'sess_match_hmac', 'sess_cookie_name', 'sess_expiration_use_custom_setting', 'cookie_path', 'cookie_domain', 'cookie_secure', 'sess_time_to_update', 'time_reference', 'cookie_prefix', 'encryption_key') as $key) {
			$this->$key = (isset($params[$key])) ? $params[$key] : $this->CI->config->item($key);
		}

		if ($this->encryption_key == '') {
			show_error('In order to use the Session class you are required to set an encryption key in your config file.');
		}

		// Load the string helper so we can use the strip_slashes() function
		$this->CI->load->helper('string');

		// Do we need encryption? If so, load the encryption class
		if ($this->sess_encrypt_cookie == TRUE) {
			$this->CI->load->library('encrypt');
		}

		// Are we using a database?  If so, load it
		// if ($this->sess_use_database === TRUE AND $this->sess_table_name != '') {
		// 	$this->CI->load->database();
		// }

		// Set the "now" time.  Can either be GMT or server time, based on the
		// config prefs.  We use this to set the "last activity" time
		$this->now = $this->_get_time();

		// Set the session length. If the session expiration is
		// set to zero we'll set the expiration two years from now.

		$this->set_sess_expiration();

		if(empty($this->cookie_prefix)){
			$this->cookie_prefix='sess_';
		}

		// Set the cookie name
		$this->sess_cookie_name = $this->cookie_prefix . $this->sess_cookie_name;

		//convert timezone
		$this->how_many_hours_from_utc=config_item('how_many_hours_from_utc');

		// $expire_seconds = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration+$how_many_hours_from_utc*3600;

		// $this->CI->utils->debug_log('session_set_cookie_params', $expire_seconds, $this->cookie_path, $this->cookie_domain);

		// session_set_cookie_params($expire_seconds, $this->cookie_path, $this->cookie_domain);
		// session_name($this->sess_cookie_name);
		// @session_start();

		//setcookie

		// Run the Session routine. If a session doesn't exist we'll
		// create a new one.  If it does, we'll update it.
		if (!$this->sess_read()) {
			$this->sess_create();
		} else {
			$this->sess_update();
		}

		// Delete 'old' flashdata (from last request)
		$this->_flashdata_sweep();

		// Mark all new flashdata as old (data will be deleted before next request)
		$this->_flashdata_mark();

		// Delete expired sessions if necessary
		$this->_sess_gc();

		// log_message('debug', "Session routines successfully run");
	}

	// --------------------------------------------------------------------

	function check_sess_expiration_custom_setting() {
		return (!empty($this->sess_expiration_use_custom_setting) && in_array($this->sess_table_name, $this->sess_expiration_use_custom_setting)) ? true : false;
	}

	/**
	 * set the sess_expiration
	 *
	 * @return void
	 */
	function set_sess_expiration() {

		if($this->check_sess_expiration_custom_setting()){
			$this->set_sess_expiration_from_operator_settings();
		}

		if ($this->sess_expiration == 0) {
            $this->sess_expiration = (60 * 60 * 24 * 365 * 2);
        }

	}

	/**
	 * set custom sess_expiration setting from DB table 'operator_settings'
	 *
	 * @access	public
	 * @return	bool
	 */
	function set_sess_expiration_from_operator_settings() {

		$this->CI->load->model('Operatorglobalsettings');
		$operator_data = $this->CI->operatorglobalsettings->getSettingValue(self::TABLE_OPERATOR_SETTING_MAP[$this->sess_table_name]);
		if (!empty($operator_data)) {
			$autoLogoutSetting = json_decode($operator_data, true);
			if($autoLogoutSetting['enable'] == 1) {
				$this->sess_expiration = $autoLogoutSetting['sess_expiration'];
			} else {
				$this->sess_expiration = 0;
			}
		}
	}

	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	public
	 * @return	bool
	 */
	function sess_read() {

		//read cookie

		$session_id=null;
		// if(isset($_SESSION[$this->prefix.'_session_id'])){
		// 	$session_id=$_SESSION[$this->prefix.'_session_id'];
		// }

		//$this->sess_cookie_name
		if(isset($_COOKIE[$this->sess_cookie_name])){
			$session_id=$_COOKIE[$this->sess_cookie_name];
		}

		//get session id
		$session = ['session_id'=>$session_id];

		if (empty($session['session_id'])) {
			log_message('debug', 'A session cookie was not found.');
			return FALSE;
		}

		if ($this->sess_use_database === TRUE) {
			// $this->CI->utils->debug_log('read session id:'.$session['session_id']);
			log_message('debug', 'read session id:'.$session['session_id']);

			//load it from db
			$this->CI->db->where('session_id', $session['session_id']);

			$query = $this->CI->db->get($this->sess_table_name);

			// No result?  Kill it!
			if(!empty($query) && $query->num_rows() == 0) {
				$this->sess_destroy();
				return FALSE;
			}

	        $this->session_id=$session_id;

			// Is there custom data?  If so, add it to the main session array
			$row = $query->row();
			if (isset($row->user_data) AND $row->user_data != '') {
				$custom_data = $this->_unserialize($row->user_data);

				if (is_array($custom_data)) {
					foreach ($custom_data as $key => $val) {
						$session[$key] = $val;
					}
				}
			}

			$session['ip_address']=$row->ip_address;
			$session['user_agent']=$row->user_agent;
			$session['last_activity']=$row->last_activity;

		}else if($this->isEnabledRedis()){
			//try get it from redis
			$data=$this->readBySessionIdFromRedis($session['session_id']);
			if(empty($data)){
				$this->sess_destroy();
				return FALSE;
			}

	        $this->session_id=$session_id;
			if (isset($data['user_data']) AND !empty($data['user_data'])) {
				$custom_data = $data['user_data'];

				if (is_array($custom_data)) {
					foreach ($custom_data as $key => $val) {
						$session[$key] = $val;
					}
				}
			}
			$session['ip_address']=$data['ip_address'];
			$session['user_agent']=$data['user_agent'];
			$session['last_activity']=$data['last_activity'];
		}else{
			log_message('error', 'wrong settings, no db, no redis', $this->sess_use_database);
			return false;
		}

		// Does the IP Match?
		if ($this->sess_match_ip == TRUE AND $session['ip_address'] != $this->CI->input->ip_address()) {
			$this->sess_destroy();
			return FALSE;
		}

		// Session is valid!
		$this->userdata = $session;
		unset($session);

		return TRUE;

/*
		// Fetch the cookie
		$session = $this->CI->input->cookie($this->sess_cookie_name);

		// log_message('debug','read session:'.$session);

		// No cookie?  Goodbye cruel world!...
		if ($session === FALSE) {
			log_message('debug', 'A session cookie was not found.');
			return FALSE;
		}

		// HMAC authentication
		$len = strlen($session) - 40;

		if ($len <= 0) {
			log_message('error', 'Session: The session cookie was not signed.');
			return FALSE;
		}

		// Check cookie authentication
		$hmac = substr($session, $len);
		$session = substr($session, 0, $len);

		if($this->sess_match_hmac){
			// Time-attack-safe comparison
			$hmac_check = hash_hmac('sha1', $session, $this->encryption_key);
			$diff = 0;

			for ($i = 0; $i < 40; $i++) {
				$xor = ord($hmac[$i]) ^ ord($hmac_check[$i]);
				$diff |= $xor;
			}

			if ($diff !== 0) {
				log_message('error', 'Session: HMAC mismatch. The session cookie data did not match what was expected.');
				$this->sess_destroy();
				return FALSE;
			}
		}

		// Decrypt the cookie data
		if ($this->sess_encrypt_cookie == TRUE) {
			$session = $this->CI->encrypt->decode($session);
		}

		// log_message('debug','session:'.$session);

		// Unserialize the session array
		$session = $this->_unserialize($session);

		// Is the session data we unserialized an array with the correct format?
		if (!is_array($session) OR !isset($session['session_id']) OR !isset($session['ip_address']) OR !isset($session['user_agent']) OR !isset($session['last_activity'])) {
			$this->sess_destroy();
			return FALSE;
		}

		// Is the session current?
		if (($session['last_activity'] + $this->sess_expiration) < $this->now) {
			$this->sess_destroy();
			return FALSE;
		}

		// Does the IP Match?
		if ($this->sess_match_ip == TRUE AND $session['ip_address'] != $this->CI->input->ip_address()) {
			$this->sess_destroy();
			return FALSE;
		}

		// Does the User Agent Match?
		if ($this->sess_match_useragent == TRUE AND trim($session['user_agent']) != trim(substr($this->CI->input->user_agent(), 0, 120))) {
			$this->sess_destroy();
			return FALSE;
		}

		// Is there a corresponding session in the DB?
		if ($this->sess_use_database === TRUE) {
			$this->CI->db->where('session_id', $session['session_id']);

			if ($this->sess_match_ip == TRUE) {
				$this->CI->db->where('ip_address', $session['ip_address']);
			}

			if ($this->sess_match_useragent == TRUE) {
				$this->CI->db->where('user_agent', $session['user_agent']);
			}

			$query = $this->CI->db->get($this->sess_table_name);

			// No result?  Kill it!
			if ($query->num_rows() == 0) {
				$this->sess_destroy();
				return FALSE;
			}

			// Is there custom data?  If so, add it to the main session array
			$row = $query->row();
			if (isset($row->user_data) AND $row->user_data != '') {
				$custom_data = $this->_unserialize($row->user_data);

				if (is_array($custom_data)) {
					foreach ($custom_data as $key => $val) {
						$session[$key] = $val;
					}
				}
			}
		}

		// Session is valid!
		$this->userdata = $session;
		unset($session);

		return TRUE;
*/
	}

	// --------------------------------------------------------------------

	/**
	 * Write the session data
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_write() {

		if(empty($this->userdata)){
			return;
		}

		$custom_userdata = $this->userdata;
		$data = array();

		// Before continuing, we need to determine if there is any custom data to deal with.
		// Let's determine this by removing the default indexes to see if there's anything left in the array
		// and set the session data while we're at it
		foreach (array('session_id', 'ip_address', 'user_agent', 'last_activity') as $val) {
			if(isset($this->userdata[$val])){
				$data[$val] = $this->userdata[$val];
			}
			unset($custom_userdata[$val]);
		}

		// Did we find any custom data?  If not, we turn the empty array into a string
		// since there's no reason to serialize and store an empty array in the DB

		if(!empty($this->userdata['session_id'])){
	        if ($this->sess_use_database === TRUE) {
				if (count($custom_userdata) === 0) {
					$custom_userdata = '';
				} else {
					// Serialize the custom data array so we can store it
					$custom_userdata = $this->_serialize($custom_userdata);
				}
				// Run the update query
				$this->CI->db->where('session_id', $this->userdata['session_id']);
				$this->CI->db->update($this->sess_table_name, array('last_activity' => $this->userdata['last_activity'], 'user_data' => $custom_userdata));
			}else if($this->isEnabledRedis()){
				log_message('debug', 'call writeToRedis '.$this->userdata['session_id']);
				$data['user_data']=$custom_userdata;
				$this->writeToRedis($this->userdata['session_id'], $data);
			}else{
				log_message('error', 'wrong settings, no db, no redis', $this->sess_use_database);
				return false;
			}

			$this->writeSessionIdToCookie($this->userdata['session_id']);
		}

		// $_SESSION[$this->prefix.'_session_id']=$this->userdata['session_id'];

/*

		// Are we saving custom data to the DB?  If not, all we do is update the cookie
		if ($this->sess_use_database === FALSE) {
			$this->_set_cookie();
			return;
		}

		// set the custom userdata, the session data we will set in a second
		$custom_userdata = $this->userdata;
		$cookie_userdata = array();

		// Before continuing, we need to determine if there is any custom data to deal with.
		// Let's determine this by removing the default indexes to see if there's anything left in the array
		// and set the session data while we're at it
		foreach (array('session_id', 'ip_address', 'user_agent', 'last_activity') as $val) {
			unset($custom_userdata[$val]);
			$cookie_userdata[$val] = $this->userdata[$val];
		}

		// Did we find any custom data?  If not, we turn the empty array into a string
		// since there's no reason to serialize and store an empty array in the DB
		if (count($custom_userdata) === 0) {
			$custom_userdata = '';
		} else {
			// Serialize the custom data array so we can store it
			$custom_userdata = $this->_serialize($custom_userdata);
		}

		// Run the update query
		$this->CI->db->where('session_id', $this->userdata['session_id']);
		$this->CI->db->update($this->sess_table_name, array('last_activity' => $this->userdata['last_activity'], 'user_data' => $custom_userdata));

		//update last activity
		// log_message('debug', 'update last_activity', ['last_activity'=>$this->userdata['last_activity'], 'session_id'=>$this->userdata['session_id']]);

		// Write the cookie.  Notice that we manually pass the cookie data array to the
		// _set_cookie() function. Normally that function will store $this->userdata, but
		// in this case that array contains custom data, which we do not want in the cookie.
		$this->_set_cookie($cookie_userdata);
*/

	}

	// --------------------------------------------------------------------

	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_create() {

		// $sessid = '';
		// while (strlen($sessid) < 32) {
		// 	$sessid .= mt_rand(0, mt_getrandmax());
		// }

		// To make the session ID even more secure we'll combine it with the user's IP
		// $sessid .= $this->CI->input->ip_address();

        // $session_id=md5(uniqid($sessid, TRUE));
        $session_id=md5(openssl_random_pseudo_bytes(256));

        $data=[
			'session_id' => $session_id,
			'ip_address' => $this->CI->input->ip_address(),
			'user_agent' => substr($this->CI->input->user_agent(), 0, 120),
			'last_activity' => $this->now,
			'user_data' => '',
		];

        if ($this->sess_use_database === TRUE) {
			$this->CI->db->query($this->CI->db->insert_string($this->sess_table_name, $data));
		}else if($this->isEnabledRedis()){
			$this->writeToRedis($session_id, $data);
		}else{
			log_message('error', 'wrong settings, no db, no redis', $this->sess_use_database);
			return false;
		}

		$this->userdata = $data;
        $this->session_id=$session_id;

		log_message('debug', 'create session:' . $session_id);

		$this->added_cookies=false;
		$this->writeSessionIdToCookie($this->userdata['session_id']);

		// $sessid = '';
		// while (strlen($sessid) < 32) {
		// 	$sessid .= mt_rand(0, mt_getrandmax());
		// }

		// // To make the session ID even more secure we'll combine it with the user's IP
		// $sessid .= $this->CI->input->ip_address();

		// // $this->CI->utils->debug_log('ip', $this->CI->input->ip_address(), 'user_agent', $this->CI->input->user_agent());
		// $this->userdata = array(
		// 	'session_id' => md5(uniqid($sessid, TRUE)),
		// 	'ip_address' => $this->CI->input->ip_address(),
		// 	'user_agent' => substr($this->CI->input->user_agent(), 0, 120),
		// 	'last_activity' => $this->now,
		// 	'user_data' => '',
		// );

		// // Save the data to the DB if needed
		// if ($this->sess_use_database === TRUE) {
		// 	$this->CI->db->query($this->CI->db->insert_string($this->sess_table_name, $this->userdata));
		// }

		// // Write the cookie
		// $this->_set_cookie();
	}

	// --------------------------------------------------------------------

	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_update() {
		// We only update the session every five minutes by default
		if (isset($this->userdata['last_activity']) && ($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now) {
			return;
		}


		$old_sessid = $this->userdata['session_id'];
		$new_sessid = $old_sessid;
		if (!$this->CI->utils->getConfig('not_change_session_id_on_update')) {
        	$new_sessid=md5(openssl_random_pseudo_bytes(256));
		}
		// Update the session data in the session data array
		$this->userdata['session_id'] = $new_sessid;
		$this->userdata['last_activity'] = $this->now;

        $this->session_id=$this->userdata['session_id'];

		$custom_userdata = $this->userdata;
		// _set_cookie() will handle this for us if we aren't using database sessions
		// by pushing all userdata to the cookie.

		// Update the session ID and last_activity field in the DB if needed
		// if ($this->sess_use_database === TRUE) {
			// set cookie explicitly to only have our session data
		$data = array();
		foreach (array('session_id', 'ip_address', 'user_agent', 'last_activity') as $val) {
			$data[$val] = $this->userdata[$val];
			unset($custom_userdata[$val]);
		}

        if ($this->sess_use_database === TRUE) {
			$this->CI->db->query($this->CI->db->update_string($this->sess_table_name, array('last_activity' => $this->now, 'session_id' => $new_sessid), array('session_id' => $old_sessid)));
		}else if($this->isEnabledRedis()){
			$data['user_data']=$custom_userdata;
			$this->writeToRedis($this->session_id, $data);
		}else{
			log_message('error', 'wrong settings, no db, no redis', $this->sess_use_database);
			return false;
		}

		// }

		// $_SESSION[$this->prefix.'_session_id']=$new_sessid;

		$this->writeSessionIdToCookie($this->userdata['session_id']);

/*
		// $this->CI->utils->debug_log('recreate session id');
		// Save the old session id so we know which record to
		// update in the database if we need it
		$old_sessid = $this->userdata['session_id'];
		$new_sessid = $old_sessid;
		if (!$this->CI->utils->getConfig('not_change_session_id_on_update')) {

			while (strlen($new_sessid) < 32) {
				$new_sessid .= mt_rand(0, mt_getrandmax());
			}

			// To make the session ID even more secure we'll combine it with the user's IP
			$new_sessid .= $this->CI->input->ip_address();

			// Turn it into a hash
			$new_sessid = md5(uniqid($new_sessid, TRUE));

		}
		// Update the session data in the session data array
		$this->userdata['session_id'] = $new_sessid;
		$this->userdata['last_activity'] = $this->now;

		// _set_cookie() will handle this for us if we aren't using database sessions
		// by pushing all userdata to the cookie.
		$cookie_data = NULL;

		// Update the session ID and last_activity field in the DB if needed
		if ($this->sess_use_database === TRUE) {
			// set cookie explicitly to only have our session data
			$cookie_data = array();
			foreach (array('session_id', 'ip_address', 'user_agent', 'last_activity') as $val) {
				$cookie_data[$val] = $this->userdata[$val];
			}

			$this->CI->db->query($this->CI->db->update_string($this->sess_table_name, array('last_activity' => $this->now, 'session_id' => $new_sessid), array('session_id' => $old_sessid)));
		}

		// Write the cookie
		$this->_set_cookie($cookie_data);
*/

	}

	// --------------------------------------------------------------------

	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_destroy() {

		// $this->CI->utils->debug_log('destroy session:' . @$this->userdata['session_id']);
		log_message('debug', 'destroy session:' . @$this->userdata['session_id']);

		if (isset($this->userdata['session_id'])) {
    	    if ($this->sess_use_database === TRUE) {
				$this->CI->db->where('session_id', $this->userdata['session_id']);
				$this->CI->db->delete($this->sess_table_name);
			}else if($this->isEnabledRedis()){
				$this->deleteBySessionIdFromRedis($this->userdata['session_id']);
			}else{
				log_message('error', 'wrong settings, no db, no redis', $this->sess_use_database);
				return false;
			}
		}

		// unset($_SESSION[$this->prefix.'_session_id']);
		// session_unset();
  		// session_destroy();

		$this->deleteSessionIdFromCookie();
		$this->userdata = array();

        $this->session_id=null;

/*
		// Kill the session DB row
		if ($this->sess_use_database === TRUE && isset($this->userdata['session_id'])) {
			if (!$this->CI->utils->getConfig('always_keep_admin_session')) {
				// $this->CI->utils->debug_log('delete session', $this->userdata['session_id'], 'user id', @$this->userdata['user_id'], 'player id', @$this->userdata['player_id']);
				$this->CI->db->where('session_id', $this->userdata['session_id']);
				$this->CI->db->delete($this->sess_table_name);
			}
		}

		// Kill the cookie
		setcookie(
			$this->sess_cookie_name,
			addslashes(serialize(array())),
			($this->now - 31500000),
			$this->cookie_path,
			$this->cookie_domain,
			0
		);

		// Kill session data
		$this->userdata = array();
*/
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a specific item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function userdata($item) {
		return (!isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	array
	 */
	function all_userdata() {
		return $this->userdata;
	}

	// --------------------------------------------------------------------

	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_userdata($newdata = array(), $newval = '') {
		if (is_string($newdata)) {
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0) {
			foreach ($newdata as $key => $val) {
				$this->userdata[$key] = $val;
			}
		}

		// $this->CI->utils->debug_log('write to session:'.$this->userdata['session_id'], $newdata, $newval);

		$this->sess_write();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */
	function unset_userdata($newdata = array()) {
		if (is_string($newdata)) {
			$newdata = array($newdata => '');
		}

		if (count($newdata) > 0) {
			foreach ($newdata as $key => $val) {
				unset($this->userdata[$key]);
			}
		}

		$this->sess_write();
	}

	// ------------------------------------------------------------------------

	/**
	 * Add or change flashdata, only available
	 * until the next request
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_flashdata($newdata = array(), $newval = '') {
		if (is_string($newdata)) {
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0) {
			foreach ($newdata as $key => $val) {
				$flashdata_key = $this->flashdata_key . ':new:' . $key;
				$this->set_userdata($flashdata_key, $val);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Keeps existing flashdata available to next request.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function keep_flashdata($key) {
		// 'old' flashdata gets removed.  Here we mark all
		// flashdata as 'new' to preserve it from _flashdata_sweep()
		// Note the function will return FALSE if the $key
		// provided cannot be found
		$old_flashdata_key = $this->flashdata_key . ':old:' . $key;
		$value = $this->userdata($old_flashdata_key);

		$new_flashdata_key = $this->flashdata_key . ':new:' . $key;
		$this->set_userdata($new_flashdata_key, $value);
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch a specific flashdata item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function flashdata($key) {
		$flashdata_key = $this->flashdata_key . ':old:' . $key;
		return $this->userdata($flashdata_key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Identifies flashdata as 'old' for removal
	 * when _flashdata_sweep() runs.
	 *
	 * @access	private
	 * @return	void
	 */
	function _flashdata_mark() {
		$userdata = $this->all_userdata();
		foreach ($userdata as $name => $value) {
			$parts = explode(':new:', $name);
			if (is_array($parts) && count($parts) === 2) {
				$new_name = $this->flashdata_key . ':old:' . $parts[1];
				$this->set_userdata($new_name, $value);
				$this->unset_userdata($name);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Removes all flashdata marked as 'old'
	 *
	 * @access	private
	 * @return	void
	 */

	function _flashdata_sweep() {
		$userdata = $this->all_userdata();
		foreach ($userdata as $key => $value) {
			if (strpos($key, ':old:')) {
				$this->unset_userdata($key);
			}
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Get the "now" time
	 *
	 * @access	private
	 * @return	string
	 */
	function _get_time() {
		if (strtolower($this->time_reference) == 'gmt') {
			$now = time();
			$time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
		} else {
			$time = time();
		}

		return $time;
	}

	// --------------------------------------------------------------------

	/**
	 * Write the session cookie
	 *
	 * @access	public
	 * @return	void
	 */
	function _set_cookie($cookie_data = NULL) {
		// if (is_null($cookie_data)) {
		// 	$cookie_data = $this->userdata;
		// }

		// $this->cookie_userdata=$cookie_data;

		// Serialize the userdata for the cookie
		// $cookie_data = $this->_serialize($cookie_data);

		// if ($this->sess_encrypt_cookie == TRUE) {
		// 	$cookie_data = $this->CI->encrypt->encode($cookie_data);
		// }

		// $cookie_data .= hash_hmac('sha1', $cookie_data, $this->encryption_key);

		// $expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();

		// // Set the cookie
		// setcookie(
		// 	$this->sess_cookie_name,
		// 	$cookie_data,
		// 	$expire,
		// 	$this->cookie_path,
		// 	$this->cookie_domain,
		// 	$this->cookie_secure
		// );

		// if($expire>0){
	 //        header('Set-Cookie: '.$this->sess_cookie_name.'='.urlencode($cookie_data).'; expires='.gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expire).'; path='.$this->cookie_path, true);
		// }else{
  //       	header('Set-Cookie: '.$this->sess_cookie_name.'='.urlencode($cookie_data).'; path='.$this->cookie_path, true);
		// }

	}


	public function writeAllCookies(){

/*
		log_message('debug', 'write all cookies');

		$cookie_data=$this->cookie_userdata;

		if(!empty($cookie_data)){
			// Serialize the userdata for the cookie
			$cookie_data = $this->_serialize($cookie_data);

			if ($this->sess_encrypt_cookie == TRUE) {
				$cookie_data = $this->CI->encrypt->encode($cookie_data);
			}

			$cookie_data .= hash_hmac('sha1', $cookie_data, $this->encryption_key);

			$expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();

			// Set the cookie
			setcookie(
				$this->sess_cookie_name,
				$cookie_data,
				$expire,
				$this->cookie_path,
				$this->cookie_domain,
				$this->cookie_secure
			);
		}
*/
	}

	// --------------------------------------------------------------------

	/**
	 * Serialize an array
	 *
	 * This function first converts any slashes found in the array to a temporary
	 * marker, so when it gets unserialized the slashes will be preserved
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	function _serialize($data) {

		return $this->CI->utils->encodeJson($data);

		// array_walk_recursive($data, 'replaceSlashes');
		// return serialize($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Unserialize
	 *
	 * This function unserializes a data string, then converts any
	 * temporary slash markers back to actual slashes
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	function _unserialize($data) {

		if(!empty($data)){

			$data=$this->CI->utils->decodeJson($data);

			// $data = @unserialize(strip_slashes($data));
			// array_walk_recursive($data, 'returnSlashes');
		}
		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Garbage collection
	 *
	 * This deletes expired session rows from database
	 * if the probability percentage is met
	 *
	 * @access	public
	 * @return	void
	 */
	function _sess_gc() {
		// if ($this->sess_use_database != TRUE)
		// {
		// 	return;
		// }

		// srand(time());
		// if ((rand() % 100) < $this->gc_probability)
		// {
		// 	$expire = $this->now - $this->sess_expiration;

		// 	$this->CI->db->where("last_activity < {$expire}");
		// 	$this->CI->db->delete($this->sess_table_name);

		// 	log_message('debug', 'Session garbage collection performed.');
		// }
	}

	function updateLoginId($fieldName, $loginId){
		if ($this->sess_use_database === TRUE) {
			$this->CI->db->where('session_id', $this->userdata['session_id']);
			$this->CI->db->update($this->sess_table_name, array($fieldName=>$loginId));
			// $this->CI->db->where('session_id', $session['session_id']);
		}else if($this->isEnabledRedis()){
			//no need to update session
		}else{
			log_message('error', 'wrong settings, no db, no redis', $this->sess_use_database);
			return false;
		}
	}

	public function reinit(){

		log_message('debug', 'reinit on db '. $this->CI->db->getOgTargetDB());
		//looking for right prefix
		$this->_app_prefix=try_get_prefix();
		$this->CI->utils->resetAppPrefix();
		$this->CI->utils->resetCurrency();
		$old=$this->sess_expire_append_on_ajax_request;
		$this->sess_expire_append_on_ajax_request=true;
		// if (!$this->sess_read()) {
		$this->sess_create();
		$this->sess_expire_append_on_ajax_request=$old;
		// } else {
			// $this->sess_update();
		// }

	}

}

function replaceSlashes(&$item, $key) {
	$item = str_replace('\\', '{{slash}}', $item);
}

function returnSlashes(&$item, $key) {
	$item = str_replace('{{slash}}', '\\', $item);
}
// END Session Class

/* End of file Session.php */
/* Location: ./system/libraries/Session.php */