<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_column_to_promorule_20180214 extends CI_Migration
{
    private $tableName = 'promorules';

    public function up()
    {
        $fields = array(
            'donot_allow_any_withdrawals_after_deposit' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );
        if (!$this->db->field_exists('donot_allow_any_withdrawals_after_deposit', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields, 'donot_allow_other_promotion');
        }

        $fields = array(
            'donot_allow_any_despoits_after_deposit ' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );

        if (!$this->db->field_exists('donot_allow_any_despoits_after_deposit', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields, 'donot_allow_any_withdrawals_after_deposit');
        }
    }

    public function down()
    {
        if ($this->db->field_exists('donot_allow_any_withdrawals_after_deposit', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'donot_allow_any_withdrawals_after_deposit');
        }
        if ($this->db->field_exists('donot_allow_any_despoits_after_deposit', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'donot_allow_any_despoits_after_deposit');
        }
    }
}
