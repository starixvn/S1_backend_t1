<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_country_201610131848 extends CI_Migration {

    public function up() {

        // $fields = array(
        //     'countryId' => array(
        //         'type' => 'INT',
        //         'null' => false,
        //     ),
        //     'countryName' => array(
        //         'type' => 'VARCHAR',
        //         'null' => false,
        //         'constraint'=> 200,
        //     ),
        //     'createTime' => array(
        //         'type' => 'DATETIME',
        //         'null' => false,
        //     ),
        //     'createPerson' => array(
        //         'type' => 'INT',
        //         'null' => false,
        //     ),
        //     'status' => array(
        //         'type' => 'INT',
        //         'null' => false,
        //     ),
        //     'remarks' => array(
        //         'type' => 'VARCHAR',
        //         'null' => false,
        //         'constraint'=> 300,
        //     ),
        // );

        // $this->dbforge->add_field($fields);
        // $this->dbforge->add_key('countryId', TRUE);
        // $this->dbforge->create_table('country');
    }

    public function down() {
        // $this->dbforge->drop_table('country');
    }
}