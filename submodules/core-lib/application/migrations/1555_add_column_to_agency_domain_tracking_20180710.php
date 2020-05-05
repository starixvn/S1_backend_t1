<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_domain_tracking_20180710 extends CI_Migration {
    private $tableName = 'agency_tracking_domain';

    public function up() {
        $fields = array(
            'rebate_rate' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('rebate_rate', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('rebate_rate', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'rebate_rate');
        }
    }
}