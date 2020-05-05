<?php

class Auth_module {

	private $CI;

	public function __construct() {
		$this->CI = &get_instance();
	}

	public function index() {

        $this->CI->load->model(['country_rules']);
        $ip = $this->CI->utils->getIP();
        $isSiteBlock = $this->CI->country_rules->getBlockedStatus($ip, 'is_agent');
        if ($isSiteBlock && $this->CI->utils->isEnabledFeature('enable_country_blocking_agency')) {
            list($city, $countryName) = $this->CI->utils->getIpCityAndCountry($ip);
            $block_page_url = $this->CI->country_rules->getBlockedPageUrl($countryName, $city);
            if (empty($block_page_url)) {
                show_error('blocked', 403);
            } else {
                redirect($block_page_url);
            }
        }else{
            $this->CI->utils->nocache();
        }
	}

}