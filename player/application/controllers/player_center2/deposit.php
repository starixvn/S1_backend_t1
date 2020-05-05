<?php
require_once 'PlayerCenterBaseController.php';

/**
 * Provides deposit function
 *
 * @property Playerbankdetails $playerbankdetails
 * @property Operatorglobalsettings $operatorglobalsettings
 */
class Deposit extends PlayerCenterBaseController {
	public function __construct(){
		parent::__construct();

        $this->preloadCashierVars();
        $this->preloadDepositVars();
	}

    protected function preloadDepositVars(){
		$this->load->model(['banktype', 'payment_account', 'playerbankdetails', 'sale_order', 'promorules', 'financial_account_setting']);

		$playerId = $this->load->get_var('playerId');

        $payment_all_accounts = $this->payment_account->getAvailableDefaultCollectionAccount($this->load->get_var('playerId'));

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


		$this->load->vars('payment_accounts', $payment_accounts);
		$this->load->vars('payment_manual_accounts', $payment_manual_accounts);
		$this->load->vars('payment_auto_accounts', $payment_auto_accounts);

        $this->load->vars('deposit_process_mode', $this->operatorglobalsettings->getSettingIntValue('deposit_process', DEPOSIT_PROCESS_MODE1));
		$this->load->vars('enabled_withdrawal_password', $this->utils->isEnabledFeature('enabled_withdrawal_password'));

        $player_bank_accounts = (array)$this->playerbankdetails->getAvailableDepositBankDetail($playerId);

		$this->load->vars('player_bank_accounts', $player_bank_accounts);
        $this->load->vars('sub_nav_active', 'deposit');
    }

	public function index(){
        $payment_accounts = $this->load->get_var('payment_accounts');

        if(empty($payment_accounts)){
            redirect('/player_center2/deposit/empty_payment_account');
            return;
        }

        if(empty($this->load->get_var('payment_manual_accounts'))){
            if($this->utils->isEnabledFeature('enable_deposit_category_view')) {
                redirect('/player_center2/deposit/deposit_category');
            }
            else if($this->utils->is_mobile() && !$this->config->item('disable_old_mobile_deposit_category_page') ) {
                $this->loadOldMobileCategoryView();
            }
            else {
                $payment_auto_accounts = $this->load->get_var('payment_auto_accounts');
                $payment_account = (empty($payment_auto_accounts)) ? FALSE : array_shift($payment_auto_accounts);
                redirect('/player_center2/deposit/auto_payment/' . $payment_account->payment_account_id);
            }
        }else{
            if($this->utils->isEnabledFeature('enable_deposit_category_view')) {
                redirect('/player_center2/deposit/deposit_category');
            }
            else {
                if($this->utils->is_mobile() && !$this->config->item('disable_old_mobile_deposit_category_page') ) {
                    $this->loadOldMobileCategoryView();
                }
                else {
                    redirect('/player_center2/deposit/manual_payment');
                }
            }
        }
	}

    public function loadOldMobileCategoryView() {
        $this->loadTemplate();
        $this->template->append_function_title(lang('Deposit'));
        $this->template->add_js('/common/js/player_center/player-cashier.js');
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/deposit_form');
        $this->template->render();
    }

	public function empty_payment_account(){
        $playerId = $this->load->get_var('playerId');
        $data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenField($playerId);

        $this->loadTemplate();
        $this->template->add_js('/common/js/player_center/player-cashier.js');
        $this->template->add_js('/common/js/player_center/deposit.js');
        $this->template->add_js('/common/js/player_center/player-bank-account.js');
        $this->template->add_js('/common/js/plugins/province_city_select.js');
        $this->template->add_js('/resources/js/validator.js');
        $this->template->add_js('/resources/third_party/clipboard/clipboard.min.js');
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/empty_payment_account', []);
        $this->template->render();
    }

