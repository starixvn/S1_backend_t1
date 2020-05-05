<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_dynamic_class_lib_20190725 extends CI_Migration {

    public function up() {

        $fields = array(
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('dynamic_class_lib', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('dynamic_class_lib', 'md5_sum');
    }
}
