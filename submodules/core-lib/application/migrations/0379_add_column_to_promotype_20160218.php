<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promotype_20160218 extends CI_Migration {

	private $tableName = 'promotype';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'promoTypeCode' => array(
				'type' => 'VARCHAR',
				'constraint' => '45',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'promoTypeCode');
	}
}

///END OF FILE//////////
