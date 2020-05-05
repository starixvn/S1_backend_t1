<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_on_playerdetails_201810302339 extends CI_Migration {

    private $tableName = 'playerdetails';

    public function up(){

        $fields = array(
            'address2' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'address3' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'imAccount3' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'imAccountType3' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'imAccount4' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'imAccountType4' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'imAccount5' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'imAccountType5' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
    }
}
