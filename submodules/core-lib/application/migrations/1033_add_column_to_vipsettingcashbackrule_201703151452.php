<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_vipsettingcashbackrule_201703151452 extends CI_Migration {

    protected $tableName = "vipsettingcashbackrule";

    public function up() {
        $this->dbforge->add_column($this->tableName, array(
            'cashback_daily_maxbonus' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'cashback_daily_maxbonus');
    }

}

