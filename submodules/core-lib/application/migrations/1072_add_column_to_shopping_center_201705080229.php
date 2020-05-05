<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_shopping_center_201705080229 extends CI_Migration {

	public function up() {
		$fields = array(
			'is_default_banner_flag' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'banner_url' => array(
				'type' => 'VARCHAR',
				'constraint' => 300,
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('shopping_center', $fields);
	}

	public function down() {
		if( $this->db->field_exists('is_default_banner_flag','shopping_center') ){
		$this->dbforge->drop_column('shopping_center','is_default_banner_flag');
		}
		if( $this->db->field_exists('banner_url','shopping_center') ){
		$this->dbforge->drop_column('shopping_center','banner_url');
		}
	}
}