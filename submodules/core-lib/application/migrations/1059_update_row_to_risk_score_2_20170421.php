<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_row_to_risk_score_2_20170421 extends CI_Migration {

	public function up() {
		$data = json_encode(array(
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
                        'risk_score' => '2'
                    ),
                    array(
                        'rule_name' => '< 20000',
                        'risk_score' => '1'
                    ),
                    array(
                        'rule_name' => '0',
                        'risk_score' => '0'
                    ),
                ));

		$this->db->trans_start();

		$this->db->where('category_name', 'R1')
						->update('risk_score', array(
							'rules' => $data
						));

		$this->db->trans_complete();
	}

	public function down() {

	}
}