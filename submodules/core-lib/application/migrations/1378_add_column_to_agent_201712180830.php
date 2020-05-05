<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agent_201712180830 extends CI_Migration {

    public function up() {
        # This column controls whether this agent has permission to perform do settlement action
        $this->dbforge->add_column('agency_agents', array(
            'can_do_settlement' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ),
        ));
        $this->dbforge->add_column('agency_structures', array(
            'can_do_settlement' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('agency_agents', 'can_do_settlement');
        $this->dbforge->drop_column('agency_structures', 'can_do_settlement');
    }
}