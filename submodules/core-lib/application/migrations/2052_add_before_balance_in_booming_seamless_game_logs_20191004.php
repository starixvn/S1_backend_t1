<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_before_balance_in_booming_seamless_game_logs_20191004 extends CI_Migration {

	private $tableName = 'boomingseamless_game_logs';

    public function up() {

        $fields = array(
            'before_balance' => array(
                'type' => 'double',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('before_balance', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('before_balance', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'before_balance');
        }
    }
}