<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_bbin_game_logs_20180902 extends CI_Migration {

    private $tableName = 'bbin_game_logs';

    public function up() {
        $fields = array(
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
        $this->dbforge->add_key('last_sync_time');
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'last_sync_time');
        $this->dbforge->drop_column($this->tableName, 'md5_sum');
    }
}