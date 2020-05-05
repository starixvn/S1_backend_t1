<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201902132100 extends CI_Migration {

    private $tableName = 'promorules';

    public function up() {
        $fields = [
            'transferRequirementWalletsInfo' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('transferRequirementWalletsInfo', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'transferRequirementConditionType' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
                'default' => '0',
                'null' => false,
            ],
        ];

        if(!$this->db->field_exists('transferRequirementConditionType', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'transferRequirementBetCntCondition' => [
                'type' => 'DOUBLE',
                'default' => '0',
                'null' => false,
            ],
        ];

        if(!$this->db->field_exists('transferRequirementBetCntCondition', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('transferRequirementWalletsInfo', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'transferRequirementWalletsInfo');
        }
        if($this->db->field_exists('transferRequirementConditionType', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'transferRequirementConditionType');
        }
        if($this->db->field_exists('transferRequirementBetCntCondition', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'transferRequirementBetCntCondition');
        }
    }
}