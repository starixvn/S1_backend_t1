<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_queue_results_201901272140 extends CI_Migration {

    private $tableName='queue_results';

    public function up() {
        $fields = array(
           'final_result' => array(
                'type' => 'TEXT',
                'null'=> true
            ),
        );

        if(!$this->db->field_exists('final_result', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_created_at', 'created_at');
        $this->player_model->addIndex($this->tableName, 'idx_updated_at', 'updated_at');

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'final_result');
    }
}
