<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_go_live_and_go_live_date_to_game_api_update_history_20191024 extends CI_Migration {

    private $tableName = 'game_api_update_history';

    public function up() {

        $fields = array(
            'go_live' => array(
                'type' => 'TINYINT',
                'null' => true,
            ),
            'go_live_date' => array(
                 'type' => 'DATETIME',
                  'null' => true
            )
        );

        if($this->db->table_exists($this->tableName)){

            if(! $this->db->field_exists('go_live', $this->tableName) && ! $this->db->field_exists('go_live_date', $this->tableName)){
                $this->dbforge->add_column($this->tableName, $fields);
            }

        }

    }

    public function down() {

        if($this->db->field_exists('go_live', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'go_live');
        }

        if($this->db->field_exists('go_live_date', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'go_live_date');
        }
    }
}