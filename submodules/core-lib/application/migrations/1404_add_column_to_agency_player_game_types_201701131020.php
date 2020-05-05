<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agency_player_game_types_201701131020 extends CI_Migration {

    # Add on missing column to agency player game types as opposed to agency_agent/structure_game_types
    public function up() {
        $fields = array(
            'min_rolling_comm' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ),
            'platform_fee' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0,
            ),
        );

        $this->dbforge->add_column('agency_player_game_types', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_player_game_types', 'min_rolling_comm');
        $this->dbforge->drop_column('agency_player_game_types', 'platform_fee');
    }

}
