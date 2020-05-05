<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_email_verify_exptime_to_player_20190805 extends CI_Migration {

    private $tableName = 'player';

    public function up()
    {
        # Add column
        $fields = array(
            'email_verify_exptime' => array(
                'type' => 'DATETIME',
                'null' => true,
                'default' => '0000-00-00 00:00:00',
            )
        );

        if(!$this->db->field_exists('email_verify_exptime', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'verified_email');
        }
    }

    public function down()
    {
        if($this->db->field_exists('email_verify_exptime', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'email_verify_exptime');
        }
    }
}
