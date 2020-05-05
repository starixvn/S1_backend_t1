<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_rgs_game_logs_20200221 extends CI_Migration {

    private $tableName = 'rgs_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'betId' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'betStatusId' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'betStatusValue' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'betResultId' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'betResultValue' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'memberUserCode' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'memberCurrencyCode' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'totalStake' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'memberResultAmount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'betTypeId' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'betTypeValue' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'eventDisplayDateTime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'selectionTypeId' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'odds' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'raceCountry' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'raceVenue' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'raceTime' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'contenderName_en' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'contenderName_zh-TW' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'contenderName_zh-CN' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),

            // additional info
            'player_id' => array(
                'type' => 'int',
                'unsigned' => TRUE,
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

        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addIndex($this->tableName, 'idx_betId', 'betId');
            $this->player_model->addIndex($this->tableName, 'idx_selectionTypeId', 'selectionTypeId');
            $this->player_model->addIndex($this->tableName, 'idx_raceVenue', 'raceVenue');
            $this->player_model->addIndex($this->tableName, 'idx_md5_sum', 'md5_sum');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid');
        }
    }

    public function down() {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}