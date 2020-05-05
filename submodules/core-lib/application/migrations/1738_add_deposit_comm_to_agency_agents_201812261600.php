<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_deposit_comm_to_agency_agents_201812261600 extends CI_Migration {

    private $tableName = 'agency_agents';

    public function up() {

        $field = [
            // Deposit commission rate in its percentage, 100 means 100%
            'deposit_comm' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ]
        ];

        if(!$this->db->field_exists('deposit_comm', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $field);
        }
    }

    public function down() {
        if($this->db->field_exists('deposit_comm', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'deposit_comm');
        }
    }
}
