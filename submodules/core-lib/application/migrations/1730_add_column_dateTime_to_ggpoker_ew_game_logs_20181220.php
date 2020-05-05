<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_dateTime_to_ggpoker_ew_game_logs_20181220 extends CI_Migration {

    public function up() {
        $fields = [
            'dateTime' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
        ];

        if(!$this->db->field_exists('dateTime', 'ggpoker_ew_game_logs')){
            $this->dbforge->add_column('ggpoker_ew_game_logs', $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('dateTime', 'ggpoker_ew_game_logs')){
            $this->dbforge->drop_column('ggpoker_ew_game_logs', 'dateTime');
        }
    }
}
