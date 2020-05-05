<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Whitelist_library {

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->model(array('ip_whitelist_model', 'country_whitelist_model'));
	}

	public function ip_whitelisted($game_platform_id, $ip_address, $country_whitelist = true, $ip_whitelist = true) {
		
		if ($country_whitelist) {
			$country = $this->ci->utils->getCountry($ip_address);
			$result = $this->ci->country_whitelist_model->country_whitelisted($game_platform_id, $country);
		}

		if ($ip_whitelist && ! $result) {
			$result = $this->ci->ip_whitelist_model->ip_whitelisted($game_platform_id, $ip_address);
		}

		return $result;
	}

}

/* End of file whitelist_library.php */
/* Location: ./application/libraries/whitelist_library.php */