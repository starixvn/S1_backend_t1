<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_column_to_payment_account_20180212 extends CI_Migration 
{
    private $tableName = 'payment_account';

    public function up()
    {
        $fields = array(
            'only_allow_affiliate ' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );
        if (!$this->db->field_exists('only_allow_affiliate ', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down()
    {
        if ($this->db->field_exists('only_allow_affiliate ', $this->tableName)) {
            $this->dbforge->drop_column('only_allow_affiliate ', $key);
        }
    }
}
