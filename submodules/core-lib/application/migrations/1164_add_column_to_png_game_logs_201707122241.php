<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_png_game_logs_201707122241 extends CI_Migration {

    private $tableName = 'png_game_logs';

    public function up() {
        $fields = array(
            'GamesessionState' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'RoundData' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'RoundLoss' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'JackpotLoss' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'JackpotGain' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'TotalLoss' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'TotalGain' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'ExternalFreegameId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'NumRounds' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
        $this->dbforge->drop_column($this->tableName, 'casinoTransactionReleaseOpen');
        $this->dbforge->drop_column($this->tableName, 'casinoTransactionReleaseClosed');
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'GamesessionState');
        $this->dbforge->drop_column($this->tableName, 'RoundData');
        $this->dbforge->drop_column($this->tableName, 'RoundLoss');
        $this->dbforge->drop_column($this->tableName, 'JackpotLoss');
        $this->dbforge->drop_column($this->tableName, 'JackpotGain');
        $this->dbforge->drop_column($this->tableName, 'TotalGain');
        $this->dbforge->drop_column($this->tableName, 'ExternalFreegameId');
        $this->dbforge->drop_column($this->tableName, 'NumRounds');
        $this->dbforge->drop_column($this->tableName, 'TotalLoss');

         $fields = array(
            'casinoTransactionReleaseOpen' => array(
                'type' => 'text',
                'null' => true
            ),
            'casinoTransactionReleaseClosed' => array(
                'type' => 'text',
                'null' => true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }
}