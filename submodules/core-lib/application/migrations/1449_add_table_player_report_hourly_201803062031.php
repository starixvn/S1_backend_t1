<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_player_report_hourly_201803062031 extends CI_Migration {

    private $tableName = 'player_report_hourly';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'player_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'affiliate_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'agent_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'level_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'level_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ),
            'group_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ),
            'total_deposit' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'total_withdrawal' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'total_bonus' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'total_cashback' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'total_manaual' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'total_gross' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'total_bet' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'total_win' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'total_loss' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'total_result' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
            'date_hour' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {

        $this->dbforge->drop_table($this->tableName);

    }
}
