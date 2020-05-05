<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_report_date_to_player_report_2_20181002 extends CI_Migration {

    private $tableName = 'player_report_2';

    public function up() {
        $fields = array(
            'report_date' => array(
                'type' => 'DATE',
                'null' => true,
            )
        );

        if(!$this->db->field_exists('report_date', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('report_date', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'report_date');
        }
    }
}