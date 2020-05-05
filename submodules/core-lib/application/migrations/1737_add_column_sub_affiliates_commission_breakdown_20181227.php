<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_sub_affiliates_commission_breakdown_20181227 extends CI_Migration {

    private $tblName = 'aff_monthly_earnings';

    public function up() {

        $field = [
            'sub_aff_commission_breakdown' => [
                'type' => 'TEXT',
                'null' => true
            ]
        ];
        if(!$this->db->field_exists('sub_aff_commission_breakdown', $this->tblName)){
            $this->dbforge->add_column($this->tblName, $field);
        }

    }

    public function down() {
        if($this->db->field_exists('sub_aff_commission_breakdown', $this->tblName)){
            $this->dbforge->drop_column($this->tblName, 'sub_aff_commission_breakdown');
        }
    }
}
