<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_withdrawal_condition_20180628 extends CI_Migration {

    private $tableName = 'withdraw_conditions';

    public function up() {

        if ( ! $this->db->field_exists('detail_status', $this->tableName)) {

            $fields = array(
                'detail_status' => array(
                    'type' => 'INT',
                    'default' => 1, #default to active
                ),
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {

        if ($this->db->field_exists('detail_status', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'detail_status');
        }
    }
}

