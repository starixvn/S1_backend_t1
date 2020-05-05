<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_sale_orders_notes_20190712 extends CI_Migration {

    private $tableName = 'sale_orders_notes';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'sale_order_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => false,
            ),
            'note_type' => array(
                'type' => 'TINYINT',
                'null' => false,
            ),
            'content' => array(
                'type' => 'TEXT',
                'null' => false,
            ),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'created_by' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 0
            ),
            'visible_by' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
        );

        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addIndex($this->tableName, 'idx_sale_order_id', 'sale_order_id');
            $this->player_model->addIndex($this->tableName, 'idx_created_at', 'created_at');
        }
    }

    public function down() {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}