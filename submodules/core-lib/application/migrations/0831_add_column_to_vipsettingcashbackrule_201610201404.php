<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201610201404 extends CI_Migration {

    public function up() {
        $fields = array(
            'period_up_down' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 1,
            ),
        );

        $this->dbforge->add_column('vipsettingcashbackrule', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('vipsettingcashbackrule', 'period_up_down');
    }
}
