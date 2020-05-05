<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_tables_for_promorules_and_payment_account_20160603 extends CI_Migration {

	public function up() {

		# PROMORULES

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'promoruleId' => array(
				'type' => 'INT',
				'null' => false,
			),
			'affiliateId' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('promorulesallowedaffiliate');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'promoruleId' => array(
				'type' => 'INT',
				'null' => false,
			),
			'playerId' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('promorulesallowedplayer');

		# PAYMENT_ACCOUNT

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'payment_account_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('payment_account_affiliate');

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'payment_account_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('payment_account_player');
	}

	public function down() {
		$this->dbforge->drop_table('promorulesallowedaffiliate');
		$this->dbforge->drop_table('promorulesallowedplayer');
		$this->dbforge->drop_table('payment_account_affiliate');
		$this->dbforge->drop_table('payment_account_player');
	}
}