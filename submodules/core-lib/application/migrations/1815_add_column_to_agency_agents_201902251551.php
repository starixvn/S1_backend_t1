<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_agents_201902251551 extends CI_Migration {

    private $tableName = 'agency_agents';

    public function up() {
        $fields = [
            'no_prefix_on_username' => [
                'type' => 'TINYINT',
                'default' => '0',
                'null' => false,
            ],
        ];

        if(!$this->db->field_exists('no_prefix_on_username', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('no_prefix_on_username', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'no_prefix_on_username');
        }
    }
}