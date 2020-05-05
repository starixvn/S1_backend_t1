<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_external_system_20190726 extends CI_Migration {

    public function up() {

        $fields = array(
            'class_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('external_system', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('external_system', 'class_key');
    }
}
