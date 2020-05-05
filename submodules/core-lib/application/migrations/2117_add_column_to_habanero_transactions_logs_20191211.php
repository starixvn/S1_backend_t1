<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_habanero_transactions_logs_20191211 extends CI_Migration {

    private $tableName = 'habanero_transactions';

    public function up() {

        $fields = array(
            'fundinfo_dtevent_parsed' => array(
                "type" => "DATETIME",
                "null" => true
            ),            
            'dtsent_parsed' => array(
                "type" => "DATETIME",
                "null" => true
            ),
            'is_refunded' => array(
				'type' => 'TINYINT(1)',
				'null' => false,
				'default' => 0
			),
            'fundinfo_jpid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
        );
        
        if(!$this->db->field_exists('fundinfo_dtevent_parsed', $this->tableName) 
            && !$this->db->field_exists('dtsent_parsed', $this->tableName) 
            && !$this->db->field_exists('is_refunded', $this->tableName)
            && !$this->db->field_exists('fundinfo_jpid', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('fundinfo_dtevent_parsed', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'fundinfo_dtevent_parsed');
        }
        if($this->db->field_exists('dtsent_parsed', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'dtsent_parsed');
        }
        if($this->db->field_exists('is_refunded', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'is_refunded');
        }
        if($this->db->field_exists('fundinfo_jpid', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'fundinfo_jpid');
        }
    }
    
}