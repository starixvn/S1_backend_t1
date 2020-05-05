<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_transfer_request_201706102107 extends CI_Migration {

    public function up() {
        $fields = array(
            'flag' => array(
                'type' => 'INT',
                'null'=>true,
                'default'=>1,
            ),
        );

        $this->dbforge->add_column('transfer_request', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('transfer_request', 'flag');
    }
}
