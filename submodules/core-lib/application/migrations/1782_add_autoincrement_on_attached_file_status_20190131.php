<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_autoincrement_on_attached_file_status_20190131 extends CI_Migration {

    private $tableName = 'attached_file_status';

    public function up() {

        $update_fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ),
        );
        
        $this->dbforge->modify_column($this->tableName, $update_fields);
        
    }

    public function down() {}
}
