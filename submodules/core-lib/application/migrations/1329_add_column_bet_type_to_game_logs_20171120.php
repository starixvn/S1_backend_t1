<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_bet_type_to_game_logs_20171120 extends CI_Migration {

	public function up() {
        $field = array(
            'bet_type' => array(
                'type' => 'varchar',
                'null' => true,
                'constraint' => '100'
            ),
        );
        if (!$this->db->field_exists('bet_type', 'game_logs')) {
            $this->dbforge->add_column('game_logs', $field);
        }
	}

	public function down() {
        if ($this->db->field_exists('bet_type', 'game_logs')) {
            $this->dbforge->drop_column('game_logs', 'bet_type');
        }
	}

}