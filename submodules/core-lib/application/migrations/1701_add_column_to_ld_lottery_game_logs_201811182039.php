<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_ld_lottery_game_logs_201811182039 extends CI_Migration {

    public function up() {
        $fields = [
            'round_key' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('round_key', 'ld_lottery_game_logs')){
            $this->dbforge->add_column('ld_lottery_game_logs', $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('round_key', 'ld_lottery_game_logs')){
            $this->dbforge->drop_column('ld_lottery_game_logs', 'round_key');
        }
    }
}
