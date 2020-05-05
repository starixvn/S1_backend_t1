<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_description_history_20181031 extends CI_Migration {

    private $tableName = 'game_description_history';

    public function up() {
        $fields = [
            'auto_sync_enable' => [
                'type' => 'boolean',
                'null' => false,
                'constrain' => 1,
                'default' => 1
            ],
        ];

        if(!$this->db->field_exists('auto_sync_enable', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('auto_sync_enable', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'auto_sync_enable');
        }
    }
}