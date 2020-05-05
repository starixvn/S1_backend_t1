<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_preference_20180531 extends CI_Migration {

    private $tableName = 'player_preference';

    public function up() {
        $fields = [
            'myfavorites' => [
                'type' => 'text',
                'null' => FALSE,
            ],
        ];

        if(!$this->db->field_exists('myfavorites', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('created_at', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ];

        if(!$this->db->field_exists('updated_at', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('updated_at', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'updated_at');
        }
        if($this->db->field_exists('created_at', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'created_at');
        }
        if($this->db->field_exists('myfavorites', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'myfavorites');
        }
    }
}