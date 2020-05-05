<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_playerpromo_201604070059 extends CI_Migration {

	private $tableName = 'playerpromo';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'level_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));

		//update game_type_id, level_id, updated_at
		$sql = <<<EOD
update playerpromo set
level_id=(select levelId from player where player.playerId=playerpromo.playerId)
where playerId is not null
EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'level_id');
	}
}

///END OF FILE//////////