<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_affiliate_domain_20161013 extends CI_Migration {

	public function up() {

		$this->dbforge->add_field(array(
			'affiliateId' => array(
				'type' => 'INT',
				'null' => false,
			),
			'domainId' => array(
				'type' => 'INT',
				'null' => false,
			),
		));
		$this->dbforge->add_key(array('affiliateId','domainId'), TRUE);
		$this->dbforge->create_table('affiliate_domain');

		$fields = array(
			'show_to_affiliate' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
			),
		);

		$this->dbforge->add_column('domain', $fields);
	}

	public function down() {
		$this->dbforge->drop_table('affiliate_domain');
		$this->dbforge->drop_column('domain', 'show_to_affiliate');
	}

}