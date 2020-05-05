<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_datetime_to_betmaster_game_logs_201707120046 extends CI_Migration {

    public function up() {
        $fields = array(
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )

        );
        $this->dbforge->add_column('betmaster_game_logs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('betmaster_game_logs', 'created_at');
        $this->dbforge->drop_column('betmaster_game_logs', 'updated_at');
    }
}