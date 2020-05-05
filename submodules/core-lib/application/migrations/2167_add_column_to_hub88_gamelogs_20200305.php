<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_hub88_gamelogs_20200305 extends CI_Migration {

    private $tableName = 'hub88_game_logs';

    public function up() {

        $fields = array(
            'external_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('external_game_id', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
        }

    }

    public function down() {
        if($this->db->field_exists('external_game_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'external_game_id');
        }
    }

}