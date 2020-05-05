<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_notification_20180926 extends CI_Migration {

    private $tableName = 'player_notification';

    public function up() {
        $fields = array(
            'player_notify_id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'player_id' => array(
                'type' => 'INT',
            ),
            'notify_id' => array(
                'type' => 'INT',
            ),
            'notify_contents' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true
            )
        );

        if (!$this->db->table_exists($this->tableName)) {
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('player_notify_id', TRUE);
            $this->dbforge->add_key('player_id');
            $this->dbforge->create_table($this->tableName);
        }
    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}