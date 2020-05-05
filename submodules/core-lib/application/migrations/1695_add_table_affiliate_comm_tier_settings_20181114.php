<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_affiliate_comm_tier_settings_20181114 extends CI_Migration {

    private $tableName = 'affiliate_comm_tier_settings';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'level' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'min_net_revenue' => array(
                'type' => 'INT',
                'default' => 0,
            ),
            'max_net_revenue' => array(
                'type' => 'INT',
                'default' => 0,
            ),
            'commission_rates' => array(
                'type' => 'INT',
                'default' => 0,
            ),
            'affiliate_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'deleted_at' => array(
                'type' => 'DATETIME',
                'null' => true
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
