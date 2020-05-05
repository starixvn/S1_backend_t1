<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerbankdetails_201710181103 extends CI_Migration {

    private $tableName = 'playerbankdetails';

    public function up() {
        if(!$this->db->field_exists('isDeleted', $this->tableName)){
            $field = array(
                'isDeleted' => array(
                    'type' => 'ENUM("0","1")',
                    'default' => '0',
                    'null' => true,
                )
            );
            $this->dbforge->add_column($this->tableName, $field, 'isRemember');
        }
        
        if(!$this->db->field_exists('deletedOn', $this->tableName)){
            $field = array(
                'deletedOn' => array(
                    'type' => 'DATETIME',
                    'default' => '0',
                    'null' => false,
                )
            );
            $this->dbforge->add_column($this->tableName, $field, 'updatedOn');
        }
    }

    public function down() {
        if($this->db->field_exists('isDeleted', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'isDeleted');
        }
        
        if($this->db->field_exists('deletedOn', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'deletedOn');
        }
    }
}
