<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_habanero_transactions_logs_20191210 extends CI_Migration {

    private $tableName = 'habanero_transactions';

    public function up() {

        $fields = array(
            'balance_before' => array(
                'type' => 'double',
                'null' => true,
            ),
            'fundinfo_originaltransferid' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
        );
        
        if(!$this->db->field_exists('balance_before', $this->tableName) && !$this->db->field_exists('fundinfo_originaltransferid', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('balance_before', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'balance_before');
        }
        if($this->db->field_exists('fundinfo_originaltransferid', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'fundinfo_originaltransferid');
        }
    }
}