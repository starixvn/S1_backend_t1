<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_status_to_oneworks_game_report_20190106 extends CI_Migration {

    private $tableName = 'oneworks_game_report';

    public function up() {
        $fields = array(
	       'status' => array(			
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'status');
    }
}
