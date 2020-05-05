<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_dynamic_class_lib_20190721 extends CI_Migration {

    private $tableName = 'dynamic_class_lib';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            //1: game api , 2: payment api , 3: promo rule
            'class_type'=> array(
                'type' => 'INT',
                'null' => false,
            ),
            'unique_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => false,
            ),
            'class_content' => array(
                'type' => 'LONGTEXT',
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_created_at', 'created_at');
        $this->player_model->addUniqueIndex($this->tableName, 'idx_unique_key', 'unique_key');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
