<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_firebase_cloud_messaging_token_20183191500 extends CI_Migration {

    private $tableName = 'firebase_cloud_messaging_token';

    public function up() 
    {
        $fields = array (
            'id' => array (
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'player_id' => array (
                'type' => 'INT',
                'null' => true,
            ),
            'notification_token' => array (
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => false,
            ),
            'device_type' => array (
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => false,
            ),
            'app_type' => array (
                'type' => 'INT',
                'null' => false,
            ),
            'ip' => array (
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => false,
            ),
            'update_time' => array (
                'type' => 'DATETIME',
                'null' => false,
            )
        );

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
        }
    }

    public function down() {
        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}