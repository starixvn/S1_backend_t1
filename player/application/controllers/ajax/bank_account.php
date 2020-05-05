<?php
require_once 'AjaxBaseController.php';

/**
 * Provides bank account ajax function
 *
 * @property Banktype $banktype
 * @property Playerbankdetails $playerbankdetails
 * @property CI_Form_validation $form_validation
 */
class Bank_account extends AjaxBaseController {
	public function __construct(){
		parent::__construct();

		$this->load->model(['banktype', 'playerbankdetails', 'player_model', 'financial_account_setting']);
		$this->load->library(['form_validation', 'player_functions']);
	}

    public function _remap($method){
        global $CI, $URI;
        if(!$this->load->get_var('isLogged')){
            return $this->returnJsonResult(array('status' => 'failed', 'msg' => lang('Not Login')));
        }

        $method = strtolower($this->input->server('REQUEST_METHOD')) . ucfirst($method);
        if(!method_exists($CI, $method)){
            return show_404();
        }

        $this->preloadBankData();

		return call_user_func_array(array(&$CI, $method), array_slice($URI->rsegments, 2));
    }

    protected function preloadBankData(){
        $player                 = $this->load->get_var('player');
        $bankTypeList           = $this->banktype->getBankTypes();
        $withdrawalBankTypeList = NULL;
        $depositBankTypeList    = NULL;

        foreach($bankTypeList as $bankType){
            if($bankType->payment_type_flag == Financial_account_setting::PAYMENT_TYPE_FLAG_API){
                continue;
            }

            if($bankType->enabled_withdrawal){
                $withdrawalBankTypeList[] = $bankType;
            }

            if($bankType->enabled_deposit){
                $depositBankTypeList[] = $bankType;
            }
        }

        $this->load->vars('bankTypeList', $bankTypeList);
        $this->load->vars('withdrawalBankTypeList', $withdrawalBankTypeList);
        $this->load->vars('depositBankTypeList', $depositBankTypeList);
        $this->load->vars('realname', Player::getPlayerFullName($player['firstName'], $player['lastName'], $player['language']));
    }

    public function validate_bank_account_number($bank_account_number, $params){
        list($dwBank, $bank_type_id) = explode(',', $params);
        $playerId = $this->load->get_var('playerId');
        $bank_detail_id = $this->input->post('input-bank-detail-id');

        return $this->playerbankdetails->validate_bank_account_number($playerId, $bank_account_number, $dwBank, $bank_detail_id, $bank_type_id);
    }

	/**
     * Checks fields modified on player bank info
     *
     * @param array $origbank
     * @param array $data
     * @return boolean
     */
	protected function checkBankChanges($origbank, $data) {
		$array = null;

        if(isset($data['bankTypeId'])){
            $array .= $origbank['bankTypeId'] != $data['bankTypeId'] ? lang('player.ui35') . ', ' : '';
        }

        if(isset($data['bankAccountNumber'])){
            $array .= $origbank['bankAccountNumber'] != $data['bankAccountNumber'] ? lang('cashier.69') . ', ' : '';
        }

        if(isset($data['bankAccountFullName'])){
            $array .= $origbank['bankAccountFullName'] != $data['bankAccountFullName'] ? lang('cashier.68') . ', ' : '';
        }

        if(isset($data['province'])){
            $array .= $origbank['province'] != $data['province'] ? lang('cashier.70') . ', ' : '';
        }

        if(isset($data['city'])){
            $array .= $origbank['city'] != $data['city'] ? lang('cashier.71') . ', ' : '';
        }

        if(isset($data['branch'])){
            $array .= $origbank['branch'] != $data['branch'] ? lang('cashier.72') . ', ' : '';
        }

		return $modifiedField = empty($array) ? '' : substr($array, 0, -2);
	}

