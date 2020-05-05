<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_payment_report_daily_201901211657 extends CI_Migration {

    private $tableName='payment_report_daily';

    public function up() {
        $fields = array(
           'player_group_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null'=> true
            ),
           'player_group_level_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'player_group_name');
        $this->dbforge->drop_column($this->tableName, 'player_group_level_name');
    }
}
