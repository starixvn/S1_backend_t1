<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_aff_tracking_link_201607150122 extends CI_Migration {

	public function up() {

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'aff_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'tracking_code' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
			'tracking_domain' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
			'tracking_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('aff_id');
		// $this->dbforge->add_key('tracking_code');
		// $this->dbforge->add_key('tracking_domain');
		$this->dbforge->create_table('aff_tracking_link');

		$this->db->query('create unique index idx_tracking_code on aff_tracking_link(tracking_code)');
		$this->db->query('create unique index idx_tracking_domain on aff_tracking_link(tracking_domain)');

		//import tracking code
		// $this->load->model(['affiliatemodel']);
		// $this->affiliatemodel->startTrans();

		// $this->affiliatemodel->importAffiliateTrackingCodeAndDomain();

		// $succ = $this->affiliatemodel->endTransWithSucc();
		// if (!$succ) {
		// 	throw new Exception('migration is failed');
		// }
	}

	public function down() {
		$this->dbforge->drop_table('aff_tracking_link');
	}
}