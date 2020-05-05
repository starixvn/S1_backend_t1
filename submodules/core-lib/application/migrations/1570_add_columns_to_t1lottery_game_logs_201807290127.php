<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_t1lottery_game_logs_201807290127 extends CI_Migration {

    public function up(){

        $fields = array(
            'md5_sum' => array(
                'type' => 'varchar',
                'constraint' => '64',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('t1lottery_game_logs', $fields);
        //vr too
        $this->dbforge->add_column('vr_game_logs', $fields);

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex('t1lottery_game_logs', 'idx_md5_sum', 'md5_sum');
        $this->player_model->addIndex('vr_game_logs', 'idx_md5_sum', 'md5_sum');

    }

    public function down() {
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->dropIndex('t1lottery_game_logs', 'idx_md5_sum');
        $this->player_model->dropIndex('vr_game_logs', 'idx_md5_sum');

        $this->dbforge->drop_column('t1lottery_game_logs', 'md5_sum');
        $this->dbforge->drop_column('vr_game_logs', 'md5_sum');

    }
}
