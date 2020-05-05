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
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Common Functions
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/
 */

// ------------------------------------------------------------------------

/**
 * Determines if the current version of PHP is greater then the supplied value
 *
 * Since there are a few places where we conditionally test for PHP > 5
 * we'll set a static variable.
 *
 * @access	public
 * @param	string
 * @return	bool	TRUE if the current version is $version or higher
 */
if (!function_exists('is_php')) {
	function is_php($version = '5.0.0') {
		static $_is_php;
		$version = (string) $version;

		if (!isset($_is_php[$version])) {
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
		}

		return $_is_php[$version];
	}
}

// ------------------------------------------------------------------------

/**
 * Tests for file writability
 *
 * is_writable() returns TRUE on Windows servers when you really can't write to
 * the file, based on the read-only attribute.  is_writable() is also unreliable
 * on Unix servers if safe_mode is on.
 *
 * @access	private
 * @return	void
 */
if (!function_exists('is_really_writable')) {
	function is_really_writable($file) {
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE) {
			return is_writable($file);
		}

		// For windows servers and safe_mode "on" installations we'll actually
		// write a file then read it.  Bah...
		if (is_dir($file)) {
			$file = rtrim($file, '/') . '/' . md5(mt_rand(1, 100) . mt_rand(1, 100));

			if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		} elseif (!is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}

// ------------------------------------------------------------------------

/**
 * Class registry
 *
 * This function acts as a singleton.  If the requested class does not
 * exist it is instantiated and set to a static variable.  If it has
 * previously been instantiated the variable is returned.
 *
 * @access	public
 * @param	string	the class name being requested
 * @param	string	the directory where the class should be found
 * @param	string	the class name prefix
 * @return	object
 */
if (!function_exists('load_class')) {
	function &load_class($class, $directory = 'libraries', $prefix = 'CI_') {
		static $_classes = array();

		// Does the class exist?  If so, we're done...
		if (isset($_classes[$class])) {
			return $_classes[$class];
		}

		$name = FALSE;

		// Look for the class first in the local application/libraries folder
		// then in the native system/libraries folder
		foreach (array(APPPATH, BASEPATH) as $path) {
			if (file_exists($path . $directory . '/' . $class . '.php')) {
				$name = $prefix . $class;

				if (class_exists($name) === FALSE) {
					require $path . $directory . '/' . $class . '.php';
				}

				break;
			}
		}

		// Is the request a class extension?  If so we load it too
		if (file_exists(APPPATH . $directory . '/' . config_item('subclass_prefix') . $class . '.php')) {
			$name = config_item('subclass_prefix') . $class;

			if (class_exists($name) === FALSE) {
				require APPPATH . $directory . '/' . config_item('subclass_prefix') . $class . '.php';
			}
		}

		// Did we find the class?
		if ($name === FALSE) {
			// Note: We use exit() rather then show_error() in order to avoid a
			// self-referencing loop with the Excptions class
			exit('Unable to locate the specified class: ' . $class . '.php');
		}

		// Keep track of what we just loaded
		is_loaded($class);

		$_classes[$class] = new $name();
		return $_classes[$class];
	}
}

// --------------------------------------------------------------------

/**
 * Keeps track of which libraries have been loaded.  This function is
 * called by the load_class() function above
 *
 * @access	public
 * @return	array
 */
if (!function_exists('is_loaded')) {
	function &is_loaded($class = '') {
		static $_is_loaded = array();

		if ($class != '') {
			$_is_loaded[strtolower($class)] = $class;
		}

		return $_is_loaded;
	}
}

// ------------------------------------------------------------------------

/**
 * Loads the main config.php file
 *
 * This function lets us grab the config file even if the Config class
 * hasn't been instantiated yet
 *
 * @access	private
 * @return	array
 */
if (!function_exists('get_config')) {
	function &get_config($replace = array()) {
		static $_config;

		if (isset($_config)) {
			return $_config[0];
		}

		// Is the config file in the environment folder?
		if (!defined('ENVIRONMENT') OR !file_exists($file_path = APPPATH . 'config/' . ENVIRONMENT . '/config.php')) {
			$file_path = APPPATH . 'config/config.php';
		}

		// Fetch the config file
		if (!file_exists($file_path)) {
			exit('The configuration file does not exist.');
		}

		require $file_path;

		// Does the $config array exist in the file?
		if (!isset($config) OR !is_array($config)) {
			exit('Your config file does not appear to be formatted correctly.');
		}

		// Are any values being dynamically replaced?
		if (count($replace) > 0) {
			foreach ($replace as $key => $val) {
				if (isset($config[$key])) {
					$config[$key] = $val;
				}
			}
		}

		$_config[0] = &$config;
		return $_config[0];
	}
}

// ------------------------------------------------------------------------

/**
 * Returns the specified config item
 *
 * @access	public
 * @return	mixed
 */
if (!function_exists('config_item')) {
	function config_item($item) {
		static $_config_item = array();

		if (!isset($_config_item[$item])) {
			$config = &get_config();

			if (!isset($config[$item])) {
				return FALSE;
			}
			$_config_item[$item] = $config[$item];
		}

		return $_config_item[$item];
	}
}

// ------------------------------------------------------------------------

/**
 * Error Handler
 *
 * This function lets us invoke the exception class and
 * display errors using the standard error template located
 * in application/errors/errors.php
 * This function will send the error page directly to the
 * browser and exit.
 *
 * @access	public
 * @return	void
 */
if (!function_exists('show_error')) {
	function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered') {
		$_error = &load_class('Exceptions', 'core');
        $error_template = (strtolower(php_sapi_name()) == 'cli') ? 'error_cli' : 'error_general';
		echo $_error->show_error($heading, $message, $error_template, $status_code);
		exit;
	}
}

