<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_bet_threshold_column_to_agency_agent_game_types_201711151520 extends CI_Migration {

    public function up() {
        $fields = array(
            'bet_threshold' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ),
        );

        $this->dbforge->add_column('agency_agent_game_types', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_agent_game_types', 'bet_threshold');
    }

}
