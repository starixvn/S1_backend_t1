<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_dongsen_game_logs_columns_length_201909270200 extends CI_Migration {
    public function up() {
        //modify column size
        $fields = array(
            'userName' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'userId' => array(
              'type' => 'VARCHAR',
              'constraint' => '100',
              'null' => true,
            ),
        );
        $this->dbforge->modify_column('dongsen_lottery_game_logs', $fields);
        $this->dbforge->modify_column('dongsen_es_cny1_game_logs', $fields);
    }

    public function down() {
        // not able to rollback due to data truncation
    }
}
