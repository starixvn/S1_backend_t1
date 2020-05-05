<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_gl_game_logs_20181025 extends CI_Migration {

    private $tableName = 'gl_game_logs';

    public function up() {
        $fields = array(
            'i18n_method_name_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'i18n_lottery_name_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'i18n_method_lv1_name_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'i18n_status_flag_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'cnname_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'method_name_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'i18n_method_name_key');
        $this->dbforge->drop_column($this->tableName, 'i18n_lottery_name_key');
        $this->dbforge->drop_column($this->tableName, 'i18n_method_lv1_name_key');
        $this->dbforge->drop_column($this->tableName, 'i18n_status_flag_key');
        $this->dbforge->drop_column($this->tableName, 'cnname_key');
        $this->dbforge->drop_column($this->tableName, 'method_name_key');
    }
}