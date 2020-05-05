<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_agency_agents_20190513 extends CI_Migration {

    private $tableName='agency_agents';

    public function up() {
        $fields = array(
           'readonly_sub_account' => array(
                'type' => 'JSON',
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'first_category_flag');
    }
}
