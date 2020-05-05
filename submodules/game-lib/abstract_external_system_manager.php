<?php

abstract class Abstract_external_system_manager {

	protected $API = null;
	protected $CI = null;

	//type => array(platformCode => instance)
	protected $API_ARRAY = array();

	public function __construct($params = null) {
		//load all class
		$this->CI = &get_instance();

		// log_message("error", var_export(self::API_MAPS, true));
		$externalSystemTypes = $this->CI->config->item('external_system_types');
		foreach ((array) $externalSystemTypes as $systemType) {
			$this->API_ARRAY[$systemType] = array();
		}
		if ($params && !empty($params["platform_code"]) && $params["platform_code"]) {
			$this->initApi($params["platform_code"], $params);
		}
	}

	public function initApi($platformCode = null, $params = null) {
		$this->CI->utils->debug_log('platformCode', $platformCode);
		//use utils
		list($loaded, $clsName) = $this->CI->utils->loadExternalSystemLib($platformCode, $params);
		if ($loaded && $clsName) {
			$this->API = $this->CI->$clsName;
			$this->initCustom($platformCode, $params);
		}
		return $this->API;
	}

	public function getApi($platformCode = null, $params = null) {
		if (!empty($platformCode)) {
			//reinit
			$this->initApi($platformCode, $params);
		}

		// log_message("debug", 'API : ' . ($this->API == null));
		return $this->API;
	}

	public function loadAllApiByType($type) {
		$this->CI->load->model('external_system');
		$allApis = $this->CI->external_system->getAllActiveSystemApiByType($type);

		foreach ($allApis as $anApi) {
			$platformCode = $anApi['id'];
			if ($this->CI->utils->isEmptyInArray($platformCode, $this->API_ARRAY)) {
				list($loaded, $clsName) = $this->CI->utils->loadExternalSystemLib($platformCode);
				if ($loaded && $clsName && !empty($this->CI->$clsName)) {
					$this->API_ARRAY[$type][$platformCode] = $this->CI->$clsName;
					$this->initCustom($platformCode);
				}
			}
		}

		return $this->API_ARRAY;
	}

	abstract function initCustom($platformCode, $params = null);

}

///END OF FILE////