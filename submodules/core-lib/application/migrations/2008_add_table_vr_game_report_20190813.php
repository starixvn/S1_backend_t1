<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_vr_game_report_20190813 extends CI_Migration {

	private $tableName = 'vr_game_report';

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'unsigned' => true,
				'auto_increment' => true,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'game_description_id' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'player_username' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'player_level' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'affiliate_username' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'agent_name' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'merchant_code' => array(
	            'type' => 'VARCHAR',
	            'constraint' => '100',
	            'null' => true,
			),
			'merchantPrize' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'playerPrize' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_bets' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_wins' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'total_loss' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'payout' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'payout_rate' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'game_date' => array(
				'type' => 'DATE',
				'null' => true,
			),
            'external_unique_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
			'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
			),
		);

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);

            $this->load->model('player_model'); # Any model class will do
        	$this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');
        	$this->player_model->addIndex($this->tableName, 'idx_game_platform_id', 'game_platform_id');
        	$this->player_model->addIndex($this->tableName, 'idx_game_type_id', 'game_type_id');
        	$this->player_model->addIndex($this->tableName, 'idx_affiliate_username', 'affiliate_username');
        	$this->player_model->addIndex($this->tableName, 'idx_game_date', 'game_date');
	        $this->player_model->addIndex($this->tableName, 'idx_md5_sum', 'md5_sum');
        }
	}

	public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
	}
}
