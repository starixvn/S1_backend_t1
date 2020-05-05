<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_pt_v2_game_logs_20191105 extends CI_Migration {

	private $tableName = 'pt_v2_game_logs';

    public function up() {

        $fields = array(
			'is_valid_game_logs' => array(
				'type' => 'TINYINT(1)',
				'null' => false,
				'default' => 1
			),
        );

        if(!$this->db->field_exists('is_valid_game_logs', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('is_valid_game_logs', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'is_valid_game_logs');
        }
    }
}