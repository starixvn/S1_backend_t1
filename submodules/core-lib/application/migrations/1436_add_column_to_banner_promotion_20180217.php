<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_column_to_banner_promotion_20180217 extends CI_Migration
{
    private $tableName = 'banner_promotion_ss';

    public function up()
    {
        $fields = array(
            'banner_promotion_order ' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );      

        if (!$this->db->field_exists('banner_promotion_order', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down()
    {
        if ($this->db->field_exists('banner_promotion_order', $this->tableName)) {
            $this->dbforge->drop_column($this->tableName, 'banner_promotion_order');
        }
    }
}
