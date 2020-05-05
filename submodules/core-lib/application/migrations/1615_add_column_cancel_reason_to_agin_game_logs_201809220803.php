<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_cancel_reason_to_agin_game_logs_201809220803 extends CI_Migration {

    private $tableName = 'agin_game_logs';

    public function up() {
        $fields = array(
            'cancelReason' => array( 
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'cancelReason');
    }
}