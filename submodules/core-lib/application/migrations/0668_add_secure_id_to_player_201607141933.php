<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_secure_id_to_player_201607141933 extends CI_Migration {

	public function up() {
		$fields = array(
			'secure_id' => array(
				'type' => 'VARCHAR',
				'constraint' => 64,
				'null' => true,
			),
		);

		$this->dbforge->add_column('player', $fields);

		$this->load->model(['player_model']);
		$this->player_model->batchCreateSecureId();

	}

	public function down() {
		$this->dbforge->drop_column('player', 'secure_id');
	}



}