<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_logs_unsettle_20180802 extends CI_Migration {

    public function up() {

        $fields = array(

            'sync_index' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),

        );
        $this->dbforge->add_column('game_logs_unsettle', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('game_logs_unsettle', 'sync_index');
    }
}