<?php

require_once dirname(__FILE__) . '/BaseController.php';
/**
 * PlayerBaseController
 *
 * @property CI_DB_driver $db
 * @property CI_Template $template
 * @property Utils $utils
 * @property Authentication $authentication
 * @property Player_security_library $player_security_library
 */
class PlayerBaseController extends BaseController {

	protected function goBankDetails() {
		redirect(site_url('player_center/iframe_bankDetails'));
	}

	protected function goPlayerHome() {
		redirect(site_url($this->utils->getPlayerHomeUrl()));
	}

	protected function goViewCashier() {
		redirect(site_url('player_center/dashboard'));
	}

	protected function getPlayerHomeUrl() {
		return '/player_center/dashboard';
	}

	protected function goPlayerSettings($playerId) {
		redirect(site_url('player_center/profile/' . $playerId));
	}

	protected function goMessages() {
		redirect($this->utils->getPlayerMessageUrl());
	}

	protected function goPlayerPromotions() {
		redirect(site_url('player_center2/promotion'));
	}

	public function logout_to_home() {
        $this->load->library(['player_library']);

        $result = $this->authentication->logout();

		$return_url = $this->input->post('return_url');
    	if(empty($return_url)){
    		//try referrer
			if (isset($_SERVER['HTTP_REFERER'])) {
				$return_url = $_SERVER['HTTP_REFERER'];
			}
		}

    	$this->utils->debug_log('return_url', $return_url);

    	if(!empty($return_url)){

    		return redirect($return_url);

    	}else{

            return redirect($result['redirect_url']);

    	}
	}

    protected function preloadSharedVars(){
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
            $this->load->model('playerbankdetails');
            $playerBanks = $this->playerbankdetails->getBankDetails($playerId);

            $this->load->vars('isBankInfoAdded', !empty($playerBanks['deposit']) || !empty($playerBanks['withdrawal']));

            # Security Info
            $this->utils->debug_log("Start loading security info...");
            $this->utils->startEvent("Load security info");
            $this->load->library(['player_security_library']);
            $this->player_security_library->setPlayer($this->load->get_var('player'));
            $this->player_security_library->assign_common_vars();
            $this->utils->endEvent("Load security info");
        }else{
            $this->load->vars('isLogged', FALSE);
        }
    }

    protected function preloadDepositVars(){
        $this->load->model(['banktype', 'payment_account', 'playerbankdetails', 'sale_order', 'promorules']);
        $payment_all_accounts=null;
        $payment_manual_accounts=null;
        $payment_auto_accounts=null;
        $payment_accounts=null;
        $playerId = $this->load->get_var('playerId');
        if(!empty($playerId)){

	        $payment_all_accounts = $this->payment_account->getAvailableDefaultCollectionAccount($playerId);

	        $payment_manual_accounts = ($payment_all_accounts[MANUAL_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[MANUAL_ONLINE_PAYMENT]['list'] : [];
	        if($payment_all_accounts[LOCAL_BANK_OFFLINE]['enabled']){
	            foreach($payment_all_accounts[LOCAL_BANK_OFFLINE]['list'] as $payment_account){
	                $payment_manual_accounts[] = $payment_account;
	            }
	        }
	        $payment_auto_accounts = ($payment_all_accounts[AUTO_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[AUTO_ONLINE_PAYMENT]['list'] : [];

	        $payment_accounts = [];
	        foreach($payment_manual_accounts as $payment_account){
	            $payment_accounts[] = $payment_account;
	        }
	        foreach($payment_auto_accounts as $payment_account){
	            $payment_accounts[] = $payment_account;
	        }

        }

        $this->load->vars('payment_accounts', $payment_accounts);
        $this->load->vars('payment_manual_accounts', $payment_manual_accounts);
        $this->load->vars('payment_auto_accounts', $payment_auto_accounts);

        $this->load->vars('deposit_process_mode', $this->operatorglobalsettings->getSettingIntValue('deposit_process', DEPOSIT_PROCESS_MODE1));
        $this->load->vars('enabled_withdrawal_password', $this->utils->isEnabledFeature('enabled_withdrawal_password'));

        $player_bank_accounts = (array)$this->playerbankdetails->getAvailableDepositBankDetail($playerId);

        $this->load->vars('player_bank_accounts', $player_bank_accounts);
        $this->load->vars('sub_nav_active', 'deposit');


    }

}