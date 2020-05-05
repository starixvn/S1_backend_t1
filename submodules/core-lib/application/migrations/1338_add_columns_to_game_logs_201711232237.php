<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_game_logs_201711232237 extends CI_Migration {

    public function up() {
        $fields = array(
            'running_platform' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );

        $this->dbforge->add_column('game_logs', $fields);

    }

    public function down() {
        $this->dbforge->drop_column('game_logs', 'running_platform');
    }

}
