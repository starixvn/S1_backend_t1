<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ag_subproviders_201702211327 extends CI_Migration {

	public function up() {
        $fields = array(
            'jackpotsettlement' => array(
                'type' => 'double',
                'null' => true,
            ),
        );

        $this->dbforge->add_column("ag_game_logs", $fields);
        $this->dbforge->add_column("agbbin_game_logs", $fields);
        $this->dbforge->add_column("agshaba_game_logs", $fields);
    }

    public function down() {
        $this->dbforge->drop_column("ag_game_logs", 'jackpotsettlement');
        $this->dbforge->drop_column("agbbin_game_logs", 'jackpotsettlement');
        $this->dbforge->drop_column("agshaba_game_logs", 'jackpotsettlement');
    }
}