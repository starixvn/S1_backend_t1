<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_idn_game_logs_201902211533 extends CI_Migration {

    private $tableName='idn_game_logs';

    public function up() {
        $fields = array(
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);

        $this->load->model('player_model');
        $this->player_model->dropIndex($this->tableName, 'idx_transaction_no');   # not unique
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'md5_sum');
    }
}
