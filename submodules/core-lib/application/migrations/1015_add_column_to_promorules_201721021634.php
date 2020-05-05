<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201721021634 extends CI_Migration {

    public function up() {
        $fields = array(
            'request_limit' => array(
                'type' => 'INT',
                'constraint' => '3',
                'null' => true,
            )
        );

        $this->dbforge->add_column('promorules', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('promorules', 'request_limit');
    }
}
