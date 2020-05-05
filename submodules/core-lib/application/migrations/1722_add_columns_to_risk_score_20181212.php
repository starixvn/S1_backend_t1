<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_risk_score_20181212 extends CI_Migration {

    public function up() {
        $fields = [
            'status' => [
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => 0,
                'null' => true,
            ]
        ];

        if(!$this->db->field_exists('status', 'risk_score')){
            $this->dbforge->add_column('risk_score', $fields);
        }

        $this->db->where_in('category_name', ['R1','R2','R3','R4','R5','R6','RC'])
                        ->update('risk_score', array(
                            'status' => 1
                        ));

    }

    public function down() {
        if($this->db->field_exists('status', 'risk_score')){
            $this->dbforge->drop_column('risk_score', 'status');
        }
    }
}
