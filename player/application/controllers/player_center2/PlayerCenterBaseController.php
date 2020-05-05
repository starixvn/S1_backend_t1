<?php
require_once APPPATH . 'controllers/BaseController.php';
require_once APPPATH . 'controllers/modules/player_profile.php';
// OGP-2967
require_once APPPATH . 'controllers/allowed_withdrawal_kyc_risk_score.php';

require_once  dirname(dirname(__FILE__)) . '/modules/withdrawal_process_flow_module.php';

/**
 * Reworked player center dashboard, provides all functionality available to player.
 *
 * @property CI_DB_driver $db
 * @property CI_Template $template
 * @property Utils $utils
 * @property Authentication $authentication
 * @property Player_security_library $player_security_library
 */
class PlayerCenterBaseController extends BaseController {
    use player_profile;
    use allowed_withdrawal_kyc_risk_score;
    use withdrawal_process_flow_module;

    # Holds the name of player site template. Does not change when visited using mobile.
    # Use $this->utils->getPlayerCenterTemplate() to return [templateName]/mobile when visited using mobile.
    protected $templateName = '';

    public function __construct(){
        parent::__construct();

        $this->load->library(['authentication']);

        # Redirect to login if not logged in
        if($this->authentication->isProtectedRoute() && !$this->authentication->isLoggedIn()) {
            redirect($this->utils->getPlayerLoginUrl());
        }

        #check suspended status
        $this->suspendLoingChk();


        $this->load->helper('url');
        $this->load->model(array('http_request', 'player', 'wallet_model', 'operatorglobalsettings'));

        $this->preloadSharedVars();
        $this->templateName = $this->utils->getPlayerCenterTemplate(false);
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
        $this->load->vars('player_center_template', $this->utils->getPlayerCenterTemplate(false));
        $this->load->vars('content_template', 'default.php'); # default content template
        $this->load->vars('activeNav', 'memberCenter');

        if($this->authentication->isLoggedIn()) {
            $playerId = $this->authentication->getPlayerId();
            $username = $this->authentication->getUsername();
            $player = $this->player_functions->getPlayerById($playerId);
            $playerStatus = $this->utils->getPlayerStatus($player['playerId']);

            if($playerStatus === Player_model::SELFEXCLUSION_STATUS){
                redirect($this->utils->getPlayerLogoutUrl());
            }

            $this->load->vars('playerId', $playerId);
            $this->load->vars('username', $username);
            $this->load->vars('player', $player);
            $this->load->vars('isLogged', TRUE);
            $this->load->vars(['playerStatus' => $playerStatus]);

            # Load wallet balances
            $big_wallet = $this->wallet_model->getOrderBigWallet($playerId);
            $this->load->vars('big_wallet', $big_wallet);
            $this->load->vars('pendingBalance', (object) ['frozen' => $big_wallet['main']['frozen']]);
            $this->load->vars('totalBalance', $big_wallet['total']);
            $this->load->vars('total_no_frozen', $big_wallet['total'] - $big_wallet['main']['frozen']);
            $subwallets = $big_wallet['sub'];
            $this->load->vars('subwallets', $subwallets);
            $this->load->vars('walletinfo', array(
                'mainWallet' => $big_wallet['main']['total_nofrozen'],
                'frozen' => $big_wallet['main']['frozen'],
                'subwallets' => $subwallets
            ));

            # For overview area, verified status
            $this->load->model(['playerbankdetails', 'common_token']);
            $playerBanks = $this->playerbankdetails->getBankDetails($playerId);
            $this->load->vars('isBankInfoAdded', !empty($playerBanks['deposit']) || !empty($playerBanks['withdrawal']));

            $this->load->vars('currency', $this->utils->getCurrentCurrency());

            $this->load->model(['agency_model']);
            $agency_agent = $this->agency_model->get_agent_by_binding_player_id($playerId);
            $this->load->vars(['player_binding_agency_agent' => $agency_agent]);
            $is_stable_center2_template=$this->operatorglobalsettings->getSettingValue('player_center_template', 'stable_center2')=='stable_center2';
            $this->load->vars(['show_agency_menu_in_nav' => !empty($agency_agent) && $is_stable_center2_template]);

            # Security Info
            $this->utils->debug_log("Start loading security info...");
            $this->utils->startEvent("Load security info");
            $this->load->library(['player_security_library']);
            $this->player_security_library->setPlayer($this->load->get_var('player'));
            $this->player_security_library->assign_common_vars();
            $this->utils->endEvent("Load security info");
        }else{
            $this->load->vars('isLogged', FALSE);
            $this->load->vars(['player_binding_agency_agent' => null]);
            $this->load->vars(['show_agency_menu_in_nav' =>false]);
        }

    }

    protected function preloadCashierVars() {
        $playerId = $this->load->get_var('playerId');

        # Load total balance
        $bigWallet = $this->utils->getBigWalletByPlayerId($playerId);
        $subwalletsBalance = array();
        foreach ($bigWallet['sub'] as $apiId => $subWallet) {
            $subwalletsBalance[$apiId] = $subWallet['total_nofrozen'];
        }

        $total_main_wallet_balance = $bigWallet['main']['total_nofrozen'];
        $total_subwallet_balance = array_sum($subwalletsBalance);

        # Load balances
        $this->load->vars('total_frozen', $bigWallet['total_frozen']);
        $this->load->vars('total_balance', $total_main_wallet_balance + $total_subwallet_balance + $bigWallet['main']['frozen']);
        $this->load->vars('total_subwallet_balance', $total_subwallet_balance);
        $this->load->vars('total_main_wallet_balance', $total_main_wallet_balance);
        $this->load->vars('total_no_frozen', $total_main_wallet_balance + $total_subwallet_balance);
        $this->load->vars('playerBalance', $this->utils->getTotalDepositWithdrawalBonusCashbackByPlayers($playerId));
        $this->load->vars('currency', $this->utils->getCurrentCurrency());
        $this->load->vars('content_template', 'cashier.php'); # cashier content template
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

    protected function suspendLoingChk(){
        $playerId = $this->authentication->getPlayerId();
        $chkStatus = $this->utils->getPlayerStatus($playerId);

        $route = $this->uri->segment(2);

        switch($route){
            case "deposit":
            case "withdraw":
            case "bank_account":
                if($chkStatus == 5){
                    redirect(site_url('player_center/dashboard'));
                }

                break;
        }
    }

    public function showPlayerAuthFailed($redirect_uri = NULL){
        $message = lang('Sorry, session timeout. please login again');

        if ($this->input->is_ajax_request()) {
            return $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
        }else{
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            redirect((empty($redirect_uri)) ? $this->utils->getPlayerLoginUrl() : $redirect_uri);
        }
        return;
    }
}
