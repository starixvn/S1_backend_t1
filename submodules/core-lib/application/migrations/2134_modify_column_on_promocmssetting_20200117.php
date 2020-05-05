<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_modify_column_on_promocmssetting_20200117 extends CI_Migration

{
    private $tableName = "promocmssetting";

    public function up()
    {
        $fields = array(
            'promo_multi_lang' => array(
                'type' => 'MEDIUMTEXT',
                'null' => true,
            ),
        );

        if($this->db->field_exists('promo_multi_lang', $this->tableName)) {
            $this->dbforge->modify_column($this->tableName, $fields);
        }

    }

    public function down()
    {

    }
}