<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_is_finished_to_withdraw_conditions_20191021 extends CI_Migration {

    private $tableName = 'withdraw_conditions';

    public function up() {
        $fields = array(
            'is_finished' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'default' => 0,
                'null' => false,
            ),
        );
        if(!$this->db->field_exists('is_finished', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('is_finished', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'is_finished');
        }
    }
}
