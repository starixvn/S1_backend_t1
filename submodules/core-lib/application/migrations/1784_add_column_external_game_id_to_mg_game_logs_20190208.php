<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_external_game_id_to_mg_game_logs_20190208 extends CI_Migration {

    private $tableName='mg_game_logs';

    public function up() {
        $fields = array(
           'external_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null'=> true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_external_game_id', 'external_game_id');

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'external_game_id');
    }
}
