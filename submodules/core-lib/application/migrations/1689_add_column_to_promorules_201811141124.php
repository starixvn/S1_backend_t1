<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201811141124 extends CI_Migration {

    private $tableName = 'promorules';

    public function up() {
        $fields = [
            'dont_allow_request_promo_from_same_ips' => [
                'type' => 'TINYINT',
                'null' => false,
                'constrain' => 1,
                'default' => 0
            ],
        ];

        if(!$this->db->field_exists('dont_allow_request_promo_from_same_ips', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('dont_allow_request_promo_from_same_ips', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'dont_allow_request_promo_from_same_ips');
        }
    }
}