<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_total_player_game_hour_201512162019 extends CI_Migration {

	public function up() {
		$fields = array(
			'date_hour' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => false,
			),
		);
		$this->dbforge->add_column('total_player_game_hour', $fields);
		$this->dbforge->add_column('total_operator_game_hour', $fields);

		//fill field
		$sql = <<<EOD
update total_player_game_hour
set date_hour=concat(date_format(date,'%Y%m%d'), LPAD(hour,2,'0'))
EOD;

		$this->db->query($sql);

		$sql = <<<EOD
update total_operator_game_hour
set date_hour=concat(date_format(date,'%Y%m%d'), LPAD(hour,2,'0'))
EOD;

		$this->db->query($sql);

	}

	public function down() {
		$this->dbforge->drop_column('total_player_game_hour', 'date_hour');
		$this->dbforge->drop_column('total_operator_game_hour', 'date_hour');
	}

}
