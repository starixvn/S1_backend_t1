<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_merchants_201707151437 extends CI_Migration {

    private $tableName = 'merchants';

    public function up() {
        $fields = array(
            'agent_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'agent_id');
    }
}
