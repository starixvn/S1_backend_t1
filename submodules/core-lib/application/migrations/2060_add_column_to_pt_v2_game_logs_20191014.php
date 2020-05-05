<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_pt_v2_game_logs_20191014 extends CI_Migration {

	private $tableName = 'pt_v2_game_logs';

    public function up() {

        $fields = array(
            'game_server_session_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('game_server_session_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('game_server_session_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'game_server_session_id');
        }
    }
}