<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201705142125 extends CI_Migration {

    public function up() {
        $fields = array(
            'badge' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
        );

        $this->dbforge->add_column('vipsettingcashbackrule', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('vipsettingcashbackrule', 'badge');
    }
}