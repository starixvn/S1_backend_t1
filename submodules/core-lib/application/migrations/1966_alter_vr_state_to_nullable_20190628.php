<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_vr_state_to_nullable_20190628 extends CI_Migration {

    CONST TableName = 'vr_game_logs';

    public function up() {

        $fields = array(
            'state' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true
            )
        );
        $this->dbforge->modify_column(self::TableName, $fields);
    }

    public function down() {

       $fields = array(
            'state' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            )
        );
        $this->dbforge->modify_column(self::TableName, $fields);
    }
    
}