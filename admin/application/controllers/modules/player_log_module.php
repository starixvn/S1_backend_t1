<?php
trait player_log_module {

    public function depositHistory() {
        $this->load->model(['sale_order']);
        $this->load->view('player_management/ajax_ui_sale_order');
    }

    public function withdrawHistory() {
        $this->load->model(['operatorglobalsettings']);

        $data = array();
        $stages = $this->operatorglobalsettings->getCustomWithdrawalProcessingStage();
        foreach ($stages as $key => $value) {
            if(is_array($value) && array_key_exists('enabled', $value) && $value['enabled']){
                if(is_int($key)){
                    $data['customStage']['CS'.$key] = $value['name'];
                }
            }
        }

        $this->load->view('player_management/ajax_ui_withdraw', $data);
    }

    public function transferHistory() {
        $this->load->model(['wallet_model', 'external_system']);
        $this->utils->loadAnyGameApiObject();

        $data = ['game_platforms' => $this->external_system->getAllActiveSytemGameApi()];

        $this->load->view('player_management/ajax_ui_transfer', $data);
    }

    public function balance_history() {
        $this->load->view('player_management/ajax_ui_balance_history');
    }

    public function adjustment_history_tab(){
        $this->load->view('player_management/ajax_ui_adjustment_history_tab');
    }

    public function adjustment_history_tab_V2(){
        $this->load->view('player_management/ajax_ui_adjustment_history_tab_V2');
    }

    public function transactionHistory() {
        $this->load->view('player_management/ajax_ui_transaction');
    }

    public function personalHistory($player_id) {
        $data['personal_history'] = $this->player_model->getPlayerUpdates($player_id);
        $this->load->view('player_management/ajax_ui_personal', $data);
    }

    public function bankHistory() {
        $this->load->view('player_management/ajax_ui_bank');
    }

    public function promoStatus() {
        $this->load->model('promorules');
        $sort = 'promoName';
        $data['promoList'] = $this->promorules->getPromoSettingList($sort, null, null);
        $this->load->view('player_management/ajax_ui_promostatus',$data);
    }

    public function gamesHistory($bet_type = 1) {
        $this->load->model(array('external_system', 'game_logs'));
        $data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
        $data['bet_type'] = $bet_type; # bet_type = 1 (settled) bet_type = 2 (unsettled)
        $this->load->view('player_management/ajax_ui_game', $data);
    }

    public function friendReferralStatus() {
        $this->load->view('player_management/ajax_ui_friend_referral');
    }

    public function chatHistory() {
        $this->load->view('player_management/messages_history');
    }

    public function ipHistory() {
        $this->load->view('player_management/ajax_ui_ip');
    }

    public function dupAccounts() {
        $this->load->view('player_management/ajax_ui_dup');
    }

    public function linked_account($player_id) {
        if ($this->permissions->checkPermissions('linked_account') && $this->utils->isEnabledFeature('linked_account')) {
            $playerUsername = $this->player_model->getUsernameById($player_id);
            $linkedAccounts = $this->getLinkedAccountDetails($playerUsername);
            $data['player_username'] = $playerUsername;
            $data['linked_accounts'] = !empty($linkedAccounts) ? $linkedAccounts[self::FIRST_CHILD_INDEX]['linked_accounts'] : null;

            $this->load->view('player_management/linked_account/player_info_linked_account_details',$data);
        }
    }

    public function cancelled_withdrawal(){
        $this->load->view('player_management/ajax_ui_cancelled_withdrawal');
    }

    public function cancelledTransferCondition(){
        $this->load->view('player_management/ajax_ui_cancelled_transfer_condition');
    }

    public function kyc_history(){
        $this->load->view('player_management/ajax_ui_kyc_history');
    }

    public function risk_score_history(){
        $this->load->view('player_management/ajax_ui_risk_score_history');
    }

    public function rgHistory(){
        $this->load->view('player_management/ajax_ui_rg_history');
    }

    public function communicationPreferenceHistory(){
        $this->load->view('player_management/ajax_ui_comm_pref_history');
    }






    //TO BE REMOVED
    public function unsettlegamesHistory() {
        $this->load->model(array('external_system', 'game_logs'));
        $data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
        $this->load->view('player_management/ajax_ui_unsettlegame', $data);
    }
}

///END OF FILE
