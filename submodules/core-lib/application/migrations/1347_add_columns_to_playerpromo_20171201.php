<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_playerpromo_20171201 extends CI_Migration {

	public function up() {
        $fields = array(
            'login_ip' => array(
                'type' => 'varchar',
                'constraint' => '32',
                'null' => true,
            )
        );
        $this->dbforge->add_column('playerpromo', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('playerpromo', 'login_ip');
    }
}