<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201810251644 extends CI_Migration {

    private $tableName = 'promorules';

    public function up() {
        $fields = [
            'withdrawRequirementDepositConditionType' => [
                'type' => 'TINYINT',
                'null' => false,
                'default' => 0
            ],
        ];

        if(!$this->db->field_exists('withdrawRequirementDepositConditionType', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'withdrawRequirementDepositAmount' => [
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ],
        ];

        if(!$this->db->field_exists('withdrawRequirementDepositAmount', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('withdrawRequirementDepositConditionType', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'withdrawRequirementDepositConditionType');
        }
        if($this->db->field_exists('withdrawRequirementDepositAmount', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'withdrawRequirementDepositAmount');
        }
    }
}