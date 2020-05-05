<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OG-693 add some fields to player info
 * if doesn't exist on DB ,create field on DB first.
 * add flag enabled_withdrawal , default set to 1,
 * 1=enabled
 * 0=disabled
 * if enabled_withdrawal==0 then show error for player withdraw also hide withdrawal button on player dashboard
 */
class Migration_Add_column_enabled_withdrawal_to_player_20150909 extends CI_Migration {

	private $tableName = 'player';

	public function up() {
		$this->dbforge->add_column($this->tableName, ["enabled_withdrawal INT(1) DEFAULT 1  NOT NULL  COMMENT '1 - enabled; 0 - disabled' AFTER frozen"]);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'enabled_withdrawal');
	}
}

///END OF FILE/////