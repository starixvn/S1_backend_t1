<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_extra_to_logs_20180717 extends CI_Migration {

    private $tableName = 'logs';

    public function up() {
        $field = array(
            'extra' => array(
                'type' => 'TEXT', # show $_POST and $_GET data
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $field);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'extra');
    }
}
