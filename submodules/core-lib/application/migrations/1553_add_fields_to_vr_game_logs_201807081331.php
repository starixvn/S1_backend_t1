<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_fields_to_vr_game_logs_201807081331 extends CI_Migration {

    public function up() {

        $fields = array(
            'issue_key' => array(
                'type' => 'VARCHAR',
				'constraint' => '200',
                'null' => true,
            )
        );
        $this->dbforge->add_column('vr_game_logs', $fields);

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('vr_game_logs', 'idx_createTime', 'createTime');
        $this->player_model->addIndex('vr_game_logs', 'idx_updateTime', 'updateTime');
        $this->player_model->addIndex('vr_game_logs', 'idx_issue_key', 'issue_key');
    }

    public function down() {

        $this->load->model('player_model');
        $this->player_model->dropIndex('vr_game_logs', 'idx_createTime');
        $this->player_model->dropIndex('vr_game_logs', 'idx_updateTime');
        $this->player_model->dropIndex('vr_game_logs', 'idx_issue_key');

        if($this->db->field_exists('issue_key', 'vr_game_logs')){
            $this->dbforge->drop_column('vr_game_logs', 'issue_key');
        }
    }
}
