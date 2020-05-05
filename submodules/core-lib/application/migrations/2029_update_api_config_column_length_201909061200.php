<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_api_config_column_length_201909061200 extends CI_Migration {
    public function up() {
        //modify column size
        $fields = array(
            'api_last_sync_index' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('api_config', $fields);
    }

    public function down() {
        // not able to rollback due to data truncation
    }
}
