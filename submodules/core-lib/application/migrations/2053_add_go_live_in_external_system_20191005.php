<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_go_live_in_external_system_20191005 extends CI_Migration {

    private $tableName = 'external_system';

    public function up() {

        $fields = array(
            'go_live' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'go_live_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('go_live', $this->tableName) && !$this->db->field_exists('go_live_date', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('go_live', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'go_live');
        }
        if($this->db->field_exists('go_live_date', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'go_live_date');
        }
    }
}