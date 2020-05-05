<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebet_th_game_logs_20181107 extends CI_Migration {

    private $tableName = 'ebet_th_game_logs';
    private $username_col = 'username';
    private $timestamp_col = 'payoutTime';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'gameType' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'betMap' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'judgeResult' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'roundNo' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'payout' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'bankerCards' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'playerCards' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'allDices' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'dragonCard' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'tigerCard' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'number' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'createTime' => array(
                'type' => 'TIMESTAMP',
                'null' => true,
            ),
            'payoutTime' => array(
                'type' => 'TIMESTAMP',
                'null' => true,
            ),
            'betHistoryId' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'validBet' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'userId' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'subChannelId' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'gameshortcode' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'response_result_id' => array(
                'type' => 'INT',
                'null' => true,
            ),

            // 0665
            'realBet' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),

            // 0763
            'origCreateTime' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'origPayoutTime' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
            ),

            // 0893 --> 0898
            'createdAt' => array(
                'type' => 'timestamp',
            ),
            'updatedAt' => array(
                'type' => 'timestamp',
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_uniqueid', 'uniqueid', true);
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid', true);
        $this->player_model->addIndex($this->tableName, 'idx_gameshortcode', 'gameshortcode');
        $this->player_model->addIndex($this->tableName, 'idx_player_name', 'username');
        $this->player_model->addIndex($this->tableName, 'idx_game_date', 'payoutTime');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}