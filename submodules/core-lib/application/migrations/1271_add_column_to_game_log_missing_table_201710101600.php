<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_game_log_missing_table_201710101600 extends CI_Migration {

    private $tableName = 'game_logs_missing';

    public function up() {
        if (!$this->db->field_exists('sync_result', $this->tableName)) {
            $field = array(
                'sync_result' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 300,
                    'null' => true,
                ),
            );
            $this->dbforge->add_column($this->tableName, $field);
        }
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'sync_result');
    }
}
