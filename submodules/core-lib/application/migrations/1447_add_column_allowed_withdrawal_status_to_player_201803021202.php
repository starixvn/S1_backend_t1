<?php

defined('BASEPATH') OR exit('No direct script access allowed');


// class Migration_add_column_to_game_description_20180227 extends CI_Migration
//         1446_add_column_password_plain_to_adminuser_201803011145
class Migration_add_column_allowed_withdrawal_status_to_player_201803021202 extends CI_Migration
{
    private $tableName = 'player';

    public function up()
    {
        $fields = array(
            'allowed_withdrawal_status' => array(
                'type' => 'INT',
                'default' => 0,
                'null' => false,
            ),
        );

            $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
            $this->dbforge->drop_column($this->tableName, 'allowed_withdrawal_status');
    }
}
