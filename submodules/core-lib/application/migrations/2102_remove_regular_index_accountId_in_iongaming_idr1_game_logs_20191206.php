<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_remove_regular_index_accountId_in_iongaming_idr1_game_logs_20191206 extends CI_Migration
{
    private $tableName = "iongaming_idr1_game_logs";


    public function up(){

        if($this->db->table_exists($this->tableName)){

            $this->load->model("player_model");

            #remove regular index accountId
            $this->player_model->dropIndex($this->tableName,"accountId");
        }
    }

    public function down(){

    }
}