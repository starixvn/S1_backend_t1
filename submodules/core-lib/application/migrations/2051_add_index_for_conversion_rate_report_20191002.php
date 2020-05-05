<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_for_conversion_rate_report_20191002 extends CI_Migration {

    private $tableName = 'player';

    public function up() {
        $this->load->model(['player_model']);
        $this->player_model->addIndex('player_report_hourly', 'idx_first_deposit_datetime', 'first_deposit_datetime');

        $this->player_model->addIndex('player_report_hourly', 'idx_first_deposit_amount', 'first_deposit_amount');

        $this->player_model->addIndex('player_report_hourly', 'idx_affiliate_id', 'affiliate_id');

        $this->player_model->addIndex('player_report_hourly', 'idx_agent_id', 'agent_id');

        $this->player_model->addIndex('player', 'idx_refereePlayerId', 'refereePlayerId');

        $this->player_model->addIndex('player', 'idx_affiliateId', 'affiliateId');

    }

    public function down() {
        $this->load->model(['player_model']);

        $this->player_model->dropIndex('player_report_hourly', 'idx_first_deposit_datetime');
        $this->player_model->dropIndex('player_report_hourly', 'idx_first_deposit_amount');
        $this->player_model->dropIndex('player_report_hourly', 'idx_affiliate_id');
        $this->player_model->dropIndex('player_report_hourly', 'idx_agent_id');

        $this->player_model->dropIndex('player', 'idx_refereePlayerId');
        $this->player_model->dropIndex('player', 'idx_affiliateId');
    }
}
