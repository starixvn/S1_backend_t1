<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_hb_incomplete_games_20190424 extends CI_Migration {

    private $tableName = 'hb_incomplete_games';

    public function up() {
        $fields = array(
            'game_platform_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );

        if (!$this->db->field_exists('game_platform_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down() {
        if ($this->db->field_exists('game_platform_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'game_platform_id');
        }
    }
}
