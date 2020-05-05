<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_account_edit_on_registration_fields_20190408 extends CI_Migration {

    private $tableName = 'registration_fields';

    public function up()
    {
        # Add column
        $fields = array(
            'account_edit' => array(
                'type' => 'SMALLINT',
                'default' => 1,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'account_edit');
    }
}
