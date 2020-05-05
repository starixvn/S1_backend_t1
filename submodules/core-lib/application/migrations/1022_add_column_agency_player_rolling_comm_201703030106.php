<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_agency_player_rolling_comm_201703030106 extends CI_Migration {

    protected $tableName = "agency_player_rolling_comm";

    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'notes' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'notes');
    }

}

///END OF FILE//////////////////