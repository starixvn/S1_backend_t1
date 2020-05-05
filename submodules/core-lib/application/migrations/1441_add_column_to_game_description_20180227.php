<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_column_to_game_description_20180227 extends CI_Migration
{
    private $tableName = 'game_description';

    public function up()
    {
        $fields = array(
            'demo_link' => array(
                'type' => 'varchar',
                'constraint' => 500,
                'null' => true,
            ),
        );

            $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
            $this->dbforge->drop_column($this->tableName, 'demo_link');
    }
}
