<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ibc_game_logs_20171205 extends CI_Migration {

    public function up() {
        $fields = array(
            'odds_type' => array(
                'type' => 'int',
                'null' => true,
                'constraint' => '11'
            )
        );
        $this->dbforge->add_column('ibc_game_logs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ibc_game_logs', 'odds_type');
    }
}