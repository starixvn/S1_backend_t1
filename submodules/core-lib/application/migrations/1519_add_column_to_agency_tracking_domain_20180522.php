<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_tracking_domain_20180522 extends CI_Migration{
    private $tableName = 'agency_tracking_domain';

    public function up(){
        $fields = [
            'bonus_rate' => [
                'type' => 'INT',
                'default' => 0,
                'null' => FALSE,
            ],
        ];

        if(!$this->db->field_exists('bonus_rate', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $fields = [
            'player_type' => [
                'type' => 'INT',
                'default' => 0,
                'null' => FALSE,
            ],
        ];

        if(!$this->db->field_exists('player_type', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down(){
        if($this->db->field_exists('player_type', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'player_type');
        }

        if($this->db->field_exists('bonus_rate', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'bonus_rate');
        }
    }
}
