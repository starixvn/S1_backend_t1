<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_affiliate_earning_tables_20180910 extends CI_Migration {

    private $tblAffDaily = 'aff_daily_earnings';
    private $tblAffMonth = 'aff_monthly_earnings';

    public function up() {
        $field = array(
            'total_rake' => array(
                'type' => 'double',
                'null' => true,
                'default' => 0,
            ),
        );

        if(!$this->db->field_exists('total_rake', $this->tblAffDaily)){
            $this->dbforge->add_column($this->tblAffDaily, $field);
        }

        if(!$this->db->field_exists('total_rake', $this->tblAffMonth)){
            $this->dbforge->add_column($this->tblAffMonth, $field);
        }
    }

    public function down() {
        if($this->db->field_exists('total_rake', $this->tblAffDaily)){
            $this->dbforge->drop_column($this->tblAffDaily, 'total_rake');
        }
        if($this->db->field_exists('total_rake', $this->tblAffMonth)){
            $this->dbforge->drop_column($this->tblAffMonth, 'total_rake');
        }
    }
}