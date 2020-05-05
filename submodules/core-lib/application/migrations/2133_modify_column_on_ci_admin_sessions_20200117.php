<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_modify_column_on_ci_admin_sessions_20200117 extends CI_Migration

{
    private $tableName = "ci_admin_sessions";

    public function up()
    {
        $fields = array(
            'user_data' => array(
                'type' => 'MEDIUMTEXT',
                'null' => true,
            ),
        );

        if($this->db->field_exists('user_data', $this->tableName)) {
            $this->dbforge->modify_column($this->tableName, $fields);
        }

    }

    public function down()
    {

    }
}