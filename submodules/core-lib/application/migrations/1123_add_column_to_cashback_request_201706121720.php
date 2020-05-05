<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_cashback_request_201706121720 extends CI_Migration {

    private $tableName = 'cashback_request';

    public function up() {
        $fields = array(
            'request_starttime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'cashback_request_type' => array(
                'type' => 'ENUM("period","real","real_auto","rescue")',
                'default' => 'real',
                'null' => true,
            ),
            'deduction_bonus' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'request_starttime');
        $this->dbforge->drop_column($this->tableName, 'cashback_request_type');
        $this->dbforge->drop_column($this->tableName, 'deduction_bonus');
    }
}