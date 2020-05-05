<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_jumb_gamelogs_201706021720 extends CI_Migration {

    private $tableName = 'jumb_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'PlayerId' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),
            'Username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'seqNo' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'mtype' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'gameDate' => array(
                'type' => 'VARCHAR',
                'constraint' => '19',
                'null' => false,
            ),
            'bet' => array(
                'type' => 'double',
                'null' => false,
            ),
            'win' => array(
                'type' => 'double',
                'null' => false,
            ),
            'total' => array(
                'type' => 'double',
                'null' => false,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '2',
                'null' => false,
            ),
            'jackpot' => array(
                'type' => 'double',
                'null' => false,
            ),
            'jackpotContribute' => array(
                'type' => 'double',
                'null' => false,
            ),
            'denom' => array(
                'type' => 'double',
                'null' => false,
            ),
            'lastModifyTime' => array(
                'type' => 'VARCHAR',
                'constraint' => '19',
                'null' => false,
            ),
            'gameName' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => false,
            ),
            'playerIp' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
            ),
            'clientType' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => false,
            ),
            'hasFreegame' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'hasGamble' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'systemTakeWin' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'err_text' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'response_result_id' => array(
                'type' => 'INT',
                'null' => true,
            )
        );


        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
