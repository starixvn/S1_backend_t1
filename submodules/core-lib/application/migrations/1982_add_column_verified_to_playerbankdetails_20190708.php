<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_verified_to_playerbankdetails_20190708 extends CI_Migration {

    private $tableName = 'playerbankdetails';

    public function up()
    {
        # Add column
        $fields = array(
            'verified' => array(
                'type' => 'TINYINT',
                'unsigned' => TRUE,
                'null' => false,
                'default' => 1,
                'comment' => "0 - unverified, 1 - verified",
            )
        );

        if(!$this->db->field_exists('verified', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'status');
        }
    }

    public function down()
    {
        if($this->db->field_exists('verified', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'verified');
        }
    }
}
