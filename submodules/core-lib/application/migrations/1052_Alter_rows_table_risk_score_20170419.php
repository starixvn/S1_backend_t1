<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_rows_table_risk_score_20170419 extends CI_Migration {

	public function up() {
		$data = json_encode(array(
								array(
									'rule_name' => 'Reload Card',
									'risk_score' => '4'
								),
								array(
									'rule_name' => '3rd Party Payment',
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
							));

		$this->db->where('id', 3)
						->update('risk_score', array(
							'rules' => $data,
						));

		$this->db->trans_complete();

		$data = json_encode(array(
								array(
									'rule_name' => 'South Korea',
									'risk_score' => '4'
								),
								array(
									'rule_name' => 'United States',
									'risk_score' => '3'
								),
								array(
									'rule_name' => 'China',
									'risk_score' => '0'
								),
							));

		$this->db->where('id', 4)
						->update('risk_score', array(
							'rules' => $data,
						));

		$this->db->trans_complete();
	}

	public function down() {
		
	}
}