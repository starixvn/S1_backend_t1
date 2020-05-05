<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_sync_imslots_games_031020161256 extends CI_Migration {

    public function up() {
        $this->load->model('imslots_game_logs');
        if(isset($this->imslots_game_logs)){
	        $this->imslots_game_logs->syncTtg();
	        $this->imslots_game_logs->syncGos();
	        $this->imslots_game_logs->syncPrg();
        }
    }

    public function down() {
    }
}
