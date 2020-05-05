<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_transfer_status_to_transfer_request_201803250113 extends CI_Migration {

    private $tableName = 'transfer_request';

    public function up() {

        if ( ! $this->db->field_exists('transfer_status', $this->tableName)) {
            $fields = array(
                'transfer_status' => array(
                    'type' => 'INT',
                    'null' => true
                )
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if ($this->db->field_exists('transfer_status', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'transfer_status');
        }

    }

}
