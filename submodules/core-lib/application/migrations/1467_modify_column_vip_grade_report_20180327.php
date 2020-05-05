<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_vip_grade_report_20180327 extends CI_Migration {

    private $tableName = 'vip_grade_report';

    public function up() {
        $this->dbforge->modify_column($this->tableName, array(
            'remark' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
    }

    public function down() {}
}