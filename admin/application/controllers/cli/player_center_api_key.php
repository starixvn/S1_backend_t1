<?php
// if (PHP_SAPI === 'cli') {
// 	exit('No web access allowed');
// }

class Player_center_api_key extends CI_Controller {

	protected $co = [
		0 => "\033[0m" ,
		11 => "\033[1;31;40m" ,
		12 => "\033[1;32;40m" ,
		13 => "\033[1;33;40m" ,
		14 => "\033[1;34;40m" ,
		15 => "\033[1;35;40m" ,
		16 => "\033[1;36;40m" ,
		17 => "\033[1;37;40m" ,
		21 => "\033[1;37;40m" ,
		22 => "\033[1;31;40m" ,
		33 => "\033[0;30;43m"
	];

	function __construct() {
		parent::__construct();
	}

	function index() {
		$this->api_key_check_status();
	}

	function status() { $this->api_key_check_status(); }

	protected function api_key_get_status() {
		$apikey_stat = [
			'api_key_player_center_required' => $this->config->item('api_key_player_center_required') ,
			'api_request_ip_prefer_remote_addr' => $this->config->item('api_request_ip_prefer_remote_addr') ,
			'api_key_player_center' => $this->config->item('api_key_player_center')
		];

		return $apikey_stat;
	}

	public function add() {
		$this->pr_header1("Checking current api_key and generating new one...");

		$apikey_stat = $this->api_key_get_status();

		if (!$apikey_stat['api_key_player_center_required']) {
			$this->pr_warn("api_key_player_center_required is not set.");
			echo "
  Change following item to true or add it in config_secret_local.php:
    \$config['api_key_player_center_required'] = true;\n";
		}

		if (empty($apikey_stat['api_key_player_center'])) {
			$this->pr_warn("No api_key is configured so far.");
			echo "
  Add following item in config_secret_local.php:
    \$config['api_key_player_center'] = [
      'test_api_key' => [ '220.135.118.23'] ,
    ];\n";
		}

		// if ($apikey_stat['api_key_player_center_required'] && !empty($apikey_stat['api_key_player_center'])) {
		if (1) {
			$this->pr_header("Generating new api_key...");
			$api_key_new = $this->utils->generateRandomCode(10);
			echo "
  New api_key: $api_key_new
  Add following item in \$config['api_key_player_center'] in config_secret_local.php of the site
  with ip_list changed to desired IP(s):
    '$api_key_new' => [ 'ip_list' ] ,
    \n";
		}
	}

	public function api_key_check_status() {
		$this->pr_header1("Checking current api_key status");
		$co = $this->co;
		$apikey_stat = $this->api_key_get_status();

		$this->pr_header("Api_key switches:");
		foreach ($apikey_stat as $sw => $val) {
			if ($sw == 'api_key_player_center') { continue; }
			echo $this->col(sprintf("\t%-36s", $sw), 14);
			$co_v = ($val === true) ? 21 : 22;
			echo $this->col(sprintf("%-10s\n", var_export($val, 1)), $co_v);
		}

		if (!$apikey_stat['api_key_player_center_required']) {
			$this->pr_warn("api_key_player_center_required is set false or not present, this disables any access to Api_common.");
		}

		// List configured api_keys
		$api_key_player_center = $apikey_stat['api_key_player_center'];
		$this->pr_header("Configured api_keys:");
		if (empty($api_key_player_center)) {
			$this->pr_warn("No api_key configured.");
		}
		else {
			$k = 0;
			foreach ($api_key_player_center as $api_key => $ip_list) {
				++$k;
				echo sprintf("\t{$co[14]}%2d: {$co[17]}$api_key{$co[0]}\n", $k);
				echo sprintf("\t    {$co[14]}Allowed IP: {$co[17]}%s{$co[0]}\n", implode(', ', $ip_list));
				if (in_array('*', $ip_list) || in_array('any', $ip_list)) {
					$this->pr_warn2("\t    api_key '{$api_key}' can be used with any IP.");
				}
			}
		}
	}

	protected function pr_header($s) { echo "\n{$this->col($s, 16)}\n"; }
	protected function pr_header1($s) { echo "\n{$this->col($s, 12)}\n"; }
	protected function pr_warn($s) { echo "\n{$this->col($s, 33)}\n"; }
	protected function pr_warn2($s) { echo "{$this->col($s, 13)}\n"; }

	protected function col($s, $c) {
		$co = $this->co;
		return "{$co[$c]}{$s}{$co[0]}";
	}

}
