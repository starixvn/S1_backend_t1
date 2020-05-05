<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_lucky_game_game_logs_20190622 extends CI_Migration {

    private $tableName = 'lucky_game_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'recordid ' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'fieldlevel ' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'roomname' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'gameid ' => array(
                'type' => 'INT',
                'null' => true
            ),
            'gamename' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true
            ),
            'tableno' => array(
                'type' => 'INT',
                'null' => true
            ),
            'losewincoin' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'winextract' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'entercoin' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'exitcoin ' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'recordtime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'recordInfo' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'platformid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'platformno' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'platformname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'nickname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'totalbet ' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'effectivebet' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            # SBE additional info
            'response_result_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'external_uniqueid' => array(
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
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );


        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
        # Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_username', 'username');
        $this->player_model->addIndex($this->tableName, 'idx_recordtime', 'recordtime');
        $this->player_model->addUniqueIndex($this->tableName, 'idx_recordid', 'recordid');
        $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
