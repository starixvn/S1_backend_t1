<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_pragmaticplay_seamless_wallet_transactions_20200109 extends CI_Migration {

    private $tableName = 'pragmaticplay_seamless_wallet_transactions';

    public function up() {

        $fields = array(
            'bonus_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('bonus_code', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'transaction_id');
        }

    }

    public function down() {
        if($this->db->field_exists('bonus_code', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'transaction_type');
        }
    }

}