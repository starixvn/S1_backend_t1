<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_deleted_at_column_to_banner_20180719 extends CI_Migration {

    private $tableName = 'banner';

    public function up(){

        $fields = array(
            'deleted_at' => array(
                'type' => 'datetime',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'deleted_at');
    }
}