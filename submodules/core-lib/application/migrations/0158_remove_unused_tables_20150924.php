<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_unused_tables_20150924 extends CI_Migration {

	private $tableName = 'game_description';

	function up() {
		$this->dbforge->drop_table('mkt_currency');
		$this->dbforge->drop_table('mkt_level');
		$this->dbforge->drop_table('mkt_promo');
		$this->dbforge->drop_table('mkt_promocategory');
		$this->dbforge->drop_table('mkt_promocurrency');
		$this->dbforge->drop_table('mkt_promodescription');
		$this->dbforge->drop_table('mkt_promogame');
		$this->dbforge->drop_table('mkt_promolevel');
		$this->dbforge->drop_table('mkt_promorule');
	}

	public function down() {
	}
}