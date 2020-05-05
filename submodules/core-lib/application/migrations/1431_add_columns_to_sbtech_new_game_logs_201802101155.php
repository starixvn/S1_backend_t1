<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_sbtech_new_game_logs_201802101155 extends CI_Migration {

    private $tableName = 'sbtech_new_game_logs';

    public function up() {
        $fields = array(
            'branch_name' => array(      # newly add in api
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'branch_name');
    }
}
