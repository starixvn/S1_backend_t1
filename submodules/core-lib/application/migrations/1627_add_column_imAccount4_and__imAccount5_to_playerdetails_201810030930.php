<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_imAccount4_and__imAccount5_to_playerdetails_201810030930 extends CI_Migration {

	private $tableName = 'playerdetails';

	public function up() {

		$exist_fields = $this->db->list_fields($this->tableName);

		// Alter playerdetails
		$fields = array(
			'imAccount4' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
			),
			'imAccountType4' => array(
				'type' => 'VARCHAR',
				'constraint' => 32,
			),
			'imAccount5' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
			),
			'imAccountType5' => array(
				'type' => 'VARCHAR',
				'constraint' => 32,
			),
		);
		foreach ($fields as $key => $value) {
			if(!in_array($key, $exist_fields)){
				$after = null;
				switch ($key) {
					case 'imAccount4':
					$after = 'imAccountType3';
					break;
					case 'imAccountType4':
					$after = 'imAccount4';
					break;
					case 'imAccount5':
					$after = 'imAccountType4';
					break;
					case 'imAccountType5':
					$after = 'imAccount5';
					break;
					default:
						# 
					break;
				}
				$this->dbforge->add_column($this->tableName, array($key=>$value), $after);
			}
		}
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'imAccountType4');
		$this->dbforge->drop_column($this->tableName, 'imAccount4');
		$this->dbforge->drop_column($this->tableName, 'imAccountType5');
		$this->dbforge->drop_column($this->tableName, 'imAccount5');
	}
}