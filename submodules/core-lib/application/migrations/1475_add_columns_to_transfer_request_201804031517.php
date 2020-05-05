<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_transfer_request_201804031517 extends CI_Migration {

    private $tableName = 'transfer_request';

    public function up() {

        if ( ! $this->db->field_exists('reason_id', $this->tableName)) {
            //defined in abstract_game_api
            $fields = array(
                'reason_id' => array(
                    'type' => 'INT',
                    'null' => true
                )
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if ($this->db->field_exists('reason_id', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'reason_id');
        }

    }

}
