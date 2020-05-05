<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_reports_201811141614 extends CI_Migration {

    public function up() {
        $fields = [
            'currency_key' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('currency_key', 'player_report_hourly')){
            $this->dbforge->add_column('player_report_hourly', $fields);
        }
        if(!$this->db->field_exists('currency_key', 'summary2_report_daily')){
            $this->dbforge->add_column('summary2_report_daily', $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('currency_key', 'player_report_hourly')){
            $this->dbforge->drop_column('player_report_hourly', 'currency_key');
        }
        if($this->db->field_exists('currency_key', 'summary2_report_daily')){
            $this->dbforge->drop_column('summary2_report_daily', 'currency_key');
        }
    }
}
