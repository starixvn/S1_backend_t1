<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_group_name_to_player_201509180049 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$this->dbforge->add_column($this->tableName, [
			'groupName' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
		]);

		$this->db->query('update player set ' .
			' groupName=(select vipsetting.groupName from vipsettingcashbackrule join vipsetting on (vipsettingcashbackrule.vipSettingId=vipsetting.vipSettingId) where player.levelId=vipsettingcashbackrule.vipsettingcashbackruleId) ' .
			' where levelId is not null and levelId!=""');
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'groupName');
	}
}