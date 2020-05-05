<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_hb_incomplete_games_20190420 extends CI_Migration {

    private $tableName = 'hb_incomplete_games';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'player_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'game_instance_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'friendly_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'game_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'game_key_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'provider' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'brand_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'dt_started' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'stake' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'payout' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'game_state_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'game_state_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),

            # SBE additional info
            'response_result_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
        );


        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
        # Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_username', 'username');
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid',true);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
