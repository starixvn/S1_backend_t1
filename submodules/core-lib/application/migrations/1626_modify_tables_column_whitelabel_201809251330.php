<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_tables_column_whitelabel_201809251330 extends CI_Migration {

    private $tableName = 'whitelabel_game_logs';

    public function up() {
        $fields_to_add = array(
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
            'external_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );

        $this->dbforge->add_column($this->tableName, $fields_to_add);

        $fields_to_modify = array(
            'stake' => array(
                'name'=>'stake',
                'type' => 'double',
                'null' => true,
                'default' => 0,
            ),
            'actualStake' => array(
                'name'=>'actualStake',
                'type' => 'double',
                'null' => true,
                'default' => 0,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields_to_modify);
    }

    public function down() {
        $this->dbforge->drop_column('md5_sum', 'last_sync_time','external_game_id');
    }
}