<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_game_types_20171227 extends CI_Migration {

    public function up() {
        $fields = array(
            'min_rolling_comm' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ),
        );

        $this->dbforge->add_column('agency_structure_game_types', $fields);
        $this->dbforge->add_column('agency_agent_game_types', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_structure_game_types', 'min_rolling_comm');
        $this->dbforge->drop_column('agency_agent_game_types', 'min_rolling_comm');
    }

}
