<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_risk_score_20181212 extends CI_Migration {

    public function up() {
        $data = array(
            array(
                'category_name' => 'R7',
                'category_description' => 'Total Withdrawal - Total Deposit',
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
                'created_at' => '2018-12-12 21:00:00',
            )
        );
        $this->db->insert_batch('risk_score', $data);
    }

    public function down() {
        $this->db->where_in('category_name', array('R7'));
        $this->db->delete('risk_score');
    }
}