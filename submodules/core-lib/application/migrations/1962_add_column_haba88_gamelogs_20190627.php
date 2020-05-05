<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_haba88_gamelogs_20190627 extends CI_Migration {
    public function up() {
        $fields = array(
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('haba88_game_logs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('haba88_game_logs', 'md5_sum');
    }
}
