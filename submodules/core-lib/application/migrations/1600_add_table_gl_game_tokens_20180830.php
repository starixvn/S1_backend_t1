<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_gl_game_tokens_20180830 extends CI_Migration {

    private $tableName = 'gl_game_tokens';

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
            'token' => array(
                'type' => 'VARCHAR',
                'constraint' => '64'
            ),
            'active' => array(
                'type' => 'boolean',
                'default' => '1'
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '16',
                'null' => false,
                'default' => 'login'
            ),
            'payload' => array(
                'type' => 'TEXT',
                'null' => true
            ),
            'created_at' => array(
                'type' => 'TIMESTAMP',
                'null' => true
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);

		$this->db->query("ALTER TABLE {$this->tableName} ADD INDEX token0 (`token`)");
		$this->db->query("ALTER TABLE {$this->tableName} ADD INDEX token_type_created_active0 (`token`, `type`, `created_at`, `active`)");
    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
