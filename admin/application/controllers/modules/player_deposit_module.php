<?php
trait player_deposit_module {

    protected function checkAgencyCreditMode($playerId){
        $this->load->model(['player_model', 'wallet_model']);

        $player=$this->player_model->getPlayerById($playerId);

        if ($this->utils->isEnabledFeature('agent_player_cannot_use_deposit_withdraw') && $player && $player->credit_mode) {
            if ($this->wallet_model->isPlayerWalletAccountZero($playerId)) {
                $this->player_model->disabledCreditMode($playerId);
                // $this->player_model->updatePlayer($playerId, array('credit_mode'=> FALSE));
            } else {
                // show_error('credit mode is on, deposit is disabled');

                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('credit mode is on, deposit/withdraw is not allowed'));
                $this->goPlayerHome();
                return false;
            }
        }

        return true;
    }

    public function iframe_makeDeposit($depositType = self::MIN_ORDER, $bankTypeId = null, $paymentAccountId=null) {
        // $this->utils->openProfiler();
        $this->utils->markProfilerStart('deposit');

                // $player_id = $this->authentication->getPlayerId();
        // if (!$this->authentication->isLoggedIn()) {
        //  $this->goPlayerLogin();
        // } else {
        // set variables
        // $currentDate = date('Y-m-d');
        // $currentDateTime = date('Y-m-d H:m:s');
        /*// get total deposit should pe approved
        if (!$data['depositDailyTotal'] = $this->player_functions->getPlayerTotalDepositDaily($player_id, $currentDate)) {
        $data['depositDailyTotal'] = 0;
        }
        // get max deposit
        if (!$data['depositDailyMax'] = $this->payment_account->getAvailableAccount($player_id, null, null)) {
        $data['depositDailyMax'] = 0;
        }*/

        // get total deposit should pe approved
        // if (!$data['depositTotal'] = $this->transactions->getTotalDeposit($currentDateTime)) {
        //  $data['depositTotal'] = 0;
        // }
        // get max deposit
        // if (!$data['depositDailyMax'] = $this->transactions->getTotalDailyDeposit()) {
        //  $data['depositDailyMax'] = 0;
        // }
        $this->load->model(array('payment_account','player_model','wallet_model'));

        // Check if agency wallet has balance
        $playerId = $this->authentication->getPlayerId();
        // if ($this->player_model->getPlayerById($playerId)->credit_mode) {
        //  if ($this->wallet_model->isPlayerWalletAccountZero($playerId)) {
        //      $this->player_model->updatePlayer($playerId, array('credit_mode'=> FALSE));
        //  } else {
        //      // show_error('credit mode is on, deposit is disabled');

        //      return $this->goPlayerHome();
        //  }
        // }
        if(!$this->checkAgencyCreditMode($playerId)){
            return;
        }

        switch ($depositType) {
        case self::MIN_ORDER:
            $playerId = $this->authentication->getPlayerId();
            $paymentAccount = $this->payment_account->getAvailableAccount($playerId, null, $bankTypeId, false, $paymentAccountId);
            $flag = Payment_account::FLAG_MANUAL_ONLINE_PAYMENT;
            if (!empty($paymentAccount) && !empty($paymentAccount->flag)) {
                $flag = $paymentAccount->flag;
            }
            if ($flag == Payment_account::FLAG_AUTO_ONLINE_PAYMENT) {
                if ($this->utils->getPlayerCenterTemplate() == 'iframe') {
                    redirect('iframe_module/auto_payment/' . Payment_account::FLAG_AUTO_ONLINE_PAYMENT . '/' . $bankTypeId.'/'.$paymentAccountId);
                } else {
                    $paymentAccounts = $this->utils->getPaymentAccounts();
                    $depositMenuList = $this->utils->getDepositMenuList();
                    // redirect('player_center/auto_payment/' . Payment_account::FLAG_AUTO_ONLINE_PAYMENT . '/' . $bankTypeId);
                    foreach ($paymentAccounts as $id => $account) {
                        if(!empty($account['enabled']) && ($account['lang_key']=="pay.auto_online_payment")){
                               redirect('player_center/auto_payment/' . $id);
                        }
                    }
                    foreach ($depositMenuList as $key => $list) {
                        redirect('player_center/auto_payment/' . Payment_account::FLAG_AUTO_ONLINE_PAYMENT . '/' . $list->bankTypeId.'/'.$paymentAccountId);
                    }
                }
            } else {
                if ($this->utils->getPlayerCenterTemplate() == 'iframe') {
                    redirect('iframe_module/manual_payment/' . $flag . '/' . $bankTypeId.'/'.$paymentAccountId);
                } else {
                    redirect('player_center/manual_payment/' . $flag . '/' . $bankTypeId.'/'.$paymentAccountId);
                }

            }
            break;

        case MANUAL_ONLINE_PAYMENT:
            $paymentAccount = $this->payment_account->getAvailableAccount($playerId, null, $bankTypeId, false, $paymentAccountId);
            $flag = Payment_account::FLAG_MANUAL_ONLINE_PAYMENT;
            if (!empty($paymentAccount) && !empty($paymentAccount->flag)) {
                $flag = $paymentAccount->flag;
            }
            if ($this->utils->getPlayerCenterTemplate() == 'iframe') {
                redirect('iframe_module/manual_payment/' . $flag . '/' . $bankTypeId.'/'.$paymentAccountId);
            } else {
                redirect('player_center/manual_payment/' . $flag . '/' . $bankTypeId.'/'.$paymentAccountId);
            }

            // redirect('iframe_module/manualDeposit3rdParty');
            break;

        case AUTO_ONLINE_PAYMENT:
            $paymentAccount = $this->payment_account->getAvailableAccount($playerId, null, $bankTypeId, false, $paymentAccountId);
            $flag = Payment_account::FLAG_AUTO_ONLINE_PAYMENT;
            if (!empty($paymentAccount) && !empty($paymentAccount->flag)) {
                $flag = $paymentAccount->flag;
            }
            //create bank tree
            if ($this->utils->getPlayerCenterTemplate() == 'iframe') {
                redirect('iframe_module/auto_payment/' . $flag . '/' . $bankTypeId.'/'.$paymentAccountId);
            } else {
                redirect('player_center/auto_payment/' . $flag . '/' . $bankTypeId.'/'.$paymentAccountId);
            }

            // $this->load->model(array('banktype', 'external_system', 'bank_list', 'transactions', 'group_level'));
            // $this->load->helper(array('form'));

            // $playerId = $this->authentication->getPlayerId();
            // $depositRule = $this->group_level->getPlayerDepositRule($playerId);
            // $data['minDeposit'] = isset($depositRule[0]['minDeposit']) ? $depositRule[0]['minDeposit'] : 0;
            // $data['maxDeposit'] = isset($depositRule[0]['maxDeposit']) ? $depositRule[0]['maxDeposit'] : 0;
            // $data['currentLang'] = $this->language_function->getCurrentLanguage();
            // # PWEDENG IDERETSO
            // # FROM SESSION SET IN applyDepositPromo(Entry point ng deposit promo redirect dito)
            // // $data['depositPromoId'] = $this->session->userdata('applicationPromoId'); # WALA SA CASHCARD DEPOSIT
            // // $data['promoCmsSettingId'] = $this->session->userdata('promoCmsSettingId'); # WALA SA AUTO AT CASHCARD DEPOSIT

            // // load model for Payment_account::
            // // $this->load->model(array('payment_account'));
            // // $this->load->model(array('transactions'));

            // $defaultPaymentGateway = $this->config->item('default_3rdparty_payment');

            // //load payment gateway from bankTypeId
            // if (!empty($bankTypeId)) {
            //  //load
            //  $banktype = $this->banktype->getBankTypeById($bankTypeId);
            //  if ($banktype && $banktype->external_system_id) {
            //      $defaultPaymentGateway = $banktype->external_system_id;
            //  }
            // }
            // //check payment account , if don't found
            // $paymentAccount = $this->payment_account->getAvailableAccount($playerId, Payment_account::FLAG_AUTO_ONLINE_PAYMENT, $bankTypeId);
            // if ($paymentAccount) {
            //  $data['exists_payment_account'] = true;
            //  //change to payment account name
            //  $data['paymentGatewayName'] = lang('payment.type.' . $defaultPaymentGateway);
            // } else {
            //  $data['exists_payment_account'] = false;
            //  $data['paymentGatewayName'] = lang('payment.type.' . $defaultPaymentGateway);
            // }


            // list($bankList, $bankTree) = $this->bank_list->getBankTypeTree($defaultPaymentGateway);
            // $data['bankTree'] = $bankTree;
            // $data['bankList'] = $bankList;
            // $data['defaultPaymentGateway'] = $defaultPaymentGateway;

            // $this->loadTemplate(lang('header.deposit') . ' - ' . $data['paymentGatewayName']);
            // $this->template->write_view('main_content', 'iframe/cashier/auto_3rdparty_deposit', $data);

            // $this->template->add_js('resources/js/player/cashier.js');
            // $this->template->render();
            break;

        case LOCAL_BANK_OFFLINE:
            if ($this->utils->getPlayerCenterTemplate() == 'iframe') {
                redirect('iframe_module/manual_payment/' . Payment_account::FLAG_MANUAL_LOCAL_BANK . '/' . $bankTypeId.'/'.$paymentAccountId);
            } else {
                redirect('player_center/manual_payment/' . Payment_account::FLAG_MANUAL_LOCAL_BANK . '/' . $bankTypeId.'/'.$paymentAccountId);
            }

            //get playeravailable bank account from bank account manager list according to its level
            // $data['bankAccountDetails'] = $this->player_functions->getPlayerAvailableBankAccountDeposit($playerId);
            // //get bank details
            // $data['playerBankDetails'] = $this->player_functions->getDepositBankDetails($playerId);
            // //get all bank types
            // $data['banks'] = $this->player_functions->getAllBankType();
            // $this->loadTemplate(lang('cashier.46'));
            // $this->template->write_view('main_content', 'iframe/cashier/localbank_deposit', $data);
            break;

        // case self::CARD:
        //  $this->loadTemplate(lang('cashier.47'));
        //  $this->template->write_view('main_content', 'iframe/cashier/cashcard_deposit', $data);
        //  break;

        // case self::CARD:
        //  $this->loadTemplate(lang('cashier.47'));
        //  $this->template->write_view('main_content', 'iframe/cashier/cashcard_deposit', $data);
        //  break;

        // case 'localBankAndATM':
        //     $this->deposit_local_bank();
        //     break;

        default:
            $this->utils->show_message('danger', null, lang('No Deposit account Available'), '/');
        }
        // }

        $this->utils->markProfilerEnd('deposit');
    }

    public function auto_payment($flag, $bankTypeId = null, $paymentAccountId=null) {
        // if(!$this->utils->is_mobile()){
        //     redirect('/player_center2/deposit/auto_payment/' . $flag);
        // }

        //create bank tree
        $this->load->model(array('payment_account', 'banktype', 'external_system', 'bank_list',
            'transactions', 'group_level', 'promorules', 'vipsetting', 'wallet_model', 'player_model','sale_order'));
        $this->load->helper(array('form'));

        $playerId = $this->authentication->getPlayerId();
        // if ($this->player_model->getPlayerById($playerId)->credit_mode) {
        //  if ($this->wallet_model->isPlayerWalletAccountZero($playerId)) {
        //      $this->player_model->updatePlayer($playerId, array('credit_mode'=> FALSE));
        //  } else show_error('credit mode is on, deposit is disabled');
        // }
        if(!$this->checkAgencyCreditMode($playerId)){
            //TODO show permission error
            $this->returnBadRequest();
            return;
        }

        $depositRule = $this->group_level->getPlayerDepositRule($playerId);

        if(empty($bankTypeId)) {
            # No payment account specified, we filter available paymentAccounts against Default Collection Account setting
            $availablePaymentAccounts = $this->payment_account->getAvailableAccount($playerId, Payment_account::FLAG_AUTO_ONLINE_PAYMENT, null, true, $paymentAccountId);
            $defaultCollectionAccounts = $this->utils->getDepositMenuList();
            $defaultCollectionAccountIds = array();
            foreach($defaultCollectionAccounts as $aCollectionAccount) {
                $defaultCollectionAccountIds[] = $aCollectionAccount->bankTypeId;
            }

            foreach($availablePaymentAccounts as $aPaymentAccount) {
                if(in_array($aPaymentAccount->payment_type_id, $defaultCollectionAccountIds)) {
                    $payment_account = $aPaymentAccount;
                    break;
                }
            }
        }

        # Cannot find an available payment account in Default Collection Account setting, use the first one that's available
        if (!isset($payment_account)) {
            $payment_account = $this->payment_account->getAvailableAccount($playerId, Payment_account::FLAG_AUTO_ONLINE_PAYMENT, $bankTypeId, false, $paymentAccountId);
        }

        $data['flag'] = $flag;
        $data['currentLang'] = $this->language_function->getCurrentLanguage();
        $data['payment_account'] = $payment_account;
        # PWEDENG IDERETSO
        # FROM SESSION SET IN applyDepositPromo(Entry point ng deposit promo redirect dito)
        // $data['depositPromoId'] = $this->session->userdata('applicationPromoId'); # WALA SA CASHCARD DEPOSIT
        // $data['promoCmsSettingId'] = $this->session->userdata('promoCmsSettingId'); # WALA SA AUTO AT CASHCARD DEPOSIT

        // load model for Payment_account::
        // $this->load->model(array('payment_account'));
        // $this->load->model(array('transactions'));

        # Remove default_3rdparty_payment config, leave the default to payment_account->getAvailableAccount
        # $defaultPaymentGateway = $this->config->item('default_3rdparty_payment');
        $defaultPaymentGateway = 0;

        //load payment gateway from bankTypeId
        if (!empty($bankTypeId)) {
            //load
            $banktype = $this->banktype->getBankTypeById($bankTypeId);
            if ($banktype && $banktype->external_system_id) {
                $defaultPaymentGateway = $banktype->external_system_id;
                $defaultPaymentGatewayName = lang($banktype->bankName);
            }
        }
        $data['bankTypeId'] = $bankTypeId;

        //change to payment account name
        if(isset($defaultPaymentGatewayName)) {
            $data['paymentGatewayName'] = $defaultPaymentGatewayName;
        }

        //check payment account , if don't found
        // $this->utils->debug_log('paymentAccount', $payment_account, $playerId);

        $minDeposit = 0;
        $maxDeposit = 0;
        if (empty($payment_account)){
            $this->returnBadRequest();
            return;
        }

        $defaultPaymentGateway = $payment_account->external_system_id;

        $api = $this->utils->loadExternalSystemLibObject($payment_account->external_system_id);
        if(empty($api)) {
            $this->returnBadRequest();
            return;
        }

        // if( $this->input->is_ajax_request() ) return $data;
        # Show the 'payment stopped' message if api is not available
        if(!$api->isAvailable()) {
            $this->returnBadRequest();
            return;
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

        $data['isPopupNewWindowOnDeposit'] = $api->isPopupNewWindowOnDeposit();

        // list($bankList, $bankTree) = $this->bank_list->getBankTypeTree($defaultPaymentGateway);
        // $data['bankTree'] = $bankTree;
        // $data['bankList'] = $bankList;
        $data['defaultPaymentGateway'] = $defaultPaymentGateway;

        if ($this->utils->isEnabledFeature('use_self_pick_subwallets')) {
            $data['pick_subwallets'] = $data['subwallets'] = $this->wallet_model->getSubwalletMap();
            $this->utils->debug_log('subwallets', $data['subwallets']);
        }else{
            $data['pick_subwallets'] = $data['subwallets'] = [];
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
        $data['avail_promocms_list']=$this->promorules->getAvailPromoOnDeposit($playerId);

        $this->utils->verbose_log('avail_promocms_list', $data['avail_promocms_list']);

        if ($this->utils->getPlayerCenterTemplate() == 'webet' || !array_key_exists('paymentGatewayName', $data)) {
            $this->loadTemplate(lang('deposit.onlinepayment'));
        } else {
            $this->loadTemplate(lang('deposit.onlinepayment') . ' - ' . lang($data['paymentGatewayName']));
        }

        if( $this->input->is_ajax_request() ) return $this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/auto_3rdparty_deposit', $data);

        $this->template->add_js('common/js/player_center/deposit.js');
        $this->template->add_js('resources/js/validator.js');
        $this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/auto_3rdparty_deposit', $data);
        $this->template->render();
    }

    public function manual_payment($flag, $bankTypeId = null, $paymentAccountId=null) {
        $playerId = $this->authentication->getPlayerId();

        // load model
        $this->load->model(array('banktype','payment_account', 'sale_order', 'http_request', 'group_level', 'transactions','wallet_model', 'playerbankdetails', 'vipsetting', 'player_model', 'player_promo', 'promorules', 'financial_account_setting'));

        if(!$this->checkAgencyCreditMode($playerId)){
            return;
        }

        if ($this->isPostMethod()) {
            try {
                $this->form_validation->set_rules('depositAmount', lang('cashier.53'), 'required');
                $this->form_validation->set_rules('pa_bankName', lang('cashier.65'), 'trim|xss_clean');
                $this->form_validation->set_rules('fullName', lang('cashier.88'), 'trim|xss_clean');
                $this->form_validation->set_rules('na_bankName', lang('cashier.67'), 'trim|xss_clean');
                $this->form_validation->set_rules('bank_slip', lang('Bank slip'), 'trim|xss_clean');
                $this->form_validation->set_rules('deposit_datetime', lang('Deposit Date time'), 'trim|xss_clean');
                $this->form_validation->set_rules('reference_no', lang('Reference No'), 'trim|xss_clean');
                $this->form_validation->set_rules('notes', lang('sys.dm4'), 'trim|xss_clean');
                if (empty($this->utils->isEnabledFeature('show_deposit_bank_details'))) {
                    $this->form_validation->set_rules('depositAccountNo', lang('cashier.69'), 'trim|xss_clean|callback_check_new_deposit_bank_account_number');
                }
                $this->utils->setFormValidationLang();

                if ($this->form_validation->run() == false) {
                    if ($this->input->is_ajax_request()) {
                        $this->returnJsonResult(['status' => 'error', 'msg' => validation_errors()]);
                        return;
                    }
                    throw new Exception(validation_errors());
                }
                # RETRIEVE PARAMETERS
                $depositAmount = $this->input->post('depositAmount');
                $rememberMyAccount = $this->input->post('rememberMyAccount_cb');
                $payment_account_id = $this->input->post('payment_account_id');
                $pendingDepositWalletType = $this->input->post('pendingDepositWalletType');
                $pa_bankName = $this->input->post('pa_bankName');
                $fullName = $this->input->post('fullName');
                $fullName2 = $fullName;
                $na_bankName = $this->input->post('na_bankName');
                $depositAccountNo = $this->input->post('depositAccountNo');
                $depositAccountNo2 = $depositAccountNo;
                $itemAccount = $this->input->post('itemAccount');
                # sub wallet id
                $sub_wallet_id = $this->input->post('sub_wallet_id');
                # group level id
                $group_level_id = $this->input->post('group_level_id');
                $bankSlipImageName = $this->input->post('bank_slip');
                $playerBankDetailsId = $pa_bankName; //preferred player bank account id
                $depositDatetime = $this->input->post('deposit_datetime');
                $depositReferenceNo = $this->input->post('reference_no');
                $depositMethod = $this->input->post('deposit_method');
                $player_submit_datetime = $this->input->post('player_submit_datetime');
                $playerDepositMethod = $this->input->post('playerDepositMethod');

                $paymentAccount = $this->payment_account->getPaymentAccount($payment_account_id);
                if(empty($paymentAccount)){
                    $e = new Exception(sprintf(lang('gen.error.not_exist'), lang('pay.manual_online_payment')));
                    $e->goPlayerHome = true;
                    throw $e;
                }
            }catch(Exception $e){
                log_message('error', $e->getTraceAsString());
                if ($this->input->is_ajax_request()) {
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $e->getMessage()));
                    return;
                }

                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $e->getMessage());
                if($e->goPlayerHome){
                    $this->goPlayerHome();
                    return;
                }
            }
        }else{
            if (!empty($bankTypeId)) {
                //load
                $banktype = $this->banktype->getBankTypeById($bankTypeId);
                if ($banktype && $banktype->external_system_id) {
                    $defaultPaymentGateway = $banktype->external_system_id;
                    $defaultPaymentGatewayName = lang($banktype->bankName);
                }
            }
            $data['bankTypeId'] = $bankTypeId;
            if(empty($bankTypeId)) {
                # No payment account specified, we filter available paymentAccounts against Default Collection Account setting
                $availablePaymentAccounts = $this->payment_account->getAvailableAccount($playerId, Payment_account::FLAG_MANUAL_ONLINE_PAYMENT, null, true, $paymentAccountId);
                $defaultCollectionAccounts = $this->utils->getDepositMenuList();
                $defaultCollectionAccountIds = array();
                foreach($defaultCollectionAccounts as $aCollectionAccount) {
                    $defaultCollectionAccountIds[] = $aCollectionAccount->bankTypeId;
                }

                foreach($availablePaymentAccounts as $aPaymentAccount) {
                    if(in_array($aPaymentAccount->payment_type_id, $defaultCollectionAccountIds)) {
                        $paymentAccount = $aPaymentAccount;
                        break;
                    }
                }
            }

            if (!isset($paymentAccount)) {
                $paymentAccount = $this->payment_account->getAvailableAccount($playerId, Payment_account::FLAG_MANUAL_ONLINE_PAYMENT, $bankTypeId, false, $paymentAccountId);
            }
        }

        if ($paymentAccount) {
            $defaultPaymentGateway = $paymentAccount->external_system_id;
            $data['exists_payment_account'] = true;
            # change to payment account name
            if(isset($defaultPaymentGatewayName)) {
                $data['paymentGatewayName'] = $defaultPaymentGatewayName;
            }

            $paymentAccountMinDeposit = 0;
            $paymentAccountMaxDeposit = $paymentAccount->max_deposit_daily;

            # If defined min/max deposit per transaction, use them
            if ($paymentAccount->max_deposit_trans > 0 && $paymentAccount->max_deposit_trans < $paymentAccountMaxDeposit) {
                $paymentAccountMaxDeposit = $paymentAccount->max_deposit_trans;
            }

            $paymentAccountMinDeposit = $paymentAccount->min_deposit_trans;
        } else {
            $data['exists_payment_account'] = false;
            if(isset($defaultPaymentGatewayName)) {
                $data['paymentGatewayName'] = $defaultPaymentGatewayName;
            }
            $paymentAccountMinDeposit = 0;
            $paymentAccountMaxDeposit = $this->utils->getConfig('defaultMaxDepositDaily');
        }

        $depositRule = $this->group_level->getPlayerDepositRule($playerId);
        $data['depositRule'] = $depositRule;

        $depositRuleMinDeposit = isset($depositRule[0]['minDeposit']) ? $depositRule[0]['minDeposit'] : 0;  # TODO: REMOVE INDEX 0
        $depositRuleMaxDeposit = isset($depositRule[0]['maxDeposit']) ? $depositRule[0]['maxDeposit'] : $this->utils->getConfig('defaultMaxDepositDaily');  # TODO: REMOVE INDEX 0
        $maxDepositDaily = 0;

        # Determine overall min/max deposit to be used on the form
        # Note: value = 0 means no limit
        if ($paymentAccountMinDeposit > $depositRuleMinDeposit) {
            $minDeposit = $paymentAccountMinDeposit;
        } elseif ($depositRuleMinDeposit > 0) {
            $minDeposit = $depositRuleMinDeposit;
        } else {
            $minDeposit = 0;
        }

        # depositRuleMaxDeposit is valid and in effect
        if ($depositRuleMaxDeposit > 0 && $depositRuleMaxDeposit < $paymentAccountMaxDeposit) {
            $maxDeposit = $depositRuleMaxDeposit;
        } elseif ($paymentAccountMaxDeposit > 0) { # paymentAccountMaxDeposit is valid
            $maxDeposit = $paymentAccountMaxDeposit;
        } else {
            $maxDeposit = 0;
        }

        $data['depositRuleMinDeposit'] = $depositRuleMinDeposit;
        $data['depositRuleMaxDeposit'] = $depositRuleMaxDeposit;
        $data['minDeposit'] = $minDeposit;
        $data['maxDeposit'] = $maxDeposit;
        $data['maxDepositDaily'] = $maxDepositDaily;
        $data['double_submit_hidden_field']=$this->initDoubleSubmitAndReturnHiddenField($playerId);

        if ($this->isPostMethod()) {
            try {
                //DB transaction start
                $this->sale_order->startTrans();

                if ($itemAccount == 'new') {
                    //check if remember this account
                    $rememberMyAccount = $rememberMyAccount ? '1' : '0';

                    //add bank account details and get id
                    $data = array(
                        'playerId' => $playerId,
                        'bankTypeId' => $na_bankName,
                        'bankAccountNumber' => $depositAccountNo,
                        'bankAccountFullName' => $fullName,
                        'isRemember' => $rememberMyAccount, //1 is default
                    );
                    $playerBankDetailsId = $this->playerbankdetails->addDepositBankDetails($data);
                } else {
                    $this->utils->debug_log('playerBankDetailsId', $playerBankDetailsId);
                    //load old
                    //load depositName, depositAccount from playerBankDetailsId
                    $playerBankDetail = $this->playerbankdetails->getBankDetailsById($playerBankDetailsId);
                    $fullName = $playerBankDetail['bankAccountFullName'];
                    $depositAccountNo = $playerBankDetail['bankAccountNumber'];
                    $depositTo = $playerBankDetail['bankName'];
                }

                //playerBankDetailsId is required
                $dwIp = $this->input->ip_address();
                $geolocation = $this->utils->getGeoplugin($dwIp);

                # TODO(KAISER): IMPROVE VALIDATION
                if ($depositAmount <= 0) {
                    if ($this->input->is_ajax_request()) {
                        $this->returnJsonResult(array('status' => 'error', 'msg' => lang('notify.39')));
                        return;
                    }
                    # TODO: INCLUDE IN VALIDATION HELPER
                    throw new Exception(lang('notify.39'));
                } else if ($depositAmount < $minDeposit) {
                    if ($this->input->is_ajax_request()) {
                        $this->returnJsonResult(array('status' => 'error', 'msg' => $this->utils->renderLang('notify.40', $minDeposit)));
                        return;
                    }
                    # TODO: INCLUDE IN VALIDATION HELPER
                    throw new Exception($this->utils->renderLang('notify.40', $minDeposit));
                } else if ($maxDeposit > 0) {
                    if ($depositAmount > $maxDeposit) {
                        if ($this->input->is_ajax_request()) {
                            $this->returnJsonResult(array('status' => 'error', 'msg' => $this->utils->renderLang('notify.74', $maxDeposit)));
                            return;
                        }
                        throw new Exception($this->utils->renderLang('notify.74', $maxDeposit));
                    }

                    if ($maxDepositDaily > 0) {
                        //get players deposit total deposit
                        $playerTotalDailyDeposit = $this->transactions->sumDepositAmountToday($playerId);

                        if (($playerTotalDailyDeposit + $depositAmount) >= $maxDepositDaily) {
                            $message = $this->utils->renderLang('notify.74', $maxDepositDaily);

                            if( $this->input->is_ajax_request() ){
                                $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                                return;
                            }

                            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                            $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId);
                            return;
                        }
                    }
                }

                $defaultCurrency = $this->config->item('default_currency');

                $depositSlipPath = $bankSlipImageName;
                $promo_cms_id = $this->input->post('promo_cms_id');
                $notes = $this->input->post('notes');
                //check error
                $error = null;
                $player_promo_id = null;
                list($promo_cms_id, $promo_rules_id) = $this->process_promo_rules($playerId, $promo_cms_id, $depositAmount, $error);

                $promo_info['promo_rules_id'] = $promo_rules_id;
                $promo_info['promo_cms_id']   = $promo_cms_id;

                if(empty($error)){
                    if($this->utils->isEnabledFeature('create_sale_order_after_player_confirm')){
                        //create fake order
                        $saleOrder = $this->sale_order->createFakeDepositOrder(Sale_order::PAYMENT_KIND_DEPOSIT,
                            $payment_account_id, $playerId, $depositAmount, $defaultCurrency,
                            $player_promo_id, $dwIp, $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
                            $playerBankDetailsId, null, $bankSlipImageName, $notes, Sale_order::STATUS_PROCESSING,
                            $sub_wallet_id, $group_level_id, $depositDatetime, $depositReferenceNo, $pendingDepositWalletType,
                            $depositMethod, $this->utils->is_mobile(),$player_submit_datetime,$fullName2,$depositAccountNo2,$playerDepositMethod);
                    }else{
                        $saleOrder = $this->sale_order->createDepositOrder(Sale_order::PAYMENT_KIND_DEPOSIT,
                            $payment_account_id, $playerId, $depositAmount, $defaultCurrency,
                            $player_promo_id, $dwIp, $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
                            $playerBankDetailsId, null, $bankSlipImageName, $notes, Sale_order::STATUS_PROCESSING,
                            $sub_wallet_id, $group_level_id, $depositDatetime, $depositReferenceNo, $pendingDepositWalletType,
                            $depositMethod, $this->utils->is_mobile(),$player_submit_datetime,$promo_info,$fullName2,$depositAccountNo2,$playerDepositMethod);
                    }
                }

                $this->transferBankslipImage();
                $this->emptyPlayerTempUploadFolder();

                $this->saveHttpRequest($playerId, Http_request::TYPE_DEPOSIT);
                if (!$this->sale_order->endTransWithSucc()) {
                    if ($this->input->is_ajax_request()) {
                        $this->returnJsonResult(array('status' => 'error', 'msg' => lang('error.default.message')));
                        return;
                    }
                    throw new Exception(lang('error.default.message'));
                }

                $message = lang('notify.38');

                if(!empty($error)){
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $error);
                }else{
                    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);

                    if($this->utils->isEnabledFeature('create_sale_order_after_player_confirm')){
                        //render deposit result
                        $saleOrderObj=(object) $saleOrder;
                        $data['title'] = lang($saleOrderObj->payment_type_name);
                        $data['order_info'] = $saleOrderObj;
                        $data['currentLang'] = $this->language_function->getCurrentLanguage();

                        $title = lang('cashier.44');
                        if ($flag == Payment_account::FLAG_MANUAL_LOCAL_BANK) {
                            $title = lang('cashier.46');
                        }

                        if( $this->input->is_ajax_request() ) return $this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);

                        $this->loadTemplate($title);
                        $this->template->add_css('resources/third_party/fancybox/jquery.fancybox.css');
                        $this->template->add_js('resources/third_party/fancybox/jquery.fancybox.pack.js');
                        $this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
                        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);
                        $this->template->render();
                        return;

                    }else{

                        if ($this->input->is_ajax_request()) {
                            $this->returnJsonResult(array('status' => 'success', 'msg' => $message));
                            return;
                        }
                        if ($this->utils->isEnabledFeature('show_deposit_bank_details')){
                            return $this->iframe_makeDeposit($flag);
                        }
                        //go to deposit_result
                        if ($this->utils->getPlayerCenterTemplate() == 'iframe') {
                            redirect('/iframe_module/deposit_result/' . $saleOrder['id'] . '/' . $flag);
                        } else {
                            redirect('/player_center/deposit_result/' . $saleOrder['id'] . '/' . $flag);
                        }
                    }
                    return;
                }

            } catch (Exception $e) {
                log_message('error', $e->getTraceAsString());
                if ($this->input->is_ajax_request()) {
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $e->getMessage()));
                    return;
                }
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $e->getMessage());
            }

        }

        $this->emptyPlayerTempUploadFolder();

        $default_bank_details = $this->playerbankdetails->getDefaultBankDetail($playerId);
        if( count($default_bank_details['deposit']) > 0) {
            $data['default_bank_details'] = $default_bank_details['deposit'][0];
            unset($data['default_bank_details']['bankName']);
        }
        else {
            $data['default_bank_details'] = [];
        }
        $data['bank_details'] = $this->playerbankdetails->getBankDetails($playerId);

        //bank list of player
        $data['playerBankDetails'] = $this->playerbankdetails->getAvailableDepositBankDetail($playerId);
        $banks = $this->banktype->getAvailableBankTypeList('deposit');
        $data['banks'] = $banks;
        $data['bankTypeList'] = $banks;

        $data['playerId'] = $playerId;
        $data['pick_subwallets'] = $this->wallet_model->getSubwalletMap();
        //load from payment_account
        $paymentAccount = $this->payment_account->getAvailableAccount($playerId, $flag, $bankTypeId, false, $paymentAccountId);
        if (!empty($paymentAccount)) {
            $data['exists_payment_account'] = true;
            $data['payment_account_id'] = $paymentAccount->payment_account_id;
            $data['payment_type'] = $paymentAccount->payment_type;
            $data['payment_branch_name'] = $paymentAccount->payment_branch_name;
            $data['payment_account_name'] = $paymentAccount->payment_account_name;
            $data['payment_account_number'] = $paymentAccount->payment_account_number;
            $data['paymentAccount'] = $paymentAccount;
            $data['payment_account_hide_bank_info'] = $this->payment_account->isHideBankInfo($data['payment_type']);
            $data['payment_account_hide_bank_type'] = $this->payment_account->isHideBankType($data['payment_type']);

            $paymentAccountMinDeposit = 0;
            $paymentAccountMaxDeposit = $paymentAccount->max_deposit_daily;

            # If defined min/max deposit per transaction, use them
            if ($paymentAccount->max_deposit_trans > 0 && $paymentAccount->max_deposit_trans < $paymentAccountMaxDeposit) {
                $paymentAccountMaxDeposit = $paymentAccount->max_deposit_trans;
            }

            $paymentAccountMinDeposit = $paymentAccount->min_deposit_trans;

        } else {
            $data['exists_payment_account'] = false;
            $paymentAccountMinDeposit=0;
            $paymentAccountMaxDeposit = 0;
        }

        $depositRule = $this->group_level->getPlayerDepositRule($playerId);
        $depositRuleMinDeposit = isset($depositRule[0]['minDeposit']) ? $depositRule[0]['minDeposit'] : 0;
        $depositRuleMaxDeposit = isset($depositRule[0]['maxDeposit']) ? $depositRule[0]['maxDeposit'] : 0;

        # Determine overall min/max deposit to be used on the form
        # Note: value = 0 means no limit
        if ($paymentAccountMinDeposit > $depositRuleMinDeposit) {
            $data['minDeposit'] = $paymentAccountMinDeposit;
        } elseif ($depositRuleMinDeposit > 0) {
            $data['minDeposit'] = $depositRuleMinDeposit;
        } else {
            $data['minDeposit'] = 0;
        }

        $this->utils->debug_log('paymentAccountMinDeposit', $paymentAccountMinDeposit,
            'depositRuleMinDeposit', $depositRuleMinDeposit, 'minDeposit', $data['minDeposit']);

        # depositRuleMaxDeposit is valid and in effect
        if ($depositRuleMaxDeposit > 0 && $depositRuleMaxDeposit < $paymentAccountMaxDeposit) {
            $data['maxDeposit'] = $depositRuleMaxDeposit;
        } elseif ($paymentAccountMaxDeposit > 0) { # paymentAccountMaxDeposit is valid
            $data['maxDeposit'] = $paymentAccountMaxDeposit;
        } else {
            $data['maxDeposit'] = 0;
        }

        if( $this->utils->isEnabledFeature('show_deposit_bank_details_first')) {
            $timeout = $this->config->item('deposit_timeout_seconds');
            $data['request_on'] = $this->utils->getNowForMysql();       // created at
            $data['expire_on'] =  $this->utils->getNowAdd($timeout);    // timeout at
        }
        $data['secure_id'] =  $this->sale_order->generateSecureId();
        $data['flag'] = $flag;
        $data['bankTypeId'] = $bankTypeId;
        if ($this->utils->isEnabledFeature('use_self_pick_subwallets')) {
            $data['subwallets'] = $this->wallet_model->getSubwalletMap();
            $this->utils->debug_log('subwallets', $data['subwallets']);
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

        if( $this->utils->isEnabledFeature('enable_manual_deposit_realname') || $this->utils->isEnabledFeature('enable_manual_deposit_input_depositor_name')){
            $playerDetailObject =  $this->player_model->getPlayerDetailsById($playerId);
            $firstName = $playerDetailObject->firstName;
            if (strlen($firstName) <= 0){
                $data['firstNameflg'] = 0;
            }else{
                $data['firstNameflg'] = 1;
            }
            $data['firstName']=$firstName;
        }

        $data['avail_promocms_list'] = $this->promorules->getAvailPromoOnDeposit($playerId);

        $this->utils->verbose_log('avail_promocms_list', $data['avail_promocms_list']);

        //FIXME
        // if ($this->utils->isEnabledFeature('select_promotion_on_deposit')) {
        //  $playerPromotionList = $this->player_promo->getAvailPlayerPromoList($playerId, Promorules::PROMO_TYPE_DEPOSIT);
        //  if (!empty($playerPromotionList)) {
        //      // foreach ($playerPromotionList as $playerPromo) {
        //      //  $playerPromo['description']=;
        //      // }
        //      $data['playerPromotionList'] = $playerPromotionList;
        //  }
        // }

        $bankType = null;
        if (!empty($bankTypeId)) {
            $bankType = $this->banktype->getBankTypeById($bankTypeId);
        }


        $payment_all_accounts = $this->payment_account->getAvailableDefaultCollectionAccount($playerId);

        $payment_manual_accounts = ($payment_all_accounts[MANUAL_ONLINE_PAYMENT]['enabled']) ? $payment_all_accounts[MANUAL_ONLINE_PAYMENT]['list'] : [];
        if($payment_all_accounts[LOCAL_BANK_OFFLINE]['enabled']){
            foreach($payment_all_accounts[LOCAL_BANK_OFFLINE]['list'] as $payment_account){
                $payment_manual_accounts[] = $payment_account;
            }
        }

        $data['payment_manual_accounts'] = $payment_manual_accounts;


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

        $title = lang('Bank Deposit');
        if ($flag == Payment_account::FLAG_MANUAL_LOCAL_BANK) {
            $title = lang('cashier.46');
        }
        if (!empty($bankType)) {
            $title = lang($bankType->bankName);
        }
        $data['currentLang'] = $this->language_function->getCurrentLanguage();

        $site = $this->utils->getSystemUrl('www');

        $data['title'] = $title;
        $data['playsite'] = $site;
        $data['site'] = $site;
        $data['player'] = $this->player_model->getPlayerInfoDetailById($playerId);

        $data['enable_deposit_upload_documents']=$this->utils->isEnabledFeature('enable_deposit_upload_documents');
        $data['required_deposit_upload_file_1']=$this->utils->isEnabledFeature('required_deposit_upload_file_1');

        $data['payment_type_flag'] = Financial_account_setting::PAYMENT_TYPE_FLAG_BANK;
        if(!empty($banktype)) {
            $data['payment_type_flag'] = $banktype->payment_type_flag;
            $field_show = $this->financial_account_setting->getFieldShowByPaymentAccountFlag($banktype->payment_type_flag);
        } else {
            $field_show = $this->financial_account_setting->getFieldShowByPaymentAccountFlag(Financial_account_setting::PAYMENT_TYPE_FLAG_BANK);
        }

        $data['hide_bank_branch_in_payment_account_detail_player_center'] = !in_array(Financial_account_setting::FIELD_BANK_BRANCH, $field_show);
        $data['hide_mobile_in_payment_account_detail_player_center'] = !in_array(Financial_account_setting::FIELD_PHONE, $field_show);
        $data['hide_deposit_selected_bank_and_text_for_ole777'] = $this->utils->isEnabledFeature('hide_deposit_selected_bank_and_text_for_ole777');


        $currentPaymentAccount = null;
        if(!empty($paymentAccountId)){
            $currentPaymentAccount=$this->payment_account->getAvailableAccount($playerId, NULL, NULL, FALSE, $paymentAccountId);

            if(empty($currentPaymentAccount)) {
                $this->utils->debug_log('==============manual_payment get empty currentPaymentAccount', $currentPaymentAccount);
                redirect('/player_center2/deposit/empty_payment_account');
                return;
            }

            $data['payment_manual_accounts'] = [$paymentAccountId => $currentPaymentAccount];

            $banktype = $this->banktype->getBankTypeById($currentPaymentAccount->payment_type_id);
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
        }else{
            $data['isAlipay'] = false;
            $data['isUnionpay'] = false;
            $data['isWechat'] = false;
        }

        if($this->input->is_ajax_request()){
            return $this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/common_deposit', $data);
        }
        $this->loadTemplate($title);
        $this->template->add_js('resources/js/player/cashier.js');
        $this->template->add_js('resources/js/validator.js');
        $this->template->add_js('resources/third_party/clipboard/clipboard.min.js');
        $this->template->add_js('common/js/player_center/player-cashier.js');
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/common_deposit', $data);

        $this->template->render();
    }

    public function save_manual_order() {
        $sale_order=$this->input->post();

        $this->utils->debug_log('sale order', $sale_order);
        $success=false;

        if(!empty($sale_order)){
            //create json
            $this->load->model(['sale_order']);
            $controller=$this;
            $success=$this->dbtransOnly(function() use ($sale_order, $controller){
                return !!$controller->sale_order->saveFakeSaleOrderToDepositOrder($sale_order);
            });
        }

        if($success){
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, lang('notify.38'));
        }else{
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('Save order failed, please try again'));
        }

        $this->goPlayerHome();
    }

    public function deposit_result($saleOrderId, $flag) {
        $this->load->model(array('sale_order', 'payment_account'));
        $saleOrder = $this->sale_order->getSaleOrderById($saleOrderId);

        $data['title'] = lang($saleOrder->payment_type_name);
        $data['order_info'] = $saleOrder;
        $data['currentLang'] = $this->language_function->getCurrentLanguage();

        $title = lang('cashier.44');
        if ($flag == Payment_account::FLAG_MANUAL_LOCAL_BANK) {
            $title = lang('cashier.46');
        }

        if( $this->input->is_ajax_request() ) return $this->load->view($this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);

        $this->loadTemplate($title);
        $this->template->add_css('resources/third_party/fancybox/jquery.fancybox.css?v=2.1.5');
        $this->template->add_js('resources/third_party/fancybox/jquery.fancybox.pack.js?v=2.1.5');
        $this->template->add_js('resources/third_party/clipboard/clipboard.min.js');

        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/collection_account', $data);

        $this->template->render();
    }

    protected function process_promo_rules($playerId, $promo_cms_id, $transferAmount, &$error=null, $subWalletId=null){
        $this->load->model(['promorules']);

        if(!empty($promo_cms_id)){

            list($promorule, $promoCmsSettingId)=$this->promorules->getByCmsPromoCodeOrId($promo_cms_id);

            //simple check
            //check sub wallet only
            if($this->promorules->isTransferPromo($promorule)){

                //check
                if ($promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
                    if ($transferAmount >= $promorule['nonfixedDepositMinAmount'] && $transferAmount <= $promorule['nonfixedDepositMaxAmount']) {
                    } else {
                        $error = lang('notify.37');
                        return [null, null];
                    }
                }

                $trigger_wallets=$promorule['trigger_wallets'];
                $trigger_wallets_arr=[];
                if(!empty($trigger_wallets)){
                    $trigger_wallets_arr=explode(',',$trigger_wallets);
                }
                if(!in_array($subWalletId, $trigger_wallets_arr)){
                    $this->utils->error_log('subWalletId should be ', $trigger_wallets_arr ,'current',$subWalletId);
                    // $message = 'Only trigger on transfer right sub-wallet';
                    $error = lang('Must choose correct sub-wallet');
                    return [null, null];
                }

            }elseif($this->promorules->isDepositPromo($promorule)){
                //check
                if ($promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
                    if ($transferAmount >= $promorule['nonfixedDepositMinAmount'] && $transferAmount <= $promorule['nonfixedDepositMaxAmount']) {
                    } else {
                        $error = lang('notify.37');
                        return [null, null];
                    }
                }

            }

            return [$promoCmsSettingId, $promorule['promorulesId']];
        }

        return [null, null];
    }

    protected function process_player_promo($playerId, $promo_cms_id, $transferAmount=null, $subWalletId=null, &$error=null){
        // $this->load->library(['authentication']);
        $this->load->model(['player_promo', 'promorules']);
        //load from promo_cms_id and player id
        // $playerId=$this->authentication->getPlayerId();
        // $promo_cms_id=$this->input->get('promo_cms_id');
        //also get promo rule
        list($promorule, $promoCmsSettingId)=$this->promorules->getByCmsPromoCodeOrId($promo_cms_id);

        $player_promo_id=null;
        if(!empty($playerId) && !empty($promo_cms_id) && !empty($promorule)){
            $promorulesId=$promorule['promorulesId'];

            $allowedFlag = $this->promorules->isAllowedPlayer($promorulesId, $promorule, $playerId);
            if(!$allowedFlag){
                $error=lang('notify.35');
                return null;
            }

            //if this promorule is required pre-application
            if($promorule['disabled_pre_application']!='1'){
                //should have approved player promo
                $this->load->model(['player_promo']);
                $player_promo_id=$this->player_promo->getApprovedPlayerPromo($playerId, $promorulesId);
            // }else{
            //  $this->promorules->approvedPreApplication($promo_cms_id, $promorule['promorulesId'],
            //      $promorule['disabled_pre_application'], $playerId);
            }else{
                //check sub wallet only
                if($this->promorules->isTransferPromo($promorule)){

                    //check
                    if ($promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
                        if ($transferAmount >= $promorule['nonfixedDepositMinAmount'] && $transferAmount <= $promorule['nonfixedDepositMaxAmount']) {
                        } else {
                            $error = lang('notify.37');
                            return null;
                        }
                    }

                    $trigger_wallets=$promorule['trigger_wallets'];
                    $trigger_wallets_arr=[];
                    if(!empty($trigger_wallets)){
                        $trigger_wallets_arr=explode(',',$trigger_wallets);
                    }
                    if(!in_array($subWalletId, $trigger_wallets_arr)){
                        $this->utils->error_log('subWalletId should be ', $trigger_wallets_arr ,'current',$subWalletId);
                        // $message = 'Only trigger on transfer right sub-wallet';
                        $error = lang('Must choose correct sub-wallet');
                        return null;
                    }

                }

                if($this->promorules->isDepositPromo($promorule)){
                    //check
                    if ($promorule['depositConditionNonFixedDepositAmount'] == Promorules::NON_FIXED_DEPOSIT_MIN_MAX) {
                        if ($transferAmount >= $promorule['nonfixedDepositMinAmount'] && $transferAmount <= $promorule['nonfixedDepositMaxAmount']) {
                        } else {
                            $error = lang('notify.37');
                            return null;
                        }
                    }
                }

                // $preapplication=false;
                // $playerPromoId=null;
                // $extra_info=[];
                // //check condition first
                // list($success, $message)=$this->checkOnlyPromotion($playerId, $promorule, $promoCmsSettingId,
                //  $preapplication, $playerPromoId, $extra_info);

                //create player promo
                $player_promo_id=$this->player_promo->requestPromoToPlayer($playerId, $promorulesId, null, $promo_cms_id);
            }
        }

        $this->utils->debug_log('process player promo', $player_promo_id, 'player id', $playerId, 'promo_cms_id', $promo_cms_id);

        return $player_promo_id;
    }

    public function autoDeposit3rdParty() {

        $this->load->model(array('group_level', 'transactions', 'payment_account','sale_order'));
        $this->load->library(['authentication']);

        $this->form_validation->set_rules('deposit_from', 'Payment Method', 'trim|required|xss_clean');

        $player_id = $this->authentication->getPlayerId();
        $player = $this->player_functions->getPlayerById($player_id);

        //get player's deposit rule
        $data['depositRule'] = $this->group_level->getPlayerDepositRule($player_id);

        $bankTypeId = $this->input->post('bankTypeId');

        if(empty($bankTypeId)){
            $message = sprintf(lang('gen.error.not_exist'), lang('pay.depbanktype'));
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            redirect($this->utils->getPlayerDepositUrl());
            return;
        }

        if(isset($bankTypeId)){
            $paymentAccount = $this->payment_account->getAvailableAccount($player_id, Payment_account::FLAG_AUTO_ONLINE_PAYMENT, $bankTypeId);
            $minDeposit = $paymentAccount->vip_rule_min_deposit_trans;
            $maxDeposit = $paymentAccount->vip_rule_max_deposit_trans;
        } else {
            $minDeposit = $data['depositRule'][0]['minDeposit'];
            $maxDeposit = $data['depositRule'][0]['maxDeposit'];
        }
        $maxDepositDaily=0;

        $paymentSystemId = $this->input->post('deposit_from');
        $api = $this->utils->loadExternalSystemLibObject($paymentSystemId);
        $deposit_amount = null;
        if (empty($api)) {
            $message = sprintf(lang('gen.error.not_exist'), lang('sys.payment.api'));
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
            return;
        }

        $auto_deposit_cool_down_minutes = $api->getNextOrderCooldownTime();
        if($auto_deposit_cool_down_minutes != 0){
            $lastOrder = $this->sale_order->getLastSalesOrderByPlayerId($player_id);
            $this->utils->debug_log('lastOrder', $lastOrder, 'auto_deposit_cool_down_minutes', $auto_deposit_cool_down_minutes);
            if($lastOrder && !$this->utils->isTimeoutNow($lastOrder->created_at, $auto_deposit_cool_down_minutes) ){
                $message = sprintf(lang('notify.still_in_cooldown_time'), $auto_deposit_cool_down_minutes);
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
                return;
            }
        }

        $deposit_amount = $api->getAmount($this->getInputGetAndPost());

        //deposit limit hint
        if($this->utils->isEnabledFeature('responsible_gaming')) {
            $this->load->library(['player_responsible_gaming_library']);
            if($this->player_responsible_gaming_library->inDepositLimits($player_id, $deposit_amount)){
                return [
                    'status' => 'error',
                    'message' => lang('Wagering Limits Effect, cannot make deposit')
                ];
            }
        }

        if($bankTypeId){
            $ignore_amount_limit_for_loadcard = $paymentSystemId &&
            $this->utils->getConfig('ignore_amount_limit_for_loadcard');
        } else{
            $ignore_amount_limit_for_loadcard = $paymentSystemId == LOADCARD_PAYMENT_API &&
            $this->utils->getConfig('ignore_amount_limit_for_loadcard');
        }
        if ($this->form_validation->run() == false || empty($deposit_amount)) {
            //$message = "Please make sure the information you provided is complete!";
            $message = lang('notify.41');
            $this->form_validation->set_message('deposit_amount', lang('notify.42'));

            if( $this->input->is_ajax_request() ){
                $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                return;
            }

            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            return $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
        } elseif (!$ignore_amount_limit_for_loadcard && $minDeposit > $deposit_amount) {
            //$message = "You did not meet the minimum deposit amount requirement!";
            $message = lang('notify.43');
            if( $this->input->is_ajax_request() ){
                $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                return;
            }

            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            return $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
        } else {
            $this->load->model(array('player_model', 'bank_list'));

            if ($deposit_amount <= 0) {
                if ($this->input->is_ajax_request()) {
                    $this->returnJsonResult(array('status' => 'error', 'msg' => lang('notify.39')));
                    return;
                }

                $this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('notify.39'));
                $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
                return;
            } else if ($deposit_amount < $minDeposit) {
                if ($this->input->is_ajax_request()) {
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $this->utils->renderLang('notify.40', $minDeposit)));
                    return;
                }

                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $this->utils->renderLang('notify.40', $minDeposit));
                $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
                return;
            }
            //check if deposit reached max limit
            if ($maxDeposit > 0) {
                //get players deposit total deposit
                if ($deposit_amount > $maxDeposit) {
                    $message = lang('Maximum Deposit has been reached') . ' ' . $maxDeposit;

                    if( $this->input->is_ajax_request() ){
                        $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                        return;
                    }

                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                    $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
                    return;
                }
            }
            if ($maxDepositDaily > 0) {
                //get players deposit total deposit
                $playerTotalDailyDeposit = $this->transactions->sumDepositAmountToday($player_id);

                if (($playerTotalDailyDeposit + $deposit_amount) >= $maxDepositDaily) {
                    $message = lang('notify.74') . ' ' . $maxDepositDaily . '!';

                    if( $this->input->is_ajax_request() ){
                        $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                        return;
                    }

                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                    $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
                    return;
                }
            }

            $deposit_from = $paymentSystemId;

            $sub_wallet_id = $this->input->post('sub_wallet_id');
            $promo_cms_id = $this->input->post('promo_cms_id');
            $error = null;
            $player_promo_id = null;
            list($promo_cms_id, $promo_rules_id) = $this->process_promo_rules($player_id, $promo_cms_id, $deposit_amount, $error,$sub_wallet_id);
            $promo_info['promo_rules_id'] = $promo_rules_id;
            $promo_info['promo_cms_id'] = $promo_cms_id;

            if(!empty($error)){
                if( $this->input->is_ajax_request() ){
                    $this->returnJsonResult(array('status' => 'error', 'msg' => $error));
                    return;
                }
                $this->alertMessage(self::MESSAGE_TYPE_ERROR, $error);
                return $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
            }

            //group level id
            $group_level_id = $this->input->post('group_level_id');

            $player_deposit_reference_no = $this->input->post('player_deposit_reference_no');
            $deposit_time = $this->input->post('deposit_time');

            $bankId = $this->input->post('bank_type');
            $bankShortCode = $this->input->post('bank');
            $this->utils->debug_log('bank info', $bankId, $bankShortCode);
            if (empty($bankId) && !empty($bankShortCode)) {
                $bankId = $this->bank_list->getIdByShortcode($bankShortCode);
            }


            $extra_info_order=$this->getInputGetAndPost();
            //remove iovation
            unset($extra_info_order['ioBB']);
            unset($extra_info_order['fpBB']);

            $floatAmountLimit = $api->getFloatAmountLimit();
            if(!is_null($floatAmountLimit)){
                $floatAmountLimits = explode('|', trim($floatAmountLimit, '()'));
                if(!in_array($deposit_amount, $floatAmountLimits)){
                    $message = lang('Invalid amount');
                    if( $this->input->is_ajax_request() ){
                        $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                        return;
                    }
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                    $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
                    return;
                }
            }

            $orderId = $api->createSaleOrder($player_id, $deposit_amount, $player_promo_id,
                $extra_info_order, $sub_wallet_id, $group_level_id, $this->utils->is_mobile(), $player_deposit_reference_no, $deposit_time, $promo_info);

            $this->saveHttpRequest($player_id, Http_request::TYPE_DEPOSIT);
            $this->player_model->incTotalDepositCount($player_id);
            if( $this->input->is_ajax_request() ){
                $this->returnJsonResult(array('status' => 'success', 'msg' => ''));
                return;
            }

            return $this->goPayment($deposit_from, $deposit_amount, $player_id,
                $player_promo_id, $bankId, $orderId);
        }

        $message = lang('error.default.message');

        if( $this->input->is_ajax_request() ){
            $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
            return;
        }

        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
        return $this->goMakeDeposit(AUTO_ONLINE_PAYMENT, $bankTypeId, $paymentAccount->payment_account_id);
    }

    public function callback_success($systemId = null, $orderId = null) {
        $message = $this->session->flashdata('message');
        if (empty($message)) {
            $message = lang('payment.success');
        }
        $nextUrl = $this->session->flashdata('next_url');
        if (empty($nextUrl)) {
            //go home or go game
            $nextUrl = $this->utils->getPlayerHomeUrl();
        }

        list($saleOrder, $transaction, $promoTrans) = $this->getSaleOrderAndTransaction($orderId);

        $data = array('message' => $message, 'next_url' => $nextUrl,
            'system_id' => $systemId, 'order_id' => $orderId,
            'sale_order' => $saleOrder, 'transaction' => $transaction, 'promo_trans' => $promoTrans);

        $data['currentLang'] = $this->language_function->getCurrentLanguage();
        $data['content_template'] = 'default_iframe.php'; # this page uses default content template in stable_center2
        $data['player'] = array('username' => $this->authentication->getUsername());

        $this->loadTemplate(lang('payment.success'), '', '', '');
        $url = $this->utils->getPlayerCenterTemplate() . '/callback/callback_success';
        $this->template->write_view('main_content', $url, $data);
        $this->template->render();
    }

    private function getSaleOrderAndTransaction($orderId) {
        $this->load->model(array('sale_order', 'transactions'));
        $saleOrder = $this->sale_order->getSaleOrderById($orderId);
        $transaction = null;
        $promoTrans = null;
        if ($saleOrder) {
            if (isset($saleOrder->transaction_id) && $saleOrder->transaction_id) {
                $transaction = $this->transactions->getTransaction($saleOrder->transaction_id);
            }
            if (isset($saleOrder->player_promo_id) && $saleOrder->player_promo_id) {
                $promoTrans = $this->transactions->getTransactionByPlayerPromoId($saleOrder->id, $saleOrder->player_promo_id);
            }

        }
        return array($saleOrder, $transaction, $promoTrans);
    }

    public function gameHistory() {
        $this->load->model(array('external_system', 'game_logs'));

        $data['playerId'] = $this->authentication->getPlayerId();
        $data['game_platforms'] = $this->external_system->getAllActiveSytemGameApi();
        $data['currentLang'] = $this->language_function->getCurrentLanguage();

        $this->loadTemplate(lang('player.ui48'), '', '', 'settings');
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/player/game_history', $data);
        $this->template->render();
    }

    /**
     * only check new deposit bank account number
     * @param  [type] $bank_account_number [description]
     * @return [type]                      [description]
     */
    public function check_new_deposit_bank_account_number($bank_account_number){
        $playerId = $this->authentication->getPlayerId();
        if(empty($playerId)){
            $this->form_validation->set_message('check_new_deposit_bank_account_number', lang('Please login again'));
            return false;
        }

        // $success=true;
        // $itemAccount = $this->input->post('itemAccount');
        // if($itemAccount=='new'){

            $bank_details_id=null;

            $this->load->model(['playerbankdetails']);
            $bank_type=Playerbankdetails::DEPOSIT_BANK;
        //  $success= $this->playerbankdetails->validate_bank_account_number($playerId, $bank_account_number,
        //      $bank_type, $bank_details_id);

        // }

        // if(!$success){
        //  $this->form_validation->set_message('check_new_deposit_bank_account_number', lang('Bank Account Number already exist'));
        // }

        // return $success;

        return $this->common_validate_bank_account_number('check_new_withdrawal_bank_account_number',
            $playerId, $bank_account_number, $bank_type, $bank_details_id);

    }

    public function deposit_local_bank() {
        $this->load->model(array('payment_account'));

        // $allPaymentAccountDetails = $this->payment_account->getAllPaymentAccountDetails($sort, null, null);
        // $paymentTypes = array();

        // foreach ($allPaymentAccountDetails as $allPaymentAccountDetail) {
        //     $flag_id = $allPaymentAccountDetail->flag;

        //     if ($flag_id == 1 || $flag_id == 3) {
        //         $paymentTypes[$allPaymentAccountDetail->payment_account_id] = $allPaymentAccountDetail->payment_type;
        //     }
        // }
        //
        // $data['paymentTypes'] = $paymentTypes;

        $depositMenuList = $this->utils->getDepositMenuList();

        foreach ($depositMenuList as $depositValue) {
            $flag_id = $depositValue->flag;

            if ($flag_id == 1 || $flag_id == 3) {
                $bankTypes[$depositValue->bankTypeId] = $depositValue->bankName;
            }
        }

        $data['bankTypes'] = $bankTypes;

        $this->loadTemplate();
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/cashier/deposit_local_bank', $data);
        $this->template->render();
    }

    /**
     * OGP-8278 Ported from xcyl
     * @param   int     $playerId   == player.playerId
     * @return  array
     */
    public function getDepositLocalBank($playerId) {
        $this->load->model(array('payment_account', 'operatorglobalsettings'));

        $allPaymentAccountDetails = $this->payment_account->getAllPaymentAccountDetails(null, null, null);
        $paymentTypes = array();

        $bank_transfer = array();
        $mobile_transfer = array();
        $a = 0;
        $b = 0;

        $special_payment_list = $this->utils->is_mobile() ? $this->operatorglobalsettings->getSpecialPaymentListMobile() : $this->operatorglobalsettings->getSpecialPaymentList();
        $payment_account_types = json_decode($this->operatorglobalsettings->getPaymentAccountTypes(true), true);

        $bank_transfer_status = $payment_account_types[1]['enabled'];
        $mobile_transfer_status = $payment_account_types[3]['enabled'];

        foreach ($allPaymentAccountDetails as $allPaymentAccountDetail) {
            $flag_id = $allPaymentAccountDetail->flag;
            $status = $allPaymentAccountDetail->status;

            if ($status == 1 && in_array($allPaymentAccountDetail->payment_account_id, $special_payment_list)) {
                $isExist = $this->payment_account->existsAvailableAccount($playerId, $flag_id, $allPaymentAccountDetail->payment_type_id, $allPaymentAccountDetail->payment_account_id, $allPaymentAccountDetail->only_allow_affiliate);

                if(!$isExist) {
                    continue;
                }

                // Bank Transfer
                if ($flag_id == 1) {
                    $bank_transfer[$a]['payment_account_id'] = $allPaymentAccountDetail->payment_account_id;
                    $bank_transfer[$a]['payment_order'] = $allPaymentAccountDetail->payment_order;
                    $bank_transfer[$a]['payment_type'] = $allPaymentAccountDetail->payment_type;
                    $bank_transfer[$a]['min_deposit_trans'] = $allPaymentAccountDetail->min_deposit_trans;
                    $bank_transfer[$a]['max_deposit_trans'] = $allPaymentAccountDetail->max_deposit_trans;
                    $a++;
                }

                // Mobile Transfer
                if ($flag_id == 3) {
                    $mobile_transfer[$b]['payment_account_id'] = $allPaymentAccountDetail->payment_account_id;
                    $mobile_transfer[$b]['payment_order'] = $allPaymentAccountDetail->payment_order;
                    $mobile_transfer[$b]['payment_type'] = $allPaymentAccountDetail->payment_type;
                    $mobile_transfer[$b]['min_deposit_trans'] = $allPaymentAccountDetail->min_deposit_trans;
                    $mobile_transfer[$b]['max_deposit_trans'] = $allPaymentAccountDetail->max_deposit_trans;
                    $b++;
                }
            }

            // if ($flag_id == 1 || $flag_id == 3) {
            //     if ($status == 1) {
            //         $paymentTypes[$allPaymentAccountDetail->payment_account_id]['payment_type'] = $allPaymentAccountDetail->payment_type;
            //         $paymentTypes[$allPaymentAccountDetail->payment_account_id]['min_deposit_trans'] = $allPaymentAccountDetail->min_deposit_trans;
            //         $paymentTypes[$allPaymentAccountDetail->payment_account_id]['max_deposit_trans'] = $allPaymentAccountDetail->max_deposit_trans;
            //     }
            // }
        }


        if (count($bank_transfer) > 0 && $bank_transfer_status) {
            usort($bank_transfer, function($a, $b) { return $a['payment_order'] - $b['payment_order']; });

            $bank_transfer_label = array(
                    '1' => 'Bank Transfer',
                    '2' => lang('pay.manual_online_payment')
                );

            $bank_json_encoded = json_encode($bank_transfer_label);
            $bank_transfer_new_label = '_json:' . $bank_json_encoded;
            $bank_transfer[0]['payment_type'] = $bank_transfer_new_label;

            // unset($bank_transfer[0]);
        }

        if (count($mobile_transfer) > 0 && $mobile_transfer_status) {
            usort($mobile_transfer, function($a, $b) { return $a['payment_order'] - $b['payment_order']; });

            $mobile_transfer_label = array(
                    '1' => 'Mobile Transfer',
                    '2' => lang('pay.local_bank_offline')
                );

            $mobile_transfer_encoded = json_encode($mobile_transfer_label);
            $mobile_transfer_new_label = '_json:' . $mobile_transfer_encoded;
            $mobile_transfer[0]['payment_type'] = $mobile_transfer_new_label;

            // unset($mobile_transfer[0]);
        }

        $paymentTypes = array_merge($bank_transfer, $mobile_transfer);
        usort($paymentTypes, function($a, $b) { return $a['payment_order'] - $b['payment_order']; });

        return $paymentTypes;
    } // End function getDepositLocalBank()

    public function do_deposit() {
        if ($this->isPostMethod()) {
            $this->load->model(array('sale_order', 'http_request', 'group_level', 'transactions','wallet_model', 'playerbankdetails'));

            $playerId = $this->authentication->getPlayerId();
            $last_sale_order = $this->sale_order->getLastSalesOrderByPlayerId($playerId);

            if (!$this->checkAgencyCreditMode($playerId)) {
                // $this->alertMessage(self::MESSAGE_TYPE_ERROR, "Credit card mode is on.");
                $data['deposit_status'] = 'credit_card_mode';
            } elseif ($last_sale_order != null && $last_sale_order->status != 5 && $last_sale_order->status != 8 && $this->input->post('pending_deposit_request') == 0) {
                // $this->alertMessage(self::MESSAGE_TYPE_ERROR, "There's a pending deposit transaction.");
                $data['deposit_status'] = 'pending_deposit_request';
            } else {
                try {
                    if ($this->input->post('pending_deposit_request') == 1) {
                        $saleOrderID = $last_sale_order->id;
                        $this->sale_order->updateLastSalesOrderToRejectByID($saleOrderID);

                        $loggedAdminUserId = method_exists($this->authentication, 'getUserId') ? $this->authentication->getUserId() : Users::SUPER_ADMIN_ID;
                        $saleOrder = $this->sale_order->getSaleOrderById($saleOrderID);
                        $this->transactions->createDeclinedDepositTransaction($saleOrder, $loggedAdminUserId, Transactions::MANUAL);
                    }

                    $depositName = $this->input->post('depositName');
                    $amount = $this->input->post('depositAmount');
                    $bankTypeID = $this->input->post('bankTypeID');
                    $bankTypeName = $this->input->post('bankTypeName');

                    $defaultCurrency = $this->config->item('default_currency');
                    $player_promo_id = null;
                    $dwIp = $this->input->ip_address();
                    $geolocation = $this->utils->getGeoplugin($dwIp);
                    $fullname = $depositName;

                    $playerBankDetailsId = "WHERE TO GET THIS?";
                    $playerDepositMethod = "WHERE TO GET THIS?";

                    if (empty($error)) {
                        if ($this->utils->isEnabledFeature('create_sale_order_after_player_confirm')) {
                            $saleOrder = $this->sale_order->createFakeDepositOrder(Sale_order::PAYMENT_KIND_DEPOSIT,
                                    $bankTypeID, $playerId, $amount, $defaultCurrency,
                                    $player_promo_id, $dwIp, $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
                                    $playerBankDetailsId, null, null, null, Sale_order::STATUS_PROCESSING,
                                    $sub_wallet_id, null, null, $depositReferenceNo, $pendingDepositWalletType,
                                    $depositMethod, $this->utils->is_mobile(), null, $fullname, null, $playerDepositMethod);
                        } else {
                            $saleOrder = $this->sale_order->createDepositOrder(Sale_order::PAYMENT_KIND_DEPOSIT,
                                    $bankTypeID, $playerId, $amount, $defaultCurrency,
                                    $player_promo_id, $dwIp, $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
                                    $playerBankDetailsId, null, null, null, Sale_order::STATUS_PROCESSING,
                                    null, null, null, $depositReferenceNo, $pendingDepositWalletType,
                                    $depositMethod, $this->utils->is_mobile(), null, null, $fullname, null, $playerDepositMethod);
                        }

                        $loggedAdminUserId = method_exists($this->authentication, 'getUserId') ? $this->authentication->getUserId() : Users::SUPER_ADMIN_ID;
                        $this->transactions->createPendingDepositTransaction($saleOrder, $loggedAdminUserId, Transactions::MANUAL);
                    }

                    if (!empty($bankTypeID)) {
                        $paymentAccountDetails = $this->payment_account->getPaymentAccountDetails($bankTypeID);

                        $data['bankAccountName'] = $paymentAccountDetails->payment_account_name;
                        $data['bankAccountNumber'] = $paymentAccountDetails->payment_account_number;
                    }

                    $data['deposit_status'] = 'success';
                    $data['depositName'] = $depositName;
                    $data['depositAmount'] = $amount;
                    $data['bankTypeID'] = $bankTypeID;
                    $data['bankTypeName'] = $bankTypeName;
                    $data['secure_id'] = $saleOrder['secure_id'];

                    // $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, "Deposit Successful.");
                } catch (Exception $e) {
                    $data['deposit_status'] = 'error';
                    // $this->alertMessage(self::MESSAGE_TYPE_ERROR, "Deposit Error.");
                }
            }

            return $this->returnJsonResult($data);
        }
    }

    public function getAvailableAccount($playerId, $flag, $bankTypeId, $paymentAccountId=null){
        $this->load->model("payment_account");
        $paymentAccount = $this->payment_account->getAvailableAccount($playerId, $flag, $bankTypeId, false, $paymentAccountId);
        return $this->returnJsonResult($paymentAccount);
    }

    public function ajaxManualDeposit(){
        $initializeDeposit = $this->input->post('initializeDeposit');
        $depositAmount = $this->input->post('depositAmount');
        $payment_account_id = $this->input->post('payment_account_id');
        $playerBankDetailsId = $this->input->post('playerBankDetailsId');
        $playerId = $this->authentication->getPlayerId();
        $defaultCurrency = $this->config->item('default_currency');
        $secure_id = $this->input->post("secure_id");
        $deposit_time = $this->input->post("deposit_time");
        $deposit_time_out = $this->input->post("deposit_time_out");

        $direct_deposit = $this->input->post('direct_deposit');

        $dwIp = $this->input->ip_address();
        $geolocation = $this->utils->getGeoplugin($dwIp);
        $player_promo_id =null;
        $sub_wallet_id = null;
        $group_level_id = null;
        $depositDatetime = $this->utils->getNowForMysql();
        $depositReferenceNo = null;
        $pendingDepositWalletType  = null;

        $promo_cms_id=$this->input->post('promo_cms_id');

        $error=null;
        $player_promo_id =null;

        list($promo_cms_id, $promo_rules_id) = $this->process_promo_rules($playerId, $promo_cms_id, $depositAmount, $error,$sub_wallet_id);
        $promo_info['promo_rules_id']=$promo_rules_id;
        $promo_info['promo_cms_id']=$promo_cms_id;

        if($initializeDeposit){
            $saleOrder = $this->sale_order->createFakeDepositOrder(Sale_order::PAYMENT_KIND_DEPOSIT,
                    $payment_account_id, $playerId, $depositAmount, $defaultCurrency,
                    $player_promo_id, $dwIp, $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
                    $playerBankDetailsId, null, null, null, Sale_order::STATUS_PROCESSING,
                    $sub_wallet_id, $group_level_id, $depositDatetime, $depositReferenceNo, $pendingDepositWalletType,
                    null, $this->utils->is_mobile());

            return $this->returnJsonResult($saleOrder);
        }else{
            $data['depositRule'] = $this->group_level->getPlayerDepositRule($playerId);
            $minDeposit = $data['depositRule'][0]['minDeposit']; # TODO: REMOVE INDEX 0
            $maxDeposit = $data['depositRule'][0]['maxDeposit']; # TODO: REMOVE INDEX 0
            $maxDepositDaily=0;

            if ($depositAmount <= 0) {
                return $this->returnJsonResult(array('status' => 'error', 'msg' => lang('notify.39')));
            } else if ($depositAmount < $minDeposit) {
                return $this->returnJsonResult(array('status' => 'error', 'msg' => $this->utils->renderLang('notify.40', $minDeposit)));
            } else if ($maxDeposit>0) {
                if ($depositAmount > $maxDeposit) {
                    return $this->returnJsonResult(array('status' => 'error', 'msg' => $this->utils->renderLang('notify.74', $maxDeposit)));
                } elseif ($maxDepositDaily > 0) {
                    $playerTotalDailyDeposit = $this->transactions->sumDepositAmountToday($playerId);
                    if (($playerTotalDailyDeposit + $depositAmount) >= $maxDepositDaily) {
                        $message = $this->utils->renderLang('notify.74', $maxDepositDaily);
                        return $this->returnJsonResult(array('status' => 'error', 'msg' => $message));
                    }
                }else{

                    $saleOrder = $this->sale_order->createDepositOrder(Sale_order::PAYMENT_KIND_DEPOSIT,
                            $payment_account_id, $playerId, $depositAmount, $defaultCurrency,
                            $player_promo_id, $dwIp, $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
                            $playerBankDetailsId, null, null, null, Sale_order::STATUS_PROCESSING,
                            $sub_wallet_id, $group_level_id, $depositDatetime, $depositReferenceNo, $pendingDepositWalletType,
                            null, $this->utils->is_mobile(),null,$promo_info,null,null,Sale_order::PLAYER_DEPOSIT_METHOD_UNSPECIFIED,$secure_id, $deposit_time,
                            $deposit_time_out);

                    if($direct_deposit) {
                        return $this->returnJsonResult($saleOrder);
                    } else {
                        $message = array(
                                "status" => "success",
                                "msg" => lang('Thank you for your deposit Please check back again later.'),
                        );
                        return $this->returnJsonResult($message);
                    }

                }
            }else{
                $saleOrder = $this->sale_order->createDepositOrder(Sale_order::PAYMENT_KIND_DEPOSIT,
                            $payment_account_id, $playerId, $depositAmount, $defaultCurrency,
                            $player_promo_id, $dwIp, $geolocation['geoplugin_city'] . ',' . $geolocation['geoplugin_countryName'],
                            $playerBankDetailsId, null, null, null, Sale_order::STATUS_PROCESSING,
                            $sub_wallet_id, $group_level_id, $depositDatetime, $depositReferenceNo, $pendingDepositWalletType,
                            null, $this->utils->is_mobile(),null,$promo_info,null,null,Sale_order::PLAYER_DEPOSIT_METHOD_UNSPECIFIED);

                if($direct_deposit) {
                    return $this->returnJsonResult($saleOrder);
                } else {
                    $message = array(
                            "status" => "success",
                            "msg" => lang('Thank you for your deposit Please check back again later.'),
                    );
                    return $this->returnJsonResult($message);
                }
            }
        }
    }

    public function isValidDepositAmount() {

        $this->load->model(array('payment_account'));

        $playerId = $this->authentication->getPlayerId();

        $payment_account_id = $this->input->post('payment_account_id');
        $depositAmount = $this->input->post('depositAmount');

        $paymentAccount = $this->payment_account->getPaymentAccount($payment_account_id);

        if ($paymentAccount) {
            $paymentAccountMaxDeposit = $paymentAccount->max_deposit_daily;

            if ($paymentAccount->max_deposit_trans > 0 && $paymentAccount->max_deposit_trans < $paymentAccountMaxDeposit) {
                $paymentAccountMaxDeposit = $paymentAccount->max_deposit_trans;
            }

            $paymentAccountMinDeposit = $paymentAccount->min_deposit_trans;

        } else {
            $paymentAccountMinDeposit = 0;
            $paymentAccountMaxDeposit = $this->utils->getConfig('defaultMaxDepositDaily');
        }

        $depositRule = $this->group_level->getPlayerDepositRule($playerId);
        $data['depositRule'] = $depositRule;

        $depositRuleMinDeposit = isset($depositRule[0]['minDeposit']) ? $depositRule[0]['minDeposit'] : 0;
        $depositRuleMaxDeposit = isset($depositRule[0]['maxDeposit']) ? $depositRule[0]['maxDeposit'] : $this->utils->getConfig('defaultMaxDepositDaily');

        if ($paymentAccountMinDeposit > $depositRuleMinDeposit) {
            $minDeposit = $paymentAccountMinDeposit;
        } elseif ($depositRuleMinDeposit > 0) {
            $minDeposit = $depositRuleMinDeposit;
        } else {
            $minDeposit = 0;
        }

        if ($depositRuleMaxDeposit > 0 && $depositRuleMaxDeposit < $paymentAccountMaxDeposit) {
            $maxDeposit = $depositRuleMaxDeposit;
        } elseif ($paymentAccountMaxDeposit > 0) {
            $maxDeposit = $paymentAccountMaxDeposit;
        } else {
            $maxDeposit = 0;
        }

        if ($depositAmount <= 0) {
            // Deposit amount should not be 0
            return $this->returnJsonResult(array('status' => 'error', 'msg' => lang('notify.39')));
        }

        if ($depositAmount < $minDeposit) {
            // Minimum Deposit is $minDeposit
            return $this->returnJsonResult(array('status' => 'error', 'msg' => $this->utils->renderLang('notify.40', $minDeposit)));
        }

        if ($depositAmount > $maxDeposit) {
            // Maximum Deposit is $minDeposit
            return $this->returnJsonResult(array('status' => 'error', 'msg' => $this->utils->renderLang('notify.74', $maxDeposit)));
        }

        return $this->returnJsonResult(array('status' => 'valid'));
    }

    //for safety browser only
    public function safetyBrowserOnly(){
        //Array ( [bankTypeId] => 58 [deposit_from] => 461 [deposit_amount] => 10 )


        $callback= $_GET['callback'];
        $arr=explode("__",$callback);

        $url = site_url('player_center/autoDeposit3rdParty');
        //echo $url;
        echo  '<form id="form-deposit" action="'.$url.'" method="post" autocomplete="off">';
        foreach($arr as $key=>$val){

            list($field,$value)=explode("-",$val);

            switch($field){
                case "bankTypeId":
                    echo '<input type="hidden" name = "bankTypeId" value="'.$value.'">';
                    //echo "$field = $value <br/>";
                    break;
                case "deposit_from":
                    echo '<input type="hidden" name = "deposit_from" value="'.$value.'">';
                    //echo "$field = $value <br/>";
                    break;
                case "deposit_amount":
                    echo '<input type="hidden" name = "deposit_amount" value="'.$value.'">';
                    //echo "$field = $value <br/>";
                    break;
            }
        }

        echo '</form>';
        // SUBMIT FORM
        echo '<script type="text/javascript"> document.getElementById(\'form-deposit\').submit();</script>';

    }

}
////END OF FILE/////////