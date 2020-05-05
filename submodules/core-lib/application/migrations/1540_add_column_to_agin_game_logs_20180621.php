<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agin_game_logs_20180621 extends CI_Migration {

    private $tableName = 'agin_game_logs';

    public function up() {
        $fields = array(
            'transferType' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'fishIdStart' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'fishIdEnd' => array(
                'type' => 'INT',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'transferType');
        $this->dbforge->drop_column($this->tableName, 'fishIdStart');
        $this->dbforge->drop_column($this->tableName, 'fishIdEnd');
    }
}
