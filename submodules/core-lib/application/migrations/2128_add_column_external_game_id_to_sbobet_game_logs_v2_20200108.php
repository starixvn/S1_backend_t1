<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_external_game_id_to_sbobet_game_logs_v2_20200108 extends CI_Migration {

    private $tableName = 'sbobet_game_logs_v2';

    public function up() {

        $fields = array(
            'external_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('external_game_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'portfolio');
        }

        # add index
        $indexPreStr = 'idx_';
        $this->player_model->addIndex($this->tableName, $indexPreStr. 'external_game_id', 'external_game_id');

    }

    public function down() {
        if($this->db->field_exists('external_game_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'external_game_id');
        }
    }

}