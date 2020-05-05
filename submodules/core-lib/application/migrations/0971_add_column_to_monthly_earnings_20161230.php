<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_monthly_earnings_20161230 extends CI_Migration {

    private $tableName = 'monthly_earnings';

    public function up() {

        $fields = array(
            'platform_fee' => array(
                'type' => 'double',
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'platform_fee');
    }
}
