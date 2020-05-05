<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_total_cashback_player_game_daily_201809241519 extends CI_Migration {

    private $tableName = 'total_cashback_player_game_daily';

    public function up() {
        $fields = array(
            'append_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);

        //change index
        $this->load->model(['player_model']);
        $this->player_model->dropIndex($this->tableName, 'idx_unique_record');
        $this->player_model->addIndex($this->tableName, 'idx_unique_record', 'player_id,game_description_id,total_date,cashback_type,append_time', true);
        $this->player_model->addIndex($this->tableName, 'idx_uniqueid', 'uniqueid', true);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'append_time');
        $this->dbforge->drop_column($this->tableName, 'uniqueid');

        //change index
        $this->load->model(['player_model']);
        $this->player_model->dropIndex($this->tableName, 'idx_unique_record');
        $this->player_model->addIndex($this->tableName, 'idx_unique_record', 'player_id,game_description_id,total_date,cashback_type', true);

    }
}