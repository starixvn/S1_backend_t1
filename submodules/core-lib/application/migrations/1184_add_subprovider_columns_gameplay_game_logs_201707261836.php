<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_subprovider_columns_gameplay_game_logs_201707261836 extends CI_Migration {

    private $tableName = 'gameplay_game_logs';

    public function up() {
        if( !$this->db->field_exists('betNo',$this->tableName) ){
        $fields = array(
            'betNo' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'memberType' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'drawId' => array(
                'type' => 'INT',
                'constraint' => '20',
                'null' => true,
            ),
            'drawNo' => array(
                'type' => 'INT',
                'constraint' => '20',
                'null' => true,
            ),
            'areaName' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'betType' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'betContent' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'odds' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'isWin' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'winAmount' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'timeBet' => array(
                'type' => 'datetime',
                'null' => true,
            ),
            'actionTime' => array(
                'type' => 'datetime',
                'null' => true,
            ),
            'keno_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'OS' => array(
                'type' => 'int',
                'constraint' => '2',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if( $this->db->field_exists('memberType', $this->tableName) ){
        $this->dbforge->drop_column($this->tableName, 'memberType');
        $this->dbforge->drop_column($this->tableName, 'drawId');
        $this->dbforge->drop_column($this->tableName, 'drawNo');
        $this->dbforge->drop_column($this->tableName, 'areaName');
        $this->dbforge->drop_column($this->tableName, 'betType');
        $this->dbforge->drop_column($this->tableName, 'betContent');
        $this->dbforge->drop_column($this->tableName, 'odds');
        $this->dbforge->drop_column($this->tableName, 'isWin');
        $this->dbforge->drop_column($this->tableName, 'winAmount');
        $this->dbforge->drop_column($this->tableName, 'timeBet');
        $this->dbforge->drop_column($this->tableName, 'actionTime');
        $this->dbforge->drop_column($this->tableName, 'keno_status');
        $this->dbforge->drop_column($this->tableName, 'OS');
        }
    }
}