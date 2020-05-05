<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_logs_unsettled_20171128 extends CI_Migration {

	public function up() {
        $fields = array(
            'bet_type' => array(
                'type' => 'varchar',
                'null' => true,
                'constraint' => '100'
            ),
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
        $this->dbforge->add_column('game_logs_unsettle', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('game_logs_unsettle', 'bet_type');
        $this->dbforge->drop_column('game_logs_unsettle', 'match_details');
        $this->dbforge->drop_column('game_logs_unsettle', 'match_type');
        $this->dbforge->drop_column('game_logs_unsettle', 'bet_info');
        $this->dbforge->drop_column('game_logs_unsettle', 'handicap');
    }
}