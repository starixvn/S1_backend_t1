<?php
/**
 * player_responsible_gaming_library.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 *
 * @property BaseController $CI
 * @property Internal_message $internal_message
 * @property CI_Form_validation $form_validation
 */
class Player_responsible_gaming_library {
    /* @var BaseController */
    public $CI;

    /* @var  Responsible_gaming */
    public $responsible_gaming;

    /* @var Responsible_gaming_history */
    public $responsible_gaming_history;

    public function __construct(){
        $this->CI =& get_instance();

        $this->CI->load->model(array('responsible_gaming', 'responsible_gaming_history', 'transactions', 'communication_preference_model'));
        $this->responsible_gaming = $this->CI->responsible_gaming;
        $this->responsible_gaming_history = $this->CI->responsible_gaming_history;
    }

    public function getSelfExclusionPeriodType(){
        $periodType = array(Responsible_gaming::PERIOD_TYPE_DAY => lang('Daily'),
            Responsible_gaming::PERIOD_TYPE_WEEK => lang('Weekly'),
            Responsible_gaming::PERIOD_TYPE_MONTH => lang('Monthly'),
        );
        return $periodType;
    }

    public function getSelfExclusionStatusType(){
        $statusType = array(Responsible_gaming::STATUS_REQUEST => lang('Requesting'),
            Responsible_gaming::STATUS_APPROVED => lang('Approved'),
            Responsible_gaming::STATUS_CANCELLED => lang('Cancelled'),
            Responsible_gaming::STATUS_COOLING_OFF => lang('rg.coolingoff'),
        );
        return $statusType;
    }

