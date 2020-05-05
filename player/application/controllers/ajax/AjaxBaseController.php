<?php
require_once APPPATH . 'controllers/BaseController.php';

/**
 * Reworked player center dashboard, provides all functionality available to player.
 *
 * @property Utils $utils
 * @property CI_Template $template
 */
abstract class AjaxBaseController extends BaseController {
	public function __construct(){
		parent::__construct();

		$this->load->library(['authentication']);
        
		$this->load->helper('url');
		$this->load->model(array('http_request', 'player', 'wallet_model', 'operatorglobalsettings'));

		$this->loadTemplate();
        
        $this->preloadSharedVars();
	}

	/**
	 * Preload required variable for views
	 *
	 * if need to use these variables in controller, can use:
	 * <code>
	 * $val = $this->load->get_var('{key}');
	 *
	 * // or
	 *
	 * $val = get_instance()->load->get_var('{key}');
	 * </code>
	 *
	 * @author Elvis Chen<elvis.chen@tripleonetech.net>
	 * @since version 20170831
	 *
	 * @access private
	 * @return void
	 */
	private function preloadSharedVars(){
		$this->load->vars('system_hosts', $this->utils->getSystemUrls());

		if($this->authentication->isLoggedIn()) {
			$playerId = $this->authentication->getPlayerId();
			$username = $this->authentication->getUsername();
			$this->load->vars('playerId', $playerId);
			$this->load->vars('username', $username);
			$this->load->vars('player', $this->player_functions->getPlayerById($playerId));

			$this->load->vars('isLogged', TRUE);

		}else{
			$this->load->vars('isLogged', FALSE);
		}
	}

    /**
     * Loads template for view based on regions in config > template.php
     *
     * $params parameter will be written into template based on their key.
     * Use keys like 'title', 'description', 'keywords' to control the META of page.
     */
    protected function loadTemplate($params = array()) {
        $this->template->set_template($this->utils->getPlayerCenterTemplate(FALSE));

        foreach($params as $metaKey => $metaValue){
            $this->template->write($metaKey, $metaValue);
        }
    }
}