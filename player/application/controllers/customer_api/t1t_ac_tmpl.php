<?php
/**
 * Access control wrapper of api_common as abstract class
 *
 * @see		routes		(player/application/config/routes.php)
 * @see		api_common	(player/application/controllers/api_common.php)
 *
 * @author 	Rupert Chen <rupert.php.tw@tripleonetech.net>
 */

require_once dirname(__FILE__) . '/../api_common.php';

abstract class T1t_ac_tmpl extends Api_common {

	/**
	 * $black_list			: list of disallowed API methods
	 * $black_list_enabled	: True to use black list, false to disable
	 * $white_list			: list of allowed API methods
	 * $white_list_enabled	: True to use white list, false to disable
	 *
	 * By default, only apiEcho and apiPostEcho are allowed
	 * Each client class must extend this class and set their own access rules
	 */
	protected $black_list_enabled = false;
	protected $black_list = [];

	protected $white_list_enabled = true;
	protected $white_list = ['apiEcho' , 'apiPostEcho' ];

	protected $log_ident;
	protected $debug = 0;
	protected $enable_cross_domain=true;

	function __construct() {
		parent::__construct();

		if($this->enable_cross_domain && $this->utils->isOptionsRequest()){
			//options request
			$this->returnJsonResult(null);
			exit(0);
		}

		// Read white/black list from config, if available
		$this_class = $this->router->class;
		$this->log_ident = "api/{$this_class}";
		$config_ident = strtolower($this_class);
		$conf_access_control = $this->utils->getConfig('api_common_access_control');

		if (isset($conf_access_control[$config_ident])) {
			$conf_ac = $conf_access_control[$config_ident];

			if (isset($conf_ac['debug_level'])) {
				$this->debug = $conf_ac['debug_level'];
				$this->log('cons', [ 'mesg' => "Using debug_level from config", 'debug_level' => $this->debug ]);
			}

			if (isset($conf_ac['black_list'])) {
				$this->black_list = $conf_ac['black_list'];
				if ($this->debug >= 2)
					{ $this->log('cons', "Using black list from config"); }
			}

			if (isset($conf_ac['white_list'])) {
				$this->white_list = $conf_ac['white_list'];
				if ($this->debug >= 2)
					{ $this->log('cons', "Using white list from config"); }
			}

			if (isset($conf_ac['black_list_enabled'])) {
				$this->black_list_enabled = $conf_ac['black_list_enabled'];
				if ($this->debug >= 2)
					{ $this->log('cons', "Using black_list_enable from config"); }
			}

			if (isset($conf_ac['white_list_enabled'])) {
				$this->white_list_enabled = $conf_ac['white_list_enabled'];
				if ($this->debug >= 2)
					{ $this->log('cons', "Using white_list_enable from config"); }
			}
		}

		if ($this->debug >= 2)
			{ $this->log('cons', 'white_list', $this->white_list, 'white_list_enabled', $this->white_list_enabled, 'black_list', $this->black_list, 'black_list_enabled', $this->black_list_enabled); }

		// Check if white list empty and enabled
		if ($this->white_list_enabled == true && empty($this->white_list)) {
			$this->log('cons', "WARNING: Empty white list active - all methods are blocked");
		}
	}

	/**
	 * API access regulator, checks method against black/white list
	 *
	 * @uses	$this->black_list
	 * @uses	$this->white_list
	 * @param	string	$method		Name of method being called
	 *
	 * @return	none
	 */
    public function _remap($method){
        global $CI, $URI;

        // Block methods listed in black list
        if ($this->black_list_enabled == true && in_array($method, $this->black_list)) {
        	$this->log("_remap()", [ 'mesg' => "Access denied - method in blacklist", 'method' => $method ]);
        	return show_404();
        }

        // Block methods NOT listed in white list
        if ($this->white_list_enabled == true && !in_array($method, $this->white_list)) {
        	$this->log("_remap()", [ 'mesg' => "Access denied - method not in whitelist", 'method' => $method ]);
        	return show_404();
        }

		return call_user_func_array(array(&$CI, $method), array_slice($URI->rsegments, 2));
    }

	protected function log() {
		$args = func_get_args();
		$lead = "{$this->log_ident} {$args[0]}";
		$args[0] = $lead;
		call_user_func_array( [ & $this->utils, 'debug_log' ], $args);
	}

}