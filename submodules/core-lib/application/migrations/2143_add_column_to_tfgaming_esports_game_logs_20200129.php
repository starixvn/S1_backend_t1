<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_tfgaming_esports_game_logs_20200129 extends CI_Migration {

    private $tableName = 'tfgaming_esports_game_logs';

    public function up() {

        $fields = array(
            'member_odds' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'member_odds_style' => array(
                'type' => 'VARCHAR',
                'constraint' => '16',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('member_odds', $this->tableName) && !$this->db->field_exists('member_odds_style', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('member_odds', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'member_odds');
        }
        if($this->db->field_exists('member_odds_style', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'member_odds_style');
        }
    }
}