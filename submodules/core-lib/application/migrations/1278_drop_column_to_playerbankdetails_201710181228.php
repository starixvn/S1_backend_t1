<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_column_to_playerbankdetails_201710181228 extends CI_Migration {

    private $tableName = 'playerbankdetails';

    public function up() {
        if($this->db->field_exists('isDeleted', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'isDeleted');
        }
    }

    public function down() {
    }
}
