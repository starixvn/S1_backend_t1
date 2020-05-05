<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_teleId_column_to_adminuser_201701200135 extends CI_Migration {

    private $tableName = 'adminusers';

    public function up() {
        $fields = array(
            'tele_id' => array(
                'type' => 'varchar(50)',
                'null' => true,
            )
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'tele_id');
    }
}
