<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_transfer_conditions_201902132102 extends CI_Migration {

    private $tableName = 'transfer_conditions';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'promotion_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE,
            ),
            'player_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE,
            ),
            'player_promo_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE,
            ),
            'disallow_transfer_in' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'disallow_transfer_out' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'bet_details' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'wallet_json' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'condition_amount' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE,
            ),
            'started_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
            ),
            'completed_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
            ),
            'admin_user_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => TRUE,
            ),
        );

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
        }
    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}