<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_to_ibc_game_logs_201804031610 extends CI_Migration {

    private $tableName = 'ibc_game_logs';

    public function up() {

        $this->dbforge->modify_column($this->tableName, array(
            'parlay_refno' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
        ));

    }

    public function down() {

    }

}
