<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_contact_number_backup_20181108 extends CI_Migration {

    private $tableName = 'player_contact_number_backup';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'playerId' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'contactNumber' => array(
                'type' => 'varchar',
                'constraint' => '50',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
        );
        $this->dbforge->drop_table($this->tableName);
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('playerId');
        $this->dbforge->create_table($this->tableName);

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
