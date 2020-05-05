<?php
require_once 'PlayerCenterBaseController.php';

class Responsible_game extends PlayerCenterBaseController{
    /* @var Player_responsible_gaming_library $player_responsible_gaming_library */
    public $player_responsible_gaming_library;

    public function __construct(){
        parent::__construct();
        $this->load->helper('url');

        $this->load->library(['player_responsible_gaming_library']);

        $this->preloadResponsibleGameVars();
        // $this->load->vars('content_template', 'responsible_gaming.php'); # response gameing content template
    }

    private function preloadResponsibleGameVars(){
        $playerId = $this->load->get_var('playerId');
        $this->load->vars('responsegame', $this->player_responsible_gaming_library->getActiveResponsibleGamingSettings($playerId));
        $this->load->vars('currency', $this->utils->getCurrentCurrency());
    }

    public function index(){
        if(!$this->utils->isEnabledFeature('responsible_gaming')){
            redirect('/');
            return;
        }

        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }

        $data = [];

        # Template-related variables
        $data['activeNav'] = 'responsible_gaming';
        $data['content_template'] = 'default_with_menu.php';

        # Templates
        $this->loadTemplate();
        $this->template->append_function_title(lang('Responsible Gaming'));

        # Custom
        $this->template->add_js('/resources/js/validator.js');
        $this->template->add_js('/resources/third_party/jquery-validate/1.6.0/jquery.validate.min.js');

