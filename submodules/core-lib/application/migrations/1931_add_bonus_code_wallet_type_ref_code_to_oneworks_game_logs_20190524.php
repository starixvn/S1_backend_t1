<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_bonus_code_wallet_type_ref_code_to_oneworks_game_logs_20190524 extends CI_Migration {

    private $tableName = 'oneworks_game_logs';

    public function up() {
        $field = array(
           'bonus_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
           'wallet_type' => array(
                'type' => 'INT',
                'null' => true,
           ),
           'ref_code' => array(
                'type' => 'INT',
                'null' => true,
           ),
        );

        $this->dbforge->add_column($this->tableName, $field);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'bonus_code');
        $this->dbforge->drop_column($this->tableName, 'wallet_type');
        $this->dbforge->drop_column($this->tableName, 'ref_code');
    }
}



