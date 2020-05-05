<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_yggdrasil_game_logs_20190906 extends CI_Migration {

	private $tableName = 'yggdrasil_game_logs';

    public function up() {

        $fields = array(
            'topOrg' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'org' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('topOrg', $this->tableName) && !$this->db->field_exists('org', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('topOrg', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'topOrg');
        }
        if($this->db->field_exists('org', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'org');
        }
    }
}