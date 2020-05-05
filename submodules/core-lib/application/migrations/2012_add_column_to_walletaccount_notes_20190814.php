<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_walletaccount_notes_20190814 extends CI_Migration {

    private $tableName = 'walletaccount_notes';

    public function up() {
        $fields = [
            'status_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
        ];

        if(!$this->db->field_exists('status_name', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('status_name', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'status_name');
        }
    }
}