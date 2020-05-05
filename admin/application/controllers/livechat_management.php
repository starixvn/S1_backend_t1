<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

/**
 * livechat management
 */
class livechat_management extends BaseController {

	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('template'));

	}

	
	/**
	 * All methods being called go through this function first
	 * 
	 * @param  string $method The method being called
	 * @param  array  $params Method parameters
	 */
	function _remap($method, $params = array()){
		// -- OGP-8657 Remove LiveChat Setting from the SBE system menu
		// -- This forces 404 page, remove this to re-enable access to pages of live chat management
		return show_404();

		if (method_exists($this, $method))
            return call_user_func_array(array($this, $method), $params);
        
        return show_404();
	}

	/**
	 * overview : loads template
	 *
	 * detail : Loads template for view based on regions in config > template.php
	 *
	 * @param  string 	$title
	 * @param  string 	$description
	 * @param  string 	$keywords
	 * @param  string 	$activenav
	 * @return load template
	 */
	private function loadTemplate($title, $description, $keywords, $activenav) {
		$this->template->add_js('resources/js/system_management/user_management.js');

		$this->template->add_js('resources/js/datatables.min.js');
		//$this->template->add_js('resources/js/jquery.dataTables.min.js');
		//$this->template->add_js('resources/js/dataTables.responsive.min.js');

		$this->template->add_css('resources/css/general/style.css');
		//$this->template->add_css('resources/css/jquery.dataTables.css');
		//$this->template->add_css('resources/css/dataTables.responsive.css');
		$this->template->add_css('resources/css/datatables.min.css');

		$this->template->write('title', $title);
		$this->template->write('description', $description);
		$this->template->write('keywords', $keywords);
		$this->template->write('activenav', $activenav);
		$this->template->write('username', $this->authentication->getUsername());
		$this->template->write('userId', $this->authentication->getUserId());

		/*$lang = $this->language_function->getCurrentLanguage();
			$langCode = $this->language_function->getLanguageCode($lang);
			$language = $this->language_function->getLanguage($lang);
		*/
		$this->template->write_view('sidebar', 'system_management/sidebar');
	}

	/**
	 * overview : alert message
	 *
	 * detail: set message for users
	 * @param int 		$type
	 * @param string 	$message
	 * @return  set session user data
	 */
	public function alertMessage($type, $message) {
		switch ($type) {
		case '1':
			$show_message = array(
				'result' => 'success',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			break;

		case '2':
			$show_message = array(
				'result' => 'danger',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			break;

		case '3':
			$show_message = array(
				'result' => 'warning',
				'message' => $message,
			);
			$this->session->set_userdata($show_message);
			break;
		}
	}

	public function liveChatTipCheck(){
		require_once __DIR__ . '/../libraries/lib_livechat.php';

		$username = $this->input->post('username');
		$tipAmount = $this->input->post('tipAmount');
		$operatorName = $this->input->post('operatorName');

		$result = Lib_livechat::checkTipIfValid($username, $tipAmount, $operatorName);

		return $result;
	}

	public function livechatSetting(){
		$this->load->library(array('permissions'));
		$this->permissions->checkSettings();
		$this->permissions->setPermissions();

		if(!($this->utils->isEnabledFeature('show_admin_support_live_chat') || 
			$this->utils->isEnabledFeature('enable_player_center_live_chat') || 
			$this->utils->isEnabledFeature('enable_player_center_mobile_live_chat')) || 
			!$this->permissions->checkPermissions('live_chat_settings'))
			return $this->error_access();

		$this->load->model('livechat_setting_model');
		$this->loadTemplate('User Management', '', '', 'system');

		$data['items'] = $this->livechat_setting_model->getLivechatSetting();

		$this->template->write_view('main_content', 'livechat_management/view_livechat_setting', $data);
		$this->template->render();
	}

	public function saveLivechatSetting(){
		$this->load->model('livechat_setting_model');
		$livechat_setting = $this->livechat_setting_model->getLivechatSetting();

		foreach ($livechat_setting as $key => $value) {
			$livechat_data = $this->input->post($value['livechatSettingName']);
			
			$data = array(
				'livechatData' => $livechat_data,
			);
			$this->livechat_setting_model->saveLivechatSetting($data, $value['livechatSettingName']);
		}

		$data = array(
			'username' => $this->authentication->getUsername(),
			'management' => 'System Management',
			'userRole' => $this->rolesfunctions->getRoleByUserId($this->authentication->getUserId())['roleName'],
			'action' => 'Edit Livechat Settings',
			'description' => "User " . $this->authentication->getUsername() . " edited Livechat Setting",
			'logDate' => date("Y-m-d H:i:s"),
			'status' => '0',
		);
		$this->report_functions->recordAction($data);

		$message = lang('con.livechat');
		$this->alertMessage(1, $message);

		redirect(BASEURL . 'livechat_management/livechatSetting');
	}

	/**
	 * overview : error access
	 *
	 * detail : show error message if user can't access the page
	 */
	private function error_access() {
		$this->loadTemplate('Livechat Management', '', '', 'player');

		$message = lang('con.usm01');
		$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);

		$this->template->render();
	}

}