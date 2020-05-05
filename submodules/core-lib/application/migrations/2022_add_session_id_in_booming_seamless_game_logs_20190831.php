<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_session_id_in_booming_seamless_game_logs_20190831 extends CI_Migration {

	private $tableName = 'boomingseamless_game_logs';

    public function up() {

        $fields = array(
            'session_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('session_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('session_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'session_id');
        }
    }
}