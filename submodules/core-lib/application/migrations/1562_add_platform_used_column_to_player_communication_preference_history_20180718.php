<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_platform_used_column_to_player_communication_preference_history_20180718 extends CI_Migration {

    private $tableName = 'player_communication_preference_history';

    public function up() {

        $fields = array(
            'platform_used' => array(
                'type' => 'INT',
                'default' => 1,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);

        $alter_fields = array(
            'status' => array(
                'type' => 'INT',
                'default' => 1,
            ),
        );

        $this->dbforge->modify_column($this->tableName, $alter_fields);
    }

    public function down() {

        $this->dbforge->drop_column($this->tableName, 'platform_used');
    }
}
