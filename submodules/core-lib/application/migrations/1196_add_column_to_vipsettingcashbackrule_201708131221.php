<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201708131221 extends CI_Migration {

    private $tableName = 'vipsettingcashbackrule';

    /**
     * Daily 0
     * Weekly 1-Mon, 2-Tues etc..
     */
    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'cashback_period' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'cashback_period');
    }
}