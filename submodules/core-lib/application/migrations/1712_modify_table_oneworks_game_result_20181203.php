<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_table_oneworks_game_result_20181203 extends CI_Migration {

	private $tableName = 'oneworks_game_result';

	public function up() {
		$fields = array(
			'home_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'away_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'home_score' => array(
				'type' => 'INT',
				'null' => false,
			),
			'away_score' => array(
				'type' => 'INT',
				'null' => true,
			),
			'ht_home_score' => array(
                'type' => 'INT',
                'null' => true,
			),
			'ht_away_score' => array(
                'type' => 'INT',
				'null' => true,
			),
			'first_ball' => array(
                'type' => 'INT',
				'null' => true,
			),
			'second_ball' => array(
                'type' => 'INT',
				'null' => true,
			),
			'third_ball' => array(
                'type' => 'INT',
				'null' => true,
			),
			'key' => array(
                'type' => 'INT',
				'null' => true,
			),
			'lane' => array(
                'type' => 'INT',
				'null' => true,
			),
			'placing' => array(
                'type' => 'INT',
				'null' => true,
			)
		);

        $this->dbforge->modify_column($this->tableName, $fields);
	}

	public function down() {
	}
}
