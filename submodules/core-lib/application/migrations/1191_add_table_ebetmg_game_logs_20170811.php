<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebetmg_game_logs_20170811 extends CI_Migration {

    private $tableName = 'ebetmg_game_logs';
    
    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'ebet_mg_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'thirdParty' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'tag' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'colId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'agentId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'mbrId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'mbrCode' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'transId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'gameId' => array(
                'type' => 'INT',
                'constraint' => '10',
                'null' => true,
            ),
            'transType' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'transTime' => array(
                'type' => 'TIMESTAMP',
                'null' => true,
            ),
            'mgsGameId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'mgsActionId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'amnt' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'clrngAmnt' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'balance' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'refTransId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'refTransType' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'playerId' => array(
                'type' => 'INT',
                'constraint' => '10',
                'null' => false,
            ),
            'uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
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
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);
        
        $this->db->query('create unique index idx_uniqueid on ebetmg_game_logs(uniqueid)');
        $this->db->query('create unique index idx_external_uniqueid on ebetmg_game_logs(external_uniqueid)');
    }

    public function down() {
        if( $this->db->table_exists($this->tableName) ){
        $this->db->query('drop index idx_uniqueid on ebetmg_game_logs');
        $this->db->query('drop index idx_external_uniqueid on ebetmg_game_logs');            
        $this->dbforge->drop_table($this->tableName);
        }
    }
}
