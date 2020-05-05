<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_on_agent_id_201901102120 extends CI_Migration {

    public function up() {
        $this->load->model(['player_model']);

        $this->player_model->addIndex('player', 'idx_agent_id', 'agent_id');
        $this->player_model->addIndex('agency_agents', 'idx_agent_name', 'agent_name', true);
    }

    public function down() {

    }
}
