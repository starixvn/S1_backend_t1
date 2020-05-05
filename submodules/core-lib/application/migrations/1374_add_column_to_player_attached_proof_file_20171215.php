<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_attached_proof_file_20171215 extends CI_Migration {

    private $tableName = 'player_attached_proof_file';

    public function up() {
        $fields = array(
            'sales_order_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'null' => true,
			),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'sales_order_id');
    }
}