// ------------------------------------------------------------------------

/**
 * 404 Page Handler
 *
 * This function is similar to the show_error() function above
 * However, instead of the standard error template it displays
 * 404 errors.
 *
 * @access	public
 * @return	void
 */
if (!function_exists('show_404')) {
	function show_404($page = '', $log_error = TRUE) {
		$_error = &load_class('Exceptions', 'core');
		$_error->show_404($page, $log_error);
		exit;
	}
}

// ------------------------------------------------------------------------

/**
 * Error Logging Interface
 *
 * We use this as a simple mechanism to access the logging
 * class and send messages to be logged.
 *
 * @access	public
 * @return	void
 */
if (!function_exists('log_message')) {
	function log_message($level, $message, $context=array()) {
		static $_log;

		if (config_item('log_threshold') == 0) {
			return;
		}

		$_log = &load_class('Log');
		$_log->write_log($level, $message, $context);
	}
}

if (!function_exists('raw_debug_log')) {
	function raw_error_log() {

		$args = func_get_args();

//		raw_debug_log('print raw_error_log',$args);

		$functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

//		raw_debug_log('print raw_error_log functions',$functions);

		$logFile = '/tmp/raw_error.log';
//		$logFile = '/tmp/php_app_raw_error.log';

		$msg=json_encode(['request_id'=>_REQUEST_ID, 'context'=>$args, 'trace'=>$functions, 'now'=>date('Y-m-d H:i:s') ]) . "\n";

		// if (!empty($logFile)) {
			// log_message('error', 'logFile:' . $logFile);
			//only for payment error
			if (!@error_log($msg, 3, $logFile)) {
				error_log('write failed:' . $logFile . ', got error: ' . $msg);
				// log_message('error', 'write failed:' . $logFile . ', got error: ' . $msg);
			}
		// }

		// raw_debug_log($args);

	}

	function raw_debug_log() {

		$args = func_get_args();

		$functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		// $msg = buildDebugMessage($args, $functions);

		// $this->writeServiceErrorLog('app_debug_log', $msg);
		$logFile = '/tmp/raw_debug.log';
//		$logFile = '/tmp/php_app_raw_debug.log';

		$msg=json_encode(['request_id'=>_REQUEST_ID, 'context'=>$args, 'now'=>date('Y-m-d H:i:s') ]) . "\n";
		// if (!empty($logFile)) {

			// log_message('error', 'logFile:' . $logFile);
			//only for payment error
			if (!@error_log($msg, 3, $logFile)) {
				error_log('write failed:' . $logFile . ', got error: ' . $msg);
				// log_message('error', 'write failed:' . $logFile . ', got error: ' . $msg);
			}
		// } else {
		// 	error_log($logName . ' is wrong ' . $logFile . ', got error: ' . $msg);
		// 	// log_message('error', $logName . ' is wrong ' . $logFile . ', got error: ' . $msg);
		// }
		// log_message('debug', $msg);
		// $this->addToDebugBar($msg);
		return $msg;
	}

	function formatDebugMessage($value) {

		if (is_object($value)) {
			if ($value instanceof \DateTime) {
				//print date time
				$str = $value->format(\DateTime::ISO8601);
			// } else if ($value instanceof \CI_DB_result) {
				// $str = $this->CI->db->last_query();
			} else if ($value instanceof \SimpleXMLElement) {
				$str = $value->asXML();
			} else if (method_exists($value, '__toString')) {
				$str = $value->__toString();
			} else if (method_exists($value, 'toString')) {
				$str = $value->toString();
			} else if (method_exists($value, 'toJson')) {
				$str = json_encode($value->toJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			} else {
				$str = json_encode((array) $value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			}
		} else if (is_array($value)) {
			$str = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		} else if (is_null($value)) {
			$str = '(NULL)';
		} else if (is_bool($value)) {
			$str = $value ? 'true' : 'false';
		} else {
			$str = $value;
		}

		return $str;
	}

	function formatObjectForJson($data){
		if(is_object($data)){
	        if($data instanceof DateTime){
	            return $data->format('Y-m-d H:i:s u'); //\DateTime::ISO8601);
	        }else if($data instanceof \SimpleXMLElement){
	            return $data->asXML();
	        } else if (method_exists($data, '__toString')) {
	            return $data->__toString();
	        } else if (method_exists($data, 'toString')) {
	            return $data->toString();
	        } else if (method_exists($data, 'toJson')) {
	            return $data->toJson();
	        }
		}
        return $data;
	}

	function getSubTitleFromBacktrace($functions) {
		$subtitle = "";

		if (!empty($functions)) {
			$cnt = 0;
			foreach ($functions as $call) {
				//ignore CodeIgniter.php
				if (isset($call['file']) && strpos($call['file'], 'CodeIgniter.php') !== FALSE) {
					continue;
				}
				if (isset($call['function']) && strpos($call['function'], 'debug_log') !== FALSE) {
					continue;
				}
				if (isset($call['file']) && strpos($call['file'], 'public/index.php') !== FALSE) {
					continue;
				}
				if (empty($call['file']) && !empty($call['class'])) {
					$subtitle .= $call['class'] . "->" . $call['function'] . " > ";
				} else if (!empty($call['file'])) {
					$funcName = "";
					if (isset($call['function'])) {
						$funcName = '@' . @$call['function'];
					}
					$subtitle .= str_replace(array('/home/vagrant/Code/og', '/home/vagrant/Code', 'admin/application', 'player/application', 'aff/application', 'agency/application'), array('..', '..', '..', '..', '..', '..'), $call['file']) . ":" . $call['line'] . $funcName . " > ";
				}
				if (!empty($call['file']) || !empty($call['class'])) {
					$cnt++;
				}
				if ($cnt > 1) {
					break;
				}
			}
		}
		return $subtitle;
	}
	function buildDebugMessage($args, $functions, $title = 'APP', $fullStack = false, $addHeader = true) {

		// $functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		// $functions = array_reverse($functions);
		if (empty($functions)) {
			$functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		}

		$msg = '';

		if ($addHeader) {
			$subtitle = getSubTitleFromBacktrace($functions);

			// $env = $this->getConfig('RUNTIME_ENVIRONMENT');
			// $host = $this->getCallHost();

			// $functions = debug_backtrace();
			// if (count($functions) > 1) {
			// 	$last = $functions[1];
			// 	$subtitle = @$last['class'] . '.' . @$last['function'];
			// 	if (isset($last['line'])) {
			// 		$subtitle .= ':' . @$last['line'];
			// 	}
			// }
			$msg .= "[" . $title . "] [" . getmypid() . "] [";
			if (!empty($subtitle)) {
				$msg = $msg . $subtitle . '] [';
			}
		}
		foreach ($args as $key => $value) {
			$str = formatDebugMessage($value);

			$msg .= $key . ": " . $str . ", ";
		}

		if ($addHeader) {
			$msg .= ' ]';
		}

		if ($fullStack) {
			$msg .= "\nStack:\n";
			foreach ($functions as $call) {
				if (empty($call['file']) && !empty($call['class'])) {
					$msg .= $call['class'] . "->" . $call['function'] . "\n";
				} else if (!empty($call['file'])) {
					$funcName = "";
					if (isset($call['function'])) {
						$funcName = '@' . @$call['function'];
					}
					$msg .= str_replace(array('/home/vagrant/Code/og', 'admin/application', 'player/application', 'aff/application'), array('..', '..', '..', '..'), $call['file']) . ":" . $call['line'] . $funcName . "\n";
				}
			}
		}

		return $msg;
	}
}

// ------------------------------------------------------------------------

/**
 * Set HTTP Status Header
 *
 * @access	public
 * @param	int		the status code
 * @param	string
 * @return	void
 */
if (!function_exists('set_status_header')) {
	function set_status_header($code = 200, $text = '') {
		$stati = array(
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',

			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',

			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',

			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
		);

		if ($code == '' OR !is_numeric($code)) {
			show_error('Status codes must be numeric', 500);
		}

		if (isset($stati[$code]) AND $text == '') {
			$text = $stati[$code];
		}

		if ($text == '') {
			show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
		}

		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if (substr(php_sapi_name(), 0, 3) == 'cgi') {
			header("Status: {$code} {$text}", TRUE);
		} elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0') {
			header($server_protocol . " {$code} {$text}", TRUE, $code);
		} else {
			header("HTTP/1.1 {$code} {$text}", TRUE, $code);
		}
	}
}

// --------------------------------------------------------------------

/**
 * Exception Handler
 *
 * This is the custom exception handler that is declaired at the top
 * of Codeigniter.php.  The main reason we use this is to permit
 * PHP errors to be logged in our own log files since the user may
 * not have access to server logs. Since this function
 * effectively intercepts PHP errors, however, we also need
 * to display errors based on the current error_reporting level.
 * We do that with the use of a PHP error template.
 *
 * @access	private
 * @return	void
 */
if (!function_exists('_exception_handler')) {
	function _exception_handler($severity, $message, $filepath, $line) {
		// We don't bother with "strict" notices since they tend to fill up
		// the log file with excess information that isn't normally very helpful.
		// For example, if you are running PHP 5 and you use version 4 style
		// class functions (without prefixes like "public", "private", etc.)
		// you'll get notices telling you that these have been deprecated.
		if ($severity == E_STRICT) {
			return;
		}

		$_error = &load_class('Exceptions', 'core');

		// Should we display the error? We'll get the current error_reporting
		// level and add its bits with the severity bits to find out.
		if (($severity & error_reporting()) == $severity) {
			$show_php_error=true;
			//ignore soap fault
			if (strpos($message, 'SoapClient') !== FALSE) {
				$show_php_error=false;
			}

			if(config_item('ignore_undefined_php')==true){
				// log_message('info', 'severity:'.$severity.', message:'.$message);
				//ignore undefined php
				if($severity==E_NOTICE && strpos($message, 'Undefined') !== false ){
					$show_php_error=false;
				}
			}

			// raw_debug_log('ignore_unexpect_warning_php', $message, $severity, E_WARNING);

			if(config_item('ignore_unexpect_warning_php')==true){
				if($severity==E_WARNING && strpos($message, 'expects parameter') !== false ){
					$show_php_error=false;
				}
			}

			if(config_item('ignore_count_warning')==true){
				if($severity==E_WARNING && strpos($message, 'count()') !== false ){
					$show_php_error=false;
					return;
				}
			}

			if(config_item('ignore_php_warning_reference_variable_on_param')==true){
				if(strpos($message, 'Only variables should be passed by reference') !== false ){
					$show_php_error=false;
					return;
				}
			}

			if(config_item('disabled_all_php_page_error')==true){
				$show_php_error=false;
			}

			if(config_item('ignore_all_notice_error')==true && $severity==E_NOTICE){
				$show_php_error=false;
			}

			if(strpos($message, 'Redis::connect') !== false ){
				//ignore
				$show_php_error=false;
				return;
			}

			if(strpos($message, 'Cannot modify header information') !== false ){
				//ignore
				$show_php_error=false;
				return;
			}

			//ignore undifined
			if($show_php_error && !config_item('ignore_all_php_errors')){
				$_error->show_php_error($severity, $message, $filepath, $line);
			}
		}

		// Should we log the error?  No?  We're done...
		if (config_item('log_threshold') == 0) {
			return;
		}

		$_error->log_exception($severity, $message, $filepath, $line);

		if (config_item('log_use_elasticsearch')) {
            $_es = &load_class('Elasticsearch', 'core');
            $msg = 'Severity: '. $severity. '; Message: ' . $message . '; File: ' . $filepath . '; Line: ' . $line;
            $res = $_es->insertDocument($msg, 'php_err');
            //log_message('error', 'Core - ES insertDocument res: ' . json_encode($res));
        }
	}
}

// --------------------------------------------------------------------

/**
 * Remove Invisible Characters
 *
 * This prevents sandwiching null characters
 * between ascii characters, like Java\0script.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if (!function_exists('remove_invisible_characters')) {
	function remove_invisible_characters($str, $url_encoded = TRUE) {
		$non_displayables = array();

		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)

		if ($url_encoded) {
			$non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

		do {
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		} while ($count);

		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * Returns HTML escaped variable
 *
 * @access	public
 * @param	mixed
 * @return	mixed
 */
if (!function_exists('html_escape')) {
	function html_escape($var) {
		if (is_array($var)) {
			return array_map('html_escape', $var);
		} else {
			return htmlspecialchars($var, ENT_QUOTES, config_item('charset'));
		}
	}
}

if (!function_exists('is_local_ip')) {
	function is_local_ip($ip) {
        $pri_addrs = array(
			'10.0.0.0|10.255.255.255',
			'172.16.0.0|172.31.255.255',
			'192.168.0.0|192.168.255.255',
			'169.254.0.0|169.254.255.255',
			'127.0.0.0|127.255.255.255'
		);

        $long_ip = ip2long($ip);
        if($long_ip != -1) {
            foreach($pri_addrs AS $pri_addr)
            {
                list($start, $end) = explode('|', $pri_addr);

                 // IF IS PRIVATE
                if($long_ip >= ip2long($start) && $long_ip <= ip2long($end))
                 	return (TRUE);
            }
    	}

		return (FALSE);
	}
}

if (!function_exists('try_get_prefix')) {
	/**
	 * try_get_prefix from db setting or hostname
	 * @return prefix
	 */
	function try_get_prefix() {
		$_multiple_db=Multiple_db::getSingletonInstance();
		$default_db=$_multiple_db->getDBNameFromTargetDB();
		log_message('debug', 'load database from getDBNameFromTargetDB', $default_db);
        if($default_db!='og'){
            $_app_prefix=$default_db;
        }else{
            static $_log;
            $_log = &load_class('Log');

            $_app_prefix=$_log->getHostname();
        }
		$is_staging=config_item('RUNTIME_ENVIRONMENT')=='staging';
        if($is_staging && strpos($_app_prefix, 'staging')===false){
        	//try append staging
        	$_app_prefix.='_staging';
        }

        return $_app_prefix;
	}
}

if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}

