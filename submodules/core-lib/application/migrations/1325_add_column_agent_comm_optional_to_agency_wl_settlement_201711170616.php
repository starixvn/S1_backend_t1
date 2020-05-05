<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_agent_comm_optional_to_agency_wl_settlement_201711170616 extends CI_Migration {

    private $tableName = 'agency_wl_settlement';

    public function up() {
        $fields = array(
            'agent_comm_optional' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'agent_comm_optional');
    }
}
