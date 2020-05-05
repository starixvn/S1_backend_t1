<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_promorules_201610300040 extends CI_Migration {

    private $tableName = "promorules";

    public function up() {
        $fields = array(
            'release_to_same_sub_wallet' => array(
                'type' => 'INT',
                'null' => TRUE,
                'default' => 0,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'release_to_same_sub_wallet');
    }
}
