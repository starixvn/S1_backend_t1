<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_jumb_game_logs_201706221603 extends CI_Migration {

    private $tableName = 'jumb_game_logs';

    public function up() {
        $fields = array(
            'gambleBet' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);

        $fields = array(
            'seqNo' => array(
                'type' => 'varchar',
                'constraint' => '100',
                'null' => true,
            ),
            'PlayerId' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'Username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'mtype' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'gameDate' => array(
                'type' => 'VARCHAR',
                'constraint' => '19',
                'null' => true,
            ),
            'bet' => array(
                'type' => 'double',
                'null' => true,
            ),
            'win' => array(
                'type' => 'double',
                'null' => true,
            ),
            'total' => array(
                'type' => 'double',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'jackpot' => array(
                'type' => 'double',
                'null' => true,
            ),
            'jackpotContribute' => array(
                'type' => 'double',
                'null' => true,
            ),
            'denom' => array(
                'type' => 'double',
                'null' => true,
            ),
            'lastModifyTime' => array(
                'type' => 'VARCHAR',
                'constraint' => '19',
                'null' => true,
            ),
            'gameName' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'playerIp' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'clientType' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ),
            'hasFreegame' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'hasGamble' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'systemTakeWin' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'err_text' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            )
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'gambleBet');
    }
}