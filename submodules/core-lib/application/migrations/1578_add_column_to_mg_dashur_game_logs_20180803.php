<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_mg_dashur_game_logs_20180803 extends CI_Migration {

    public function up() {

        $fields = array(
            'item_id' => array(
                'type' => 'BIGINT',
                'null' => true,
            )
        );
        $this->dbforge->add_column('mg_dashur_game_logs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('mg_dashur_game_logs', 'item_id');
    }
}