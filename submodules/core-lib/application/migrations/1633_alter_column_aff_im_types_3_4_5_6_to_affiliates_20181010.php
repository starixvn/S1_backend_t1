<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_column_aff_im_types_3_4_5_6_to_affiliates_20181010 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {

		// Alter affiliates
		$fields = array(
			'imType3' => array(
				'type' => 'VARCHAR',
				'constraint' => 100
			),
			'imType4' => array(
				'type' => 'VARCHAR',
				'constraint' => 100
			),
			'imType5' => array(
				'type' => 'VARCHAR',
				'constraint' => 100
			),
			'imType6' => array(
				'type' => 'VARCHAR',
				'constraint' => 100
			),
		);
		
		$this->dbforge->modify_column($this->tableName, $fields);

	}


	public function down() {
       // Alter affiliates
		$fields = array(
			'imType3' => array(
				'type' => 'VARCHAR',
				'constraint' => 45
			),
			'imType4' => array(
				'type' => 'VARCHAR',
				'constraint' => 45
			),
			'imType5' => array(
				'type' => 'VARCHAR',
				'constraint' => 45
			),
			'imType6' => array(
				'type' => 'VARCHAR',
				'constraint' => 45
			),
		);
		
		$this->dbforge->modify_column($this->tableName, $fields);
	}
}