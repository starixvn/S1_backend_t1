<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_responsible_gaming_201808171550 extends CI_Migration {

    private $tableName = 'responsible_gaming';

    public function up() {
        $fields = [
            'cooling_off_to' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('cooling_off_to', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('cooling_off_to', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'cooling_off_to');
        }
    }
}