<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_settlement_time_to_oneworks_game_result_201908081200 extends CI_Migration {

    private $tableName = 'oneworks_game_result';

    public function up() {
        $field = array(
           'settlement_time' => array(
                'type' => 'datetime',
                'null' => true,
            )
        );

        if(!$this->db->field_exists('settlement_time', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $field);
        }

    }

    public function down() {
        if($this->db->field_exists('settlement_time', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'settlement_time');
        }
    }
}
