<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_secretQuestion_and_secretAnswer_to_affiliate_20170222 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {
		$this->dbforge->add_column($this->tableName, array(
			'secretQuestion' => array(
				'type' => 'VARCHAR',
                'constraint' => '100',
				'null' => true,
			),
			'secretAnswer' => array(
				'type' => 'VARCHAR',
                'constraint' => '100',
				'null' => true,
			)
		));
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'secretQuestion');
		$this->dbforge->drop_column($this->tableName, 'secretAnswer');
	}	

}

///END OF FILE//////////