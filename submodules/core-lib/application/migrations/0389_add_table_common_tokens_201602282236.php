<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_common_tokens_201602282236 extends CI_Migration {

	private $tableName = 'common_tokens';

	const NORMAL = 1;

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'player_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'admin_user_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => true,
			),
			'token' => array(
				'type' => 'VARCHAR',
				'constraint' => '200',
				'null' => false,
			),
			'timeout' => array(
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
			'timeout_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => self::NORMAL,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		$this->db->query('create index idx_player_id on common_tokens(player_id)');
		$this->db->query('create index idx_admin_user_id on common_tokens(admin_user_id)');
		$this->db->query('create index idx_affiliate_id on common_tokens(affiliate_id)');
		$this->db->query('create index idx_token on common_tokens(token)');
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}