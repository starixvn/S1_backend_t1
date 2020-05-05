<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_reports_201811141714 extends CI_Migration {

    public function up() {
        $fields = [
            'unique_key' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('unique_key', 'player_report_hourly')){
            $this->dbforge->add_column('player_report_hourly', $fields);
        }
        if(!$this->db->field_exists('unique_key', 'summary2_report_daily')){
            $this->dbforge->add_column('summary2_report_daily', $fields);
        }

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('player_report_hourly', 'idx_unique_key', 'unique_key', true);
        $this->player_model->addIndex('summary2_report_daily', 'idx_unique_key', 'unique_key', true);
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex('player_report_hourly', 'idx_unique_key');
        $this->player_model->dropIndex('summary2_report_daily', 'idx_unique_key');
        if($this->db->field_exists('unique_key', 'player_report_hourly')){
            $this->dbforge->drop_column('player_report_hourly', 'unique_key');
        }
        if($this->db->field_exists('unique_key', 'summary2_report_daily')){
            $this->dbforge->drop_column('summary2_report_daily', 'unique_key');
        }
    }
}
