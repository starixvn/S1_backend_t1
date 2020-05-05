<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_ibc_game_logs_2018052120180521 extends CI_Migration {

    private $tableName = 'ibc_game_logs';

    public function up() {
        $fields = array(
            'parlay_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,              
            ),
            'combo_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'is_lucky' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'bet_tag' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'last_ball_no' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'parlay_type');
        $this->dbforge->drop_column($this->tableName, 'combo_type');
        $this->dbforge->drop_column($this->tableName, 'is_lucky');
        $this->dbforge->drop_column($this->tableName, 'bet_tag');
        $this->dbforge->drop_column($this->tableName, 'last_ball_no');
    }
}