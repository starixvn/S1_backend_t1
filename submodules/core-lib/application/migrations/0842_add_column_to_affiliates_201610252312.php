<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_affiliates_201610252312 extends CI_Migration {

    private $tableName = "affiliates";

    public function up() {
        $fields = array(
            'prefix_of_player' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint'=> 10,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

        $this->db->query('create unique index idx_prefix on affiliates(prefix_of_player)');
    }

    public function down() {

        $this->db->query('drop index idx_prefix on affiliates');

        $this->dbforge->drop_column($this->tableName, 'prefix_of_player');
    }
}
