<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_tcg_game_logs_2018082018 extends CI_Migration {

    private $tableName = 'tcg_game_logs';

    public function up() {
        $fields = array(
            'round_key' => array(  # bet_order_no + bet_content_id
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName,'idx_round_key' , 'round_key');

        $fields2 = array(
            'bet_order_no' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'bet_content_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'numero' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields2);
    }

    public function down() {
        $this->load->model('player_model');
        $this->player_model->dropIndex($this->tableName, 'idx_round_key');

        $this->dbforge->drop_column($this->tableName, 'round_key');
    }
}