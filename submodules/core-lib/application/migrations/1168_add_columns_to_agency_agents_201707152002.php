<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_agency_agents_201707152002 extends CI_Migration {

    private $tableName = 'agency_agents';

    public function up() {
        $fields = array(
            'merchant_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'live_mode' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 0,
            ),
            'staging_secure_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'staging_sign_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'live_secure_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'live_sign_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

        $fields = [
            'agent_id' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
        ];
        $this->dbforge->add_column('common_tokens', $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'merchant_name');
        $this->dbforge->drop_column($this->tableName, 'live_mode');
        $this->dbforge->drop_column($this->tableName, 'staging_secure_key');
        $this->dbforge->drop_column($this->tableName, 'staging_sign_key');
        $this->dbforge->drop_column($this->tableName, 'live_secure_key');
        $this->dbforge->drop_column($this->tableName, 'live_sign_key');

        $this->dbforge->drop_column('common_tokens', 'agent_id');
    }
}

