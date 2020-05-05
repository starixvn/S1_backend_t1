<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_details_to_aff_monthly_earnings_20180316 extends CI_Migration
{
    private $tableName = 'aff_monthly_earnings';

    public function up()
    {
        $fields = array(
            'details' => array(
                'type' => 'TEXT',
                'null' => true,
            )
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'details');
    }
}
