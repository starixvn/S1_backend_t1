<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_rebuild_index_on_player_ip_last_request_201903081814 extends CI_Migration{
    private $tableName = 'player_ip_last_request';

    public function up(){
        $this->load->model(['player_model']);

        // clean up duplicate data
        $this->db->query("DELETE u1 FROM player_ip_last_request u1, player_ip_last_request u2 WHERE u1.id < u2.id AND u1.player_id = u2.player_id");
        $this->db->query("DELETE u1 FROM player_device_last_request u1, player_device_last_request u2 WHERE u1.id < u2.id AND u1.player_id = u2.player_id");

        // create index
        $this->player_model->addIndex('player_ip_last_request', 'idx_player_id', 'player_id', TRUE);
        $this->player_model->addIndex('player_device_last_request', 'idx_player_id', 'player_id', TRUE);
    }

    public function down(){
        $this->load->model(['player_model']);
        $this->player_model->dropIndex('player_ip_last_request', 'idx_player_id');
        $this->player_model->dropIndex('player_device_last_request', 'idx_player_id');
    }
}
