<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_admin_dashboard_20180420 extends CI_Migration {

    private $tableName = 'admin_dashboard';

    public function up() {

        $fields = array(
            'today_total_deposit_list' => array(
                'type' => 'text',
                'null' => true,
            ),
            'today_total_withdrawal_list' => array(
                'type' => 'text',
                'null' => true,
            ),
           
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'today_total_deposit_list');
        $this->dbforge->drop_column($this->tableName, 'today_total_withdrawal_list');
    }
}
