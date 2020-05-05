<?php

require_once 'PlayerCenterBaseController.php';

/**
 * Provides bank account function
 *
 * @property Playerbankdetails $playerbankdetails
 * @property Player_Functions $player_functions
 */
class Bank_account extends PlayerCenterBaseController {
    public function __construct() {
        parent::__construct();

        $this->preloadCashierVars();

        $this->load->model(['playerbankdetails', 'banktype', 'financial_account_setting']);
    }

    public function index() {
        $playerId = $this->load->get_var('playerId');

        $bank_details = $this->playerbankdetails->getNotDeletedBankInfoList($playerId);

        $data['bank_details'] = $bank_details;
        $data['sub_nav_active'] = 'bank_account';
        $data['banktype'] = $this->banktype;
        $data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenField($playerId);

        # List of subwallet and game wallet with custom list control display in quick transfer bar
        $subwallet=null;
        $success=$this->wallet_model->lockAndTransForPlayerBalance($playerId, function () use (
            $playerId, &$subwallet) {

            $subwallet = $this->wallet_model->getAllPlayerAccountByPlayerId($playerId);
            return !empty($subwallet);
        });
        $data['game'] = $this->external_system->getAllActiveSytemGameApi();
        $data['subwallet'] = $subwallet;
        $data['active_flag'] = $this->banktype->getDistinctActivePaymentTypeFlag();

        $this->loadTemplate();
        $this->template->append_function_title(lang('Bank Account'));
        $this->template->add_js('/common/js/player_center/player-cashier.js');
		$this->template->add_js('/common/js/plugins/province_city_select.js');
        $this->template->add_js('/common/js/player_center/player-bank-account.js');
		$this->template->add_js('/resources/js/validator.js');
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/bank_account/list', $data);
        $this->template->render();
    }
}
