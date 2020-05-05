<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_adjust_username_201707162148 extends CI_Migration {

    private $tableName = 'agency_agents';

    public function up() {
        $fields = array(
            'player_prefix' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

        $this->load->model(['player_model']);

        $this->player_model->dropIndex('player', 'idx_un_username');

        $this->db->query('update player set agent_id=0 where agent_id is null');

        //add unique username/agent_id
        $this->db->query('create unique index idx_un_username on player(username, agent_id)');

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'player_prefix');

    }
}

