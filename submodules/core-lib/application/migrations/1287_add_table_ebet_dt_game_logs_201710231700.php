<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebet_dt_game_logs_201710231700 extends CI_Migration {

    private $tableName = 'ebet_dt_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            
            'game_unique_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'third_party' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'tag' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'player_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'bet_price' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
            ),
            'credit_before' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
            ),
            'credit_after' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
            ),
            'bet_wins' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
            ),
            'prize_wins' => array(
                'type' => 'DOUBLE',
                'null' => FALSE,
                'default' => 0.00,
            ),
            'create_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'parent_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'bet_lines' => array(
                'type' => 'INT',                
                'null' => true,
            ),

            // SBE data
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),            
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
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
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->db->query('create unique index idx_external_uniqueid on ebet_dt_game_logs(external_uniqueid)');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
        $this->db->query('drop index idx_external_uniqueid on ebet_dt_game_logs');
    }
}
