<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_in_transfer_request_201804051403 extends CI_Migration {

    private $tableName = 'transfer_request';

    public function up() {

        $this->dbforge->modify_column($this->tableName, array(
            'transfer_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
        ));

    }

    public function down() {

    }

}
