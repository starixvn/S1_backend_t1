<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_firstname_char_length_to_playerdetails_201807102224 extends CI_Migration {

    private $tableName = 'playerdetails';

    public function up() {

        $fields = array(
            'firstName' => array(
                'type' => 'VARCHAR',
                'constraint'=> '60',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        
        $fields = array(
            'firstName' => array(
                'type' => 'VARCHAR',
                'constraint'=> '60',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }
}
