<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_super_report_tables_column_player_level_201803121626 extends CI_Migration {

	public function up() {
		
		$fields = array(
                    'player_level' => array(
                                    'type' => 'VARCHAR',
									'constraint' => '400',
									'null' => true
                                     )
				);

      	$this->dbforge->modify_column('super_player_report', $fields);

		$fields = array(
		            'player_level' => array(
		                            'type' => 'VARCHAR',
									'constraint' => '400',
									'null' => true
		                             )
				);

		$this->dbforge->modify_column('super_cashback_report', $fields);

		$fields = array(
		            'player_level' => array(
		                            'type' => 'VARCHAR',
									'constraint' => '400',
									'null' => true
		                             )
				);

		$this->dbforge->modify_column('super_game_report', $fields);

		$fields = array(
		            'player_level' => array(
		                            'type' => 'VARCHAR',
									'constraint' => '400',
									'null' => true
		                             )
				);

		$this->dbforge->modify_column('super_payment_report', $fields);

		$fields = array(
		            'player_level' => array(
		                            'type' => 'VARCHAR',
									'constraint' => '400',
									'null' => true
		                             )
				);

		$this->dbforge->modify_column('super_promotion_report', $fields);

	}

	public function down() {

		$fields = array(
                        'player_level' => array(
                                        'type' => 'VARCHAR',
										'constraint' => '200',
										'null' => true
                                         )
					);

		$this->dbforge->modify_column('super_player_report', $fields);

		$fields = array(
		            'player_level' => array(
		                            'type' => 'VARCHAR',
									'constraint' => '200',
									'null' => true
		                             )
				);

		$this->dbforge->modify_column('super_cashback_report', $fields);

		$fields = array(
		            'player_level' => array(
		                            'type' => 'VARCHAR',
									'constraint' => '200',
									'null' => true
		                             )
				);

		$this->dbforge->modify_column('super_game_report', $fields);

		$fields = array(
		            'player_level' => array(
		                            'type' => 'VARCHAR',
									'constraint' => '200',
									'null' => true
		                             )
				);

		$this->dbforge->modify_column('super_payment_report', $fields);

		$fields = array(
		            'player_level' => array(
		                            'type' => 'VARCHAR',
									'constraint' => '200',
									'null' => true
		                             )
				);

		$this->dbforge->modify_column('super_promotion_report', $fields);
	}
}