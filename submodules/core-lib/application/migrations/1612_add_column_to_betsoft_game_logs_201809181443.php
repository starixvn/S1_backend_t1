<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_betsoft_game_logs_201809181443 extends CI_Migration {

    private $tableName = 'betsoft_game_logs';

    public function up() {
        $fields = array(
            // check if bet was refunded
            'is_refunded' => array(      # if refund don't merge logs
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'transaction_id' => array(   # concat from bet (amount|transid) and win (amount|transid)
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'is_refunded');
        $this->dbforge->drop_column($this->tableName, 'transaction_id');
    }
}