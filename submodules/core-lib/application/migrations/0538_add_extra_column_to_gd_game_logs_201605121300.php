<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_extra_column_to_gd_game_logs_201605121300 extends CI_Migration {

	private $tableName = 'gd_game_logs';

	public function up() {

		$this->db->trans_start();
		$this->dbforge->add_column($this->tableName, [
			
			'uniqueid' => array(
				'type' => 'VARCHAR',
				'constraint' => '150',
				'null' => true,
			),
			'gameshortcode' => array(
				'type' => 'VARCHAR',
				'constraint' => '150',
				'null' => true,
			)
		]);

		$fields = array(
                        'bet_result' => array(
                                        'type' => 'VARCHAR',
										'constraint' => '100',
										'null' => true,
                                         )
					);

       $this->dbforge->modify_column($this->tableName, $fields);

	    $this->db->trans_complete();
	
	}

	public function down() {
		$this->db->trans_start();
		$this->dbforge->drop_column($this->tableName, 'uniqueid');
		$this->dbforge->drop_column($this->tableName, 'gameshortcode');
		$this->db->trans_complete();
	}
}