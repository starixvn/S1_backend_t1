<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_t1lottery_game_logs_201807281430 extends CI_Migration {

    private $tableName = 't1lottery_game_logs';

    public function up(){

        $fields = array(
            'last_sync_time' => array(
                'type' => 'datetime',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
        //vr too
        $this->dbforge->add_column('vr_game_logs', $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'last_sync_time');
        $this->dbforge->drop_column('vr_game_logs', 'last_sync_time');
    }
}
