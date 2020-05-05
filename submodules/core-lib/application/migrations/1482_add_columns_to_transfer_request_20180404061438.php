<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_transfer_request_20180404061438 extends CI_Migration {

    private $tableName = 'transfer_request';

    public function up() {

        if ( ! $this->db->field_exists('fix_flag', $this->tableName)) {
            $fields = array(
                'fix_flag' => array(
                    'type' => 'SMALLINT',
                    'null' => true,
                    'default' => 0,
                )
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = array(
            'transfer_request_id' => array(
                'type' => 'INT',
                'null' => true,
            )
        );
        $this->dbforge->add_column('transactions', $fields);

    }

    public function down() {

        if ($this->db->field_exists('fix_flag', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'fix_flag');
        }

        $this->dbforge->drop_column('transactions', 'transfer_request_id');

    }

}
