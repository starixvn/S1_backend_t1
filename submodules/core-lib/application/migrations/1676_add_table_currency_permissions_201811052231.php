<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_currency_permissions_201811052231 extends CI_Migration {

    private $tableName = 'currency_permissions';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'player_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'agency_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'vip_level_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'affiliate_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'currency_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => false,
            ),
            'status' => array(
                'type' => 'TINYINT',
                'null' => false,
                'default' => 0,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('player_id');
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('agency_id');
        $this->dbforge->add_key('vip_level_id');
        $this->dbforge->add_key('affiliate_id');
        $this->dbforge->create_table($this->tableName);

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