    protected function setupValidationRules($dwbank, $bank_type, $field_required, $is_new = TRUE){
        $this->form_validation->set_rules('input-bank-type-id', lang('cashier.81'), 'trim|required|xss_clean');
        if($is_new){
            $this->form_validation->set_rules('input-acct-num', lang('financial_account.bankaccount'), 'trim|required|xss_clean|callback_validate_bank_account_number[' . $dwbank . ',' . $bank_type . ']');
            $this->form_validation->set_message('validate_bank_account_number', lang('account_number_can_not_be_duplicate'));
        }

        if(in_array(Financial_account_setting::FIELD_NAME, $field_required)){
            $this->form_validation->set_rules('input-acct-name', lang('financial_account.name'), 'trim|xss_clean|required');
        }
        else{
            $this->form_validation->set_rules('input-acct-name', lang('financial_account.name'), 'trim|xss_clean');
        }

        if(in_array(Financial_account_setting::FIELD_BANK_AREA, $field_required)){
            $this->form_validation->set_rules('input-province', lang('financial_account.province'), 'trim|xss_clean|required');
            $this->form_validation->set_rules('input-city', lang('financial_account.city'), 'trim|xss_clean|required');
        }
        else{
            $this->form_validation->set_rules('input-province', lang('financial_account.province'), 'trim|xss_clean');
            $this->form_validation->set_rules('input-city', lang('financial_account.city'), 'trim|xss_clean');
        }

        if(in_array(Financial_account_setting::FIELD_BANK_BRANCH, $field_required)){
            $this->form_validation->set_rules('input-branch', lang('financial_account.branch'), 'trim|xss_clean|required');
        }
        else{
            $this->form_validation->set_rules('input-branch', lang('financial_account.branch'), 'trim|xss_clean');
        }

        if(in_array(Financial_account_setting::FIELD_PHONE, $field_required)){
            $this->form_validation->set_rules('input-mobile-num', lang('financial_account.phone'), 'trim|xss_clean|required');
        }
        else{
            $this->form_validation->set_rules('input-mobile-num', lang('financial_account.phone'), 'trim|xss_clean');
        }

        if(in_array(Financial_account_setting::FIELD_BANK_ADDRESS, $field_required)){
            $this->form_validation->set_rules('input-address', lang('financial_account.address'), 'trim|xss_clean|required');
        }
        else{
            $this->form_validation->set_rules('input-address', lang('financial_account.address'), 'trim|xss_clean');
        }
    }

    public function getEditDeposit($bank_detail_id){
        return $this->processGetEdit($bank_detail_id);
    }

    public function getEditWithdrawal($bank_detail_id){
        return $this->processGetEdit($bank_detail_id);
    }

    protected function processGetEdit($bank_detail_id){
        $playerId = $this->load->get_var('playerId');

        $playerBankDetail = $this->playerbankdetails->getPlayerBankDetailById($playerId, $bank_detail_id);
        if (empty($playerBankDetail)) {
            $message = sprintf(lang('gen.error.not_exist'), lang('pay.bankinfo'));
            $this->returnJsonResult(array('status' => 'success', 'msg' => $message));
            return;
        }

        $data = [];
        if($playerBankDetail->dwBank == Playerbankdetails::DEPOSIT_BANK){
            $data['preferredBankTypeList'] = $this->load->get_var('depositBankTypeList');
        }else{
            $data['preferredBankTypeList'] = $this->load->get_var('withdrawalBankTypeList');
        }

        $banktype = $this->banktype->getBankTypeById($playerBankDetail->bankTypeId);
        $payment_type_flag = $banktype->payment_type_flag;

        $data['account_validator'] = $this->financialAccountValidatorBuilder($payment_type_flag);
        $data['playerBankDetail'] = $playerBankDetail;

        return $this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/bank_account/ajax/modal_edit_bank', $data);
    }

