<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_observation_period_on_dispatch_account_level_20190418 extends CI_Migration {

    private $tableName = 'dispatch_account_level';

    public function up()
    {
        # Add column
        $fields = array(
            'level_observation_period' => array(
                'type' => 'INT',
                'default' => 0,
            )
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'level_observation_period');
    }
}
