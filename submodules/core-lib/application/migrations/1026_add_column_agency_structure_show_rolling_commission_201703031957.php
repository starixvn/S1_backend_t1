<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_agency_structure_show_rolling_commission_201703031957 extends CI_Migration {

   private $tableName = 'agency_structures';

    public function up() {
        $fields = array(
            'show_rolling_commission' => array(
                'type' => 'INT',
                'default' => 1,
            ),
        );
        $this->dbforge->add_column('agency_structures', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_structures', 'show_rolling_commission');
    }

}

///END OF FILE//////////////////