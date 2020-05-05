<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_asiastar_game_logs_20191202 extends CI_Migration {
    private $tableName = 'asiastar_game_logs';

    public function up() {
        $fields = array(
            'gametype' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'betdetails' => array(
              'type' => 'TEXT',
              'null' => true,
            ),
        );

        if(!$this->db->field_exists('gametype', $this->tableName) && !$this->db->field_exists('betdetails', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('gametype', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'gametype');
        }

        if($this->db->field_exists('betdetails', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'betdetails');
        }        
    }
}
