<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_static_sites_201612291115 extends CI_Migration {

    private $tableName = 'static_sites';

    public function up() {

        $fields = array(
            'player_center_css' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'admin_css' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'aff_css' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'agency_css' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'player_center_css');
        $this->dbforge->drop_column($this->tableName, 'admin_css');
        $this->dbforge->drop_column($this->tableName, 'agency_css');
        $this->dbforge->drop_column($this->tableName, 'aff_css');
    }
}
