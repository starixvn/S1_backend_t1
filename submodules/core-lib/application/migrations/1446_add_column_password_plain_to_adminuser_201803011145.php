<?php

defined('BASEPATH') OR exit('No direct script access allowed');


// class Migration_add_column_to_game_description_20180227 extends CI_Migration
//         1446_add_column_password_plain_to_adminuser_201803011145
class Migration_add_column_password_plain_to_adminuser_201803011145 extends CI_Migration
{
    private $tableName = 'adminusers';

    public function up()
    {
        $fields = array(
            'password_plain' => array(
                'type' => 'varchar',
                'constraint' => 255,
                'null' => true,
            ),
        );

            $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
            $this->dbforge->drop_column($this->tableName, 'password_plain');
    }
}
