<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_le_gaming_gamelogs_2018081400 extends CI_Migration {

    private $tableName = 'le_gaming_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'GameID' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'Accounts' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'ServerID' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => true,
            ),
            'KindID' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => true,
            ),
            'TableID' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => true,
            ),
            'ChairID' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => true,
            ),
            'UserCount' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => true,
            ),
            'CellScore' => array(
                'type' => 'double',
                'null' => true,
            ),
            'AllBet' => array(
                'type' => 'double',
                'null' => true,
            ),
            'Profit' => array(
                'type' => 'double',
                'null' => true,
            ),
            'Revenue' => array(
                'type' => 'double',
                'null' => true,
            ),
            'GameStartTime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'GameEndTime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'CardValue' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'ChannelID' => array(
                'type' => 'INT',
                'constraint' => '50',
                'null' => true,
            ),
            'LineCode' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'player_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
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
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('external_uniqueid');
        $this->dbforge->add_key('GameEndTime');
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
