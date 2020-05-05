<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_agency_player_rolling_comm_201703011221 extends CI_Migration {

    protected $tableName = "agency_player_rolling_comm";

    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'start_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'end_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'real_bets' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'rolling_rate' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'start_at');
        $this->dbforge->drop_column($this->tableName, 'end_at');
        $this->dbforge->drop_column($this->tableName, 'real_bets');
        $this->dbforge->drop_column($this->tableName, 'rolling_rate');
    }

}

///END OF FILE//////////////////