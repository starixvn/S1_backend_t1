<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_logs_20171110 extends CI_Migration {

	public function up() {
		foreach($this->fields() as $key => $field){
            if (!$this->db->field_exists($key, 'game_logs')) {
                $data = array($key => $field);
                $this->dbforge->add_column('game_logs', $data);
            }
        }
	}

	public function down() {
        foreach($this->fields() as $key => $field){
            if ($this->db->field_exists($key, 'game_logs')) {
                $this->dbforge->drop_column('game_logs', $key);
            }
        }
	}

    private function fields(){
        return $fields = array(
            'match_details' => array(
                'type' => 'varchar',
                'constraint' => '200',
                'null' => true,
            ),
            'match_type' => array(
                'type' => 'varchar',
                'constraint' => '100',
                'null' => true,
            ),
            'bet_info' => array(
                'type' => 'varchar',
                'constraint' => '100',
                'null' => true,
            ),
            'handicap' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );
    }
}