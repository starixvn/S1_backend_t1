<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_gl_game_logs_20190625 extends CI_Migration {

	public function up() {
		$fields = array(
			'i18n_lottery_name_show' => array(
				'type' => 'varchar',
				'constraint' => '100',
				'null' => TRUE,
			),
			'i18n_method_name_show' => array(
				'type' => 'varchar',
				'constraint' => '100',
				'null' => TRUE,
			),
			'i18n_method_lv1_name_show' => array(
				'type' => 'varchar',
				'constraint' => '100',
				'null' => TRUE,
			),
			'i18n_status_flag_show' => array(
				'type' => 'varchar',
				'constraint' => '100',
				'null' => TRUE,
			),
			'method_name_show' => array(
				'type' => 'varchar',
				'constraint' => '100',
				'null' => TRUE,
			),
			'cnname_show' => array(
				'type' => 'varchar',
				'constraint' => '100',
				'null' => TRUE,
			),
			
		);

		$this->dbforge->add_column('gl_game_logs', $fields);

	}

	public function down() {
		$this->dbforge->drop_column('gl_game_logs', 'i18n_lottery_name_show');
		$this->dbforge->drop_column('gl_game_logs', 'i18n_method_name_show');
		$this->dbforge->drop_column('gl_game_logs', 'i18n_method_lv1_name_show');
		$this->dbforge->drop_column('gl_game_logs', 'i18n_status_flag_show');
		$this->dbforge->drop_column('gl_game_logs', 'method_name_show');
		$this->dbforge->drop_column('gl_game_logs', 'cnname_show');
	}
}
