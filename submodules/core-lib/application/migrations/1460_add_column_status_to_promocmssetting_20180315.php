<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_status_to_promocmssetting_20180315 extends CI_Migration
{
    private $tableName = 'promocmssetting';

    public function up()
    {
        $fields = array(
            'default_lang' => array(
                'type' => 'INT',
                'default' => 1,
                'null' => false,
            )
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'default_lang');
    }
}
