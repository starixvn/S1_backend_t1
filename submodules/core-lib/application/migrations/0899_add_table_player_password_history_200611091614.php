<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_password_history_200611091614 extends CI_Migration {

    protected $tableName = "player_password_history";

    public function up() {

        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'player_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'current_password' => array(
                'type' => 'VARCHAR',
                'constraint'=> 100,
                'null' => false,
            ),
            'new_password' => array(
                'type' => 'VARCHAR',
                'constraint'=> 100,
                'null' => false,
            ),
            'action' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
