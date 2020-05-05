<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_agency_player_rolling_comm_201703031610 extends CI_Migration {

    protected $tableName = "agency_player_rolling_comm";

    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'transaction_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'transaction_id');
    }

}

///END OF FILE//////////////////