<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_t1lottery_game_logs_201807261734 extends CI_Migration {

    private $tableName = 't1lottery_game_logs';

    public function up(){

        $fields = array(
            'last_updated_time' => array(
                'type' => 'datetime',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        if($this->db->field_exists('last_updated_time', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'last_updated_time');
        }
    }
}
