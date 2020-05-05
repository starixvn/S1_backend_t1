<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_operator_settings_201703190115 extends CI_Migration {

    private $tableName = 'operator_settings';

    public function up() {
    	// $this->db->insert($this->tableName, array(
    	// 	'name' => 'special_payment_list_mobile',
    	// 	'value' => '',
    	// 	'note' => '',
    	// ));

     //    $this->load->model('operatorglobalsettings');
     //    $this->operatorglobalsettings->setSpecialPaymentListMobile($this->operatorglobalsettings->getSpecialPaymentList());
    }

    public function down() {
        // $this->db->delete($this->tableName, ['name' => 'special_payment_list_mobile'] );
    }
}