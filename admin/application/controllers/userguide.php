<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

class Userguide extends BaseController {

	function __construct() {
		parent::__construct();
		$this->load->library(array('template','authentication'));
	
	}


	public function index() {

		if (!$this->authentication->isLoggedIn()) {
			
			redirect('auth/login');
		}

			$props = array('template' => 'template/admin_iframe_template');
			$this->template->add_template('iframe', $props, TRUE);
			$this->template->add_js('resources/js/jquery.unveil.js');
			$this->template->write_view('main_content', 'userguide');
			$this->template->render();

     }

}

/* End of file player_management.php */
/* Location: ./application/controllers/userguide.php */