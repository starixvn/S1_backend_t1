<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_cashback_report_daily_201901251955 extends CI_Migration {

    private $tableName='cashback_report_daily';

    public function up() {
        $fields = array(
           'game_platform_code' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'game_platform_code');
    }
}
