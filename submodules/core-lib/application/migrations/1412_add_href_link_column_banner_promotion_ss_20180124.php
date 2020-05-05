<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_href_link_column_banner_promotion_ss_20180124 extends CI_Migration {

    private $tableName = 'banner_promotion_ss';

    public function up() {
        $fields = array(
            'href_link' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'href_link');
    }
}