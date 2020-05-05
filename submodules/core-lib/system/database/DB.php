<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * Initialize the database
 *
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 * @param 	string
 * @param 	bool	Determines if active record should be used or not
 */
function &DB($params = '', $active_record_override = NULL)
{

	// Load the DB config file if a DSN string wasn't passed
	if (is_string($params) AND strpos($params, '://') === FALSE)
	{
		// Is the config file in the environment folder?
		if ( ! defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/database.php'))
		{
			if ( ! file_exists($file_path = APPPATH.'config/database.php'))
			{
				show_error('The configuration file database.php does not exist.');
			}
		}

		include($file_path);

		if ( ! isset($db) OR count($db) == 0)
		{
			show_error('No database connection settings were found in the database config file.');
		}

		// log_message('debug', 'load db config', $db);

		$active_group = null;
		if (!empty($params)){
			$active_group = $params;
		}else{
			$_multiple_db=Multiple_db::getSingletonInstance();
			//from active targetdb
			$active_group = $_multiple_db->getActiveTargetDB();
		}

		if ( ! isset($active_group) OR ! isset($db[$active_group]))
		{
			show_error('You have specified an invalid database connection group. active_group: '.$active_group);
		}

		$params = $db[$active_group];
		$params['active_group']=$active_group;

	}else{
		// don't accept DSN format
		show_error('Wrong DB settings');
	}

	// No DB specified yet?  Beat them senseless...
	if ( ! isset($params['dbdriver']) OR $params['dbdriver'] == '')
	{
		show_error('You have not selected a database type to connect to.');
	}

	// Load the DB classes.  Note: Since the active record class is optional
	// we need to dynamically create a class that extends proper parent class
	// based on whether we're using the active record class or not.
	// Kudos to Paul for discovering this clever use of eval()

	if ($active_record_override !== NULL)
	{
		$active_record = $active_record_override;
	}

	require_once(BASEPATH.'database/DB_driver.php');

	if ( ! isset($active_record) OR $active_record == TRUE)
	{
		require_once(BASEPATH.'database/DB_active_rec.php');

		if ( ! class_exists('CI_DB'))
		{
			eval('class CI_DB extends CI_DB_active_record { }');
		}
	}
	else
	{
		if ( ! class_exists('CI_DB'))
		{
			eval('class CI_DB extends CI_DB_driver { }');
		}
	}

	require_once(BASEPATH.'database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver.php');

	// Instantiate the DB adapter
	$driver = 'CI_DB_'.$params['dbdriver'].'_driver';
	$DB = new $driver($params);

	if ($DB->autoinit == TRUE)
	{
		$DB->initialize();
	}

	if (isset($params['stricton']) && $params['stricton'] == TRUE)
	{
		$DB->query('SET SESSION sql_mode="STRICT_ALL_TABLES"');
	}

	return $DB;
}

class Multiple_db{

    private static $instance;
	private $db_list=[];

	private function __construct(){
		$this->CI= & get_instance();
		//init var
		//save db config
		include(APPPATH.'config/database.php');
		$this->_database_config=$db;

		$this->disabled_multiple_database=config_item('disabled_multiple_database');
		$this->fallback_target_db=config_item('fallback_target_db');

		$this->_multiple_databases=config_item('multiple_databases');
		$this->_enabled_multiple_level=config_item('enabled_multiple_level');
		$this->_multiple_level_tree=config_item('multiple_level_tree');
		$this->_multiple_level_tree_top_key=config_item('multiple_level_tree_top_key');

		$this->init();

		// log_message('debug', $this->getIpCountry('119.9.106.90'));
	}

    public static function getSingletonInstance(){
        if(!isset(self::$instance))
        {
            // Creates sets object to instance
            self::$instance = new Multiple_db();
        }
        // Returns the instance
        return self::$instance;
    }

	const __OG_TARGET_DB='__OG_TARGET_DB';
	const SUPER_TARGET_DB='super';
	const READONLY_DB='readonly';
	const DEFAULT_DB='default';
	private $active_target_db=self::SUPER_TARGET_DB;
	private $_multiple_databases=null;
	private $_multiple_level_tree=null;
	private $_multiple_level_tree_top_key=null;
	private $_enabled_multiple_level=false;
	private $disabled_multiple_database=false;

	public function isEnabledMDB(){
		if($this->disabled_multiple_database){
			return false;
		}
		if($this->_enabled_multiple_level){
			return !empty($this->_multiple_level_tree);
		}else{
			return !empty($this->_multiple_databases);
		}
	}

	public function isEnabledMultipleLevel(){
		return $this->isEnabledMDB() && $this->_enabled_multiple_level;
	}

	public function isSuperModeOnMDB(){
		return $this->active_target_db==self::SUPER_TARGET_DB;
	}

	public function getActiveTargetDB(){
		return $this->active_target_db;
	}

	public function rememberActiveTargetDB(){
		$this->saveActiveTargetDBToCookies();
	}

	private function setActiveTargetDB($active_target_db){
		$this->active_target_db=$active_target_db;
		$this->rememberActiveTargetDB();
		return $this->active_target_db;
	}

	public function checkCurrencyDomain(){
		//get host first
		$host=$this->getHttpHost();
		//check domain settings
		$auto_domain_switcher_group_list=config_item('auto_domain_switcher_group_list');
		if(!empty($auto_domain_switcher_group_list)){
			//check
			foreach ($auto_domain_switcher_group_list as $groupKey => $list) {
				foreach ($list as $domain_target_db => $bindDomain) {
					if($host==$bindDomain){
						if($this->isValidTargetDB($domain_target_db)){
							log_message('debug', 'try get target db from auto_domain_switcher_group_list',
								['domain_target_db'=>$domain_target_db]);
							//found
							return $domain_target_db;
						}
					}
				}
			}
		}
		$domain_to_currency_key=config_item('domain_to_currency_key');
		if(!empty($domain_to_currency_key)){
			if($host!='localhost'){
				if(array_key_exists($host, $domain_to_currency_key)){
					$domain_target_db=$domain_to_currency_key[$host];
					if($this->isValidTargetDB($domain_target_db)){
						log_message('debug', 'try get target db from domain',
							['domain_target_db'=>$domain_target_db]);
						return $domain_target_db;
					}
				}
			}
		}
		return null;
	}

	public function init($force_to_target_db=null){

		if($this->disabled_multiple_database){
			$active_target_db=self::DEFAULT_DB;
			//no mdb
			log_message('debug', 'disabled mdb');
			return $this->setActiveTargetDB($active_target_db);
		}

		if($this->_enabled_multiple_level){
			//no multiple level config
			if(empty($this->_multiple_level_tree)){
				$active_target_db=self::DEFAULT_DB;
				//no mdb
				log_message('debug', 'no mdb');
				return $this->setActiveTargetDB($active_target_db);
			}
		}else{
			if(empty($this->_multiple_databases)){
				$active_target_db=self::DEFAULT_DB;
				//no mdb
				log_message('debug', 'no mdb');
				return $this->setActiveTargetDB($active_target_db);
			}
		}

		//default db rule
		//set fallback
		$active_target_db=$this->fallback_target_db;
		if(empty($this->fallback_target_db)){
			if($this->_enabled_multiple_level){
				//get first one
				$active_target_db=$this->_multiple_level_tree_top_key.'-'.array_key_first($this->_multiple_level_tree);
			}else{
				$active_target_db=self::SUPER_TARGET_DB;
				if(config_item('disabled_super_site')){
					$firstDBKey=null;
					$dbKeys=array_keys($this->_multiple_databases);
					if(!empty($dbKeys)){
						foreach ($dbKeys as $dbKey) {
							if($dbKey!=self::SUPER_TARGET_DB){
								$firstDBKey=$dbKey;
								break;
							}
						}
						if(!empty($firstDBKey)){
							//first one
							$active_target_db=$firstDBKey;
						}
					}
				}
			}
		}
		if(!empty($force_to_target_db) && $this->isValidTargetDB($force_to_target_db)){
			log_message('debug', 'try get target db from force_to_target_db', $force_to_target_db);
			return $this->setActiveTargetDB($force_to_target_db);
		}

		// Param/Env(command line) > SERVER variable > Domain > Input Get (__OG_TARGET_DB) >
		// Cookies (__OG_TARGET_DB) > IP Country
		$env_target_db=getenv(self::__OG_TARGET_DB);
		if($this->isValidTargetDB($env_target_db)){
			log_message('debug', 'try get target db from getenv', $env_target_db);
			return $this->setActiveTargetDB($env_target_db);
		}

		if(array_key_exists(self::__OG_TARGET_DB, $_SERVER) && !empty($_SERVER[self::__OG_TARGET_DB])){
			$server_target_db=$_SERVER[self::__OG_TARGET_DB];
			log_message('debug', 'try get target db from _SERVER', $server_target_db);
			if($this->isValidTargetDB($server_target_db)){
				return $this->setActiveTargetDB($server_target_db);
			}
		}

		//check domain settings
		$domain_target_db=$this->checkCurrencyDomain();
		if(!empty($domain_target_db)){
			return $this->setActiveTargetDB($domain_target_db);
		}

		$input_target_db=$this->CI->input->get(self::__OG_TARGET_DB);
		if(empty($input_target_db)){
			$input_target_db=$this->CI->input->post(self::__OG_TARGET_DB);
		}
		//try get from input get
		if($this->isValidTargetDB($input_target_db)){
			log_message('debug', 'try get target db from input get',
				['input_target_db'=>$input_target_db]);
			return $this->setActiveTargetDB($input_target_db);
		}

		//try cookies
		// $cookie_name=self::__OG_TARGET_DB.'_'.config_item('sub_project');
		$cookie_name=config_item('cookie_for_target_db');
		if(array_key_exists($cookie_name, $_COOKIE) && !empty($_COOKIE[$cookie_name])){
			$cookie_target_db=$_COOKIE[$cookie_name];
			log_message('debug', 'try get target db from cookies: '.$cookie_name,
				['cookie_target_db'=>$cookie_target_db]);
			if($this->isValidTargetDB($cookie_target_db)){
				return $this->setActiveTargetDB($cookie_target_db);
			}
		}
		//ip country to currency key
		$ip_country_to_currency_key=config_item('ip_country_to_currency_key');
		if(!empty($ip_country_to_currency_key)){
			$ip=$this->getIP();
			$ipCountry=$this->getIpCountry($ip);
			log_message('debug', 'ip:'.$ip.', ipCountry:'.$ipCountry);

			if($ipCountry!='Local' && !empty($ipCountry)){
				if(array_key_exists($ipCountry, $ip_country_to_currency_key)){
					$ip_country_target_db=$ip_country_to_currency_key[$ipCountry];
					if($this->isValidTargetDB($ip_country_target_db)){
						log_message('debug', 'try get target db from ip country',
							['ip_country_target_db'=>$ip_country_target_db]);
						return $this->setActiveTargetDB($ip_country_target_db);
					}
				}
			}
		}

		log_message('debug', 'mdb choose default:'.$active_target_db);
		return $this->setActiveTargetDB($active_target_db);
	}

	public function switchCIDatabaseToActiveTargetDB($force=true){
		return $this->switchCIDatabase($this->active_target_db, $force);
	}

	/**
	 * switch to target db, and remember, replace ci db
	 * @param  string  $targetDB
	 * @param  boolean $force
	 * @return boolean
	 */
	public function switchCIDatabase($targetDB=null, $force=true){

		if(empty($targetDB)){
			$targetDB=$this->active_target_db;
		}

		$this->init($targetDB);
		$this->rememberActiveTargetDB();

		if(!$force && isset($this->CI->db) && is_object($this->CI->db) && $this->CI->db->getOgTargetDB()==$targetDB){
			//still old
			return true;
		}

		if(!isset($this->db_list[$targetDB])){
			$this->db_list[$targetDB]=&DB($targetDB);
		}

		$this->CI->db = '';

		// Load the DB class
		$this->CI->db = $this->db_list[$targetDB];

		return true;
	}

	public function getDBNameFromTargetDB(){
		if(!$this->isEnabledMDB()){
			//single db
			return config_item('db.default.database');
		}
		if($this->_enabled_multiple_level){
			return $this->_database_config[$this->active_target_db]['database'];
		}else{
			//mdb
			return $this->_multiple_databases[$this->active_target_db]['default']['database'];
		}
	}

	public function isValidTargetDB($dbName){
		if(empty($dbName)){
			return false;
		}

		if($this->_enabled_multiple_level){
			return array_key_exists($dbName, $this->_database_config);
		}else{
			return array_key_exists($dbName, $this->_multiple_databases);
		}
	}

	public function getMDBList(){
		if($this->isEnabledMDB()){
			if($this->_enabled_multiple_level){
				return array_keys($this->_database_config);
			}else{
				return array_keys($this->_multiple_databases);
			}

		}

		return null;
	}

	public function getMDBConfig(){
		if($this->_enabled_multiple_level){
			return $this->_database_config;
		}else{
			return $this->_multiple_databases;
		}
	}

	public function loadReadOnlyDB($db=null){
		if(empty($db)){
			$db=$this->CI->db;
		}

		if ($this->CI->utils->isEnabledReadonlyDB()) {
			$targetDB=self::READONLY_DB;
			if($this->isEnabledMDB()){
				$targetDB=$db->getActiveGroup().'_'.self::READONLY_DB;
			}

			return DB($targetDB);
		}else{
			return $this->CI->db;
		}
	}

	public function rawConnectDB($readonly, $db=null){
		if(empty($db)){
			$db=$this->CI->db;
		}
		if($readonly){
			$db=$this->loadReadOnlyDB($db);
		}

        $conn=mysqli_connect($db->hostname,
            $db->username,
            $db->password,
            $db->database,
            $db->port);
        $charset=$db->char_set;
        mysqli_set_charset($conn, $charset);
        return $conn;
	}

	private function saveActiveTargetDBToCookies($expire=0){

		if (!is_numeric($expire)) {
			$expire = time() - 86500;
		} else {
			$expire = ($expire > 0) ? time() + $expire : 0;
		}
		$path='/';
		$domain='';
		$secure=false;
		$httpOnly=config_item('enabled_http_only_on_mdb_cookies');

		// $cookie_name=self::__OG_TARGET_DB.'_'.config_item('sub_project');
		$cookie_name=config_item('cookie_for_target_db');

		setcookie($cookie_name, $this->active_target_db, $expire, $path, $domain, $secure, $httpOnly);

	}

	//===helper===========================
	const DEFAULT_IP_HEADERS=['HTTP_X_SS_CLIENT_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_TRUE_CLIENT_IP', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP',
		'HTTP_X_CLUSTER_CLIENT_IP'];

	private function getHttpHost() {
		$host = null;
		if (isset($_SERVER['HTTP_HOST'])) {
			$host = @$_SERVER['HTTP_HOST'];
		}
		if (empty($host)) {
			$host = 'localhost';
		}
		return $host;
	}

	private function searchNotLocalIP($spoofArr) {
		if (!empty($spoofArr)) {
			foreach ($spoofArr as $ip) {

				//try ignore 192.168 && 10.
				if(is_local_ip($ip)){
					continue;
				}

				return $ip;
			}
			log_message('debug', 'cannot get right ip', ['spoofArr'=>$spoofArr]);
			return $spoofArr[0];
		}
		return FALSE;
	}

	private function get_value_from_server($key){
		return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
	}

	private function getIP(){

		foreach (self::DEFAULT_IP_HEADERS as $header) {
			if (($spoof = $this->get_value_from_server($header)) !== FALSE) {
				// Some proxies typically list the whole chain of IP
				// addresses through which the client has reached us.
				// e.g. client_ip, proxy_ip1, proxy_ip2, etc.
				if (strpos($spoof, ',') !== FALSE) {
					$spoof = explode(',', $spoof);
					$spoof = $this->searchNotLocalIP($spoof);
					// $spoof = $spoof[0];
				}

				if (!$this->valid_ip($spoof)) {
					$spoof = FALSE;
				} else {
					break;
				}
			}
		}
		$remoteAddr = '';
		if (isset($_SERVER['REMOTE_ADDR'])) {
			$remoteAddr = $_SERVER['REMOTE_ADDR'];
		}
		$ip_address = ($spoof !== FALSE) ? $spoof : $remoteAddr;

		if (!$this->valid_ip($ip_address)) {
			if(!$this->is_cli_request()){
				//real invalid ip
				log_message('debug', 'invalid ip:'.$ip_address, ['spoof'=>$spoof, 'remoteAddr'=>$remoteAddr, '_SERVER'=>$_SERVER]);
				$ip_address = '0.0.0.0';
			}else{
				//means local
				$ip_address = '127.0.0.1';
			}
		}

		return $ip_address;

		// return $this->CI->input->ip_address();
	}

	private function is_cli_request() {
		return (php_sapi_name() === 'cli' OR defined('STDIN'));
	}

	private function valid_ip($ip, $which = '') {
		$which = strtolower($which);

		// First check if filter_var is available
		if (is_callable('filter_var')) {
			switch ($which) {
			case 'ipv4':
				$flag = FILTER_FLAG_IPV4;
				break;
			case 'ipv6':
				$flag = FILTER_FLAG_IPV6;
				break;
			default:
				$flag = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6;
				break;
			}

			return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flag);
		}

		if ($which !== 'ipv6' && $which !== 'ipv4') {
			if (strpos($ip, ':') !== FALSE) {
				$which = 'ipv6';
			} elseif (strpos($ip, '.') !== FALSE) {
				$which = 'ipv4';
			} else {
				return FALSE;
			}
		}

		$func = '_valid_' . $which;
		return $this->$func($ip);
	}


	/**
	 * Validate IPv4 Address
	 *
	 * Updated version suggested by Geert De Deckere
	 *
	 * @access	protected
	 * @param	string
	 * @return	bool
	 */
	private function _valid_ipv4($ip) {
		$ip_segments = explode('.', $ip);

		// Always 4 segments needed
		if (count($ip_segments) !== 4) {
			return FALSE;
		}
		// IP can not start with 0
		if ($ip_segments[0][0] == '0') {
			return FALSE;
		}

		// Check each segment
		foreach ($ip_segments as $segment) {
			// IP segments must be digits and can not be
			// longer than 3 digits or greater then 255
			if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3) {
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate IPv6 Address
	 *
	 * @access	protected
	 * @param	string
	 * @return	bool
	 */
	private function _valid_ipv6($str) {
		// 8 groups, separated by :
		// 0-ffff per group
		// one set of consecutive 0 groups can be collapsed to ::

		$groups = 8;
		$collapsed = FALSE;

		$chunks = array_filter(
			preg_split('/(:{1,2})/', $str, NULL, PREG_SPLIT_DELIM_CAPTURE)
		);

		// Rule out easy nonsense
		if (current($chunks) == ':' OR end($chunks) == ':') {
			return FALSE;
		}

		// PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well
		if (strpos(end($chunks), '.') !== FALSE) {
			$ipv4 = array_pop($chunks);

			if (!$this->_valid_ipv4($ipv4)) {
				return FALSE;
			}

			$groups--;
		}

		while ($seg = array_pop($chunks)) {
			if ($seg[0] == ':') {
				if (--$groups == 0) {
					return FALSE; // too many groups
				}

				if (strlen($seg) > 2) {
					return FALSE; // long separator
				}

				if ($seg == '::') {
					if ($collapsed) {
						return FALSE; // multiple collapsed
					}

					$collapsed = TRUE;
				}
			} elseif (preg_match("/[^0-9a-f]/i", $seg) OR strlen($seg) > 4) {
				return FALSE; // invalid segment
			}
		}

		return $collapsed OR $groups == 1;
	}

	private function getIpCountry($ip) {
		if (empty($ip) || is_local_ip($ip)) {
			return 'Local';
		}

		$city = '';
		$country = '';
		$cityDB = config_item('ip_county_db_path');
		try {
			$reader = new \GeoIp2\Database\Reader($cityDB);
			$record = $reader->country($ip);

			// $city = $record->city->name;
			$country = $record->country->name;
			if (empty($city)) {
				$city = $country;
				if (empty($city)) {
					$city = '';
				}
			}
			if (empty($country)) {
				$country = '';
			}

			$reader->close();
		} catch (Exception $e) {
			log_message('error', $e->getTraceAsString());
		}
		return $country;
	}

}

/* End of file DB.php */
/* Location: ./system/database/DB.php */