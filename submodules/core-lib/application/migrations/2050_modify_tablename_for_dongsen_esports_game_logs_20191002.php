<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_tablename_for_dongsen_esports_game_logs_20191002 extends CI_Migration {

	private $tableName = 'dongsen_es_cny1_game_logs';

	public function up() {

		$this->dbforge->rename_table($this->tableName, 'dongsen_esports_game_logs');

	}

}