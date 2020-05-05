<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_agfishing_gamelogs_and_Add_column_agin_gamelogs_201610101530 extends CI_Migration {

	private $tableName = 'agin_game_logs';

	public function up() {

		$this->dbforge->drop_table('agfishing_game_logs'); // drop ag fishing game gamelogs table

		$this->db->query("ALTER TABLE agin_game_logs MODIFY billno VARCHAR(32) NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY playername VARCHAR(32) NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY agentcode VARCHAR(32) NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY gamecode VARCHAR(32) NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY netamount DOUBLE NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY bettime DATETIME ");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY gametype VARCHAR(32) NULL");

		$fields = array(
			'playerId' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
			'logs_ID' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'tradeNo' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'sceneId' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'SceneStartTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'SceneEndTime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'Roomid' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'Roomid' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => true,
			),
			'Roombet' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Cost' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Earn' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'Jackpotcomm' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'transferAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'previousAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'currentAmount' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'exchangeRate' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			)
		);

		$this->dbforge->add_column($this->tableName , $fields);

	}

	public function down() {
		
		$this->db->query("ALTER TABLE agin_game_logs MODIFY billno VARCHAR(32) NOT NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY playername VARCHAR(32) NOT NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY agentcode VARCHAR(32) NOT NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY gamecode VARCHAR(32) NOT NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY netamount DOUBLE NOT NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY bettime DATETIME NOT NULL");
		$this->db->query("ALTER TABLE agin_game_logs MODIFY gametype VARCHAR(32) NOT NULL");


		$this->dbforge->drop_column($this->tableName , 'playerId');
		$this->dbforge->drop_column($this->tableName , 'logs_ID');
		$this->dbforge->drop_column($this->tableName , 'datatype');
		$this->dbforge->drop_column($this->tableName , 'tradeNo');
		$this->dbforge->drop_column($this->tableName , 'sceneId');
		$this->dbforge->drop_column($this->tableName , 'SceneStartTime');
		$this->dbforge->drop_column($this->tableName , 'SceneEndTime');
		$this->dbforge->drop_column($this->tableName , 'Roomid');
		$this->dbforge->drop_column($this->tableName , 'Roombet');
		$this->dbforge->drop_column($this->tableName , 'Cost');
		$this->dbforge->drop_column($this->tableName , 'Earn');
		$this->dbforge->drop_column($this->tableName , 'Jackpotcomm');
		$this->dbforge->drop_column($this->tableName , 'transferAmount');
		$this->dbforge->drop_column($this->tableName , 'previousAmount');
		$this->dbforge->drop_column($this->tableName , 'currentAmount');
		$this->dbforge->drop_column($this->tableName , 'exchangeRate');
	}
}