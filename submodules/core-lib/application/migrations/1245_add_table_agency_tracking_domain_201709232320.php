<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_tracking_domain_201709232320 extends CI_Migration {

	public function up() {

		//tracking code/domain/tag/banner/permission

		//---agency_tracking_domain----------------------------------------------------
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'tracking_type' => array(
				'type' => 'INT',
				'null' => false,
			),
			'tracking_domain' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
			'tracking_source_code' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('agent_id');
		// $this->dbforge->add_key('tracking_domain');
		$this->dbforge->add_key('tracking_source_code');
		$this->dbforge->create_table('agency_tracking_domain');

		$this->db->query('create unique index idx_tracking_domain on agency_tracking_domain(tracking_domain)');

		//---agency_banner----------------------------------------------------
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'banner_name' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
			'banner_file' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
			'width' => array(
				'type' => 'INT',
				'null' => true,
			),
			'height' => array(
				'type' => 'INT',
				'null' => true,
			),
			'file_ext' => array(
				'type' => 'VARCHAR',
				'constraint'=> 50,
				'null' => true,
			),
			'language' => array(
				'type' => 'INT',
				'null' => false,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'created_by' => array(
				'type' => 'INT',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_by' => array(
				'type' => 'INT',
				'null' => true,
			),
			'deleted_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'deleted_by' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('banner_name');
		$this->dbforge->create_table('agency_banner');

		//---agency_tag_list----------------------------------------------------
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'tag_name' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
			'tag_code' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
			'description' => array(
				'type' => 'VARCHAR',
				'constraint'=> 500,
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'created_by' => array(
				'type' => 'INT',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'updated_by' => array(
				'type' => 'INT',
				'null' => true,
			),
			'deleted_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'deleted_by' => array(
				'type' => 'INT',
				'null' => true,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('tag_name');
		$this->dbforge->add_key('tag_code');
		$this->dbforge->create_table('agency_tag_list');

		//---agency_tag----------------------------------------------------
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'tag_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('tag_id');
		$this->dbforge->add_key('agent_id');
		$this->dbforge->create_table('agency_tag');

		//---agency_functions----------------------------------------------------
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'func_code' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => false,
			),
			'func_name_lang' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => false,
			),
			'description' => array(
				'type' => 'VARCHAR',
				'constraint'=> 500,
				'null' => true,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('agency_functions');

		$this->db->query('create unique index idx_func_code on agency_functions(func_code)');

		//---agency_permissions----------------------------------------------------
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'function_id' => array(
				'type' => 'INT',
				'null' => false,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('agent_id');
		$this->dbforge->add_key('function_id');
		$this->dbforge->create_table('agency_permissions');

		//---agency_rates----------------------------------------------------
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => false,
			),
			'rev_share' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			'rolling_comm' => array(
				'type' => 'DOUBLE',
				'null' => true,
			),
			//1=available bet, 2=loss
			'rolling_basis' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'game_platform_id' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'game_type_id' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('agent_id');
		$this->dbforge->add_key('game_platform_id');
		$this->dbforge->add_key('game_type_id');
		$this->dbforge->create_table('agency_rates');

		//---tracking_code----------------------------------------------------
		$fields = array(
			'tracking_code' => array(
				'type' => 'VARCHAR',
				'constraint'=> 150,
				'null' => true,
			),
			'created_by' => array(
				'type' => 'INT',
				'null' => true,
			),
			'updated_by' => array(
				'type' => 'INT',
				'null' => true,
			),
		);
		$this->dbforge->add_column('agency_agents', $fields);

	}

	public function down() {
		$this->dbforge->drop_table('agency_tracking_domain');
		$this->dbforge->drop_table('agency_banner');
		$this->dbforge->drop_table('agency_tag_list');
		$this->dbforge->drop_table('agency_tag');
		$this->dbforge->drop_table('agency_functions');
		$this->dbforge->drop_table('agency_permissions');
		$this->dbforge->drop_table('agency_rates');

		$this->dbforge->drop_column('agency_agents', 'tracking_code');
	}
}
