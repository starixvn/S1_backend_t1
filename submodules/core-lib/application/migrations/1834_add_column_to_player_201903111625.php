<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_201903111625 extends CI_Migration {

    private $tableName = 'player';

    public function up() {

        $fields = array(
            'blocked_status_last_update' => array(
                'type' => 'datetime',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('blocked_status_last_update',$this->tableName))
            $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'blocked_status_last_update');
    }
}
