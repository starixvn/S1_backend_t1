<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_ggpoker_ew_game_logs_20190702 extends CI_Migration {

    public function up() {

        $ggpoker_ew_game_logs_fields = array(
            'winlossPoker' => array(
                'type' => 'double',
                'null' => true,
            ),
            'convertedWinlossPoker' => array(
                'type' => 'double',
                'null' => true,
            ),
        );
        $this->dbforge->add_column('ggpoker_ew_game_logs', $ggpoker_ew_game_logs_fields);
    }

    public function down() {
        $this->dbforge->drop_column('ggpoker_ew_game_logs', 'convertedWinlossPoker');
        $this->dbforge->drop_column('ggpoker_ew_game_logs', 'winlossPoker');
    }
}