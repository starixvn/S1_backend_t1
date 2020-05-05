<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Rename_column_from_cmsgameid_to_game_description_id extends CI_Migration {

	public function up() {
		$fields = array(
			'cmsgameId' => array(
				'name' => 'game_description_id',
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->modify_column('promorulesgamebetrule', $fields);
	}

	public function down() {
		$fields = array(
			'game_description_id' => array(
				'name' => 'cmsgameId',
				'type' => 'INT',
				'null' => false,
			),
		);
		$this->dbforge->modify_column('promorulesgamebetrule', $fields);
	}
}