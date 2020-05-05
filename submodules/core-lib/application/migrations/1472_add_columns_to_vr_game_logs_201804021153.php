<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_vr_game_logs_201804021153 extends CI_Migration {

    private $tableName = 'vr_game_logs';

    public function up() {

        if ( ! $this->db->field_exists('extra_java', $this->tableName)) {
            $fields = array(
                'extra_java' => array(
                    'type' => 'TEXT',
                    'null' => true,
                )
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {

        if ($this->db->field_exists('extra_java', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'extra_java');
        }

    }

}
