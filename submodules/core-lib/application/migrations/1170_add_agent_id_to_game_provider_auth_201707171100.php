<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_agent_id_to_game_provider_auth_201707171100 extends CI_Migration {

    private $tableName = 'game_provider_auth';

    public function up() {
        $fields = array(
            'agent_id' => array(
                'type' => 'INT',
                'null' => true,
                'default'=>0,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'agent_id');

    }
}

