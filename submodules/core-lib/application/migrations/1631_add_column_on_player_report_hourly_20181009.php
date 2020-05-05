<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_on_player_report_hourly_20181009 extends CI_Migration {

	private $tableName = 'player_report_hourly';

	public function up() {
        $field = array(
            'manual_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
        );

        if(!$this->db->field_exists('manual_bonus', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $field);
        }
	}

	public function down() {
        if($this->db->field_exists('manual_bonus', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'manual_bonus');
        }
	}
}