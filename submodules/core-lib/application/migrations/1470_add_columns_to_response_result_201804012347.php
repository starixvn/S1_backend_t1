<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_response_result_201804012347 extends CI_Migration {

    private $tableName = 'response_results';

    public function up() {

        if ( ! $this->db->field_exists('external_transaction_id', $this->tableName)) {
            $fields = array(
                'external_transaction_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '200',
                    'null' => true
                )
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

        if ( ! $this->db->field_exists('full_url', $this->tableName)) {
            $fields = array(
                'full_url' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '1000',
                    'null' => true
                )
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if ($this->db->field_exists('external_transaction_id', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'external_transaction_id');
        }
        if ($this->db->field_exists('full_url', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'full_url');
        }

    }

}
