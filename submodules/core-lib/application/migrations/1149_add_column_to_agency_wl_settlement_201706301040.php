<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_wl_settlement_201706301040 extends CI_Migration {

    private $tableName = 'agency_wl_settlement';

    public function up() {
        $fields = array(
            'agent_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'invoice_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'agent_id');
//        $this->dbforge->drop_column($this->tableName, 'invoice_id');
    }
}