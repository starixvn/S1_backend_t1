<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_risk_score_history_logs_20190627 extends CI_Migration {

    private $tableName = 'risk_score_history_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'player_id' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'risk_score_category' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'result_change_from' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'result_change_to' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'score_from' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'score_to' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'total_score_from' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'total_score_to' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'risk_score_level_from' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'risk_score_level_to' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'remarks' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_created_at', 'created_at');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
