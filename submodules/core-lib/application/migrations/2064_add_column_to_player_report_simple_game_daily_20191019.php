<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_report_simple_game_daily_20191019 extends CI_Migration {

    private $tableName = 'player_report_simple_game_daily';

    public function up() {

        $fields = array(
            'agent_id' => array(
                'type' => 'int',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('agent_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('agent_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'agent_id');
        }
    }
}