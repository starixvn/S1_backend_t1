<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_in_agency_agents_201607201440 extends CI_Migration {

	private $tableName = 'agency_agents';

	public function up() {
		$fields = array(
			'sub_agent_rolling_comm' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
			'player_rolling_comm' => array(
				'type' => 'DOUBLE',
				'null' => true,
				'default' => 0,
			),
        );

		$this->dbforge->add_column($this->tableName, $fields);
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'sub_agent_rolling_comm');
		$this->dbforge->drop_column($this->tableName, 'player_rolling_comm');
	}
}
