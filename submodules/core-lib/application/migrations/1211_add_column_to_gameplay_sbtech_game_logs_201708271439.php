<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gameplay_sbtech_game_logs_201708271439 extends CI_Migration {

    private $tableName = 'gameplay_sbtech_game_logs';

    public function up() {
        $fields = array(
            'doneTime' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);

        $modify = array(
            'parlays' => array(
                'type' => 'TEXT',
                'null' => true,
            )
        );
        $this->dbforge->modify_column($this->tableName, $modify);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'doneTime');
        $this->dbforge->modify_column($this->tableName, 'parlays');
    } 
}