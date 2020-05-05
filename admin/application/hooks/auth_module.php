<?php

class Auth_module {

	private $CI;

	public function __construct() {
		$this->CI = &get_instance();
	}

	public function index() {
		$this->CI->utils->nocache();
		$this->CI->utils->recordFullIP();
		$this->CI->utils->checkBlockCountry();
		$this->CI->utils->logAction();
	}

}