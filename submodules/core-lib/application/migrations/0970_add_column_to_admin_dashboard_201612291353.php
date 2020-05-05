<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_admin_dashboard_201612291353 extends CI_Migration {

    private $tableName = 'admin_dashboard';

    public function up() {

        $fields = array(
            'today_deposit_count' => array(
                'type' => 'int',
                'null' => true,
            ),
            'today_withdraw_count' => array(
                'type' => 'int',
                'null' => true,
            ),
           
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'today_deposit_count');
        $this->dbforge->drop_column($this->tableName, 'today_withdraw_count');
    }
}
