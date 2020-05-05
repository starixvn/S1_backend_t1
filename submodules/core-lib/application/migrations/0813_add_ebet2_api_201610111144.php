<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ebet2_api_201610111144 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		//add game logs table
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'gameType' => array(
				'type' => 'INT',
				'null' => true,
			),
			'betMap' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'judgeResult' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'roundNo' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'payout' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'bankerCards' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'playerCards' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'allDices' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'dragonCard' => array(
				'type' => 'INT',
				'null' => true,
			),
			'tigerCard' => array(
				'type' => 'INT',
				'null' => true,
			),
			'number' => array(
				'type' => 'INT',
				'null' => true,
			),
			'createTime' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'payoutTime' => array(
				'type' => 'TIMESTAMP',
				'null' => true,
			),
			'betHistoryId' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'validBet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'userId' => array(
				'type' => 'INT',
				'null' => true,
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'subChannelId' => array(
				'type' => 'INT',
				'null' => true,
			),
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'external_uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'gameshortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => true,
			),
			'response_result_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'realBet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'origCreateTime' => array(
				'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
			),
			'origPayoutTime' => array(
				'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('ebet2_game_logs');

		$this->db->query('create unique index idx_uniqueid on ebet2_game_logs(uniqueid)');
		$this->db->query('create unique index idx_external_uniqueid on ebet2_game_logs(external_uniqueid)');
		$this->db->query('create index idx_gameshortcode on ebet2_game_logs(gameshortcode)');
		$this->db->query('create index idx_player_name on ebet2_game_logs(username)');
		$this->db->query('create index idx_game_date on ebet2_game_logs(payoutTime)');

		//add game description

		$this->load->model('game_type_model');
		$this->db->trans_start();
		// $game_type_id = $this->game_type_model->getUnknownGameType(EBET2_API)->id;

		//unknown
		$this->db->insert('game_type', array(
			'game_platform_id' 	=> EBET2_API,
			'game_type' 		=> 'unknown',
			'game_type_lang' 	=> 'ebet.unknown',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_FALSE,
		));

		$this->db->insert('game_description', array(
			'game_platform_id' 	=> EBET2_API,
			'game_type_id' 		=> $this->db->insert_id(),
			'game_name' 		=> 'ebet.unknown',
			'english_name' 		=> 'Unknown EBET Game',
			'external_game_id' 	=> 'unknown',
			'game_code' 		=> 'unknown',
		));

		//game types
		$baccaratId=$this->game_type_model->insertData('game_type', array(
			'game_platform_id' 	=> EBET2_API,
			'game_type' 		=> '百家乐',
			'game_type_lang' 	=> 'baccarat',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_TRUE,
		));

		$dragonTigerId=$this->game_type_model->insertData('game_type', array(
			'game_platform_id' 	=> EBET2_API,
			'game_type' 		=> '龙虎',
			'game_type_lang' 	=> 'dragonTiger',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_TRUE,
		));

		$sicboId=$this->game_type_model->insertData('game_type', array(
			'game_platform_id' 	=> EBET2_API,
			'game_type' 		=> '骰宝',
			'game_type_lang' 	=> 'sicbo',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_TRUE,
		));

		$rouletteWheelId=$this->game_type_model->insertData('game_type', array(
			'game_platform_id' 	=> EBET2_API,
			'game_type' 		=> '轮盘',
			'game_type_lang' 	=> 'rouletteWheel',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_TRUE,
		));

		$fruitMachineId=$this->game_type_model->insertData('game_type', array(
			'game_platform_id' 	=> EBET2_API,
			'game_type' 		=> '水果机',
			'game_type_lang' 	=> 'fruitMachine',
			'status' 			=> self::FLAG_TRUE,
			'flag_show_in_site' => self::FLAG_TRUE,
		));

		//game
		$this->db->insert_batch('game_description', array(
			array(
				'game_platform_id' => EBET2_API,
				'game_type_id' => $baccaratId,
				'game_code' => '1',
				'external_game_id' => '1',
				'game_name' => 'baccarat',
				'english_name' => 'baccarat',
			),
			array(
				'game_platform_id' => EBET2_API,
				'game_type_id' => $dragonTigerId,
				'game_code' => '2',
				'external_game_id' => '2',
				'game_name' => 'dragonTiger',
				'english_name' => 'dragonTiger',
			),
			array(
				'game_platform_id' => EBET2_API,
				'game_type_id' => $sicboId,
				'game_code' => '3',
				'external_game_id' => '3',
				'game_name' => 'sicbo',
				'english_name' => 'sicbo',
			),
			array(
				'game_platform_id' => EBET2_API,
				'game_type_id' => $rouletteWheelId,
				'game_code' => '4',
				'external_game_id' => '4',
				'game_name' => 'rouletteWheel',
				'english_name' => 'rouletteWheel',
			),
			array(
				'game_platform_id' => EBET2_API,
				'game_type_id' => $fruitMachineId,
				'game_code' => '5',
				'external_game_id' => '5',
				'game_name' => 'fruitMachine',
				'english_name' => 'fruitMachine',
			),
		));

		$this->db->trans_complete();


	}

	public function down() {

		$this->dbforge->drop_table('ebet2_game_logs');

		$game_platform_id = EBET2_API;

		$this->db->trans_start();

		$this->db->where('game_platform_id', $game_platform_id);
		// $this->db->where('game_code !=', 'unknown');
		$this->db->delete('game_description');

		$this->db->where('game_platform_id', $game_platform_id);
		// $this->db->where('game_code !=', 'unknown');
		$this->db->delete('game_type');

		$this->db->trans_complete();

	}
}
