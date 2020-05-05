<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_columns_on_player_201810302349 extends CI_Migration {

    private $tableName = 'player';

    public function up(){

        $fields = array(
            'withdraw_password' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'withdraw_password_md5' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
            'agent_tracking_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'agent_tracking_source_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
    }
}
