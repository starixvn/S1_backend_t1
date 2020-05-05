<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_table_201709271122 extends CI_Migration {

	public function up() {
		if (!$this->db->field_exists('guaranteed_period_number', 'vipsettingcashbackrule')) {
			$field = array(
				'guaranteed_period_number' => array(
					'type' => 'INT',
					'null' => false,
					'default' => 1,
				),
			);
			$this->dbforge->add_column('vipsettingcashbackrule', $field);
		}
		if (!$this->db->field_exists('guaranteed_period_total_deposit', 'vipsettingcashbackrule')) {
			$field = array(
				'guaranteed_period_total_deposit' => array(
					'type' => 'DOUBLE',
					'null' => false,
					'default' => 0,
				),
			);
			$this->dbforge->add_column('vipsettingcashbackrule', $field);
		}
	}

	public function down() {
		$this->dbforge->drop_column('vipsettingcashbackrule', 'guaranteed_period_number');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'guaranteed_period_total_deposit');
	}
}
