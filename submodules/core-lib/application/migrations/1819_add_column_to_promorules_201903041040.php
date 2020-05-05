<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201903041040 extends CI_Migration {

    private $tableName = 'promorules';

    public function up() {
        $fields = [
            'transferRequirementBetAmount' => [
                'type' => 'DECIMAL',
                'constraint' => '19,6',
                'null' => TRUE,
                'default' => '0',
            ],
        ];

        if(!$this->db->field_exists('transferRequirementBetAmount', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'transferShouldMinusDeposit' => [
                'type' => 'TINYINT',
                'null' => FALSE,
                'constrain' => 1,
                'default' => 0
            ],
        ];

        if(!$this->db->field_exists('transferShouldMinusDeposit', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {
        if($this->db->field_exists('transferRequirementBetAmount', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'transferRequirementBetAmount');
        }
        if($this->db->field_exists('transferShouldMinusDeposit', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'transferShouldMinusDeposit');
        }
    }
}