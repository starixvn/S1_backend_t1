<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ld_lottery_game_logs_201808201017 extends CI_Migration {

    private $tableName = 'ld_lottery_game_logs';

    public function up() {
        # add new column
        $fields = array(
            'lotto_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'lotto_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'numbers' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'cmd' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'play_value' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
        $this->dbforge->add_key('created_at');        

        # Modify table column
        $fields_to_modify = array(
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields_to_modify);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'lotto_name');
        $this->dbforge->drop_column($this->tableName, 'lotto_code');
        $this->dbforge->drop_column($this->tableName, 'numbers');
        $this->dbforge->drop_column($this->tableName, 'cmd');
        $this->dbforge->drop_column($this->tableName, 'play_value');
        $this->dbforge->drop_column($this->tableName, 'md5_sum');
        $this->dbforge->drop_column($this->tableName, 'created_at');
    }
}
