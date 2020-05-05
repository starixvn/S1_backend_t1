<?php

defined('BASEPATH') OR exit('No direct script access allowed');


// class Migration_add_column_to_game_description_20180227 extends CI_Migration
//         1446_add_column_password_plain_to_adminuser_201803011145
class Migration_add_column_failed_login_attempt_timeout_until_to_player_201803051323 extends CI_Migration
{
    private $tableName = 'player';

    public function up()
    {
        $fields = array(
            'failed_login_attempt_timeout_until' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

            $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down()
    {
            $this->dbforge->drop_column($this->tableName, 'failed_login_attempt_timeout_until');
    }
}
