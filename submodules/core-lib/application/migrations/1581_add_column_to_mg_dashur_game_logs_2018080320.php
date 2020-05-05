<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_mg_dashur_game_logs_2018080320 extends CI_Migration {

    private $tableName = 'mg_dashur_game_logs';

    public function up() {
        $fields = array(
            'round_key' => array(  # accountid + game_id
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName,'idx_round_key' , 'round_key');
        $this->player_model->addIndex($this->tableName,'idx_category' , 'category');
        $this->player_model->addIndex($this->tableName,'idx_transaction_time' , 'transaction_time');
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex($this->tableName, 'idx_round_key');
        $this->player_model->dropIndex($this->tableName, 'idx_category');
        $this->player_model->dropIndex($this->tableName, 'idx_transaction_time');

        $this->dbforge->drop_column($this->tableName, 'round_key');
    }
}