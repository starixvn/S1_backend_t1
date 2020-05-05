<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_table_asia_gaming_game_logs_20200307 extends CI_Migration
{
    private $tableName = 'asia_gaming_game_logs';

    public function up()
    {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => true,
            ),
            'ugsbetid' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'txid' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'betid' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true
            ),
            'beton' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'betclosedon' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'betupdatedon' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'timestamp' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'roundid' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'roundstatus' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true
            ),
            'userid' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'riskamt' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'winamt' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'winloss' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'beforebal' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'postbal' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'cur' => array(
                'type' => 'VARCHAR',
                'constraint' => '3',
                'null' => true
            ),
            'gameprovider' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'gameprovidercode' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'gamename' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'gameid' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'platformtype' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'ipaddress' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true
            ),
            'bettype' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true
            ),
            'playtype' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ),
            'playertype' => array(
                'type' => 'INT',
                'constraint' => '1',
                'null' => true
            ),
            'turnover' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'validbet' => array(
                'type' => 'DOUBLE',
                'null' => true
            ),
            'uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true
            ),
            'match_detail' => array(
                'type' => 'VARCHAR',
                'constraint' => '2000',
                'null' => true
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
                'null' => true,
            ),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
        );

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addIndex($this->tableName, 'idx_asiagaming_roundid', 'roundid');
            $this->player_model->addIndex($this->tableName, 'idx_asiagaming_username', 'username');
            $this->player_model->addIndex($this->tableName, 'idx_asiagaming_ugsbetid', 'ugsbetid');
            $this->player_model->addIndex($this->tableName, 'idx_asiagaming_txid', 'txid');
            $this->player_model->addIndex($this->tableName, 'idx_asiagaming_betid', 'betid');
            $this->player_model->addIndex($this->tableName, 'idx_asiagaming_userid', 'userid');
            $this->player_model->addIndex($this->tableName, 'idx_asiagaming_gameid', 'gameid');
            $this->player_model->addIndex($this->tableName, 'idx_asiagaming_roundstatus', 'roundstatus');
            
            $this->player_model->addUniqueIndex($this->tableName, 'idx_asiagaming_external_uniqueid', 'external_uniqueid');
        }
    }

    public function down()
    {
        $this->dbforge->drop_table($this->tableName);
    }
}
