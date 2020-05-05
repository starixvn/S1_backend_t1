<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_index_on_agency_settlement_tables_201812280130 extends CI_Migration {
    public function up() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('agency_wl_settlement', 'idx_agency_wl_settlement_type', 'type');
        $this->player_model->addIndex('agency_wl_settlement', 'idx_agency_wl_settlement_user_id', 'user_id');
        $this->player_model->addIndex('agency_wl_settlement', 'idx_agency_wl_settlement_date_from', 'settlement_date_from');
        $this->player_model->addIndex('agency_daily_player_settlement', 'idx_agency_daily_player_settlement_player_id', 'player_id');
        $this->player_model->addIndex('agency_daily_player_settlement', 'idx_agency_daily_player_settlement_agent_id', 'agent_id');
        $this->player_model->addIndex('agency_daily_player_settlement', 'idx_agency_daily_player_settlement_date', 'settlement_date');
    }

    public function down() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->dropIndex('agency_wl_settlement', 'idx_agency_wl_settlement_type');
        $this->player_model->dropIndex('agency_wl_settlement', 'idx_agency_wl_settlement_user_id');
        $this->player_model->dropIndex('agency_wl_settlement', 'idx_agency_wl_settlement_date_from');
        $this->player_model->dropIndex('agency_daily_player_settlement', 'idx_agency_daily_player_settlement_player_id');
        $this->player_model->dropIndex('agency_daily_player_settlement', 'idx_agency_daily_player_settlement_agent_id');
        $this->player_model->dropIndex('agency_daily_player_settlement', 'idx_agency_daily_player_settlement_date');
    }
}

///END OF FILE//////////