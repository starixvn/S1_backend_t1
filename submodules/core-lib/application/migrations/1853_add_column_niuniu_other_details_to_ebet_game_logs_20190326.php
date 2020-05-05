<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_niuniu_other_details_to_ebet_game_logs_20190326 extends CI_Migration {

    private $tableName='ebet_game_logs';

    public function up() {
        $fields = array(
            'niuniuWithHoldingTotal' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'niuniuWithHoldingDetail' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'niuniuResult' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'niuniuWithHoldingTotal');
        $this->dbforge->drop_column($this->tableName, 'niuniuWithHoldingDetail');
        $this->dbforge->drop_column($this->tableName, 'niuniuResult');
    }
}