    /**
    * @deprecated No longer used because deactive operator_settings [financial_account_allow_edit]
    */
    public function postEditDeposit(){
        $playerId = $this->load->get_var('playerId');
        $bank_detail_id = $this->input->post('input-bank-detail-id');

        #Always true because deactive operator_settings [financial_account_allow_edit]
        if (true){
            $message = lang('Sorry, no permission');
            $this->returnJsonResult(array('status' => 'danger', 'msg' => $message));
            return;
        }

        $playerBankDetail = $this->playerbankdetails->getPlayerBankDetailById($playerId, $bank_detail_id, Playerbankdetails::DEPOSIT_BANK);
        if (empty($playerBankDetail)) {
            $message = sprintf(lang('gen.error.not_exist'), lang('pay.bankinfo'));
            $this->returnJsonResult(array('status' => 'success', 'msg' => $message));
            return;
        }

        $banktype = $this->banktype->getBankTypeById($playerBankDetail->bankTypeId);
        $payment_type_flag = $banktype->payment_type_flag;

        $account_validator = $this->financialAccountValidatorBuilder($payment_type_flag);
        $this->setupValidationRules(Playerbankdetails::DEPOSIT_BANK, $playerBankDetail->bankTypeId, $account_validator['field_required'], false);

        if ($this->form_validation->run() == false) {
            $validation_errors = validation_errors();
			$message = lang('notify.30');
            if(!empty($validation_errors)){
                $message .= $validation_errors;
            }
            return $this->returnJsonResult(['status' => 'error', 'msg' => $message]);
        }

        $data = [];
        $data = [
            'bankAccountFullName' => $this->input->post('input-acct-name'),
            'bankAddress' => $this->input->post('input-address'),
            'city' => $this->input->post('input-city'),
            'province' => $this->input->post('input-province'),
            'branch' => $this->input->post('input-branch'),
            'phone' => $this->input->post('input-mobile-num'),
        ];

        return $this->processPostEdit($playerId, $bank_detail_id, $data);
    }

    /**
    * @deprecated No longer used because deactive operator_settings [financial_account_allow_edit]
    */
    public function postEditWithdrawal(){
        $playerId = $this->load->get_var('playerId');
        $bank_detail_id = $this->input->post('input-bank-detail-id');

        #Always true because deactive operator_settings [financial_account_allow_edit]
        if (true){
            $message = lang('Sorry, no permission');
            $this->returnJsonResult(array('status' => 'danger', 'msg' => $message));
            return;
        }

        $playerBankDetail = $this->playerbankdetails->getPlayerBankDetailById($playerId, $bank_detail_id, Playerbankdetails::WITHDRAWAL_BANK, false);
        if (empty($playerBankDetail)) {
            $message = sprintf(lang('gen.error.not_exist'), lang('pay.bankinfo'));
            $this->returnJsonResult(array('status' => 'success', 'msg' => $message));
            return;
        }

        $banktype = $this->banktype->getBankTypeById($playerBankDetail->bankTypeId);
        $payment_type_flag = $banktype->payment_type_flag;

        $account_validator = $this->financialAccountValidatorBuilder($payment_type_flag);
        $this->setupValidationRules(Playerbankdetails::WITHDRAWAL_BANK, $playerBankDetail->bankTypeId, $account_validator['field_required']);
        if ($this->form_validation->run() == false) {
            $validation_errors = validation_errors();
			$message = lang('notify.30');
            if(!empty($validation_errors)){
                $message .= $validation_errors;
            }
            return $this->returnJsonResult(['status' => 'error', 'msg' => $message]);
        }

        $data = [];
        $data = [
            'bankAccountFullName' => $this->input->post('input-acct-name'),
            'bankAddress' => $this->input->post('input-address'),
            'city' => $this->input->post('input-city'),
            'province' => $this->input->post('input-province'),
            'branch' => $this->input->post('input-branch'),
            'phone' => $this->input->post('input-mobile-num'),
        ];

        return $this->processPostEdit($playerId, $bank_detail_id, $data);
    }

    protected function processPostEdit($playerId, $bank_detail_id, $data){
        //save bank changes
        $origbank = $this->player_functions->getBankDetailsById($bank_detail_id);
        $change = $this->checkBankChanges($origbank, $data);
        $changes = array(
            'playerBankDetailsId' => $bank_detail_id,
            'changes' => lang('lang.edit') . ' ' . lang('player.ui07') . ' (' . $change . ')',
            'createdOn' => date("Y-m-d H:i:s"),
            'operator' => $this->authentication->getUsername(),
        );
        $this->player_model->saveBankChanges($changes);

        $data['updatedOn'] = $this->utils->getNowForMysql();

        $result = $this->playerbankdetails->updatePlayerBankDetails($playerId, $bank_detail_id, $data);

        $message = ($result) ? lang('notify.32') : lang('notify.30');
        return $this->returnJsonResult(array('status' => 'success', 'msg' => $message));
    }

