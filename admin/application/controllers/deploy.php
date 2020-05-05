<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Deploy extends BaseController {

	function __construct() {
		parent::__construct();
	}

	private function get_deploy_flag() {
		return APPPATH . 'logs/deploy_flag';
	}

	public function deploy_all($token, $branch = 'master') {
		$deploy_token = $this->config->item('deploy_token');
		if (empty($deploy_token)) {
			show_error('EMPTY TOKEN', 500);
			return;
		}
		//check token
		if ($token == $deploy_token) {
			//create flag
			// $cmd = APPPATH . '../shell/deploy_all.sh ' . $branch . ' 2>&1';
			// echo "run " . $cmd;
			// echo shell_exec($cmd);
			touch($this->get_deploy_flag());
			chmod($this->get_deploy_flag(), 0666);
			echo 'SET DEPLOY FLAG:' . realpath($this->get_deploy_flag());
		} else {
			show_error('WRONG TOKEN', 401);
		}

	}

}

////END OF FILE//////////////