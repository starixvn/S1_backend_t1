<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_friend_referral_level_201705121328 extends CI_Migration {

    public function up() {
        $fields = array(
            'selected_game_tree' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
        );

        $this->dbforge->add_column('friend_referral_level', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('friend_referral_level', 'selected_game_tree');
    }
}
