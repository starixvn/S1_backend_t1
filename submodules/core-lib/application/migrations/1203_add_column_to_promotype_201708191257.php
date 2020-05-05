<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promotype_201708191257 extends CI_Migration {

	private $tableName = 'promotype';

	public function up() {
		$isUseToPromoManagerField = array(
			'isUseToPromoManager' => array(
				'type' => 'INT',
				'constraint' => 1,
				'default' => 0,
			),			
		);

		$promoIconField = array(
			'promoIcon' => array(
				'type' => 'VARCHAR',
				'constraint' => 200,				
				'null' => true,
			),			
		);

		if (!$this->db->field_exists('isUseToPromoManager', $this->tableName)) {
			$this->dbforge->add_column($this->tableName, $isUseToPromoManagerField);
		}

		if (!$this->db->field_exists('promoIcon', $this->tableName)) {
			$this->dbforge->add_column($this->tableName, $promoIconField);
		}
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'isUseToPromoManager');
		$this->dbforge->drop_column($this->tableName, 'promoIcon');
	}
}

////END OF FILE////