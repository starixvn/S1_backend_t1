<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_ag_hunter_for_ag_gamelogs_201610282049 extends CI_Migration {

	private $tableName = 'ag_game_logs';

	public function up() {


        // $this->db->trans_start();

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

			'exchangeRate' => array(
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => true,
			)
		);

		$this->dbforge->add_column($this->tableName , $fields);

		 // $this->db->trans_complete();

	}

	public function down() {

		// $this->db->trans_start();
		$this->dbforge->drop_column($this->tableName , 'playerId');
		$this->dbforge->drop_column($this->tableName , 'logs_ID');
		$this->dbforge->drop_column($this->tableName , 'tradeNo');
		$this->dbforge->drop_column($this->tableName , 'sceneId');
		$this->dbforge->drop_column($this->tableName , 'SceneStartTime');
		$this->dbforge->drop_column($this->tableName , 'SceneEndTime');
		$this->dbforge->drop_column($this->tableName , 'Roomid');
		$this->dbforge->drop_column($this->tableName , 'Roombet');
		$this->dbforge->drop_column($this->tableName , 'Cost');
		$this->dbforge->drop_column($this->tableName , 'Earn');
		$this->dbforge->drop_column($this->tableName , 'Jackpotcomm');
		$this->dbforge->drop_column($this->tableName , 'exchangeRate');
		// $this->db->trans_complete();
	}
}