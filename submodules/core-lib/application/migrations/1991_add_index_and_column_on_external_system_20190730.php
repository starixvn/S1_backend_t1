<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_and_column_on_external_system_20190730 extends CI_Migration {

    private $tableName = 'external_system';

    public function up() {
        $fields = array(
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

        $this->load->model(['player_model']);
        $this->player_model->addUniqueIndex($this->tableName, 'idx_class_key', 'class_key');
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'updated_at');
    }
}
