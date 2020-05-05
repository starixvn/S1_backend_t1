<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_column_to_promorule_20180331 extends CI_Migration
{
    private $tableName = 'promorules';

    public function up()
    {
        $fields = array(
            'donot_allow_any_transfer_in_after_transfer' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );
        if (!$this->db->field_exists('donot_allow_any_transfer_in_after_transfer', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields, 'donot_allow_any_despoits_after_deposit');
        }

        $fields = array(
            'donot_allow_any_transfer_out_after_transfer ' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );

        if (!$this->db->field_exists('donot_allow_any_transfer_out_after_transfer', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields, 'donot_allow_any_transfer_in_after_transfer');
        }
    }

    public function down()
    {
        if ($this->db->field_exists('donot_allow_any_transfer_in_after_transfer', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'donot_allow_any_transfer_in_after_transfer');
        }
        if ($this->db->field_exists('donot_allow_any_transfer_out_after_transfer', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'donot_allow_any_transfer_out_after_transfer');
        }
    }
}
