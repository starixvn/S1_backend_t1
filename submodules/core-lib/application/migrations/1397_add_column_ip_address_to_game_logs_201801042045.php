<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_ip_address_to_game_logs_201801042045 extends CI_Migration {

	private $tableName = 'game_logs';

    public function up() {
        $fields = array(
            'ip_address' => array(
                'type' => 'varchar',
                'constraint' => 100,
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'ip_address');
    }
}

////END OF FILE////