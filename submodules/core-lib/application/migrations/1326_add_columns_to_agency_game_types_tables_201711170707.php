<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_agency_game_types_tables_201711170707 extends CI_Migration {

    public function up() {
        $fields = array(
            'bet_threshold' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ),
        );

        $this->dbforge->add_column('agency_structure_game_types', $fields);

        $fields = array(
            'bet_threshold' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ),
			'rolling_comm_basis' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true,
			),
			'rev_share' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			),
			'rolling_comm' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			),
			'rolling_comm_out' => array(
				'type' => 'DOUBLE',
				'null' => false,
                'default' => 0
			)
        );
        $this->dbforge->add_column('agency_player_game_types', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('agency_structure_game_types', 'bet_threshold');
        $this->dbforge->drop_column('agency_player_game_types', 'bet_threshold');
        $this->dbforge->drop_column('agency_player_game_types', 'rolling_comm_basis');
        $this->dbforge->drop_column('agency_player_game_types', 'rolling_comm');
        $this->dbforge->drop_column('agency_player_game_types', 'rolling_comm_out');
        $this->dbforge->drop_column('agency_player_game_types', 'rev_share');
    }

}