    public function deposit_category() {
        $this->load->model('payment_account');
        $player_id = $this->authentication->getPlayerId();
        if($this->utils->getPlayerStatus($player_id)==5){
            redirect('/player_center/menu');
        }

        $payment_all_accounts = $this->payment_account->getAvailableDefaultCollectionAccount($player_id);
        $payment_manual_accounts = ($payment_all_accounts[MANUAL_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[MANUAL_ONLINE_PAYMENT]['list'] : [];
        if($payment_all_accounts[LOCAL_BANK_OFFLINE]['enabled']){
            foreach($payment_all_accounts[LOCAL_BANK_OFFLINE]['list'] as $payment_account){
                $payment_manual_accounts[] = $payment_account;
            }
        }
        $payment_auto_accounts = ($payment_all_accounts[AUTO_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[AUTO_ONLINE_PAYMENT]['list'] : [];

        $data['payment_auto_accounts'] = $payment_auto_accounts;
        $data['payment_manual_accounts'] = $payment_manual_accounts;

        if( $this->utils->isEnabledFeature('enable_manual_deposit_realname') || $this->utils->isEnabledFeature('enable_manual_deposit_input_depositor_name')){
            $this->load->model(['player_model']);
            $playerDetailObject =  $this->player_model->getPlayerDetailsById($player_id);
            $firstName = $playerDetailObject->firstName;
            if (strlen($firstName)<=0){
                $data['firstNameflg']=0;
            }else{
                $data['firstNameflg']=1;
            }
            $data['firstName']=$firstName;
        }

        $this->loadTemplate();
        $this->template->append_function_title(lang('Deposit'));
        $this->template->add_js('/common/js/player_center/player-cashier.js');
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate(false) . '/cashier/deposit/manual/deposit_category', $data);
        $this->template->render();
    }

    public function manual_payment($payment_account_id = NULL){
        if(empty($this->load->get_var('payment_manual_accounts'))){
            redirect('/player_center2/deposit/');
        }

        $data['deposit_method'] = 'manual';
        $data['payment_account_id'] = $payment_account_id;

        $playerId = $this->load->get_var('playerId');

        $secure_id = FALSE;
        if($this->load->get_var('deposit_process_mode') === DEPOSIT_PROCESS_MODE2){
            $secure_id = $this->sale_order->generateSecureId();
        }
        $data['secure_id'] = $secure_id;

        $default_bank_details = $this->playerbankdetails->getDefaultBankDetail($playerId);
        if( count($default_bank_details['deposit']) > 0) {
            $data['default_bank_details'] = $default_bank_details['deposit'][0];
            unset($data['default_bank_details']['bankName']);
        }
        else {
            $data['default_bank_details'] = [];
        }
        $data['bank_details'] = $this->playerbankdetails->getBankDetails($playerId);


        $data['bankTypeList'] = $this->banktype->getAvailableBankTypeList('deposit');
        $add_security_on_deposit_transaction = $this->utils->isEnabledFeature('add_security_on_deposit_transaction');
        $data['system_feature_add_security_on_deposit_transaction'] = $add_security_on_deposit_transaction;

        $enabled_withdrawal_password = $this->utils->isEnabledFeature('enabled_withdrawal_password');
        $data['system_feature_enabled_withdrawal_password'] = $enabled_withdrawal_password;

        if($add_security_on_deposit_transaction && $enabled_withdrawal_password){
            $data['payment_manual_accounts'] = [];
        }

        $data['force_setup_player_deposit_bank_if_empty'] = $this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_require_deposit_bank_account');


        $use_self_pick_promotion = $this->utils->isEnabledFeature('use_self_pick_promotion');
        $data['system_feature_use_self_pick_promotion'] = $use_self_pick_promotion;

        if( $this->utils->isEnabledFeature('enable_manual_deposit_realname') || $this->utils->isEnabledFeature('enable_manual_deposit_input_depositor_name')){
            $this->load->model(['player_model']);
            $playerDetailObject =  $this->player_model->getPlayerDetailsById($playerId);
            $firstName = $playerDetailObject->firstName;
            if (strlen($firstName)<=0){
                $data['firstNameflg']=0;
            }else{
                $data['firstNameflg']=1;
            }
            $data['firstName']=$firstName;
        }


        $avail_promocms_list = $this->promorules->getAvailPromoOnDeposit($playerId);
        if(empty($avail_promocms_list)){
            $avail_promocms_list = [];
        }
        $promoCategoryList = $this->utils->getAllPromoType();
        $apl = [];
        foreach ($avail_promocms_list as $promo_cms_id => $promo_name) {
            $promo_rule_id = $this->promorules->getPromorulesIdByPromoCmsId($promo_cms_id);
            $promorule = $this->promorules->getPromoruleById($promo_rule_id);
            foreach ($promoCategoryList as $row) {
                if ($promorule['promoCategory'] == $row['id']) {
                    $apl[$promo_cms_id] = $promo_name;
                }
            }
        }
        if($use_self_pick_promotion){
            $data['avail_promocms_list'] = (empty($apl)) ? [] : $apl;
        }else{
            $data['avail_promocms_list'] = [];
        }

        $last_manually_sale_order = $this->sale_order->getLastManuallyDeposit($playerId);
        $data['last_manually_sale_order'] = $last_manually_sale_order;

        if($this->utils->isEnabledFeature('enable_manually_deposit_cool_down_time')){
            $last_manually_unfinished_sale_order = $this->sale_order->getLastUnfinishedManuallyDeposit($playerId);
            $data['last_manually_unfinished_sale_order'] = $last_manually_unfinished_sale_order;

            $manually_deposit_cool_down_minutes=$this->utils->getConfig('manually_deposit_cool_down_minutes');
            $data['manually_deposit_cool_down_minutes']=$manually_deposit_cool_down_minutes;
            //check cold down time
            $this->utils->debug_log('lastOrder', $last_manually_unfinished_sale_order, 'manually_deposit_cool_down_minutes', $manually_deposit_cool_down_minutes);
            if($last_manually_unfinished_sale_order && !$this->utils->isTimeoutNow($last_manually_unfinished_sale_order['created_at'], $manually_deposit_cool_down_minutes) ){
                //not reach cool down time
                $data['in_cool_down_time'] = true;
            }else{
                $data['in_cool_down_time'] = false;
            }
        }

        $use_self_pick_subwallets = $this->utils->isEnabledFeature('use_self_pick_subwallets');
        $data['system_feature_use_self_pick_subwallets'] = $use_self_pick_subwallets;
        if($use_self_pick_subwallets){
            $data['pick_subwallets'] = $this->wallet_model->getSubwalletMap();
        }else{
            $data['pick_subwallets'] = [];
        }

        $data['show_tag_for_unavailable_deposit_accounts'] = (int) $this->system_feature->isEnabledFeature('show_tag_for_unavailable_deposit_accounts');
        $data['disable_account_transfer_when_balance_check_fails'] = (int) $this->system_feature->isEnabledFeature('disable_account_transfer_when_balance_check_fails');
        $data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenField($playerId);

        $currentPaymentAccount=null;
        $data['payment_type_flag'] = Financial_account_setting::PAYMENT_TYPE_FLAG_BANK;
        if(!empty($payment_account_id)){
            $currentPaymentAccount=$this->payment_account->getAvailableAccount($playerId, NULL, NULL, FALSE, $payment_account_id);

            if(empty($currentPaymentAccount)) {
                $this->utils->debug_log('==============manual_payment get empty currentPaymentAccount', $currentPaymentAccount);
                redirect('/player_center2/deposit/empty_payment_account');
                return;
            }

            $data['payment_manual_accounts'] = [$payment_account_id => $currentPaymentAccount];
            $data['second_category_flag'] = $currentPaymentAccount->second_category_flag;

            $banktype = $this->banktype->getBankTypeById($currentPaymentAccount->payment_type_id);
            $data['payment_type_flag'] = $banktype->payment_type_flag;
            if($this->utils->isCryptoCurrency($banktype)){
                $cryptocurrency = $this->utils->getCryptoCurrency($banktype);
                $defaultCurrency  = $this->utils->getCurrentCurrency()['currency_code'];

                list($crypto, $rate) = $this->utils->convertCryptoCurrency(1, $cryptocurrency, $defaultCurrency);
                $custom_deposit_rate = $this->config->item('custom_deposit_rate') ? $this->config->item('custom_deposit_rate') : 1;
                $player_rate = number_format($rate * $custom_deposit_rate, 4, '.', '');
                $data['cryptocurrency'] = $cryptocurrency;
                $data['cryptocurrency_rate'] = $player_rate;
                $data['currency_conversion_rate'] = (1/$data['cryptocurrency_rate']);
                $data['decimal_digit'] = $this->config->item('cryptocurrency_decimal_digit') ? $this->config->item('cryptocurrency_decimal_digit') : 8;
            }
            elseif($this->config->item('fix_currency_conversion_rate')){
                $data['fix_currency_conversion'] = true;
                $data['currency_conversion_rate'] = $this->config->item('fix_currency_conversion_rate');
                $data['decimal_digit'] = $this->config->item('fix_currency_decimal_digit') ? $this->config->item('fix_currency_decimal_digit') : 2;
                $data['base_currency']        = lang('currency.' . $this->config->item('fix_base_currency'));
                $data['target_currency']      = lang('currency.' . $this->config->item('fix_target_currency'));
                $data['base_currency_code']   = $this->config->item('fix_base_currency');
                $data['target_currency_code'] = $this->config->item('fix_target_currency');

                $searchArr  = ['{base_currency}', '{base_currency_code}', '{target_currency}', '{target_currency_code}', '{conversion_rate}', '{result}'];
                $replaceArr = [$data['base_currency'], $data['base_currency_code'], $data['target_currency'], $data['target_currency_code'], $data['currency_conversion_rate'], '<span class="currency_conversion_result"></span>'];

                $data['fix_rate_note'] = str_replace($searchArr, $replaceArr, lang('fix_rate_note'));
                $data['fix_rate_convert_result_note'] = str_replace($searchArr, $replaceArr, lang('convert_result_note'));
            }
            $data['isAlipay'] = $this->utils->isAlipay($banktype);
            $data['isUnionpay'] = $this->utils->isUnionpay($banktype);
            $data['isWechat'] = $this->utils->isWechat($banktype);
        }
        else {
            $data['isAlipay'] = false;
            $data['isUnionpay'] = false;
            $data['isWechat'] = false;
        }

        $data['enable_deposit_upload_documents']=$this->utils->isEnabledFeature('enable_deposit_upload_documents');
        $data['required_deposit_upload_file_1']=$this->utils->isEnabledFeature('required_deposit_upload_file_1');

        if(isset($banktype)){
            $field_show = $this->financial_account_setting->getFieldShowByPaymentAccountFlag($banktype->payment_type_flag);
        }
        else{
            $field_show = $this->financial_account_setting->getFieldShowByPaymentAccountFlag(Financial_account_setting::PAYMENT_TYPE_FLAG_BANK);
        }

        $data['hide_bank_branch_in_payment_account_detail_player_center'] = !in_array(Financial_account_setting::FIELD_BANK_BRANCH, $field_show);
        $data['hide_mobile_in_payment_account_detail_player_center'] = !in_array(Financial_account_setting::FIELD_PHONE, $field_show);
        $data['hide_deposit_selected_bank_and_text_for_ole777'] = $this->utils->isEnabledFeature('hide_deposit_selected_bank_and_text_for_ole777');

		$this->loadTemplate();
        $this->template->add_js('/common/js/player_center/player-cashier.js');
		$this->template->add_js('/common/js/player_center/deposit.js');
		$this->template->add_js('/common/js/plugins/province_city_select.js');
		$this->template->add_js('/resources/js/validator.js');
		$this->template->add_js('/resources/third_party/clipboard/clipboard.min.js');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate(FALSE) . '/cashier/deposit', $data);
		$this->template->render();
    }

    public function auto_payment($payment_account_id = NULL){
        $playerId = $this->load->get_var('playerId');

        $payment_auto_accounts = $this->load->get_var('payment_auto_accounts');
        if(empty($payment_auto_accounts)){
            redirect('/player_center2/deposit/');
        }

        $payment_account = FALSE;
        foreach($payment_auto_accounts as $payment_account_entry){
            if($payment_account_entry->payment_account_id == $payment_account_id){
                $payment_account = $payment_account_entry;
            }
        }

        $data = [];
        $minDeposit = 0;
        $maxDeposit = 0;
        if(empty($payment_account)){
            $this->returnBadRequest();
            return;
        }

        /* @var $api Abstract_payment_api */
        $api = $this->utils->loadExternalSystemLibObject($payment_account->external_system_id);
        if(empty($api)) {
            $this->returnBadRequest();
            return;
        }

        if(!$api->isAvailable()) {
            $this->returnBadRequest();
            return;
        }

        $auto_deposit_cool_down_minutes = $api->getNextOrderCooldownTime();
        if($auto_deposit_cool_down_minutes != 0){
            $data['auto_deposit_cool_down_minutes'] = $auto_deposit_cool_down_minutes;
            //check cold down time
            $lastOrder = $this->sale_order->getLastSalesOrderByPlayerId($playerId);
            $this->utils->debug_log('lastOrder', $lastOrder, 'auto_deposit_cool_down_minutes', $auto_deposit_cool_down_minutes);
            if($lastOrder && !$this->utils->isTimeoutNow($lastOrder->created_at, $auto_deposit_cool_down_minutes) ){
                //not reach cool down time
                $data['in_cool_down_time'] = true;
            }else{
                $data['in_cool_down_time'] = false;
            }
        }

        $data['exists_payment_account'] = TRUE;
        $data['external_system_api'] = $api;
        $data['extra_info'] = $api->getAllSystemInfo();

        $data['playerInputInfo'] = $api->getPlayerInputInfo();
        $data['disable_form'] = !!$api->getSystemInfo('disable_form');
        $data['disable_form_msg'] = $api->getSystemInfo('disable_form_msg');

        $data['minDeposit'] = $minDeposit = $payment_account->vip_rule_min_deposit_trans;
        $data['maxDeposit'] = $maxDeposit = $payment_account->vip_rule_max_deposit_trans;

        $api_special_limit_rule = $api->getSystemInfo('special_limit_rule');
        $custom_auto_deposit_amount_limit_rule = $this->CI->config->item('custom_auto_deposit_amount_limit_rule');
        if(!is_array($api_special_limit_rule)){
            $api_special_limit_rule = array($api_special_limit_rule);
        }
        if(!is_array($custom_auto_deposit_amount_limit_rule)){
            $custom_auto_deposit_amount_limit_rule = array($custom_auto_deposit_amount_limit_rule);
        }
        $data['special_limit_rules'] = array_merge($api_special_limit_rule, $custom_auto_deposit_amount_limit_rule);


        if ($this->utils->isEnabledFeature('use_self_pick_subwallets')) {
            $data['subwallets'] = $this->wallet_model->getSubwalletMap();
        }

        if ($this->utils->isEnabledFeature('use_self_pick_group')) {
            $vipsettings = $this->group_level->getAllCanJoinIn();
            if (!empty($vipsettings)) {
                foreach ($vipsettings as $vipsetting) {
                    if (!isset($data['vipsettings'][$vipsetting['vipSettingId']])) {
                        $data['vipsettings'][$vipsetting['vipSettingId']]['name'] = lang($vipsetting['groupName']);
                        $data['vipsettings'][$vipsetting['vipSettingId']]['description'] = $vipsetting['groupDescription'];
                    }
                    $data['vipsettings'][$vipsetting['vipSettingId']]['list'][] = $vipsetting;
                }
            }
        }

        $use_self_pick_promotion = $this->utils->isEnabledFeature('use_self_pick_promotion');
        $data['system_feature_use_self_pick_promotion'] = $use_self_pick_promotion;

        $avail_promocms_list = $this->promorules->getAvailPromoOnDeposit($playerId);
        $avail_promocms_list = (empty($avail_promocms_list)) ? [] : $avail_promocms_list;
        $promoCategoryList = $this->utils->getAllPromoType();
        $apl = [];
        foreach ($avail_promocms_list as $promo_cms_id => $promo_name) {
            $promo_rule_id = $this->promorules->getPromorulesIdByPromoCmsId($promo_cms_id);
            $promorule = $this->promorules->getPromoruleById($promo_rule_id);
            foreach ($promoCategoryList as $row) {
                if ($promorule['promoCategory'] == $row['id']) {
                    $apl[$promo_cms_id] = $promo_name;
                }
            }
        }
        if($use_self_pick_promotion){
            $data['avail_promocms_list'] = (empty($apl)) ? [] : $apl;
        }else{
            $data['avail_promocms_list'] = [];
        }

        $data['show_tag_for_unavailable_deposit_accounts'] = (int) $this->system_feature->isEnabledFeature('show_tag_for_unavailable_deposit_accounts');
        $data['disable_account_transfer_when_balance_check_fails'] = (int) $this->system_feature->isEnabledFeature('disable_account_transfer_when_balance_check_fails');

        $data['deposit_method'] = 'auto';
        $data['payment_account_id'] = $payment_account_id;
        $data['payment_account'] = $payment_account;
        $data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenField($playerId);

        $data['big_wallet'] = $this->wallet_model->getOrderBigWallet($playerId);
        $data['pendingBalance'] = (object) ['frozen' => $data['big_wallet']['main']['frozen']];
        $data['totalBalance'] = $data['big_wallet']['total'];
        $subwallets = $data['big_wallet']['sub'];
        $data['subwallets'] = $subwallets;
        if ($this->utils->isEnabledFeature('use_self_pick_subwallets')) {
            $data['pick_subwallets'] = $this->wallet_model->getSubwalletMap();
        }else{
            $data['pick_subwallets'] = [];
        }

        if ($this->utils->isEnabledFeature('use_self_pick_group')) {
            $vipsettings = $this->group_level->getAllCanJoinIn();
            if (!empty($vipsettings)) {
                foreach ($vipsettings as $vipsetting) {
                    if (!isset($data['vipsettings'][$vipsetting['vipSettingId']])) {
                        $data['vipsettings'][$vipsetting['vipSettingId']]['name'] = $vipsetting['groupName'];
                        $data['vipsettings'][$vipsetting['vipSettingId']]['description'] = $vipsetting['groupDescription'];
                    }
                    $data['vipsettings'][$vipsetting['vipSettingId']]['list'][] = $vipsetting;
                }
            }
        }
        $data['hide_deposit_selected_bank_and_text_for_ole777']=$this->utils->isEnabledFeature('hide_deposit_selected_bank_and_text_for_ole777');
        $data['payment_type_flag'] = Financial_account_setting::PAYMENT_TYPE_FLAG_API;

		$this->loadTemplate();
        $this->template->add_js('/common/js/player_center/player-cashier.js');
        $this->template->add_js('/common/js/player_center/deposit.js');
        $this->template->add_js('/resources/js/validator.js');
        $this->template->add_js('/resources/third_party/clipboard/clipboard.min.js');
		$this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/deposit', $data);
		$this->template->render();
    }
}
