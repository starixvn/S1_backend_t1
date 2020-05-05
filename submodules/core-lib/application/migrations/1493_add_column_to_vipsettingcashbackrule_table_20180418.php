<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_table_20180418 extends CI_Migration
{
    private $tableName = 'vipsettingcashbackrule';

    public function up()
    {
        if (!$this->db->field_exists('accumulation_period_number', $this->tableName)) {
            $field = array(
                'accumulation_period_number' => array(
                    'type' => 'INT',
                    'null' => false,
                    'default' => 1,
                ),
            );
            $this->dbforge->add_column($this->tableName, $field);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'accumulation_period_number');
    }
}