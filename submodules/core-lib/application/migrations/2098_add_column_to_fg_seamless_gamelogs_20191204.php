<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_fg_seamless_gamelogs_20191204 extends CI_Migration {

    private $tableName = 'fg_seamless_gamelogs';

    public function up() {

        $fields = array(
            'category' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
        );

        if(!$this->db->field_exists('category', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('category', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'category');
        }
    }
}