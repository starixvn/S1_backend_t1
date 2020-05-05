<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_aff_im_account_3_4_5_6_to_playerdetails_20181004 extends CI_Migration {

	private $tableName = 'affiliates';

	public function up() {

		$exist_fields = $this->db->list_fields($this->tableName);

		// Alter playerdetails
		$fields = array(
			'im3' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
			),
			'imType3' => array(
				'type' => 'VARCHAR',
				'constraint' => 45,
			),
			'im4' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
			),
			'imType4' => array(
				'type' => 'VARCHAR',
				'constraint' => 45,
			),
			'im5' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
			),
			'imType5' => array(
				'type' => 'VARCHAR',
				'constraint' => 45,
			),
			'im6' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
			),
			'imType6' => array(
				'type' => 'VARCHAR',
				'constraint' => 45,
			),
		);
		foreach ($fields as $key => $value) {
			if(!in_array($key, $exist_fields)){
				$after = null;
				switch ($key) {
					case 'im3':
					$after = 'imType2';
					break;
					case 'imType3':
					$after = 'im3';
					break;
					case 'im4':
					$after = 'imType3';
					break;
					case 'imType4':
					$after = 'im4';
					break;
					case 'im5':
					$after = 'imType4';
					break;
					case 'imType5':
					$after = 'im5';
					break;
					case 'im6':
					$after = 'imType5';
					break;
					case 'imType6':
					$after = 'im6';
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
		$this->dbforge->drop_column($this->tableName, 'im3');
		$this->dbforge->drop_column($this->tableName, 'imType3');
		$this->dbforge->drop_column($this->tableName, 'im4');
		$this->dbforge->drop_column($this->tableName, 'imType4');
		$this->dbforge->drop_column($this->tableName, 'im5');
		$this->dbforge->drop_column($this->tableName, 'imType5');
		$this->dbforge->drop_column($this->tableName, 'im6');
		$this->dbforge->drop_column($this->tableName, 'imType6');
	}
}