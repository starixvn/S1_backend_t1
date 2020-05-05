<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_sub_game_provider_to_game_description_201707061152 extends CI_Migration {

    private $tableName = 'game_description';

    public function up() {
        $fields = array(
            'sub_game_provider' => array(
                'type' => 'VARCHAR',
                'constraint' => '1100',
                'null' => true,
            )
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'sub_game_provider');
    }
}