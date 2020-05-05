<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_total_cashback_player_game_daily_201604040108 extends CI_Migration {

	private $tableName = 'total_cashback_player_game_daily';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'game_type_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'level_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		));

		//update game_type_id, level_id, updated_at
		$sql = <<<EOD
update total_cashback_player_game_daily set
game_type_id=(select game_type_id from game_description where game_description.id=total_cashback_player_game_daily.game_description_id),
level_id=(select level_id from player_level_history where player_level_history.id=total_cashback_player_game_daily.history_id),
updated_at=NOW()
EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'game_type_id');
		$this->dbforge->drop_column($this->tableName, 'level_id');
		$this->dbforge->drop_column($this->tableName, 'updated_at');
	}
}

///END OF FILE//////////