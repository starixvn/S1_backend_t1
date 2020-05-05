<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_promo_rule_templates_201605161731 extends CI_Migration {

	private $tableName = 'promo_rule_templates';

	public function up() {
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => TRUE,
			),
			'template_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => false,
			),
			'template_parameters' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'template_content' => array(
				'type' => 'TEXT',
				'null' => false,
			),
			'status' => array(
				'type' => 'INT',
				'null' => false,
				'default' => 1,
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
		$this->dbforge->create_table($this->tableName);

		$this->db->query('create unique index idx_template_name on ' . $this->tableName . '(template_name)');

		// $this->load->model(array('promo_rule_templates'));
		// $this->promo_rule_templates->fixDefaultTemplates();
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}