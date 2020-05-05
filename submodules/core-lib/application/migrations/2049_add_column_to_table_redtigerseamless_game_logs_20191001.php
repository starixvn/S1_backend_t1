<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_table_redtigerseamless_game_logs_20191001 extends CI_Migration {

	private $tableName = 'redtigerseamless_game_logs';

    public function up() {

        $fields = array(
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('md5_sum', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('md5_sum', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'md5_sum');
        }
    }
}