<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_settlement_201703062102 extends CI_Migration {

    public function up() {
        $fields = array(
            'my_earning' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('agency_settlement', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_settlement', 'my_earning');
    }

}

///END OF FILE//////////////////