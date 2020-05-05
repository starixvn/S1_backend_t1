<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_tracking_domain_20180528 extends CI_Migration {

    private $tableName = 'agency_tracking_domain';

    public function up() {
        $fields = [
            'shorturl' => [
                'type' => 'text',
                'null' => FALSE,
            ],
        ];

        if(!$this->db->field_exists('shorturl', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('shorturl', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'shorturl');
        }
    }
}