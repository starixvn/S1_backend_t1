<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_column_to_affiliatepayment_20170406 extends CI_Migration {

    protected $tableName = "affiliatepayment";

    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'editCount' => array(
                'type' => 'INT',
                'null' => false
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'editCount');
    }
}