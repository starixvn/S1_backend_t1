<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_external_system_pause_sync_20180510 extends CI_Migration {

    public function up() {
        $fields = array(
            'pause_sync' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => true,
            ),
        );
        $this->dbforge->add_column('external_system', $fields);
        $this->dbforge->add_column('external_system_list', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('external_system', 'pause_sync');
        $this->dbforge->drop_column('external_system_list', 'pause_sync');
    }
}