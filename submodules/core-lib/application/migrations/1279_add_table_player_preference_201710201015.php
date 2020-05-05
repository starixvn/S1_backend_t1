<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_preference_201710201015 extends CI_Migration {

    private $tableName = 'player_preference';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'player_id' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'preference' => array(
                'type' => 'TEXT',
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