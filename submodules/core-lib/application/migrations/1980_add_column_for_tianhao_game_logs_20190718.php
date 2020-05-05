<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_for_tianhao_game_logs_20190718 extends CI_Migration {
    
    private $tableName = 'tianhao_game_logs';

    public function up() {
        $fields = array(
            'user_id' => array(
                'type' => 'INT',
                'null' => true,
            ),

            'game_group' => array(
                'type' => 'INT',
                'null' => true,
            ),

            'tax_money' => array(
                'type' => 'FLOAT',
                'null' => true,
            ),

            'effect_money' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'user_id');
        $this->dbforge->drop_column($this->tableName, 'game_group');
        $this->dbforge->drop_column($this->tableName, 'tax_money');
        $this->dbforge->drop_column($this->tableName, 'effect_money');
    }
}
