<?php
require_once 'AjaxBaseController.php';

class Account_history extends AjaxBaseController {
    public function __construct(){
        parent::__construct();

        $this->load->model(array('report_model'));
    }

    public function player_games_history($playerId = null){

        $this->load->model(array('report_model'));
        $request = $this->input->post();
        $request['extra_search'][] = ['name' => 'by_game_flag', 'value' =>1];

        $playerId = $this->load->get_var('playerId');


        $is_export = false;

        $result = $this->report_model->player_gamesHistory($request, $playerId, $is_export);

        $this->returnJsonResult($result);
    }
}