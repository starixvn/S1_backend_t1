<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_risk_score_20190624 extends CI_Migration {

    public function up() {
        $data = array(
            array(
                'category_name' => 'R8',
                'category_description' => 'C6',
                'rules' => json_encode(array(
                                array(
                                    'rule_name' => 'True',
                                    'risk_score' => '31'
                                ),
                                array(
                                    'rule_name' => 'False',
                                    'risk_score' => '0',
                                    'default_score' => 1
                                )
                            )),
                'created_at' => '2019-06-24 21:00:00',
            )
        );
        $this->db->insert_batch('risk_score', $data);

        $data = 'PEP';

        $this->db->trans_start();

        $this->db->where('category_name', 'R5')
                        ->update('risk_score', array(
                            'category_description' => $data
                        ));

        $this->db->trans_complete();
    }

    public function down() {
        $this->db->where_in('category_name', array('R8'));
        $this->db->delete('risk_score');
    }
}