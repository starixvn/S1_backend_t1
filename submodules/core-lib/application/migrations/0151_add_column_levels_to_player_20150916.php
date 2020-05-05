<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_levels_to_player_20150916 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		# ADD COLUMNS
		$this->dbforge->add_column($this->tableName, [
			'levelId' => array(
				'type' => 'INT',
				'null' => true,
			),
			'levelName' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		]);

		# RETRIEVE DATA TO BE USED IN FILLING UP THE COLUMNS
		$playerLevels = $this->db->select('playerlevel.playerId,
				playerlevel.playerGroupId levelId,
				vipsettingcashbackrule.vipLevelName levelName')
			->from('playerlevel')
			->join('vipsettingcashbackrule', 'playerlevel.playerGroupId = vipsettingcashbackrule.vipsettingcashbackruleId')
			->get()
			->result_array();

		# FILL UP THE COLUMNS
		foreach ($playerLevels as $playerLevel) {
			$this->db->update($this->tableName, [
				'levelId' 	=> $playerLevel['levelId'],
				'levelName' => $playerLevel['levelName'],
			],[
				'playerId' 	=> $playerLevel['playerId'],
			]);
		}

	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'levelId');
		$this->dbforge->drop_column($this->tableName, 'levelName');
	}
}