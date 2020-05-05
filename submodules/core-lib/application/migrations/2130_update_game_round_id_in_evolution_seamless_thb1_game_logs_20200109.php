<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_update_game_round_id_in_evolution_seamless_thb1_game_logs_20200109 extends CI_Migration
{
    private $tableName = "evolution_seamless_thb1_game_logs";

    public function up()
    {
        $update_fields = array(
            'game_round_id' => array(
                'name' => 'game_round_id',
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
        );

            
        if($this->db->field_exists('game_round_id', $this->tableName)) {

            $this->dbforge->modify_column($this->tableName, $update_fields); 
            
        }

    }

    public function down()
    {

    }
}