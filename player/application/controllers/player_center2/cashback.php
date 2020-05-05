<?php
require_once 'PlayerCenterBaseController.php';

/**
 * Lists promotion
 */
class Cashback extends PlayerCenterBaseController {
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
    }

    protected function load_view_vars($player_id_input = null, $manually_cal_cashback_on_admin = 'false') {
        $this->load->model(['group_level', 'cashback_request', 'total_cashback_player_game']);

        $manually_cal_cashback_on_admin = $manually_cal_cashback_on_admin == 'true';

        if ( !empty($player_id_input) && $this->isLoggedAdminUser() ) {
            $player_id = $player_id_input;
            $this->load->vars("is_adminuser", true);
            $this->load->vars("player_id_input", $player_id_input);
        }else{
            $player_id = $this->authentication->getPlayerId();
        }
        $this->load->vars('amount', '');

        $this->load->vars('can_user_cashback', $this->cashback_request->checkPermissionForCashback($player_id));


        $last_approved_cashback_request = $this->cashback_request->getLastApprovedCashbackRequest($player_id);

        if (empty($last_approved_cashback_request)) {
            $last_approved_cashback_request = new stdclass;
            $last_approved_cashback_request->request_datetime = '';
            $last_approved_cashback_request->request_amount = '';
        }

        $this->load->vars('last_approved_cashback_request', $last_approved_cashback_request);
        $this->load->vars('last_pending_cashback_request', $this->cashback_request->getLastPendingCashbackRequest($player_id));

        $cashbackSettings = $this->group_level->getCashbackSettings();
        $payTimeHour = $cashbackSettings->payTimeHour;

        $payDateTime = new DateTime($this->utils->getTodayForMysql().' '.$payTimeHour);
        $disable_start_datetime = $this->utils->formatDateTimeForMysql($payDateTime->modify('-10 minutes'));
        $payDateTime = new DateTime($this->utils->getTodayForMysql().' '.$payTimeHour);
        $disable_end_datetime = $this->utils->formatDateTimeForMysql($payDateTime->modify('+20 minutes'));

        $now = $this->utils->getNowForMysql();
        //debug
        // $now = '2017-05-29 15:00:00';
        $this->load->vars('disable_start_datetime', $disable_start_datetime);
        $this->load->vars('disable_end_datetime', $disable_end_datetime);
        $this->load->vars(['disable_request' => $now >= $disable_start_datetime && $now <= $disable_end_datetime]);
        $this->load->vars('disable_hint', lang('disable.cashback.request.hint').' '.$disable_start_datetime.' - '.$disable_end_datetime);

        $this->load->vars('day_list', [
            'today' => lang('Today').' '.$this->utils->getTodayForMysql(),
            'yesterday' => lang('Yesterday').' '.$this->utils->getYesterdayForMysql(),
            'both' => lang('Today And Yesterday')
        ]);
        $this->load->vars('game_platforms', $this->utils->getGameSystemMap());

        $last_pending_cashback_request = $this->cashback_request->getLastCashbackRequestByStatus($player_id, Cashback_request::PENDING);
        $this->load->vars('cashback_request', $last_pending_cashback_request);

        $this->load->vars('time_start', $last_approved_cashback_request->request_datetime ? $last_approved_cashback_request->request_datetime : $this->utils->getYesterdayForMysql() . ' 00:00:00');
        $this->load->vars('time_end', $now);

        $this->load->vars('player', $this->player_model->getPlayerInfoDetailById($player_id));

        $this->load->vars('player_id', $player_id);
    }

    public function index() {
        $this->load_view_vars();
        # Templates
        $this->loadTemplate();

        # Custom
        $this->template->add_js('/common/js/player_center/promotions.js');

        # Template-related variables
        $data['activeNav'] = 'cashback';
        $data['content_template'] = 'default_with_menu.php';

        # Render
        $this->template->write_view('main_content', $this->templateName . '/cashback/index', $data);
        $this->template->render();
    }
}
