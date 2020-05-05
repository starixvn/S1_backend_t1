<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_comapi_settings_cache_20180523 extends CI_Migration {

    private $tableName = 'comapi_settings_cache';

    public function up() {
        if ($this->db->table_exists($this->tableName)) {
            return;
        }

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '128'
            ),
            'is_cache' => array(
                'type' => 'boolean',
                'default' => '0'
            ),
            'info' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'url' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'value' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ),
            'response' => array(
                'type' => 'MEDIUMTEXT',
                'null' => true
            ),
            'last_update' => array(
                'type' => 'TIMESTAMP',
                'null' => true,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('name');

        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}