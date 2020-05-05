<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_affiliate_term_games_201606070157 extends CI_Migration {

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			//0=default
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 0,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_description_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'game_platform_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'game_type_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'game_desc_percentage' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('game_description_id');
		$this->dbforge->add_key('affiliate_id');
		$this->dbforge->create_table('affiliate_term_games');

	}

	public function down() {
		$this->dbforge->drop_table('affiliate_term_games');
	}
}