<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_table_201710021700 extends CI_Migration {

	public function up() {
		if (!$this->db->field_exists('downgrade_promo_cms_id', 'vipsettingcashbackrule')) {
			$field = array(
				'downgrade_promo_cms_id' => array(
					'type' => 'INT',
					'null' => true,
				),
			);
			$this->dbforge->add_column('vipsettingcashbackrule', $field);
		}
		if (!$this->db->field_exists('downgrade_promo_rule_id', 'vipsettingcashbackrule')) {
			$field = array(
				'downgrade_promo_rule_id' => array(
					'type' => 'INT',
					'null' => true,
				),
			);
			$this->dbforge->add_column('vipsettingcashbackrule', $field);
		}
		if (!$this->db->field_exists('guaranteed_downgrade_period_number', 'vipsettingcashbackrule')) {
			$field = array(
				'guaranteed_downgrade_period_number' => array(
					'type' => 'INT',
					'null' => true,
				),
			);
			$this->dbforge->add_column('vipsettingcashbackrule', $field);
		}
		if (!$this->db->field_exists('guaranteed_downgrade_period_total_deposit', 'vipsettingcashbackrule')) {
			$field = array(
				'guaranteed_downgrade_period_total_deposit' => array(
					'type' => 'INT',
					'null' => true,
				),
			);
			$this->dbforge->add_column('vipsettingcashbackrule', $field);
		}
		if (!$this->db->field_exists('vip_downgrade_id', 'vipsettingcashbackrule')) {
			$field = array(
				'vip_downgrade_id' => array(
					'type' => 'INT',
					'null' => true,
				),
			);
			$this->dbforge->add_column('vipsettingcashbackrule', $field);
		}
		if (!$this->db->field_exists('period_down', 'vipsettingcashbackrule')) {
			$field = array(
				'period_down' => array(
					'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => true,
				),
			);
			$this->dbforge->add_column('vipsettingcashbackrule', $field);
		}
	}

	public function down() {
		$this->dbforge->drop_column('vipsettingcashbackrule', 'downgrade_promo_cms_id');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'downgrade_promo_rule_id');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'guaranteed_downgrade_period_number');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'guaranteed_downgrade_period_total_deposit');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'vip_downgrade_id');
		$this->dbforge->drop_column('vipsettingcashbackrule', 'period_down');
	}
}
