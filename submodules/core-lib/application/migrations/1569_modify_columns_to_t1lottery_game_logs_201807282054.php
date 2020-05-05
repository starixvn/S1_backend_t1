<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_to_t1lottery_game_logs_201807282054 extends CI_Migration {

    private $tableName = 't1lottery_game_logs';

    public function up(){

        $fields = array(
            'uniqueid' => array(
                'name'=>'uniqueid',
                'type' => 'BIGINT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->addIndex($this->tableName, 'idx_external_uniqueid', 'external_uniqueid', true);
        $this->player_model->addIndex($this->tableName, 'idx_uniqueid', 'uniqueid');

    }

    public function down() {

        $this->load->model('player_model'); # Any model class will do
        $this->player_model->dropIndex($this->tableName, 'idx_external_uniqueid');
        $this->player_model->dropIndex($this->tableName, 'idx_uniqueid');

    }
}
