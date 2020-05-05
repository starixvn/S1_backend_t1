<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_row_to_risk_score_20170421 extends CI_Migration {

	public function up() {
		$data = json_encode(array(
                    array(
                        'rule_name' => '> 21',
                        'risk_score' => 'Very High',
                        'withdrawal_status' => 'Withdrawal not allowed'
                    ),
                    array(
                        'rule_name' => '15 ~ 20',
                        'risk_score' => 'High',
                        'withdrawal_status' => 'Withdrawal not allowed'
                    ),
                    array(
                        'rule_name' => '8 ~ 14',
                        'risk_score' => 'Medium',
                        'withdrawal_status' => 'Allowed Withdraw'
                    ),
                    array(
                        'rule_name' => '0 ~ 7',
                        'risk_score' => 'Low',
                        'withdrawal_status' => 'Allowed Withdraw'
                    ),
                ));

		$this->db->trans_start();

		$this->db->where('category_name', 'RC')
						->update('risk_score', array(
							'rules' => $data
						));

		$this->db->trans_complete();
	}

	public function down() {

	}
}