<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_groupName_type_20171211 extends CI_Migration {
	
	CONST TableName = 'vipsetting';

	public function up() {

		$fields = array(
        		'groupName' => array(
		                'type' => 'VARCHAR',
		                'constraint' => '300',
		                'null' => true,
		        )
		);
	
		$this->dbforge->modify_column(self::TableName, $fields);
	}

	public function down() {

	}
}