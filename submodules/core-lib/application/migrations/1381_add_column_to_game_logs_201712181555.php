<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_logs_201712181555 extends CI_Migration {

    private $game_logs = 'game_logs';
    private $game_logs_unsettle = 'game_logs_unsettle';

    public function up() {
        $fields = array(
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
        );

        $this->dbforge->add_column($this->game_logs, $fields);
        $this->dbforge->add_column($this->game_logs_unsettle, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->game_logs, 'created_at');
        $this->dbforge->drop_column($this->game_logs_unsettle, 'created_at');
    }
}
