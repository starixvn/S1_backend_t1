<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_dynamic_class_lib_20190723 extends CI_Migration {

    public function up() {

        $fields = array(
            'class_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('dynamic_class_lib', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('dynamic_class_lib', 'class_name');
    }
}