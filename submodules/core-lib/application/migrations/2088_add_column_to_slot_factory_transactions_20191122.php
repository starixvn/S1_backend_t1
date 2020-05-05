<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_slot_factory_transactions_20191122 extends CI_Migration {

    private $tableName = 'slot_factory_transactions';

    public function up() {

        $fields = array(
            'Type' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'FreeGames' => array(
                'type' => 'TINYINT',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('Type', $this->tableName) && !$this->db->field_exists('FreeGames', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('Type', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'Type');
        }
        if($this->db->field_exists('FreeGames', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'FreeGames');
        }
    }
}