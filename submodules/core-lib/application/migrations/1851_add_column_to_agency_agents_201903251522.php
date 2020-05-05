<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_agents_201903251522 extends CI_Migration {

    public function up() {
        $fields = array(
            'default_currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('agency_agents', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_agents', 'default_currency');
    }
}