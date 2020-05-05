<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_attached_file_status_201901291850 extends CI_Migration {

    private $tableName = 'attached_file_status';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ),
            'player_id' => array(
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ),
            'attachment_tag' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}