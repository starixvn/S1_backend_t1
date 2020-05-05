<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_gsbbin_game_logs_201709071644 extends CI_Migration {

    private $tableName = 'gsbbin_game_logs';

    public function up() {

        $this->load->model(array('gsbbin_game_logs'));

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'wagers_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'wagers_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'result' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'payoff' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'commisionable' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'game_platform' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'uptime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'order_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'serial_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'round_no' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'result_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'card' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'exchange_rate' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'commision' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'is_paid' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'modified_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'game_kind' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'origin' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'flag' => array(
                'type' => 'INT',
                'null' => false,
                'default' => Gsbbin_game_logs::FLAG_FINISHED,
            ),
            'createdAt' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updatedAt' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);

        $this->db->query('create index idx_wagers_id on gsbbin_game_logs(wagers_id)');
        $this->db->query('create index idx_wagers_date on gsbbin_game_logs(wagers_date)');
        $this->db->query('create index idx_external_uniqueid on gsbbin_game_logs(external_uniqueid)');
        $this->db->query('create index idx_flag on gsbbin_game_logs(flag)');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);

        $this->db->query('drop index idx_wagers_id on gsbbin_game_logs');
        $this->db->query('drop index idx_wagers_date on gsbbin_game_logs');
        $this->db->query('drop index idx_external_uniqueid on gsbbin_game_logs');
        $this->db->query('drop index idx_flag on gsbbin_game_logs');
    }
}