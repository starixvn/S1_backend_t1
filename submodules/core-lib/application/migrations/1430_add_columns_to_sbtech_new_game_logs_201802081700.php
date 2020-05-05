<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_sbtech_new_game_logs_201802081700 extends CI_Migration {

    private $tableName = 'sbtech_new_game_logs';

    public function up() {
        $fields = array(
            'update_date' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'update_date');
    }
}
