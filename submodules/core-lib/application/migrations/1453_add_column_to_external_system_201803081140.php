<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_external_system_201803081140 extends CI_Migration {

    public function up() {
        $fields = array(
            'maintenance_mode' => array(
                'type' => 'INT',
                'default' => External_system::DB_FALSE,
                'null' => true,
            ),
        );
        $this->dbforge->add_column('external_system', $fields);
        $this->dbforge->add_column('external_system_list', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('external_system', 'maintenance_mode');
        $this->dbforge->drop_column('external_system_list', 'maintenance_mode');
    }
}