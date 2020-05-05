<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201807091546 extends CI_Migration {

    private $tableName = 'vipsettingcashbackrule';

	public function up() {
        $fields = [
            'overwrite_minimum_withdraw_setting' => [
                'type' => 'ENUM("true","false")',
                'default' => 'false',
                'null' => false,
            ],
        ];

        if(!$this->db->field_exists('overwrite_minimum_withdraw_setting', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'min_withdrawal_per_transaction' => [
                'type' => 'DOUBLE',
                'default' => '0',
                'null' => false,
            ],
        ];

        if(!$this->db->field_exists('min_withdrawal_per_transaction', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

	}

	public function down() {
        if($this->db->field_exists('overwrite_minimum_withdraw_setting', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'overwrite_minimum_withdraw_setting');
        }
        if($this->db->field_exists('min_withdrawal_per_transaction', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'min_withdrawal_per_transaction');
        }
	}
}