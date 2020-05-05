<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gl_game_logs_20180926 extends CI_Migration {

    private $tableName = 'gl_game_logs';

    public function up() {
        $fields = array(
            'log' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'lottery_group' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'enname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'animal_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'user_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);

        $fields_to_modiy = array(
            'update_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'deduct_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'bonus_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'cancel_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'third_party_trx_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields_to_modiy);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'log');
        $this->dbforge->drop_column($this->tableName, 'lottery_group');
        $this->dbforge->drop_column($this->tableName, 'enname');
        $this->dbforge->drop_column($this->tableName, 'animal_code');
        $this->dbforge->drop_column($this->tableName, 'user_ip');
    }
}