    public function getDepositDetail($bank_detail_id){
        return $this->processDetail($bank_detail_id);
    }

    public function getWithdrawalDetail($bank_detail_id){
        return $this->processDetail($bank_detail_id);
    }

    protected function processDetail($bank_detail_id){
        $playerId = $this->load->get_var('playerId');

        $playerBankDetail = $this->playerbankdetails->getPlayerBankDetailById($playerId, $bank_detail_id);
        if (empty($playerBankDetail)) {
            $message = sprintf(lang('gen.error.not_exist'), lang('pay.bankinfo'));
            $this->returnJsonResult(array('status' => 'success', 'msg' => $message));
            return;
        }

        $bankTypeDetail = new stdClass();
        $bankTypeDetail->bankTypeId = $playerBankDetail->bankTypeId;
        $bankTypeDetail->bankName = NULL;
        $bankTypeList = $this->load->get_var('bankTypeList');
        foreach($bankTypeList as $bankTypeData){
            if($playerBankDetail->bankTypeId == $bankTypeData->bankTypeId){
                $bankTypeDetail = $bankTypeData;
            }
        }

        $banktype = $this->banktype->getBankTypeById($playerBankDetail->bankTypeId);
        $payment_type_flag = $banktype->payment_type_flag;

        $data = [];
        $data['account_validator'] = $this->financialAccountValidatorBuilder($payment_type_flag);
        $data['playerBankDetail'] = $playerBankDetail;
        $data['bankTypeDetail'] = $bankTypeDetail;
        $data['double_submit_hidden_field'] = $this->initDoubleSubmitAndReturnHiddenFieldForAdmin($playerId);

        switch ($payment_type_flag) {
            case Financial_account_setting::PAYMENT_TYPE_FLAG_BANK:
                return $this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/bank_account/ajax/modal_view_bank', $data);
            case Financial_account_setting::PAYMENT_TYPE_FLAG_EWALLET:
                return $this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/bank_account/ajax/modal_view_ewallet', $data);
            case Financial_account_setting::PAYMENT_TYPE_FLAG_CRYPTO:
                return $this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/bank_account/ajax/modal_view_crypto', $data);
        }
    }

    public function getAddDeposit($data = NULL){
        return $this->getAdd(Playerbankdetails::DEPOSIT_BANK, $data);
    }

    public function getAddWithdrawal($data){
        return $this->getAdd(Playerbankdetails::WITHDRAWAL_BANK, $data);
    }

    protected function getAdd($dwBank, $payment_type_flag){
        $playerId = $this->load->get_var('playerId');

        $playerBankDetail = new stdClass();
        $playerBankDetail->playerBankDetailsId = NULL;
        $playerBankDetail->bankTypeId          = NULL;
        $playerBankDetail->bankAccountFullName = $this->load->get_var('realname');
        $playerBankDetail->bankAccountNumber   = NULL;
        $playerBankDetail->city                = NULL;
        $playerBankDetail->province            = NULL;
        $playerBankDetail->branch              = NULL;
        $playerBankDetail->phone               = NULL;
        $playerBankDetail->bankAddress         = NULL;

        $data = [];
        if($dwBank == Playerbankdetails::DEPOSIT_BANK){
            $bankTypeList = $this->load->get_var('depositBankTypeList');
        }else{
            $bankTypeList = $this->load->get_var('withdrawalBankTypeList');
        }
        $data['preferredBankTypeList'] = $bankTypeList;

        if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_one_account_per_institution')){
            $player_banktypes = $this->playerbankdetails->getBankTypesByPlayerId($playerId, $dwBank);
            if(!is_null($player_banktypes)){
                $available = array();
                foreach ($bankTypeList as $key => $value) {
                    if(!in_array($value->bankTypeId, $player_banktypes)){
                        $available[] = $value;
                    }
                }
                $data['preferredBankTypeList'] = $available;
            }
        }

