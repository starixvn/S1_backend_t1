<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ggpoker_gamelogs_20171122 extends CI_Migration {

    private $tableName = 'ggpoker_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'downline_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'game_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'net_revenue' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'profit_and_loss' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'player_id' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}