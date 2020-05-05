<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_playerbankdetails_20180519 extends CI_Migration {

    private $tableName = 'playerbankdetails';

    public function up() {

        $fields = array(
            'bankAccountFullName' => array(
                'name'=>'bankAccountFullName',
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'bankAccountNumber' => array(
                'name'=>'bankAccountNumber',
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);

    }

    public function down() {

    }
}