    public function getActiveResponsibleGamingSettings($player_id) {
        $data['respGameData']['self_exclusion_approval_day_cnt'] = $this->CI->operatorglobalsettings->getSettingValue('self_exclusion_approval_day_cnt');
        $data['respGameData']['cool_off_approval_day_cnt'] = $this->CI->operatorglobalsettings->getSettingValue('cool_off_approval_day_cnt');
        $data['respGameData']['deposit_limit_approval_day_cnt'] = $this->CI->operatorglobalsettings->getSettingValue('deposit_limit_approval_day_cnt');
        $data['respGameData']['loss_limit_approval_day_cnt'] = $this->CI->operatorglobalsettings->getSettingValue('loss_limit_approval_day_cnt');
        $data['respGameData']['player_reactication_day_cnt'] = $this->CI->operatorglobalsettings->getSettingValue('player_reactication_day_cnt');
        $data['respGameData']['disable_and_hide_wagering_limits'] = $this->CI->operatorglobalsettings->getSettingValue('disable_and_hide_wagering_limits');
        $data['wageringLimitsExists'] = FALSE;
        $data['depositLimitsExists'] = FALSE;
        $data['deposit_limits_day_options'] = $this->CI->utils->getConfig('deposit_limits_day_options');
        $data['wagering_limits_day_options'] = $this->CI->utils->getConfig('wagering_limits_day_options');

        $data['depositLimits_latest_amount'] = lang('N/A');
        $data['depositLimits_latest_date_from'] = lang('N/A');
        $data['depositLimits_latest_date_to'] = lang('N/A');
        $data['wageringLimits_latest_amount'] = lang('N/A');
        $data['wageringLimits_latest_date_from'] = lang('N/A');
        $data['wageringLimits_latest_date_to'] = lang('N/A');

        $responsible_gaming = $this->responsible_gaming->getData($player_id);
        if(empty($responsible_gaming)){
            return $data;
        }

        $check_status = [Responsible_gaming::STATUS_REQUEST, Responsible_gaming::STATUS_APPROVED];
        $deposit_limits_allow_status = [Responsible_gaming::STATUS_APPROVED, Responsible_gaming::STATUS_EXPIRED, Responsible_gaming::STATUS_CANCELLED];
        $wagering_limits_allow_status = [Responsible_gaming::STATUS_APPROVED, Responsible_gaming::STATUS_EXPIRED, Responsible_gaming::STATUS_CANCELLED];
        $deposit_limits_last_date_from =  null;
        $wagering_limits_last_date_from = null;

        foreach ($responsible_gaming as $key) {
            //check if self exclusion request already exists
            if (($key->type == Responsible_gaming::SELF_EXCLUSION_TEMPORARY &&
                    in_array($key->status,$check_status)) ||
                ($key->type == Responsible_gaming::SELF_EXCLUSION_PERMANENT &&
                    in_array($key->status,$check_status))) {
                $data['selfExclusionRequestExists'] = true;
            }

            //check if cooloff request already exists
            if ($key->type == Responsible_gaming::COOLING_OFF && in_array($key->status,$check_status)) {
                $data['coolingOffRequestExists'] = true;

                $data['coolingOffRemainHourToStart'] = 0;
                $coolingOffRemainHourToStart = (strtotime($key->date_from) - strtotime("now")) / (60*60);
                if($coolingOffRemainHourToStart > 0){
                    $data['coolingOffRemainHourToStart'] = floor($coolingOffRemainHourToStart);
                }

            }

            //check if time reminders already exists
            if ($key->type == Responsible_gaming::TIMER_REMINDERS) {
                $data['timeReminders'] = $key->period_cnt;
                $data['timerReminderStatus'] = $key->status;
            }

            //check if session limits already exists
            if ($key->type == Responsible_gaming::SESSION_LIMITS) {
                $data['sessionLimits'] = $key->period_cnt;
                $data['sessionLimitsStatus'] = $key->status;
            }

            //check if deposit limit already exists
            if ($key->type == Responsible_gaming::DEPOSIT_LIMITS && in_array($key->status,$deposit_limits_allow_status)) {
                //if have next cycle request
                if($data['depositLimitsExists'] && in_array($key->status,$check_status)){
                    $data['depositLimits_latest_amount'] = $key->amount;
                    $data['depositLimits_latest_date_from'] = $key->date_from;
                    $data['depositLimits_latest_date_to'] = $key->date_to;
                    continue;
                }

                if($key->status == Responsible_gaming::STATUS_APPROVED){
                    $data['depositLimitsAmount'] = $key->amount;
                    $data['deposit_period_cnt'] = $key->period_cnt;
                    $periodTypeArr = array("1"=>"daily","2"=>"weekly","3"=>"monthly");
                    $data['deposit_periodType'] = $periodTypeArr[$key->period_type];
                    $data['deposit_periodTypeId'] = $key->period_type;

                    $now = new DateTime('now');
                    $from = new DateTime($key->date_from);
                    $to = new DateTime($key->date_to);
                    if($now->getTimestamp() >= $from->getTimestamp() && $now->getTimestamp() <= $to->getTimestamp()){
                        $data['limit_deposit_amount'] = true;
                        $data['depositLimitsExists'] = true;
                        $data['depositLimitResetPeriodStart'] = $key->date_from;
                        $data['depositLimitResetPeriodEnd'] = $key->date_to;

                        $player_total_deposit_this_cycle = $this->CI->transactions->getPlayerTotalDeposits($player_id,$key->date_from,$this->CI->utils->getNowForMysql());
                        $usableAmount = $data['depositLimitsAmount'] - $player_total_deposit_this_cycle;
                        $data['depositLimitRemainTotalAmount'] = ($usableAmount <= 0) ? 0 : $usableAmount;
                    }else{
                        //不在时间内的都expire
                        if($this->responsible_gaming->setDepositLimitsToExpire($key->id,$player_id)){
                            $this->responsible_gaming_history->addDepositLimitsAutoExpiredRecord($key->id,$key->status);
                        }

                    }
                }

            }

            //check if loss limit already exists
            if ($key->type == Responsible_gaming::LOSS_LIMITS && $key->status !=  Responsible_gaming::STATUS_CANCELLED) {
                $data['lossLimitsAmount'] = $key->amount;
                $data['loss_period_cnt'] = $key->period_cnt;
                $periodTypeArr = array("1"=>"daily","2"=>"weekly","3"=>"monthly");
                $data['loss_periodType'] = $periodTypeArr[$key->period_type];
                $data['loss_periodTypeId'] = $key->period_type;
                $looseLimitData = $this->responsible_gaming->getData($player_id, $key->type, Responsible_gaming::STATUS_APPROVED);
                if(!is_null($looseLimitData)){
                    $looseLimitData = array_pop($looseLimitData);
                }
                if ($looseLimitData){
                    $now = new DateTime('now');
                    $from = new DateTime($looseLimitData->date_from);
                    $to = clone $from;
                    switch($key->period_type) {
                        case 1 : //daily
                            $to->add(new DateInterval('P1D'));
                            break;
                        case 2 : //weekly
                            $to->add(new DateInterval('P7D'));
                            break;
                        case 3 : //monthly
                            $to->add(new DateInterval('P1M'));
                            break;
                    }
                    //$to = new DateTime($looseLimitData->date_to);
                    if ( $now->getTimestamp() >= $from->getTimestamp() && $now->getTimestamp() <= $to->getTimestamp()){
                        $data['limit_loose_amount'] = true;
                    }
                }
            }

            //check if wagering limits already exists
            if ($key->type == Responsible_gaming::WAGERING_LIMITS && in_array($key->status,$wagering_limits_allow_status)) {
                //if have next cycle request
                if($data['wageringLimitsExists'] && in_array($key->status,$check_status)){
                    $data['wageringLimits_latest_amount'] = $key->amount;
                    $data['wageringLimits_latest_date_from'] = $key->date_from;
                    $data['wageringLimits_latest_date_to'] = $key->date_to;
                    continue;
                }

                if($key->status == Responsible_gaming::STATUS_APPROVED){
                    $data['wageringLimitsAmount'] = $key->amount;
                    $data['wagering_limit_period_cnt'] = $key->period_cnt;
                    $periodTypeArr = array("1"=>"daily","2"=>"weekly","3"=>"monthly");
                    $data['wagering_limit_periodType'] = $periodTypeArr[$key->period_type];
                    $data['wagering_limit_periodTypeId'] = $key->period_type;

                    $now = new DateTime('now');
                    $from = new DateTime($key->date_from);
                    $to = new DateTime($key->date_to);
                    if ( $now->getTimestamp() >= $from->getTimestamp() && $now->getTimestamp() <= $to->getTimestamp()){
                        $data['wageringLimitsExists'] = true;
                        $data['wageringLimitResetPeriodStart'] = $key->date_from;
                        $data['wageringLimitResetPeriodEnd'] = $key->date_to;

                        $player_total_transfer_this_cycle = $this->CI->transactions->getPlayerTotalTransferBalance($player_id,$key->date_from,$this->CI->utils->getNowForMysql());
                        $usableAmount = $data['wageringLimitsAmount'] - $player_total_transfer_this_cycle;
                        $data['wageringLimitRemainTotalAmount'] = ($usableAmount <= 0) ? 0 : $usableAmount;
                    }else{
                        //不在时间内的都expire
                        if($this->responsible_gaming->setWageringLimitsToExpire($key->id,$player_id)){
                            $this->responsible_gaming_history->addWageringLimitsAutoExpiredRecord($key->id,$key->status);
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function autoApproveImmediately($insert_id, $player_id, $old_status, $type){
        if(in_array($type,[Responsible_gaming::SELF_EXCLUSION_TEMPORARY,Responsible_gaming::SELF_EXCLUSION_PERMANENT])){
            if($this->responsible_gaming->setSelfExclusionToApprove($insert_id, $player_id)){
                $this->responsible_gaming_history->addSelfExclusionAutoApprovedRecord($insert_id, $old_status);
            }
        }elseif($type == Responsible_gaming::COOLING_OFF){
            if($this->responsible_gaming->setCoolOffToApprove($insert_id, $player_id)){
                $this->responsible_gaming_history->addCoolOffAutoApprovedRecord($insert_id, $old_status);
            }
        }elseif($type == Responsible_gaming::DEPOSIT_LIMITS){
            if($this->responsible_gaming->setDepositLimitsToApprove($insert_id, $player_id)){
                $this->responsible_gaming_history->addDepositLimitsAutoApprovedRecord($insert_id, $old_status);
            }
        }elseif($type == Responsible_gaming::WAGERING_LIMITS){
            if($this->responsible_gaming->setWageringLimitsToApprove($insert_id, $player_id)){
                $this->responsible_gaming_history->addWageringLimitsAutoApprovedRecord($insert_id, $old_status);
            }
        }

    }

    /**
     * Calculate values for date_from and date_to columns
     * date_from: now + (self_exclusion_approval_day_cnt) days
     * date_to  : date_from + (mon) months
     *
     * @param   int     $mon        Exclusion length in months
     * @return  array(string)       [ date_from, date_to ]
     */
    public function calc_date_from_to($duration, $sys_set_key = NULL, $unit = 'D', $start_on = NULL) {
        $unit_map = [ 'month' => 'M', 'm' => 'M', 'day' => 'D', 'd' => 'D' ];

        // Calculate date_from
        if(!is_null($start_on)){
            $date_from = new DateTime($start_on);
        }else{
            $date_from = new DateTime();
        }

        // Calculate date_to
        $unit = strtolower($unit);
        $unit_interval = isset($unit_map[$unit]) ? $unit_map[$unit] : 'D';
        $date_to = clone $date_from;
        $date_to->add(new DateInterval("P{$duration}{$unit_interval}"));

        $result = [
            'date_from' => NULL,
            'date_to' => NULL
        ];

        switch($sys_set_key){
            case 'self_exclusion_approval_day_cnt':
                $se_approv_days = (int) $this->CI->utils->getOperatorSetting($sys_set_key);
                $date_from->add(new DateInterval("P{$se_approv_days}D"));

                // Calculate cooling_off_to
                $se_cooling_off_days = (int) $this->CI->utils->getOperatorSetting('self_exclusion_cooling_off_day_cnt');
                $cooling_off_to = clone $date_to;
                $cooling_off_to->add(new DateInterval("P{$se_cooling_off_days}{$unit_interval}"));
                $cooling_off_to_str = $this->CI->utils->formatDateTimeForMysql($cooling_off_to);

                $result['cooling_off_to'] = $cooling_off_to_str;
                break;
            default:
                break;

        }
        $result['date_from'] = $this->CI->utils->formatDateTimeForMysql($date_from);
        $result['date_to'] = $this->CI->utils->formatDateTimeForMysql($date_to);
        return $result;
    }

    public function RequestSelfExclusionTemporary($player_id, $period_cnt, $admin_id = null){
        $dates = $this->calc_date_from_to((int)$period_cnt, 'self_exclusion_approval_day_cnt', 'day');

        $data = array(
            "player_id" => $player_id,
            "type" => Responsible_gaming::SELF_EXCLUSION_TEMPORARY,
            "period_cnt" => $period_cnt,
            "date_to" => $dates['date_to'],
            "date_from" => $dates['date_from'],
            "cooling_off_to" => $dates['cooling_off_to'],
            "period_type" => Responsible_gaming::PERIOD_TYPE_DAY,
            "created_at" => $this->CI->utils->getNowForMysql(),
            "status" => Responsible_gaming::STATUS_REQUEST,
        );

        if(!empty($admin_id)){
            $data['admin_id'] = $admin_id;
        }

        $insert_id = $this->responsible_gaming->insertData($data);

        if(!$this->CI->utils->isEnabledFeature('disable_responsible_gaming_auto_approve')){
            $this->autoApproveImmediately($insert_id, $player_id, $data['status'], Responsible_gaming::SELF_EXCLUSION_TEMPORARY);

            if($this->CI->utils->isEnabledFeature('enable_communication_preferences')) {
                $this->CI->communication_preference_model->updateCommunicationPreferenceWithSelfExclusion($player_id, 'Auto-Off All Communication Preference By Auto-Approve Self Exclusion Temporary id '.$insert_id);
            }
        }

        return ($insert_id) ? $insert_id : FALSE;
    }

    public function ExpireSelfExclusionTemporaryCoolingOffPlayer($player_id, $type, $status, $notes){
        $availableResponsibleGamingData = $this->responsible_gaming->getAvailableResponsibleGamingData($player_id, $type, $status);
        if(empty($availableResponsibleGamingData)){
            $this->utils->debug_log('==================Get AvailableResponsibleGaming Data==Failed=================',$availableResponsibleGamingData);
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('Expire Failed!');
            return $result;
        }

        $responsible_gaming = $availableResponsibleGamingData['0'];
        if(!$this->responsible_gaming->setSelfExclusionToExpire($responsible_gaming->id, $responsible_gaming->player_id, $notes)){
            $this->utils->debug_log('======================================add expire data==Failed========================================',$responsible_gaming->id);
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('Expire Failed!');
            return $result;
        }

        $this->responsible_gaming_history->addSelfExclusionExpiredRecord($responsible_gaming->id, $responsible_gaming->status, $notes);
        $this->CI->utils->unblockPlayerInGameAndWebsite($player_id);

        $result['status'] = BaseController::MESSAGE_TYPE_SUCCESS;
        $result['message'] = lang('You\'ve successfully expired!');
        return $result;
    }

    public function RequestSelfExclusionPermanent($player_id, $period_cnt, $admin_id = null){
        $dates = $this->calc_date_from_to((int)$period_cnt, 'self_exclusion_approval_day_cnt', 'day');

        $data = array(
            "player_id" => $player_id,
            "type" => Responsible_gaming::SELF_EXCLUSION_PERMANENT,
            "date_from" => $dates['date_from'],
            "period_type" => Responsible_gaming::PERIOD_TYPE_PERMANENT,
            "created_at" => $this->CI->utils->getNowForMysql(),
            "status" => Responsible_gaming::STATUS_REQUEST,
        );

        if(!empty($admin_id)){
            $data['admin_id'] = $admin_id;
        }

        $insert_id = $this->responsible_gaming->insertData($data);

        if(!$this->CI->utils->isEnabledFeature('disable_responsible_gaming_auto_approve')){
            $this->autoApproveImmediately($insert_id, $player_id, $data['status'], Responsible_gaming::SELF_EXCLUSION_PERMANENT);

            if($this->CI->utils->isEnabledFeature('enable_communication_preferences')) {
                $this->CI->communication_preference_model->updateCommunicationPreferenceWithSelfExclusion($player_id, 'Auto-Off All Communication Preference By Auto-Approve Self Exclusion Permanent id '.$insert_id);
            }
        }

        return ($insert_id) ? $insert_id : FALSE;
    }

    public function RequestCoolOff($player_id, $period_cnt, $admin_id = null){
        $cool_off_approval_day_cnt = $this->CI->operatorglobalsettings->getSetting('cool_off_approval_day_cnt');
        $currentDate = new DateTime();
        $datetime_from_add = new DateInterval('P'.$cool_off_approval_day_cnt->value.'D');
        $currentDate->add($datetime_from_add);
        $date_from = $currentDate->format("Y-m-d H:i:s");
        $status = Responsible_gaming::STATUS_REQUEST;
        if ($cool_off_approval_day_cnt->value) {
            $dateInterval = 'P' . ($period_cnt + $cool_off_approval_day_cnt->value) . 'D';
        } else {
            $dateInterval = 'P' . $period_cnt . 'D';
        }
        $currentDate = new DateTime();
        $datetime_to_add = new DateInterval($dateInterval); //add nth day to current date
        $currentDate->add($datetime_to_add);
        $date_to = $currentDate->format("Y-m-d H:i:s");
        $type = Responsible_gaming::COOLING_OFF;

        $data = array(
            "player_id" => $player_id,
            "type" => $type,
            "period_cnt" => $period_cnt,
            "period_type" => Responsible_gaming::PERIOD_TYPE_DAY,
            "created_at" => $this->CI->utils->getNowForMysql(),
            "date_from" => $date_from,
            "date_to" => $date_to,
            "status" => $status,
        );

        if(!empty($admin_id)){
            $data['admin_id'] = $admin_id;
        }

        $insert_id = $this->responsible_gaming->insertData($data);

        if(!$this->CI->utils->isEnabledFeature('disable_responsible_gaming_auto_approve')){
            $this->autoApproveImmediately($insert_id, $player_id, $status, $type);

            if($this->CI->utils->isEnabledFeature('enable_communication_preferences')) {
                $this->CI->communication_preference_model->updateCommunicationPreferenceWithSelfExclusion($player_id, 'Auto-Off All Communication Preference By Auto-Approve CoolOff id '.$insert_id);
            }
        }

        return ($insert_id) ? $insert_id : FALSE;
    }

    public function RequestDepositLimit($player_id, $amount, $period_cnt, $admin_id = null){
        $status = Responsible_gaming::STATUS_REQUEST;
        $type = Responsible_gaming::DEPOSIT_LIMITS;
        $dates = $this->calc_date_from_to((int)$period_cnt, 'deposit_limit_approval_day_cnt', 'day');
        $currentDateTime = $this->CI->utils->getNowForMysql();

        $data = array(
            "player_id" => $player_id,
            "type" => $type,
            "period_cnt" => $period_cnt,
            "period_type" => Responsible_gaming::PERIOD_TYPE_DAY,
            "date_from" => $dates['date_from'],
            "date_to" => $dates['date_to'],
            "status" => $status,
            "created_at" => $currentDateTime,
            "amount" => $amount,
        );

        if(!empty($admin_id)){
            $data['admin_id'] = $admin_id;
        }

        $result = [
            'status' => BaseController::MESSAGE_TYPE_SUCCESS,
            'message' => NULL
        ];

        $now_id = null;
        $responsible_gaming = $this->responsible_gaming->getData($player_id, $type,Responsible_gaming::STATUS_APPROVED);

        if(!empty($responsible_gaming)){ // check duplicate request
            $next_cycle_request = [
                'id' => 0,
                'status' => null
            ];
            foreach($responsible_gaming as $item){
                if($item->status == Responsible_gaming::STATUS_APPROVED && $item->date_from > $currentDateTime){
                    $next_cycle_request = [
                        'id' => $item->id,
                        'status' => $item->status
                    ];
                }
            }

            foreach($responsible_gaming as $key){
                $result = $this->checkDuplicateDepositLimit($key, $player_id, $amount, $period_cnt, $next_cycle_request, $data);

                if(FALSE === $result){
                    continue;
                }

                return $result;
            }
        }

        // new request
        $insert_id = $this->responsible_gaming->insertData($data);

        if(!$this->CI->utils->isEnabledFeature('disable_responsible_gaming_auto_approve')){
            $this->autoApproveImmediately($insert_id, $player_id, $status, $type);
        }

        if($insert_id){
            $result['insert_id'] = $insert_id;
            $result['message'] = lang('You\'ve successfully sent request!');
        }else{
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('error.default.db.message');
        }

        return $result;
    }

    public function checkDuplicateDepositLimit($deposit_limit_request_entry, $player_id, $amount, $period_cnt, $next_cycle_request, $data){
        $now = new DateTime('now');
        $from = new DateTime($deposit_limit_request_entry->date_from);
        $to = new DateTime($deposit_limit_request_entry->date_to);

        $result = [
            'status' => BaseController::MESSAGE_TYPE_SUCCESS,
            'message' => NULL
        ];

        if( $now->getTimestamp() < $from->getTimestamp() || $now->getTimestamp() > $to->getTimestamp()){
            return FALSE;
        }

        $rpgId = $deposit_limit_request_entry->id;

        //less than 1 hour
        if($this->CI->utils->isTimeoutNow($deposit_limit_request_entry->date_to,'60','-')){
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('Cannot apply again within one hour before the expiration');
            return $result;
        }

        // set amount to zero
        if($amount == 0){
            $result = $this->depositLimitsAmountEqualToZero($rpgId, $next_cycle_request['id'], $deposit_limit_request_entry->status, $deposit_limit_request_entry->amount, $player_id, $amount);
            return $result;
        }

        $date_from = strtotime($deposit_limit_request_entry->date_to)+1;
        $new_date_from = date ("Y-m-d H:i:s", intval($date_from));
        $dates = $this->calc_date_from_to((int)$period_cnt, 'deposit_limit_approval_day_cnt', 'day',$new_date_from);

        $data['date_from'] = $dates['date_from'];
        $data['date_to'] = $dates['date_to'];

        //period different
        if($period_cnt != $deposit_limit_request_entry->period_cnt){
            if(!empty($next_cycle_request['id'])){
                if($this->responsible_gaming->setPlayerResponsibleGamingToAutoCancel($next_cycle_request['id'],$player_id)){
                    $this->responsible_gaming_history->addResponsibleGamingAutoCanceledRecord($next_cycle_request['id'],$next_cycle_request['status']);
                }
            }
            $result = $this->newDepositLimits($data, $player_id); //create one
            return $result;
        }

        $player_total_deposit_this_cycle = $this->CI->transactions->getPlayerTotalDeposits($player_id, $deposit_limit_request_entry->date_from, $this->CI->utils->getNowForMysql());
        $this_cycle_remain_amount = $deposit_limit_request_entry->amount - $player_total_deposit_this_cycle;
        $new_total_amount = $player_total_deposit_this_cycle + $amount;

        //the same period below
        if($amount >= $this_cycle_remain_amount) {
            if (!empty($next_cycle_request['id'])) {
                if($this->responsible_gaming->setPlayerResponsibleGamingToAutoCancel($next_cycle_request['id'],$player_id)){
                    $this->responsible_gaming_history->addResponsibleGamingAutoCanceledRecord($next_cycle_request['id'],$next_cycle_request['status']);
                }
            }
            $result = $this->newDepositLimits($data, $player_id); //create one
            return $result;
        } else {
            //effetive immediately
            if (!$this->responsible_gaming->updateDepositLimitsCurrentAmount($rpgId, $player_id, $new_total_amount)) {
                $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
                $result['message'] = lang('Update Deposit Limits Failed!');
            } else {
                //update current request record
                $this->responsible_gaming_history->updateDepositLimitsAmountRecord($rpgId, $deposit_limit_request_entry->status, $deposit_limit_request_entry->amount, $new_total_amount);
                $result['status'] = BaseController::MESSAGE_TYPE_SUCCESS;
                $result['message'] = lang('Effective Immediately');
            }
            return $result;
        }
    }

    public function depositLimitsAmountEqualToZero($rpgId, $next_cycle_id, $origin_status, $origin_amount, $player_id, $new_amount){
        if(!empty($next_cycle_id)){
            if($this->responsible_gaming->setPlayerResponsibleGamingToAutoCancel($next_cycle_id,$player_id)){
                $this->responsible_gaming_history->addResponsibleGamingAutoCanceledRecord($next_cycle_id,$origin_status);
            }
        }

        if(!$this->responsible_gaming->updateDepositLimitsCurrentAmount($rpgId, $player_id, $new_amount)){
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('Update Deposit Limits Failed!');
        }else{
            //update current request record
            $this->responsible_gaming_history->updateDepositLimitsAmountRecord($rpgId, $origin_status, $origin_amount, $new_amount);
            $result['status'] = BaseController::MESSAGE_TYPE_SUCCESS;
            $result['message'] = lang('Update Deposit Limits Successfully!');
        }
        return $result;
    }

    public function newDepositLimits($data, $player_id){
        $insert_id = $this->responsible_gaming->insertData($data);
        if(!$this->CI->utils->isEnabledFeature('disable_responsible_gaming_auto_approve')){
            $this->autoApproveImmediately($insert_id, $player_id, Responsible_gaming::STATUS_REQUEST, Responsible_gaming::DEPOSIT_LIMITS);
        }

        if (!empty($insert_id)) {
            $result['status'] = BaseController::MESSAGE_TYPE_SUCCESS;
            $result['message'] = lang('Set New Deposit Limits Successfully!');
        } else {
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('Set New Deposit Limits Failed!');
        }
        return $result;
    }

    public function AutoSubscribeDepositLimit($player_id, $period_cnt, $origin_date_to, $amount){
        $data = [
            "player_id" => $player_id,
            "type" => Responsible_gaming::DEPOSIT_LIMITS,
            "period_cnt" => $period_cnt,
            "period_type" => Responsible_gaming::PERIOD_TYPE_DAY,
            "status" => Responsible_gaming::STATUS_REQUEST,
            "created_at" => $this->CI->utils->getNowForMysql(),
            "amount" => $amount,
        ];

        $date_from = strtotime($origin_date_to)+1;
        $new_date_from = date ("Y-m-d H:i:s", intval($date_from));
        $dates = $this->calc_date_from_to((int)$period_cnt, 'deposit_limit_approval_day_cnt', 'day',$new_date_from);

        $data['date_from'] = $dates['date_from'];
        $data['date_to'] = $dates['date_to'];

        $insert_id = $this->responsible_gaming->insertData($data);
        $this->responsible_gaming_history->addDepositLimitsAutoSubscribeRecord($insert_id, $data['status']);
        if(!$this->CI->utils->isEnabledFeature('disable_responsible_gaming_auto_approve')){
            $this->autoApproveImmediately($insert_id, $player_id, Responsible_gaming::STATUS_REQUEST, Responsible_gaming::DEPOSIT_LIMITS);
        }

        return ($insert_id) ? $insert_id : FALSE;
    }

    public function RequestWageringLimit($player_id, $amount, $period_cnt, $admin_id = null){
        $status = Responsible_gaming::STATUS_REQUEST;
        $type = Responsible_gaming::WAGERING_LIMITS;
        $dates = $this->calc_date_from_to((int)$period_cnt, 'wagering_limit_approval_day_cnt', 'day');
        $currentDateTime = $this->CI->utils->getNowForMysql();

        $data = array(
            "player_id" => $player_id,
            "type" => $type,
            "period_cnt" => $period_cnt,
            "period_type" => Responsible_gaming::PERIOD_TYPE_DAY,
            "date_from" => $dates['date_from'],
            "date_to" => $dates['date_to'],
            "status" => $status,
            "created_at" => $currentDateTime,
            "amount" => $amount,
        );

        if(!empty($admin_id)){
            $data['admin_id'] = $admin_id;
        }

        $result = [
            'status' => BaseController::MESSAGE_TYPE_SUCCESS,
            'message' => NULL
        ];

        $now_id = null;
        $responsible_gaming = $this->responsible_gaming->getData($player_id,$type,Responsible_gaming::STATUS_APPROVED);

        if(!empty($responsible_gaming)){
            $next_cycle_request = [
                'id' => 0,
                'status' => null
            ];
            foreach($responsible_gaming as $item){
                if($item->status == Responsible_gaming::STATUS_APPROVED && $item->date_from > $currentDateTime){
                    $next_cycle_request = [
                        'id' => $item->id,
                        'status' => $item->status
                    ];
                }
            }

            foreach($responsible_gaming as $key){
                $result = $this->checkDuplicateWageringLimit($key, $player_id, $amount, $period_cnt, $next_cycle_request, $data);

                if(FALSE === $result){
                    continue;
                }

                return $result;
            }
        }

        $insert_id = $this->responsible_gaming->insertData($data);

        if(!$this->CI->utils->isEnabledFeature('disable_responsible_gaming_auto_approve')){
            $this->autoApproveImmediately($insert_id, $player_id, $status, $type);
        }

        if($insert_id){
            $result['insert_id'] = $insert_id;
            $result['message'] = lang('You\'ve successfully set wagering limits!');
        }else{
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('error.default.db.message');
        }

        return $result;
    }

    public function checkDuplicateWageringLimit($wagering_limit_request, $player_id, $amount, $period_cnt, $next_cycle_request, $data){
        $now = new DateTime('now');
        $from = new DateTime($wagering_limit_request->date_from);
        $to = new DateTime($wagering_limit_request->date_to);

        if( $now->getTimestamp() < $from->getTimestamp() || $now->getTimestamp() > $to->getTimestamp()){
            return FALSE;
        }

        $rpgId = $wagering_limit_request->id;

        //less than 1 hour
        if($this->CI->utils->isTimeoutNow($wagering_limit_request->date_to,'60','-')){
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('Cannot apply again within one hour before the expiration');
            return $result;
        }

        // set amount to zero
        if($amount == 0){
            $result = $this->wageringLimitsAmountEqualToZero($rpgId, $next_cycle_request['id'], $wagering_limit_request->status, $wagering_limit_request->amount, $player_id, $amount);
            return $result;
        }

        $date_from = strtotime($wagering_limit_request->date_to)+1;
        $new_date_from = date ("Y-m-d H:i:s", intval($date_from));
        $dates = $this->calc_date_from_to((int)$period_cnt, 'deposit_limit_approval_day_cnt', 'day',$new_date_from);

        $data['date_from'] = $dates['date_from'];
        $data['date_to'] = $dates['date_to'];

        //period different
        if($period_cnt != $wagering_limit_request->period_cnt){
            if(!empty($next_cycle_request['id'])){
                if($this->responsible_gaming->setPlayerResponsibleGamingToAutoCancel($next_cycle_request['id'],$player_id)){
                    $this->responsible_gaming_history->addResponsibleGamingAutoCanceledRecord($next_cycle_request['id'],$next_cycle_request['status']);
                }
            }
            $result = $this->newWageringLimits($data, $player_id); //create one
            return $result;
        }

        $player_total_transfer_this_cycle = $this->CI->transactions->getPlayerTotalTransferBalance($player_id, $wagering_limit_request->date_from, $this->CI->utils->getNowForMysql());
        $this_cycle_remain_amount = $wagering_limit_request->amount - $player_total_transfer_this_cycle;
        $new_total_amount = $player_total_transfer_this_cycle + $amount;

        //the same period below
        if($amount >= $this_cycle_remain_amount){
            if(!empty($next_cycle_request['id'])){
                if($this->responsible_gaming->setPlayerResponsibleGamingToAutoCancel($next_cycle_request['id'],$player_id)){
                    $this->responsible_gaming_history->addResponsibleGamingAutoCanceledRecord($next_cycle_request['id'],$next_cycle_request['status']);
                }
            }
            $result = $this->newWageringLimits($data, $player_id); //create one
            return $result;
        }else{
            //effetive immediately
            if(!$this->responsible_gaming->updateWageringLimitsCurrentAmount($rpgId, $player_id, $new_total_amount)){
                $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
                $result['message'] = lang('Update Wagering Limits Failed!');
            }else{
                $this->responsible_gaming_history->updateWageringLimitsAmountRecord($rpgId, $wagering_limit_request->status, $wagering_limit_request->amount, $new_total_amount);
                $result['status'] = BaseController::MESSAGE_TYPE_SUCCESS;
                $result['message'] = lang('Effective Immediately');
            }
            return $result;
        }
    }

    public function wageringLimitsAmountEqualToZero($rpgId, $next_cycle_id, $origin_status, $origin_amount, $player_id, $new_amount){
        if(!empty($next_cycle_id)){
            if($this->responsible_gaming->setPlayerResponsibleGamingToAutoCancel($next_cycle_id,$player_id)){
                $this->responsible_gaming_history->addResponsibleGamingAutoCanceledRecord($next_cycle_id,$origin_status);
            }
        }

        if(!$this->responsible_gaming->updateWageringLimitsCurrentAmount($rpgId, $player_id, $new_amount)){
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('Update Wagering Limits Failed!');
        }else{
            //update current request record
            $this->responsible_gaming_history->updateWageringLimitsAmountRecord($rpgId, $origin_status, $origin_amount, $new_amount);
            $result['status'] = BaseController::MESSAGE_TYPE_SUCCESS;
            $result['message'] = lang('Update Wagering Limits Successfully!');
        }
        return $result;
    }

    public function newWageringLimits($data, $player_id){
        $insert_id = $this->responsible_gaming->insertData($data);
        if(!$this->CI->utils->isEnabledFeature('disable_responsible_gaming_auto_approve')){
            $this->autoApproveImmediately($insert_id, $player_id, Responsible_gaming::STATUS_REQUEST, Responsible_gaming::WAGERING_LIMITS);
        }

        if (!empty($insert_id)) {
            $result['status'] = BaseController::MESSAGE_TYPE_SUCCESS;
            $result['message'] = lang('Set New Wagering Limits Successfully!');
        } else {
            $result['status'] = BaseController::MESSAGE_TYPE_ERROR;
            $result['message'] = lang('Set New Wagering Limits Failed!');
        }
        return $result;
    }

    public function AutoSubscribeWageringLimit($player_id, $period_cnt, $origin_date_to, $amount){
        $data = [
            "player_id" => $player_id,
            "type" => Responsible_gaming::WAGERING_LIMITS,
            "period_cnt" => $period_cnt,
            "period_type" => Responsible_gaming::PERIOD_TYPE_DAY,
            "status" => Responsible_gaming::STATUS_REQUEST,
            "created_at" => $this->CI->utils->getNowForMysql(),
            "amount" => $amount,
        ];

        $date_from = strtotime($origin_date_to)+1;
        $new_date_from = date ("Y-m-d H:i:s", intval($date_from));
        $dates = $this->calc_date_from_to((int)$period_cnt, 'deposit_limit_approval_day_cnt', 'day',$new_date_from);

        $data['date_from'] = $dates['date_from'];
        $data['date_to'] = $dates['date_to'];

        $insert_id = $this->responsible_gaming->insertData($data);
        $this->responsible_gaming_history->addWageringLimitsAutoSubscribeRecord($insert_id, $data['status']);
        if(!$this->CI->utils->isEnabledFeature('disable_responsible_gaming_auto_approve')){
            $this->autoApproveImmediately($insert_id, $player_id, Responsible_gaming::STATUS_REQUEST, Responsible_gaming::WAGERING_LIMITS);
        }

        return ($insert_id) ? $insert_id : FALSE;
    }

    public function fetchResponsibleGamingDataForSBE($player_id){
        $this->CI->load->model(array('sale_order','operatorglobalsettings'));
        $data = [];
        $data['selfexclusionFlag'] = FALSE;
        $data['selfexclusionFlag'] = FALSE;
        $data['timeoutFlag'] = FALSE;
        $data['depositlimitFlag'] = FALSE;
        $data['wageringLimitsFlag'] = FALSE;
        $data['hide_comm_pref_btn_tse_approve'] = FALSE;
        $data['hide_comm_pref_btn_pse_approve'] = FALSE;
        $data['hide_comm_pref_btn_co_approve'] = FALSE;
        $data['hide_comm_pref_btn'] = FALSE;

        $data['responsible_gaming'] = $this->responsible_gaming->processPlayerResponsibleGaming($player_id);
        $data['deposit_limits_day_options'] = $this->CI->utils->getConfig('deposit_limits_day_options');
        $data['wagering_limits_day_options'] = $this->CI->utils->getConfig('wagering_limits_day_options');
        $data['disable_and_hide_wagering_limits'] = (int)$this->CI->operatorglobalsettings->getSettingValue('disable_and_hide_wagering_limits');

        if(isset($data['responsible_gaming'])){
            $data['periodType'] = $this->getSelfExclusionPeriodType();
            $data['statusType'] = $this->getSelfExclusionStatusType();

            if(!empty($data['responsible_gaming']['temp_self_exclusion'])){
                $data['selfexclusionFlag'] = TRUE;
                $currentDateTime = strtotime($this->CI->utils->getNowForMysql());
                $cooling_off_to = strtotime($data['responsible_gaming']['temp_self_exclusion']->cooling_off_to);
                $data['hide_comm_pref_btn_tse_approve'] = ($data['responsible_gaming']['temp_self_exclusion']->status) == Responsible_gaming::STATUS_APPROVED;
                if($currentDateTime >= $cooling_off_to){
                    $data['responsible_gaming']['use_uplift_instead_of_cancel_btn'] = TRUE;
                }else{
                    $data['responsible_gaming']['use_uplift_instead_of_cancel_btn'] = FALSE;
                }
            }

            if(!empty($data['responsible_gaming']['permanent_self_exclusion'])){
                $data['selfexclusionFlag'] = TRUE;
                $data['hide_comm_pref_btn_pse_approve'] = ($data['responsible_gaming']['permanent_self_exclusion']->status) == Responsible_gaming::STATUS_APPROVED;
            }

            if(!empty($data['responsible_gaming']['cool_off'])){
                $data['timeoutFlag'] = TRUE;
                $data['hide_comm_pref_btn_co_approve'] = ($data['responsible_gaming']['cool_off']->status) == Responsible_gaming::STATUS_APPROVED;
            }

            if($data['hide_comm_pref_btn_tse_approve'] || $data['hide_comm_pref_btn_pse_approve'] || $data['hide_comm_pref_btn_co_approve']){
                //hide communication preference btn
                $data['hide_comm_pref_btn'] = TRUE;
            }

            if(!empty($data['responsible_gaming']['deposit_limits'])) {
                $data['depositlimitFlag'] = TRUE;
                $usable_amount = $this->getDepositLimit($player_id);
                if ($usable_amount['status']) {
                    $data['depositLimitRemainTotalAmount'] = $usable_amount['value'];
                } else {
                    $data['depositLimitRemainTotalAmount'] = 0;
                }
            }

            if(!empty($data['responsible_gaming']['wagering_limits'])){
                $data['wageringLimitsFlag'] = TRUE;
                $usable_amount = $this->getWageringLimit($player_id);
                if($usable_amount['status']){
                    $data['wageringLimitRemainTotalAmount'] = $usable_amount['value'];
                }else{
                    $data['wageringLimitRemainTotalAmount'] = 0;
                }
            }
        }

        $this->CI->load->vars($data);
    }

    /**
     * @param $player_id
     * @return bool|mixed
     */
    public function getActiveDepositLimits($player_id){
        $result = FALSE;
        $responsible_gaming = $this->responsible_gaming->getData($player_id, Responsible_gaming::DEPOSIT_LIMITS,Responsible_gaming::STATUS_APPROVED);
        $currentDatetime = $this->CI->utils->getNowForMysql();
        if(!empty($responsible_gaming)){
            foreach($responsible_gaming as $key){
                if($key->status == Responsible_gaming::STATUS_APPROVED && $currentDatetime > $key->date_from && $currentDatetime < $key->date_to){
                    $result['data'] = $key;
                }
            }
        }
        return $result;
    }

    /**
     * @param $playerId,$transfer_amount
     * @return bool|mixed
     */

    public function inDepositLimits($player_id, $transfer_amount){
        if(FALSE === $depositLimits = $this->getActiveDepositLimits($player_id)){
            return FALSE;
        }

        $currentTime = $this->CI->utils->getNowForMysql();
        $last_date_from = null;
        $depositLimitsData = null;
        $depositLimitsData = $depositLimits['data'];

        $player_total_deposit_this_cycle = $this->CI->transactions->getPlayerTotalDeposits($player_id, $depositLimitsData->date_from, $currentTime);
        if(!empty($player_total_deposit_this_cycle)){
            $sum_total_deposit = $player_total_deposit_this_cycle + $transfer_amount;
        }else{
            $sum_total_deposit = $transfer_amount;
        }

        if($sum_total_deposit > $depositLimitsData->amount){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * @param $playerId
     * @return bool|mixed
     */
    public function getActiveWageringLimits($player_id){
        $result = FALSE;
        $responsible_gaming = $this->responsible_gaming->getData($player_id, Responsible_gaming::WAGERING_LIMITS,Responsible_gaming::STATUS_APPROVED);
        $currentDatetime = $this->CI->utils->getNowForMysql();
        if(!empty($responsible_gaming)){
            foreach($responsible_gaming as $key){
                if($key->status == Responsible_gaming::STATUS_APPROVED && $currentDatetime > $key->date_from && $currentDatetime < $key->date_to){
                    $result['data'] = $key;
                }
            }
        }
        return $result;
    }

    public function inWageringLimits($player_id, $transfer_amount){
        if(FALSE === $wageringLimits = $this->getActiveWageringLimits($player_id)){
            return FALSE;
        }

        $currentTime = $this->CI->utils->getNowForMysql();
        $last_date_from = null;
        $wageringLimitsData = null;
        $wageringLimitsData = $wageringLimits['data'];

        $player_total_deposit_this_cycle = $this->CI->transactions->getPlayerTotalTransferBalance($player_id, $wageringLimitsData->date_from, $currentTime);
        if(!empty($player_total_deposit_this_cycle)){
            $sum_total_deposit = $player_total_deposit_this_cycle + $transfer_amount;
        }else{
            $sum_total_deposit = $transfer_amount;
        }

        if($sum_total_deposit > $wageringLimitsData->amount){
            return TRUE;
        }

        return FALSE;
    }

    public function displayDepositLimitHint(){
        $this->CI->load->model(array('sale_order'));
        $this->CI->load->library(array('authentication'));

        $result = FALSE;
        $player_Id = $this->CI->authentication->getPlayerId();
        $depositLimit = $this->getDepositLimit($player_Id);
        if($depositLimit['status'] && ($depositLimit['value'] == 0)){
            $result = '<div class="responsible_gaming deposit_limit_acitvie_warning_text"><span class="text_content">'.lang("Deposit Limit Active").'</span></div>';
            return $result;
        }

        return $result;
    }

    //GET DEPOSIT LIMIT REMAIN BUDGET
    public function getDepositLimit($player_Id){
        $depositLimit['status'] = FALSE;

        $now = new DateTime('now');
        $depositLimitData =  $this->responsible_gaming->getData($player_Id, Responsible_gaming::DEPOSIT_LIMITS,Responsible_gaming::STATUS_APPROVED);
        if(is_array($depositLimitData)){
            $limitamount = 0;
            foreach($depositLimitData as $key){
                if($key->status == Responsible_gaming::STATUS_APPROVED){
                    $from = new DateTime($key->date_from);
                    $to = new DateTime($key->date_to);

                    if( $now->getTimestamp() >= $from->getTimestamp() && $now->getTimestamp() <= $to->getTimestamp()){
                        $player_total_deposit_this_cycle = $this->CI->transactions->getPlayerTotalDeposits($player_Id,$key->date_from,$this->CI->utils->getNowForMysql());
                        $usableAmount = $key->amount - $player_total_deposit_this_cycle;
                        $limitamount = ($usableAmount <= 0) ? 0 : $usableAmount ;

                        $depositLimit['value'] = $limitamount;
                        $depositLimit['status'] = TRUE;//have data
                    }
                }
            }
        }
        return $depositLimit;
    }

    //GET WAGERING LIMIT REMAIN BUDGET
    public function getWageringLimit($player_Id){
        $wageringLimit['status'] = FALSE;

        $now = new DateTime('now');
        $wageringLimitData =  $this->responsible_gaming->getData($player_Id, Responsible_gaming::WAGERING_LIMITS,Responsible_gaming::STATUS_APPROVED);
        if(is_array($wageringLimitData)){
            $limitamount = 0;
            foreach($wageringLimitData as $key){
                if($key->status == Responsible_gaming::STATUS_APPROVED){
                    $from = new DateTime($key->date_from);
                    $to = new DateTime($key->date_to);

                    if( $now->getTimestamp() >= $from->getTimestamp() && $now->getTimestamp() <= $to->getTimestamp()){
                        $player_total_transfer_this_cycle = $this->CI->transactions->getPlayerTotalTransferBalance($player_Id,$key->date_from,$this->CI->utils->getNowForMysql());
                        $usableAmount = $key->amount - $player_total_transfer_this_cycle;
                        $limitamount = ($usableAmount <= 0) ? 0 : $usableAmount ;

                        $wageringLimit['value'] = $limitamount;
                        $wageringLimit['status'] = TRUE;//have data
                    }
                }
            }
        }
        return $wageringLimit;
    }
}