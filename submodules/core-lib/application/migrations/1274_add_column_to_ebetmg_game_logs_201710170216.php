<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ebetmg_game_logs_201710170216 extends CI_Migration {

    private $tableName = 'ebetmg_game_logs';

    public function up() {
        $field = array(
            'player_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'result_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'sync_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'balance_after_bet'=>array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'extra' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $field);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'player_name');
        $this->dbforge->drop_column($this->tableName, 'bet_amount');
        $this->dbforge->drop_column($this->tableName, 'result_amount');
        $this->dbforge->drop_column($this->tableName, 'sync_datetime');
        $this->dbforge->drop_column($this->tableName, 'extra');
        $this->dbforge->drop_column($this->tableName, 'balance_after_bet');
    }
}
