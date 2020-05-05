<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_column_to_payment_account_20180112 extends CI_Migration {


    public function up() {
        foreach($this->fields() as $key => $field){
            if (!$this->db->field_exists($key, 'payment_account')) {
                $data = array($key => $field);
                $this->dbforge->add_column('payment_account', $data);
            }
        }
    }

    public function down() {
        foreach($this->fields() as $key => $field){
            if ($this->db->field_exists($key, 'payment_account')) {
                $this->dbforge->drop_column('payment_account', $key);
            }
        }
    }

    public function fields() {
      return  $fields = array(
            'total_approved_deposit_count' => array(
                'type' => 'INT',
                'null' => true,
                'constraint' => '11',
            ),
        );
       
    }



}

////END OF FILE////
