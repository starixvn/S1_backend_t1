<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_shopping_center_201705080218 extends CI_Migration {

	public function up() {
		$fields = array(
			'how_many_available' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
		);
		$this->dbforge->add_column('shopping_center', $fields);
	}

	public function down() {
		if( $this->db->field_exists('how_many_available','shopping_center')){
		$this->dbforge->drop_column('shopping_center','how_many_available');
		}
	}
}