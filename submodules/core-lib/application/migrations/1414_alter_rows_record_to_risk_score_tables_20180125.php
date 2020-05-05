<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_rows_record_to_risk_score_tables_20180125 extends CI_Migration {

    public function up() {

        $data = "30days Total Deposit";

        $this->db->trans_start();

        $this->db->where('category_name', 'R1')
                        ->update('risk_score', array(
                            'category_description' => $data
                        ));

        $this->db->trans_complete();
    }

    public function down() {
    }

}