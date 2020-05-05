<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_withdrawal_condition_20180702 extends CI_Migration {

    private $tableName = 'withdraw_conditions';

    public function up() {

        if ( ! $this->db->field_exists('stopped_at', $this->tableName)) {

            $fields = array(
                'stopped_at' => array(
                    'type' => 'DATETIME'
                ),
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {

        if ($this->db->field_exists('stopped_at', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'stopped_at');
        }
    }
}

