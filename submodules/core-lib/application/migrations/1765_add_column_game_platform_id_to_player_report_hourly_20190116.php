<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_game_platform_id_to_player_report_hourly_20190116 extends CI_Migration {

    private $tableName = 'player_report_hourly';

    public function up() {
        $field = array(
           'game_platform_id' => array(
                'type' => 'INT',
                'null'=> true
            ),
        );

        if(!$this->db->field_exists('game_platform_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $field);
        }

    }

    public function down() {
        if($this->db->field_exists('game_platform_id', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'game_platform_id');
        }
    }
}
