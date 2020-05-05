<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_admin_dashboard_20190514 extends CI_Migration {

    private $tableName = 'admin_dashboard';

    public function up() {

        $fields = array(
            'total_bet_amount_all_time' => array(
                'type' => 'double',
                'null' => true,
            ),
            'total_deposit_amount_all_time' => array(
                'type' => 'double',
                'null' => true,
            ),
            'total_withdraw_amount_all_time' => array(
                'type' => 'double',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'total_bet_amount_all_time');
        $this->dbforge->drop_column($this->tableName, 'total_deposit_amount_all_time');
        $this->dbforge->drop_column($this->tableName, 'total_withdraw_amount_all_time');
    }
}
