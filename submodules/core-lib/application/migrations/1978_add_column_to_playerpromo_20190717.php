<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_playerpromo_20190717 extends CI_Migration {

    private $tableName = 'playerpromo';

    public function up() {
        $fields = [
            'requestAdminId' => [
                'type' => 'INT',
                'null' => TRUE,
            ],
        ];

        if(!$this->db->field_exists('requestAdminId', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'requestPlayerId' => [
                'type' => 'INT',
                'null' => TRUE,
            ],
        ];

        if(!$this->db->field_exists('requestPlayerId', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {
        if($this->db->field_exists('requestAdminId', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'requestAdminId');
        }
        if($this->db->field_exists('requestPlayerId', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'requestPlayerId');
        }
    }
}