        $data['account_validator'] = $this->financialAccountValidatorBuilder($payment_type_flag);
        $data['playerBankDetail']  = $playerBankDetail;
        switch ($payment_type_flag) {
            case Financial_account_setting::PAYMENT_TYPE_FLAG_BANK:
                return $this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/bank_account/ajax/modal_edit_bank', $data);
            case Financial_account_setting::PAYMENT_TYPE_FLAG_EWALLET:
                return $this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/bank_account/ajax/modal_edit_ewallet', $data);
            case Financial_account_setting::PAYMENT_TYPE_FLAG_CRYPTO:
                return $this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/bank_account/ajax/modal_edit_crypto', $data);
        }
    }

    protected function postAddDeposit(){
        $playerId = $this->load->get_var('playerId');

        $all_banks = $this->playerbankdetails->getDepositBankDetail($playerId);
        $input_banktype_id = $this->input->post('input-bank-type-id');

        $message = '';
        if (!Playerbankdetails::AllowAddBankDetail(Playerbankdetails::DEPOSIT_BANK, $all_banks, $message, $input_banktype_id)){
            $this->returnJsonResult(array('status' => 'failed', 'msg' => $message));
            return;
        }

        $banktype = $this->banktype->getBankTypeById($input_banktype_id);
        $payment_type_flag = $banktype->payment_type_flag;

        $account_validator = $this->financialAccountValidatorBuilder($payment_type_flag);
        $this->setupValidationRules(Playerbankdetails::DEPOSIT_BANK, $input_banktype_id, $account_validator['field_required']);
        if ($this->form_validation->run() == false) {
            $validation_errors = validation_errors();
			$message = lang('notify.30');
            if(!empty($validation_errors)){
                $message .= $validation_errors;
            }
            return $this->returnJsonResult(['status' => 'error', 'msg' => $message]);
        }

        $default_bank = array_filter((empty($all_banks)) ? [] : $all_banks, function($bank){
            return ($bank['isDefault']) ? TRUE : FALSE;
        });

        $data = [
            'playerId' => $playerId,
            'bankTypeId' => $input_banktype_id,
            'bankAccountFullName' => !($this->input->post('input-acct-name')) ? '' : $this->input->post('input-acct-name'),
            'bankAccountNumber' => !($this->input->post('input-acct-num')) ? '' : $this->input->post('input-acct-num'),
            'bankAddress' => !($this->input->post('input-address')) ? '' : $this->input->post('input-address'),
            'city' => !($this->input->post('input-city')) ? '' : $this->input->post('input-city'),
            'province' => !($this->input->post('input-province')) ? '' : $this->input->post('input-province'),
            'branch' => !($this->input->post('input-branch')) ? '' : $this->input->post('input-branch'),
            'isDefault' => (empty($default_bank)) ? '1' : '0',
            'isRemember' => '1',
            'dwBank' => Playerbankdetails::DEPOSIT_BANK,
            'verified' => '1',
            'status' => '0',
            'phone' => !($this->input->post('input-mobile-num')) ? '' : $this->input->post('input-mobile-num'),
        ];

        if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_deposit_account_default_unverified')){
            $data['verified'] = '0';
        }

        return $this->processPostAdd($playerId, $data);
    }

    protected function postAddWithdrawal(){
        $playerId = $this->load->get_var('playerId');

        $all_banks = $this->playerbankdetails->getWithdrawBankDetail($playerId);
        $input_banktype_id = $this->input->post('input-bank-type-id');

        $message = '';
        if (!Playerbankdetails::AllowAddBankDetail(Playerbankdetails::WITHDRAWAL_BANK, $all_banks, $message, $input_banktype_id)){
            $this->returnJsonResult(array('status' => 'failed', 'msg' => $message));
            return;
        }

        $banktype = $this->banktype->getBankTypeById($input_banktype_id);
        $payment_type_flag = $banktype->payment_type_flag;

        $account_validator = $this->financialAccountValidatorBuilder($payment_type_flag);
        $this->setupValidationRules(Playerbankdetails::WITHDRAWAL_BANK, $input_banktype_id, $account_validator['field_required']);
        if ($this->form_validation->run() == false) {
            $validation_errors = validation_errors();
			$message = lang('notify.30');
			if(!empty($validation_errors)){
                $message .= $validation_errors;
            }
            return $this->returnJsonResult(['status' => 'error', 'msg' => $message]);
        }

        $default_bank = array_filter((empty($all_banks)) ? [] : $all_banks, function($bank){
            return ($bank['isDefault']) ? TRUE : FALSE;
        });

        $data = [
            'playerId' => $playerId,
            'bankTypeId' => $input_banktype_id,
            'bankAccountFullName' => !($this->input->post('input-acct-name')) ? '' : $this->input->post('input-acct-name'),
            'bankAccountNumber' => !($this->input->post('input-acct-num')) ? '' : $this->input->post('input-acct-num'),
            'bankAddress' => !($this->input->post('input-address')) ? '' : $this->input->post('input-address'),
            'city' => !($this->input->post('input-city')) ? '' : $this->input->post('input-city'),
            'province' => !($this->input->post('input-province')) ? '' : $this->input->post('input-province'),
            'branch' => !($this->input->post('input-branch')) ? '' : $this->input->post('input-branch'),
            'isDefault' => (empty($default_bank)) ? '1' : '0',
            'isRemember' => '1',
            'dwBank' => Playerbankdetails::WITHDRAWAL_BANK,
            'verified' => '1',
            'status' => '0',
            'phone' => !($this->input->post('input-mobile-num')) ? '' : $this->input->post('input-mobile-num'),
        ];

        if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_withdraw_account_default_unverified')){
            $data['verified'] = '0';
        }

        return $this->processPostAdd($playerId, $data);
    }

    protected function processPostAdd($playerId, $data){
        $bank_details_id=null;

        if(!$this->verifyAndResetDoubleSubmitForAdmin($playerId)){
            $message = lang('Please refresh and try, and donot allow double submit');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            $this->returnJsonResult(array('status' => 'false', 'msg' => '', 'bank_detail' => ''));
            return;
        }

        $this->playerbankdetails->dbtransOnly(function()
            use($playerId, $data, &$bank_details_id){

            $bank_details_id = $this->playerbankdetails->addBankDetailsByDeposit($data);
            if($data['isDefault'] == '1') {
                $this->playerbankdetails->setPlayerDefaultBank($playerId, $data['dwBank'], $bank_details_id);
            }

            //save bank history
            $changes = array(
                'playerBankDetailsId' => $bank_details_id,
                'changes' => lang('Add') . ' ' . lang('lang.bank'),
                'createdOn' => date("Y-m-d H:i:s"),
                'operator' => $this->authentication->getUsername(),
            );
            $this->player_model->saveBankChanges($changes);

            return !empty($bank_details_id);
        });
        $message = ($bank_details_id) ? lang('notify.31') : lang('notify.30');
        return $this->returnJsonResult(array('status' => 'success', 'msg' => $message, 'bank_detail' => $this->_displayBankDetail($this->playerbankdetails->getBankDetailInfo($bank_details_id))));
    }

    public function getDeleteDeposit($bank_detail_id){
        return $this->processDelete($bank_detail_id);
    }

    public function getDeleteWithdrawal($bank_detail_id){
        return $this->processDelete($bank_detail_id);
    }

    protected function processDelete($bank_detail_id){
        $playerId = $this->load->get_var('playerId');

        if (!$this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_allow_delete')){
            $message = lang('Sorry, no permission');
            $this->returnJsonResult(array('status' => 'danger', 'msg' => $message));
            return;
        }
        //save bank history
        $changes = array(
            'playerBankDetailsId' => $bank_detail_id,
            'changes' => lang('Delete') . ' ' . lang('lang.bank'),
            'createdOn' => date("Y-m-d H:i:s"),
            'operator' => $this->authentication->getUsername(),
        );
        $this->player_model->saveBankChanges($changes);

        $playerBankDetail = $this->playerbankdetails->getPlayerBankDetailById($playerId, $bank_detail_id);
        if (empty($playerBankDetail)) {
            $message = sprintf(lang('gen.error.not_exist'), lang('pay.bankinfo'));
            $this->returnJsonResult(array('status' => 'failed', 'msg' => $message));
            return;
        }

        $response = [];
        if($this->playerbankdetails->deletePlayerBankInfo($bank_detail_id)){
            $response = ['status' => 'success', 'msg' => lang('sys.gd29')];
        }else{
            $response = ['status' => 'failed', 'msg' => lang('sys.gd26')];
        }

        $this->returnJsonResult($response);
        return;
    }

    protected function _displayBankDetail($source_bank_detail){
        $bank_detail = [
            'bankDetailsId' => $source_bank_detail['playerBankDetailsId'],
            'bankAccountFullName' => $source_bank_detail['bankAccountFullName'],
            'bankAccountNumber' => Playerbankdetails::getDisplayAccNum($source_bank_detail['bankAccountNumber']),
            'bankName' => lang($source_bank_detail['bankName']),
            'bankAddress' => $source_bank_detail['bankAddress'],
            'bankTypeId' => $source_bank_detail['bankTypeId'],
            'branch' => $source_bank_detail['branch'],
            'city' => $source_bank_detail['city'],
            'province' => $source_bank_detail['province'],
            'phone' => $source_bank_detail['phone'],
        ];
        return $bank_detail;
    }

    protected function getSetDefault($bank_details_id) {
        $playerId = $this->load->get_var('playerId');

        $playerBankDetail = $this->playerbankdetails->getPlayerBankDetailById($playerId, $bank_details_id);
        if (empty($playerBankDetail)) {
            $this->returnJsonResult(['status' => 'failed', 'msg' => sprintf(lang('gen.error.not_exist'), lang('pay.bankinfo'))]);
        }

        $this->playerbankdetails->setPlayerDefaultBank($playerId, $playerBankDetail->dwBank, $bank_details_id);

        $message = lang('notify.28');

        $this->returnJsonResult(array('status' => 'success', 'msg' => $message));
    }

    public function financialAccountValidatorBuilder($payment_type_flag){
        $financial_account_rule = $this->financial_account_setting->getPlayerFinancialAccountRulesByPaymentAccountFlag($payment_type_flag);

        $bank_card_validator = array();
        $bank_card_validator['only_allow_numeric'] = $financial_account_rule['account_number_only_allow_numeric'];
        $bank_card_validator['allow_modify_name']  = $financial_account_rule['account_name_allow_modify_by_players'];
        $bank_card_validator['field_required']     = explode(',', $financial_account_rule['field_required']);
        $bank_card_validator['field_show']         = explode(',', $financial_account_rule['field_show']);

        $account_min = $financial_account_rule['account_number_min_length'];
        $account_max = $financial_account_rule['account_number_max_length'];
        $bank_card_validator['bankAccountNumber'] = [
            'required'       => TRUE,
            'min_max_length' => [$account_min, $account_max],
            'remote'         => '/api/bankAccountNumber',
            'error_remote'   => [
                'invalid' => 'account_number_can_not_be_duplicate',
                'valid'   => 'account_number_allow_used'
            ]
        ];
        $bank_card_validator['bankAccountFullName'] = [
            'min_max_length' => [1, 200]
        ];
        $bank_card_validator['bankAccountFullName'] = (in_array(Financial_account_setting::FIELD_NAME, $bank_card_validator['field_required'])) ?'': '';
        $bank_card_validator['phone']               = (in_array(Financial_account_setting::FIELD_PHONE, $bank_card_validator['field_required'])) ? ['required' => true] : '';
        $bank_card_validator['branch']              = (in_array(Financial_account_setting::FIELD_BANK_BRANCH, $bank_card_validator['field_required'])) ? ['required' => true] : '';
        $bank_card_validator['area']                = (in_array(Financial_account_setting::FIELD_BANK_AREA, $bank_card_validator['field_required'])) ? ['required' => true] : '';
        $bank_card_validator['bankAddress']         = (in_array(Financial_account_setting::FIELD_BANK_ADDRESS, $bank_card_validator['field_required'])) ? ['required' => true] : '';

        return $bank_card_validator;
    }
}
