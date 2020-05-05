<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agency_traffic_stats_201710021707 extends CI_Migration {

	private $tableName = 'agency_traffic_stats';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'type' => array(
				'type' => 'INT',
				'null' => false,
				'comment' => "1 - site, 2 - banner",
			),
			'tracking_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'tracking_source_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => true,
			),
			'agent_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'cookie' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
				'comment' => "value only",
			),
			'referrer' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'user_agent' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
			),
			'os' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
				'comment' => "get from user_agent",
			),
			'device' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => true,
				'comment' => "get from user_agent",
			),
			'is_mobile' => array(
				'type' => 'int',
				'null' => true,
				'comment' => "get from user_agent: 1- true, 0 - false",
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
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

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->tableName);

		$this->db->query('create index idx_player_id on '.$this->tableName.'(player_id)');
		$this->db->query('create index idx_tracking_code on '.$this->tableName.'(tracking_code)');
		$this->db->query('create index idx_tracking_source_code on '.$this->tableName.'(tracking_source_code)');
		$this->db->query('create index idx_agent_id on '.$this->tableName.'(agent_id)');
		$this->db->query('create index idx_type on '.$this->tableName.'(type)');
		$this->db->query('create index idx_created_at on '.$this->tableName.'(created_at)');
		$this->db->query('create index idx_ip on '.$this->tableName.'(ip)');

	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}