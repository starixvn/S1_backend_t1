<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_asialong_game_logs_201804131530 extends CI_Migration {

    private $tableName = 'asialong_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'auto_increment' => TRUE,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '50',
            ),
            'gtype' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '10',
            ),
            'betid' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '50',
            ),
            'rtype' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'gold' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'ioratio' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'result' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '10',
            ),
            'adddate' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'wingold' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'wgold_dm' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'orderip' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '50',
            ),
            'betcontent' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '50',
            ),
            'periodnumber' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'betdetail' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'result_ok' => array(
                'type' => 'INT',
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
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);
        $this->db->query("create index idx_asialong_game_logs_external_uniqueid on ".$this->tableName."(external_uniqueid)");
    }

    public function down() {
        $this->db->query("drop index idx_asialong_game_logs_external_uniqueid on ".$this->tableName);
        $this->dbforge->drop_table($this->tableName);
    }
}
