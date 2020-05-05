<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Merge_response_errors_to_response_results extends CI_Migration {

	public function up() {
		$fields = array(
			'flag' => array(
				'type' => 'INT',
				'default' => 1,
				'null' => false,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'related_id1' => array(
				'type' => 'INT',
				'null' => true,
			),
			'related_id2' => array(
				'type' => 'INT',
				'null' => true,
			),
			'related_id3' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('response_results', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('response_results', 'flag');
		$this->dbforge->drop_column('response_results', 'player_id');
		$this->dbforge->drop_column('response_results', 'related_id1');
		$this->dbforge->drop_column('response_results', 'related_id2');
		$this->dbforge->drop_column('response_results', 'related_id3');
	}
}
