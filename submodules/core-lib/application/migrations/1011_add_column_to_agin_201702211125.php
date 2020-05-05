<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_agin_201702211125 extends CI_Migration {

	public function up() {
        $fields = array(
            'jackpotsettlement' => array(
                'type' => 'double',
                'null' => true,
            ),
        );

        $this->dbforge->add_column("agin_game_logs", $fields);
    }

    public function down() {
        $this->dbforge->drop_column("agin_game_logs", 'jackpotsettlement');
    }
}