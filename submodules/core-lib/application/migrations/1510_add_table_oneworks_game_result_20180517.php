<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_oneworks_game_result_20180517 extends CI_Migration {

	private $tableName = 'oneworks_game_result';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			//main
			'match_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'league_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'home_id' => array(
				'type' => 'SMALLINT',
				'null' => true,
			),
			'away_id' => array(
				'type' => 'SMALLINT',
				'null' => true,
			),
			'home_score' => array(
				'type' => 'SMALLINT',
				'null' => false,
			),
			'away_score' => array(
				'type' => 'SMALLINT',
				'null' => true,
			),
			'ht_home_score' => array(
                'type' => 'SMALLINT',
                'null' => true,
			),
			'ht_away_score' => array(
                'type' => 'SMALLINT',
				'null' => true,
			),
			'game_status' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'sport_type' => array(
                'type' => 'INT',
				'null' => true,
			),
			'is_neutral' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			//sport 190,191
			'VirtualSport_info' => array(
                'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			//Number Game
			'first_ball' => array(
                'type' => 'SMALLINT',
				'null' => true,
			),
			'second_ball' => array(
                'type' => 'SMALLINT',
				'null' => true,
			),
			'third_ball' => array(
                'type' => 'SMALLINT',
				'null' => true,
			),
			'match_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'total_sum' => array(
                'type' => 'INT',
				'null' => true,
			),
			'over_count' => array(
                'type' => 'INT',
				'null' => true,
			),
			'under_count' => array(
                'type' => 'INT',
				'null' => true,
			),
			//casino
			'win_item' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'table_no' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'shoe_no' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'hand_no' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			//sbe column
			'response_result_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('match_id');
		$this->dbforge->create_table($this->tableName);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}
