<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_playerdetails_201901291830 extends CI_Migration {

    private $tableName = 'playerdetails';

    public function up() {
        $fields = array(
           'kyc_status_id' => array(
                'type' => 'int',
                'null'=> true
            ),
           'risk_score_level' => array(
                'type' => 'text',
                'null'=> true
            ),
        );

        if(!$this->db->field_exists('kyc_status_id', $this->tableName) && !$this->db->field_exists('risk_score_level', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'kyc_status_id');
        $this->dbforge->drop_column($this->tableName, 'risk_score_level');
    }
}
