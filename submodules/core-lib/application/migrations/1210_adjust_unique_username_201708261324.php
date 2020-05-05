<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_adjust_unique_username_201708261324 extends CI_Migration {

    public function up() {

        $this->load->model(['player_model']);

        $this->player_model->dropIndex('player', 'idx_un_username');

        //add unique username/agent_id
        $this->db->query('create unique index idx_un_username on player(username)');

    }

    public function down() {

    }
}