        // echo  $this->templateName;exit;
        # Render
        $this->template->write_view('main_content', $this->templateName . '/responsiblegame/responsible_gaming', $data);
        $this->template->render();
    }

    public function postSelfExclusion() {
        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }

        $player_id = $this->authentication->getPlayerId();

        $type = $this->input->post('selfExclusionType');
        $period_cnt = $this->input->post('tempPeriodCount');

        if(($type == Responsible_gaming::SELF_EXCLUSION_TEMPORARY) && empty($period_cnt)){
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = lang('Please Select a Period');
            $this->alertMessage($status, $message);
            return redirect('player_center2/responsible_game');
        }

        if($type == Responsible_gaming::SELF_EXCLUSION_TEMPORARY){
            $result = $this->player_responsible_gaming_library->RequestSelfExclusionTemporary($player_id, $period_cnt);
        }else{
            $result = $this->player_responsible_gaming_library->RequestSelfExclusionPermanent($player_id, $period_cnt);
        }

        if($result){
            $status = BaseController::MESSAGE_TYPE_SUCCESS;
            $message = lang('You\'ve successfully sent request!');
        }else{
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = lang('error.default.db.message');
        }

        $this->alertMessage($status, $message);

        redirect('player_center2/responsible_game');
    }

    public function postCoolOff() {
        if (!$this->authentication->isLoggedIn()) {
            return $this->goPlayerLogin();
        }

        $player_id = $this->authentication->getPlayerId();

        $period_cnt = $this->input->post('coolOffPeriodCount');

        if(empty($period_cnt)){
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = lang('Please Select a Period');
            $this->alertMessage($status, $message);
            return redirect('player_center2/responsible_game');
        }

        $result = $this->player_responsible_gaming_library->RequestCoolOff($player_id, $period_cnt);

        if($result){
            $status = BaseController::MESSAGE_TYPE_SUCCESS;
            $message = lang('You\'ve successfully sent request!');
        }else{
            $status = BaseController::MESSAGE_TYPE_ERROR;
            $message = lang('error.default.db.message');
        }

        $this->alertMessage($status, $message);

        redirect('player_center2/responsible_game');
    }

    public function postDepositLimits(){
        $player_id = $this->authentication->getPlayerId();
        $amount = $this->input->post('depositLimitsAmount');
        $period_cnt = $this->input->post('depositLimitsPeriodCnt');

        if(!isset($amount)){
            $message = lang('cashier.enterAmount');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            return redirect('player_center2/responsible_game');
        }

        if($amount < 0){
            $message = lang('pay.finalAmtPlayerReceiveStatus');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            return redirect('player_center2/responsible_game');
        }

        $result = $this->player_responsible_gaming_library->RequestDepositLimit($player_id, $amount, $period_cnt);

        if(!is_array($result)){
            $result = [
                'status' => BaseController::MESSAGE_TYPE_ERROR,
                'message' => lang('error.default.db.message')
            ];
        }

        $this->alertMessage($result['status'], $result['message']);

        redirect('player_center2/responsible_game');
    }

    public function postWageringLimits() {
        $player_id = $this->authentication->getPlayerId();
        $amount = $this->input->post('wageringLimitsAmount');
        $period_cnt = $this->input->post('wageringLimitsPeriodCount');

        if(!isset($amount)){
            $message = lang('cashier.enterAmount');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            return redirect('player_center2/responsible_game');
        }

        //amount can't be zero
        if($amount < 0){
            $message = lang('pay.finalAmtPlayerReceiveStatus');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
            return redirect('player_center2/responsible_game');
        }

        $result = $this->player_responsible_gaming_library->RequestWageringLimit($player_id, $amount, $period_cnt);

        if(!is_array($result)){
            $result = [
                'status' => BaseController::MESSAGE_TYPE_ERROR,
                'message' => lang('error.default.db.message')
            ];
        }

        $this->alertMessage($result['status'], $result['message']);

        redirect('player_center2/responsible_game');
    }

    /**
     * @TODO Refactor
     */
    public function postTimeReminders() {
        $player_id = $this->authentication->getPlayerId();
        $period_cnt = $this->input->post('timeReminderPeriodCount');

        $responsible_gaming = $this->responsible_gaming->getData($player_id);
        foreach ($responsible_gaming as $key) {
            if ($key->type == Responsible_gaming::TIMER_REMINDERS) {
                $data = array(
                    "player_id" => $player_id,
                    "type" => Responsible_gaming::TIMER_REMINDERS,
                    "period_cnt" => $period_cnt,
                    "period_type" => Responsible_gaming::PERIOD_TYPE_MINUTES,
                    "updated_at" => $this->utils->getNowForMysql(),
                    "status" => Responsible_gaming::STATUS_APPROVED,
                );

                if ($this->responsible_gaming->updateResponsibleGamingData($data)) {
                    $message = lang('You\'ve successfully updated the time reminders!');
                    $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
                } else {
                    $message = lang('Update Failed!');
                    $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                }


                redirect('player_center2/responsible_game');
            }
        }

        $data = array(
            "player_id" => $player_id,
            "type" => Responsible_gaming::TIMER_REMINDERS,
            "period_cnt" => $period_cnt,
            "period_type" => Responsible_gaming::PERIOD_TYPE_MINUTES,
            "created_at" => $this->utils->getNowForMysql(),
            "status" => Responsible_gaming::STATUS_APPROVED,
        );

        $this->responsible_gaming->insertData($data);

        $message = lang('You\'ve successfully set time reminders!');
        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
        redirect('player_center2/responsible_game');
    }

    /**
     * @TODO Refactor
     */
    public function postSessionLimits() {
        $this->load->model(array('responsible_gaming'));
        $player_id = $this->authentication->getPlayerId();
        $period_cnt = $this->input->post('sessionLimitPeriodCount');

        $responsible_gaming = $this->responsible_gaming->getData($player_id);
        if(!empty($responsible_gaming)){
            foreach ($responsible_gaming as $key) {
                if ($key->type == Responsible_gaming::SESSION_LIMITS) {
                    $data = array(
                        "player_id" => $player_id,
                        "type" => Responsible_gaming::SESSION_LIMITS,
                        "period_cnt" => $period_cnt,
                        "period_type" => Responsible_gaming::PERIOD_TYPE_MINUTES,
                        "date_from" => $this->utils->getNowForMysql(),
                        "updated_at" => $this->utils->getNowForMysql(),
                        "status" => Responsible_gaming::STATUS_APPROVED,
                    );

                    if ($this->responsible_gaming->updateResponsibleGamingData($data)) {
                        $message = lang('You\'ve successfully updated the session limits!');
                        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
                    } else {
                        $message = lang('Update Failed!');
                        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                    }

                    redirect('player_center2/responsible_game');
                }
            }
        }

        $data = array(
            "player_id" => $player_id,
            "type" => Responsible_gaming::SESSION_LIMITS,
            "period_cnt" => $period_cnt,
            "period_type" => Responsible_gaming::PERIOD_TYPE_MINUTES,
            "created_at" => $this->utils->getNowForMysql(),
            "status" => Responsible_gaming::STATUS_APPROVED,
        );

        $this->responsible_gaming->insertData($data);

        $message = lang('You\'ve successfully set session limits!');
        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
        redirect('player_center2/responsible_game');
    }

    /**
     * @TODO Refactor
     */
    public function postLossLimits() {
        $this->load->model(array('responsible_gaming','operatorglobalsettings'));
        $player_id = $this->authentication->getPlayerId();
        $amount = $this->input->post('lossLimitsAmount');
        $period_cnt = $this->input->post('lossLimitsReactivationPeriodCnt');
        $period_type = $this->input->post('periodType');

        $loss_limit_approval_day_cnt = $this->operatorglobalsettings->getSetting('loss_limit_approval_day_cnt');
        $currentDate = new DateTime();
        $datetime_from_add = new DateInterval('P'.$loss_limit_approval_day_cnt->value.'D');
        $currentDate->add($datetime_from_add);
        $date_from = $currentDate->format("Y-m-d H:i");

        $responsible_gaming = $this->responsible_gaming->getData($player_id);
        if(!empty($responsible_gaming)){
            foreach ($responsible_gaming as $key) {
                if ($key->type == Responsible_gaming::LOSS_LIMITS) {
                    $data = array(
                        "player_id" => $player_id,
                        "type" => Responsible_gaming::LOSS_LIMITS,
                        "amount" => $amount,
                        "period_cnt" => $period_cnt,
                        "period_type" => $period_type,
                        "date_from" => $date_from,
                        "updated_at" => $this->utils->getNowForMysql(),
                        "status" => Responsible_gaming::STATUS_REQUEST,
                    );

                    if ($this->responsible_gaming->updateResponsibleGamingData($data)) {
                        $message = lang('You\'ve successfully updated the loss limits!');
                        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
                    } else {
                        $message = lang('Update Failed!');
                        $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
                    }

                    redirect('player_center2/responsible_game');
                }
            }
        }

        $data = array(
            "player_id" => $player_id,
            "type" => Responsible_gaming::LOSS_LIMITS,
            "amount" => $amount,
            "period_cnt" => $period_cnt,
            "period_type" => $period_type,
            "created_at" => $this->utils->getNowForMysql(),
            "date_from" => $date_from,
            "status" => Responsible_gaming::STATUS_REQUEST,
        );

        $this->responsible_gaming->insertData($data);

        $message = lang('You\'ve successfully set loss limits!');
        $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
        redirect('player_center2/responsible_game');
    }

    /**
     * @TODO Refactor
     */
    public function cancelPlayerLimit($type) {
        $this->load->model(array('responsible_gaming'));
        $player_id = $this->authentication->getPlayerId();

        if($type == Responsible_gaming::DEPOSIT_LIMITS){
            $data = array(
                "player_id" => $player_id,
                "type" => Responsible_gaming::DEPOSIT_LIMITS,
                "updated_at" => $this->utils->getNowForMysql(),
                "status" => Responsible_gaming::STATUS_CANCELLED,
            );
            $message = lang('You\'ve successfully cancelled the deposit limits!');
        }elseif($type == Responsible_gaming::LOSS_LIMITS){
            $data = array(
                "player_id" => $player_id,
                "type" => Responsible_gaming::LOSS_LIMITS,
                "updated_at" => $this->utils->getNowForMysql(),
                "status" => Responsible_gaming::STATUS_CANCELLED,
            );
            $message = lang('You\'ve successfully cancelled the loss limits!');
        }

        if ($this->responsible_gaming->updateResponsibleGamingData($data)) {
            $this->alertMessage(self::MESSAGE_TYPE_SUCCESS, $message);
        } else {
            $message = lang('Update Failed!');
            $this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
        }

        redirect('player_center2/responsible_game');
    }

    /**
     * @TODO Refactor
     */
    public function timeRemindersWindow() {
        $this->load->model('responsible_gaming');
        $data['timeReminder'] = $this->responsible_gaming->getData($this->authentication->getPlayerId(), Responsible_gaming::TIMER_REMINDERS);
        // $this->template->write('timeReminder', @reset($timeReminder)->period_cnt);
        $this->load->view('iframe/cashier/view_time_reminder', $data);
    }

    /**
     * @TODO Refactor
     */
    public function setStartTimeForTimerReminder($playerId){
        $this->load->model(array('responsible_gaming'));
        $data = array(
            "player_id" => $playerId,
            "type" => Responsible_gaming::TIMER_REMINDERS,
            "date_from" => $this->utils->getNowForMysql(),
            "updated_at" => $this->utils->getNowForMysql(),
        );

        if ($this->responsible_gaming->updateResponsibleGamingData($data)) {
            return $this->returnJsonResult(array("success"=>true));
        }else{
            return $this->returnJsonResult(array("success"=>false));
        }
    }
}