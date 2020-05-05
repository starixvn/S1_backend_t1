<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_api_config_201810291400 extends CI_Migration {

    private $tableName = 'api_config';

    public function up() {
        $fields = [
            'api_last_sync_index' => [
                'type' => 'BIGINT',
                'null' => false,
                'default' => 0
            ],
        ];

        if(!$this->db->field_exists('api_last_sync_index', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('api_last_sync_index', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'api_last_sync_index');
        }
    }
}