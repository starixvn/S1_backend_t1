<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_and_modified_column_on_playernotes_20190111 extends CI_Migration {

    private $tableName = 'playernotes';
    private $deletedOn = 'deletedOn';
    private $status    = 'status';

    public function up() {
        $fields = array(
	        $this->deletedOn => array(			
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $update_fields = array(
            $this->status => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => 1,
                'null' => true,
            ),
        );

        if(!$this->db->field_exists($this->deletedOn, $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
        
        $this->dbforge->modify_column($this->tableName, $update_fields);
        $this->db->update($this->tableName, array($this->status => 1));

    }

    public function down() {
        if(!$this->db->field_exists($this->deletedOn, $this->tableName)){
            $this->dbforge->drop_column($this->tableName, $this->deletedOn);
        }

        $update_fields = array(
            $this->status => array(
                'type'    => 'VARCHAR',
                'default' => ''
            ),
        );
        $this->dbforge->modify_column($this->tableName, $update_fields);
        $this->db->update($this->tableName, array($this->status => ''));
    }
}
