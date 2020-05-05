<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_bankname_20180518 extends CI_Migration {

    private $tableName = 'banktype';

    public function up() {
        $fields = array(
            'bankName' => array(
                'type' => 'text',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);

    }

    public function down() {
        $fields = array(
            'bankName' => array(
                'type' => 'varchar',
                'constraint'=> '255',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }
}