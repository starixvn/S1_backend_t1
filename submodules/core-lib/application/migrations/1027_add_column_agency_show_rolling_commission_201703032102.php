<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_agency_show_rolling_commission_201703032102 extends CI_Migration {

    public function up() {
        $fields = array(
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('agency_player_rolling_comm', $fields);

        $this->load->model(['player_model']);
        $this->player_model->addIndex('agency_player_rolling_comm', 'idx_player_id', 'player_id');
        $this->player_model->addIndex('agency_player_rolling_comm', 'idx_payment_status', 'payment_status');
        $this->player_model->addIndex('agency_player_rolling_comm', 'idx_start_at', 'start_at');
        $this->player_model->addIndex('agency_player_rolling_comm', 'idx_end_at', 'end_at');
        $this->player_model->addIndex('agency_player_rolling_comm', 'idx_agent_id', 'agent_id');

    }

    public function down() {
        $this->dbforge->drop_column('agency_player_rolling_comm', 'updated_at');

        $this->load->model(['player_model']);
        $this->player_model->dropIndex('agency_player_rolling_comm', 'idx_player_id');
        $this->player_model->dropIndex('agency_player_rolling_comm', 'idx_payment_status');
        $this->player_model->dropIndex('agency_player_rolling_comm', 'idx_start_at');
        $this->player_model->dropIndex('agency_player_rolling_comm', 'idx_end_at');
        $this->player_model->dropIndex('agency_player_rolling_comm', 'idx_agent_id');

    }

}

///END OF FILE//////////////////