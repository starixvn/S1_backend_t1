<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_transactions_20180409 extends CI_Migration {

    private $tableName = 'transactions';

    public function up() {

        if ( ! $this->db->field_exists('request_secure_id', $this->tableName)) {
           
            $fields = array(
            'request_secure_id' => array(      
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if ($this->db->field_exists('request_secure_id', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'request_secure_id');
        }

    }

}

