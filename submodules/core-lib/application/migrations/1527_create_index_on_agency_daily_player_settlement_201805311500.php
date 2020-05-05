<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_index_on_agency_daily_player_settlement_201805311500 extends CI_Migration {

    public function up() {
        $this->load->model('agency_model'); # Any model class will do
        $this->agency_model->addIndex('agency_daily_player_settlement', 'idx_agency_daily_player_settlement', 'player_id, agent_id, settlement_date');
    }

    public function down() {
        $this->load->model('agency_model');
        $this->agency_model->dropIndex('agency_daily_player_settlement', 'idx_agency_daily_player_settlement');
    }
}
