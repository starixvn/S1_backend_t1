<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// require_once dirname(__FILE__) . '/../models/game_description/game_description_kuma.php';

class Migration_add_new_kuma_games_201612281410 extends CI_Migration {

   // use game_description_kuma;
   //  public function __construct() {
   //      parent::__construct();

   //      // $this->config->set_item('app_debug_log', APPPATH . 'logs/sync.log');

   //      $this->config->set_item('print_log_to_console', true);

   //      $default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');
   //      set_time_limit($default_sync_game_logs_max_time_second);

   //      $this->load->model(['player_model']);

   //  }

    public function up() {

        // $self=$this;

        // //===========only one time====start==========================

        // //only one time
        // $success= $this->player_model->dbtransOnly(function() use($self){
        //     $self->adjust_old_game_types_kuma();
        //     return true;
        // });

        // if(!$success){
        //     throw new Exception("Failed update ".$funcName);
        // }
        // //===========only one time===end===========================

        // //start update/insert game type and description
        // $funcName='sync_game_description_kuma';

        // $cnt=0;
        // $self=$this;
        // $success= $this->player_model->dbtransOnly(function() use($self, $funcName, &$cnt){

        //     return $self->$funcName($cnt);

        // });

        // $this->utils->debug_log('update game '.$funcName, $success, $cnt);

        // if(!$success){
        //     throw new Exception("Failed update ".$funcName);
        // }

    }

    public function down() {
        //only one time
        // $this->delete_inserted_game();
    }
}