<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_player_attached_proof_file_tables_201711201754 extends CI_Migration {

    public function up() {
        $fields = array(
            'visible_to_player' => array(
                'type' => 'TINYINT',
                'null' => false,
                'default' => 1
            ),
        );

        $this->dbforge->add_column('player_attached_proof_file', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('player_attached_proof_file', 'visible_to_player');
    }

}
