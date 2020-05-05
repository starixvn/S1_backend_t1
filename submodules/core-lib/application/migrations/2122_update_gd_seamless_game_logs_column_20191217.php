<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_gd_seamless_game_logs_column_20191217 extends CI_Migration {
    
    private $tableName = 'gd_seamless_game_logs';
    
    public function up() {

        $time_column = array('bet_time','balance_time');
        foreach ($time_column as $key => $value) {
            if($this->db->field_exists($value, $this->tableName)) {
                $this->dbforge->modify_column($this->tableName, array(
                    $value => array(
                        'type' => 'DATETIME',
                        'null' => true
                    )
                ));
            }
        }

        $amount_column = array('bet_amount','winloss','start_balance','end_balance');
        foreach ($amount_column as $key => $value) {
            if($this->db->field_exists($value, $this->tableName)) {
                $this->dbforge->modify_column($this->tableName, array(
                    $value => array(
                        'type' => 'DOUBLE',
                        'null' => true
                    )
                ));
            }
        }

        
        
    }

    public function down() {
    }
}
