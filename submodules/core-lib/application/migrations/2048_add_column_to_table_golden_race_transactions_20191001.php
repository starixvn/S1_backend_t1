<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_table_golden_race_transactions_20191001 extends CI_Migration {

	private $tableName = 'golden_race_transactions';

    public function up() {

        $fields = array(
            'before_balance' => array(
                'type' => 'double',
                'null' => true,
            ),
            'after_balance' => array(
                'type' => 'double',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('before_balance', $this->tableName) && !$this->db->field_exists('after_balance', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('before_balance', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'before_balance');
        }
        if($this->db->field_exists('after_balance', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'after_balance');
        }
    }
}