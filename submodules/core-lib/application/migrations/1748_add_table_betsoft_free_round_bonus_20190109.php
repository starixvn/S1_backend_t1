<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_betsoft_free_round_bonus_20190109 extends CI_Migration {

    private $tableName = 'betsoft_free_round_bonus';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'callback' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'bonus_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'game_username' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'is_bonus_release' => array(    # check if bonus release from callback bonus_release
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),

            # only for bonus_win callback
            'transaction_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ),
            'is_trans_success' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),


            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_bonus_id', 'bonus_id',true);
        $this->player_model->addIndex($this->tableName, 'idx_game_username', 'game_username');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}