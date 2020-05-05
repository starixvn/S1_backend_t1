<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_affiliate_traffic_stats_201608282158 extends CI_Migration {

	public function up() {
		$fields = array(
			'banner_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'banner_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'banner_url' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'source_code_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'sign_up_player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('affiliate_traffic_stats', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('affiliate_traffic_stats', 'banner_id');
		$this->dbforge->drop_column('affiliate_traffic_stats', 'banner_name');
		$this->dbforge->drop_column('affiliate_traffic_stats', 'banner_url');
		$this->dbforge->drop_column('affiliate_traffic_stats', 'source_code_id');
		$this->dbforge->drop_column('affiliate_traffic_stats', 'sign_up_player_id');
	}
}