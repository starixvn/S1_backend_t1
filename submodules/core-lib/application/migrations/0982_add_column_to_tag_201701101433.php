<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_tag_201701101433 extends CI_Migration {

    private $tableName = 'tag';

    public function up() {
        $fields = array(
            'evidence_type' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null'=>true,
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'evidence_type');
    }
}