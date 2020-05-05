<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_lucky_game_game_logs_20190713 extends CI_Migration {

    public function up() {

        $fields = array(
            'starttime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'endtime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'showpage' => array(
                'type' => 'VARCHAR',
                'constraint' => '500',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('lucky_game_game_logs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('lucky_game_game_logs', 'starttime');
        $this->dbforge->drop_column('lucky_game_game_logs', 'endtime');
        $this->dbforge->drop_column('lucky_game_game_logs', 'showpage');
    }
}