<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_risk_score_20170419 extends CI_Migration {

    public function up() {
        $data = array(
            array(
                'category_name' => 'RC',
                'category_description' => 'Risk Score Chart',
                'rules' => json_encode(array(
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
                                    'rule_name' => '0 ~ 17',
                                    'risk_score' => 'Low',
                                    'withdrawal_status' => 'Allowed Withdraw'
                                ),
                            )),
                'created_at' => '2017-04-19 17:00:00',
            )
        );
        $this->db->insert_batch('risk_score', $data);
    }

    public function down() {
        $this->db->where_in('category_name', array('Risk Chart'));
        $this->db->delete('risk_score');
    }
}