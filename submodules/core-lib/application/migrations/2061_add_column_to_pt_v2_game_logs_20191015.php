<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_pt_v2_game_logs_20191015 extends CI_Migration {

	private $tableName = 'pt_v2_game_logs';

    public function up() {

        $fields = array(
            'hash' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'balance_after' => array(
                'type' => 'double',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('hash', $this->tableName) && !$this->db->field_exists('balance_after', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('hash', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'hash');
        }
        if($this->db->field_exists('balance_after', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'balance_after');
        }
    }
}