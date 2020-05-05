<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_for_tianhao_game_logs_20190919 extends CI_Migration {
    
    private $tableName = 'tianhao_game_logs';

    public function up() {
        $fields = array(
            'room_level' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '50',
            ),
            'award_money' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('room_level', $this->tableName) && !$this->db->field_exists('award_money', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('room_level', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'room_level');
        }
        if($this->db->field_exists('award_money', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'award_money');
        }
    }
}
