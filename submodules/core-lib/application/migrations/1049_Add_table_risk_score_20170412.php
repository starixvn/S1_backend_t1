<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_risk_score_20170412 extends CI_Migration {

	private $riskScore = 'risk_score';

	public function up() {
		$fields = array(
			"id" => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'category_name' => array(
				'type' => 'VARCHAR',
				'constraint' => 3,
				'null' => false,
			),
			'category_description' => array(
				'type' => 'VARCHAR',
				'constraint' => 150,
				'null' => true,
			),
			'rules' => array(
				'type' => 'TEXT',
				'null' => true,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => false,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->riskScore);

		$data = array(
			array(
				'category_name' => 'R1',
				'category_description' => '30days Total Deposit (CNY)',
				'rules' => json_encode(array(
								array(
									'rule_name' => '>= 1000000',
									'risk_score' => '10'
								),
								array(
									'rule_name' => '50000 ~ 99999',
									'risk_score' => '8'
								),
								array(
									'rule_name' => '20000 ~ 49999',
									'risk_score' => '4'
								),
								array(
									'rule_name' => '< 20000',
									'risk_score' => '1'
								),
								array(
									'rule_name' => '0',
									'risk_score' => '0'
								),
							)),
				'created_at' => '2017-04-12 15:00:00',
			),
			array(
				'category_name' => 'R2',
				'category_description' => '30days Total Withdrawal',
				'rules' => json_encode(array(
								array(
									'rule_name' => '>= 1000000',
									'risk_score' => '10'
								),
								array(
									'rule_name' => '50000 ~ 99999',
									'risk_score' => '8'
								),
								array(
									'rule_name' => '20000 ~ 49999',
									'risk_score' => '4'
								),
								array(
									'rule_name' => '< 20000',
									'risk_score' => '1'
								),
								array(
									'rule_name' => '0',
									'risk_score' => '0'
								),
							)),
				'created_at' => '2017-04-12 15:00:00',
			),
			array(
				'category_name' => 'R3',
				'category_description' => 'Deposit Method',
				'rules' => json_encode(array(
								array(
									'rule_name' => 'Reload Card',
									'risk_score' => '4'
								),
								array(
									'rule_name' => '3 rd Party Payment',
									'risk_score' => '4'
								),
								array(
									'rule_name' => 'Online Transfer',
									'risk_score' => '1'
								),
								array(
									'rule_name' => 'ATM Transfer',
									'risk_score' => '0'
								),
							)),
				'created_at' => '2017-04-12 15:00:00',
			),
			array(
				'category_name' => 'R4',
				'category_description' => 'Country',
				'rules' => json_encode(array(
								array(
									'rule_name' => 'South Korea',
									'risk_score' => '4'
								),
								array(
									'rule_name' => 'United State',
									'risk_score' => '3'
								),
								array(
									'rule_name' => 'China',
									'risk_score' => '0'
								),
							)),
				'created_at' => '2017-04-12 15:00:00',
			),
			array(
				'category_name' => 'R5',
				'category_description' => 'PEP（C6）',
				'rules' => json_encode(array(
								array(
									'rule_name' => 'Political Figure',
									'risk_score' => '4'
								),
								array(
									'rule_name' => 'Have Problem',
									'risk_score' => '3'
								),
								array(
									'rule_name' => 'No Problem',
									'risk_score' => '0'
								),
							)),
				'created_at' => '2017-04-12 15:00:00',
			),
			array(
				'category_name' => 'R6',
				'category_description' => 'Proof of Identity',
				'rules' => json_encode(array(
								array(
									'rule_name' => 'Inconsistent',
									'risk_score' => '3'
								),
								array(
									'rule_name' => 'Consistent',
									'risk_score' => '0'
								),
							)),
				'created_at' => '2017-04-12 15:00:00',
			),
		);
		$this->db->insert_batch($this->riskScore, $data);
	}

	public function down() {
		$this->dbforge->drop_table($this->riskScore);
	}
}