if (!function_exists('add_multiple_databases_to_db')) {
	/**
	 * try_get_prefix from db setting or hostname
	 * @return prefix
	 */
	function add_config_to_db($CI, &$db) {

		$_disabled_multiple_database=$CI->config->item('disabled_multiple_database');
		if(!$_disabled_multiple_database){

			$_enabled_multiple_level=$CI->config->item('enabled_multiple_level');
			if($_enabled_multiple_level){
				$_multiple_level_tree=$CI->config->item('multiple_level_tree');
				if(!empty($_multiple_level_tree)){
					$_multiple_level_tree_top_key=$CI->config->item('multiple_level_tree_top_key');
					build_db_from_multiple_levels($CI, $_multiple_level_tree, $_multiple_level_tree_top_key, $db);
				}
				//ignore multiple_databases
			}else{
				$_multiple_databases=$CI->config->item('multiple_databases');
				if(!empty($_multiple_databases)){
					$_multiple_databases_default_setting=$CI->config->item('multiple_databases_default_setting');
					foreach ($_multiple_databases as $_single_db=>$_single_db_settings) {
						foreach ($_single_db_settings as $_single_db_type => $_single_db_conf) {

							if($_single_db_type!='default' && empty($_single_db_conf)){
								//copy default to empty
								$_single_db_conf=$_single_db_settings['default'];
							}

							//try add default
							foreach($_multiple_databases_default_setting as $_mdb_default_key=>$_mdb_default_val){
								if(!array_key_exists($_mdb_default_key, $_single_db_conf)){
									$_single_db_conf[$_mdb_default_key]=$_mdb_default_val;
								}
							}
							//assign key
							$_single_db_conf['__OG_TARGET_DB']=$_single_db;
							$_single_db_conf['__DB_TYPE']=$_single_db_type;
							if($_single_db_type=='default'){
								$db[$_single_db]=$_single_db_conf;
							}else{
								$db[$_single_db.'_'.$_single_db_type]=$_single_db_conf;
							}
						}
					}
				}
			}
		}else{
			$db['default']['hostname'] = $CI->config->item('db.default.hostname');
			$db['default']['port'] = $CI->config->item('db.default.port');
			$db['default']['username'] = $CI->config->item('db.default.username');
			$db['default']['password'] = $CI->config->item('db.default.password');
			$db['default']['database'] = $CI->config->item('db.default.database');
			$db['default']['dbdriver'] = $CI->config->item('db.default.dbdriver');
			$db['default']['dbprefix'] = $CI->config->item('db.default.dbprefix');
			$db['default']['pconnect'] = $CI->config->item('db.default.pconnect');
			$db['default']['db_debug'] = $CI->config->item('db.default.db_debug');
			$db['default']['cache_on'] = $CI->config->item('db.default.cache_on');
			$db['default']['cachedir'] = $CI->config->item('db.default.cachedir');
			$db['default']['char_set'] = $CI->config->item('db.default.char_set');
			$db['default']['dbcollat'] = $CI->config->item('db.default.dbcollat');
			$db['default']['swap_pre'] = $CI->config->item('db.default.swap_pre');
			$db['default']['autoinit'] = $CI->config->item('db.default.autoinit');
			$db['default']['stricton'] = $CI->config->item('db.default.stricton');

			$db['secondread']['hostname'] = $CI->config->item('db.default.hostname');
			$db['secondread']['port'] = $CI->config->item('db.default.port');
			$db['secondread']['username'] = $CI->config->item('db.default.username');
			$db['secondread']['password'] = $CI->config->item('db.default.password');
			$db['secondread']['database'] = $CI->config->item('db.default.database');
			$db['secondread']['dbdriver'] = $CI->config->item('db.default.dbdriver');
			$db['secondread']['dbprefix'] = $CI->config->item('db.default.dbprefix');
			$db['secondread']['pconnect'] = $CI->config->item('db.default.pconnect');
			$db['secondread']['db_debug'] = $CI->config->item('db.default.db_debug');
			$db['secondread']['cache_on'] = $CI->config->item('db.default.cache_on');
			$db['secondread']['cachedir'] = $CI->config->item('db.default.cachedir');
			$db['secondread']['char_set'] = $CI->config->item('db.default.char_set');
			$db['secondread']['dbcollat'] = $CI->config->item('db.default.dbcollat');
			$db['secondread']['swap_pre'] = $CI->config->item('db.default.swap_pre');
			$db['secondread']['autoinit'] = $CI->config->item('db.default.autoinit');
			$db['secondread']['stricton'] = $CI->config->item('db.default.stricton');

			$db['readonly']['hostname'] = $CI->config->item('db.readonly.hostname');
			$db['readonly']['port'] = $CI->config->item('db.readonly.port');
			$db['readonly']['username'] = $CI->config->item('db.readonly.username');
			$db['readonly']['password'] = $CI->config->item('db.readonly.password');
			$db['readonly']['database'] = $CI->config->item('db.readonly.database');
			$db['readonly']['dbdriver'] = $CI->config->item('db.readonly.dbdriver');
			$db['readonly']['dbprefix'] = $CI->config->item('db.readonly.dbprefix');
			$db['readonly']['pconnect'] = $CI->config->item('db.readonly.pconnect');
			$db['readonly']['db_debug'] = $CI->config->item('db.readonly.db_debug');
			$db['readonly']['cache_on'] = $CI->config->item('db.readonly.cache_on');
			$db['readonly']['cachedir'] = $CI->config->item('db.readonly.cachedir');
			$db['readonly']['char_set'] = $CI->config->item('db.readonly.char_set');
			$db['readonly']['dbcollat'] = $CI->config->item('db.readonly.dbcollat');
			$db['readonly']['swap_pre'] = $CI->config->item('db.readonly.swap_pre');
			$db['readonly']['autoinit'] = $CI->config->item('db.readonly.autoinit');
			$db['readonly']['stricton'] = $CI->config->item('db.readonly.stricton');
		}

	}

	function build_db_from_multiple_levels($CI, array $multipleLevelTree, $parentKey, &$db){
		$_multiple_databases_default_setting=$CI->config->item('multiple_databases_default_setting');
		//build tree to db array
		foreach ($multipleLevelTree as $currentDBKey => $currentDBValue) {
			if(!empty($currentDBKey) && !is_numeric($currentDBKey)){
				$currentDBValue=$multipleLevelTree[$currentDBKey];
				//build current group key
				$activeGroupKey=$parentKey.'-'.$currentDBKey;
				//content: default=>, readonly=>, sub-levels=>
				if(array_key_exists('default', $currentDBValue)){
					$_single_db_conf=$currentDBValue['default'];
					//try add default
					foreach($_multiple_databases_default_setting as $_mdb_default_key=>$_mdb_default_val){
						if(!array_key_exists($_mdb_default_key, $_single_db_conf)){
							$_single_db_conf[$_mdb_default_key]=$_mdb_default_val;
						}
					}
					$_single_db_conf['__OG_TARGET_DB']=$activeGroupKey;
					$_single_db_conf['__DB_TYPE']='default';
					$db[$activeGroupKey]=$_single_db_conf;
					if(array_key_exists('readonly', $currentDBValue)
						&& !empty($currentDBValue['readonly'])){
						foreach ($currentDBValue['readonly'] as $key=>$val) {
							$_single_db_conf[$key]=$val;
						}
						$_single_db_conf['__DB_TYPE']='readonly';
						$db[$activeGroupKey.'_readonly']=$_single_db_conf;
					}else{
						$_single_db_conf['__DB_TYPE']='readonly';
						//same with default
						$db[$activeGroupKey.'_readonly']=$_single_db_conf;
					}
				}
				if(array_key_exists('sub-levels', $currentDBValue) && !empty($currentDBValue['sub-levels'])
					&& is_array($currentDBValue['sub-levels'])){
					//sub level
					build_db_from_multiple_levels($CI, $currentDBValue['sub-levels'], $activeGroupKey, $db);
					$db[$activeGroupKey]['exists-sub-levels']=true;
				}else{
					$db[$activeGroupKey]['exists-sub-levels']=false;
				}
			}else{
				log_message('error', 'wrong db key', ['currentDBKey'=>$currentDBKey,
					'currentDBValue'=>$currentDBValue]);
			}
		}
	}

}

