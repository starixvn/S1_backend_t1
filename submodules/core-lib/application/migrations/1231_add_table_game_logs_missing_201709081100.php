<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_game_logs_missing_201709081100 extends CI_Migration {

    # A record will be added to this table when a missing period is detected by game log monitor
    private $tableName = 'game_logs_missing';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'game_platform_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'start_time' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'period' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'resync_stage' => array(
                # numeric status defining which resync stage it's at
                # See game_log_monitor for definition of resync stages
                'type' => 'INT',
                'null' => false,
                'default' => 0,
            ),
            'resync_done' => array(
                # this will be set to 1 when this missing period has game log data already
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'last_updated' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}