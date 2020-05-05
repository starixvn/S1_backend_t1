<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_change_upgradeAmount_downgradeAmount_datatype_201612161148 extends CI_Migration {
	
	CONST TableName = 'vipsettingcashbackrule';

	public function up() {

		$fields = array(
        		'upgradeAmount' => array(
		                'type' => 'double',
		        ),
		        'downgradeAmount' => array(
		                'type' => 'double',
		        )
		);
	
		$this->dbforge->modify_column(self::TableName, $fields);
	}

	public function down() {

		$fields = array(
        		'upgradeAmount' => array(
		                'type' => 'int(11)',
		        ),
		        'downgradeAmount' => array(
		                'type' => 'int(11)',
		        )
		);

		$this->dbforge->modify_column(self::TableName, $fields);

	}
}