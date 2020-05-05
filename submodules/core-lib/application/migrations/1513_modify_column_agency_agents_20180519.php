<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_agency_agents_20180519 extends CI_Migration {

    private $tableName = 'agency_agents';

    public function up() {

        $fields = array(
            'binding_player_id' => array(
                'name'=>'binding_player_id',
                'type' => 'INT',
                'null' => true,
            ),
            'agent_level_name' => array(
                'name'=>'agent_level_name',
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'last_login_ip' => array(
                'name'=>'last_login_ip',
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'last_login_time' => array(
                'name'=>'last_login_time',
                'type' => 'DATETIME',
                'null' => true,
            ),
            'last_logout_time' => array(
                'name'=>'last_logout_time',
                'type' => 'DATETIME',
                'null' => true,
            ),
            'last_activity_time' => array(
                'name'=>'last_activity_time',
                'type' => 'DATETIME',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);

        $fields = array(
            'withdraw_password' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {

    }
}