if (!function_exists('try_load_redis')) {
	function try_load_redis($CI) {

        $redis_sentinel_list=$CI->config->item('redis_sentinel_list');
        $default_mastername_of_redis_sentinel=$CI->config->item('default_mastername_of_redis_sentinel');
        $default_master_redis=$CI->config->item('default_master_redis');

        log_message('debug', 'get config of redis', [
        	'redis_sentinel_list'=>$redis_sentinel_list,
        	'default_mastername_of_redis_sentinel'=>$default_mastername_of_redis_sentinel,
        	'default_master_redis'=>$default_master_redis,
        ]);

        if(empty($redis_sentinel_list) || empty($default_mastername_of_redis_sentinel)){
            if(empty($default_master_redis)){
                //wrong setting
                log_message('error', 'wrong setting', ['redis_sentinel_list'=>$redis_sentinel_list,
                    'default_mastername_of_redis_sentinel'=>$default_mastername_of_redis_sentinel]);
                return null;
            }

            //just master redis
            $redis=new \Redis();
            try{
		        log_message('debug', 'try connect redis directly', ['default_master_redis'=>$default_master_redis]);
                $success=$redis->connect($default_master_redis['host'],
                    $default_master_redis['port'], $default_master_redis['timeout']);
            }catch(Exception $e){
                log_message('error', 'exception when connect redis', ['exception'=>$e]);
                return null;
            }
            return $redis;
        }else{

			require_once dirname(__FILE__).'/../libraries/RedisSentinel/SentinelClientNotConnectException.php';
			require_once dirname(__FILE__).'/../libraries/RedisSentinel/Sentinel.php';
			require_once dirname(__FILE__).'/../libraries/RedisSentinel/SentinelPool.php';

            $redis =null;
            try{

		        log_message('debug', 'try visit sentinel pool', ['redis_sentinel_list'=>$redis_sentinel_list]);
                //load redis sentinel
                $sentinelPool=new \RedisSentinel\SentinelPool($redis_sentinel_list);
                $redis =$sentinelPool->getRedis($default_mastername_of_redis_sentinel);
            }catch(\RedisSentinel\SentinelClientNotConnectException $e){
                log_message('error', 'SentinelClientNotConnectException', ['exception'=>$e]);
                return null;
            }
            return $redis;
        }

       	return null;

	}
}

/* End of file Common.php */
/* Location: ./system/core/Common.php */
