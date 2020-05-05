<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_settlement_201703061651 extends CI_Migration {

    public function up() {
        $fields = array(
            'actual_amt_payable' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('agency_settlement', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_settlement', 'actual_amt_payable');
    }

}

///END OF FILE//////////////////