<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_affiliate_terms_201511102116 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'affiliateId' => array(
				'type' => 'INT',
				'null' => false,
			),
			'optionType' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'optionValue' => array(
				'type' => 'TEXT',
				'null' => true,
			)
		));
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table('affiliate_terms');
	}

	public function down() {
		$this->dbforge->drop_table('affiliate_terms');
	}
}
