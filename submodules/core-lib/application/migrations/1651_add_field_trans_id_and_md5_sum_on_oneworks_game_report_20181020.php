<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_trans_id_and_md5_sum_on_oneworks_game_report_20181020 extends CI_Migration {

	private $tableName = 'oneworks_game_report';

	public function up() {
        $field = array(
            'trans_id' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $field);
        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_md5_sum', 'md5_sum');
	}

	public function down() {
        $this->dbforge->drop_column($this->tableName, 'trans_id');
        $this->dbforge->drop_column($this->tableName, 'md5_sum');
	}
}