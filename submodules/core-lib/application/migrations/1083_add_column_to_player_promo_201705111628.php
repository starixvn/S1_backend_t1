<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_player_promo_201705111628 extends CI_Migration {

    public function up() {
        $fields = array(
            'release_to_sub_wallet' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
        );

        $this->dbforge->add_column('playerpromo', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('playerpromo', 'release_to_sub_wallet');
    }
}
