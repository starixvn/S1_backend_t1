<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerpromo_201902132101 extends CI_Migration {

    private $tableName = 'playerpromo';

    public function up() {
        $fields = [
            'transferConditionAmount' => [
                'type' => 'DOUBLE',
                'default' => '0',
                'null' => false,
            ],
        ];

        if(!$this->db->field_exists('transferConditionAmount', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('transferConditionAmount', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'transferConditionAmount');
        }
    }
}