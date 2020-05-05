<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_agent_id_to_balance_history_201607241630 extends CI_Migration {

	public function up() {

		$this->dbforge->add_column('balance_history', array(
			'agent_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		));
	}

	public function down() {
		$this->dbforge->drop_column('balance_history', 'agent_id');
	}
}

///END OF FILE//////////
