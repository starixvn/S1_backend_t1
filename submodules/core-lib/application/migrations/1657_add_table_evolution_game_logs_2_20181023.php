<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_evolution_game_logs_2_20181023 extends CI_Migration {

    private $tableName = 'evolution_2_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),

            'game_round_id' => array(       # original field is "id"
                'type' => 'BIGINT',
                'null' => false,
            ),
            'started_at' => array(      # bet time
                'type' => 'DATETIME',
                'null' => false
            ),
            'settled_at' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
            'payout' => array(               #	The sum of players total withdrawal amount in particular game round
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'dealer' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'result' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'wager' => array(           #  The sum of players total bet amount in particular game round
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'table' => array(           # set to json
                'type' => 'TEXT',
                'null' => true,
            ),



            // PARTICIPANTS DATA can be more than 1 player (should loop to get multiple record)
            'participants' => array(    # many data ( set to json )
                'type' => 'TEXT',
                'null' => true,
            ),

            // inside participants data
            'player_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'decisions' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'city' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'screen_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'casino_session_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'casino_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'country' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'bet_coverage' => array(           # set to json
                'type' => 'TEXT',
                'null' => true,
            ),
            'session_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'config_overlays' => array(        # set to json
                'type' => 'TEXT',
                'null' => true,
            ),
            'bets' => array(                   # set to json
                'type' => 'TEXT',
                'null' => true,
            ),
            // process in our side from field "participants => bets"
            'player_bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'player_payout' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            // end of participants data field

            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),

        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('external_uniqueid');
        $this->dbforge->create_table($this->tableName);

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_started_at', 'started_at');
        $this->player_model->addIndex($this->tableName, 'idx_settled_at', 'settled_at');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex($this->tableName, 'idx_started_at');
        $this->player_model->dropIndex($this->tableName, 'idx_settled_at');
        $this->dbforge->drop_table($this->tableName);
    }
}