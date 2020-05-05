<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_table_player_notification_20181019 extends CI_Migration {

	private $tableName = 'player_notification';

	public function up() {
        $fields = array(
            'notify_id' => array(
                'type' => 'INT',
                'null' => false,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'player_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
            'source_type' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
            'notify_type' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
            'title' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'message' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'url' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'url_target' => array(
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true,
            ),
            'is_notify' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => TRUE,
                'default' => 0,
            ),
            'notify_time' => array(
                'type' => 'DATETIME',
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

        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('notify_id', TRUE);
        $this->dbforge->create_table($this->tableName);
        $this->db->query("ALTER TABLE {$this->tableName} ADD INDEX player_id (`player_id`)");
        $this->db->query("ALTER TABLE {$this->tableName} ADD INDEX player_notify_list (`player_id`, `is_notify`)");
	}

	public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
	}
}
