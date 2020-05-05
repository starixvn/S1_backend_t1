<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_ig_game_logs_201711201752 extends CI_Migration {

    public function up() {
        // $this->db->trans_start();
        $fields = array(
            'bet_on_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'odds_2' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ),
            'odds_c' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ),
            'odds_c2' => array(
                'type' => 'DOUBLE',
                'null' => false,
                'default' => 0
            ),
            'game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'game_info_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
        );

        $this->dbforge->add_column('ig_game_logs', $fields);

        // $this->db->trans_complete();

    }

    public function down() {
        // $this->db->trans_start();
        $this->dbforge->drop_column('ig_game_logs', 'bet_on_id');
        $this->dbforge->drop_column('ig_game_logs', 'odds_2');
        $this->dbforge->drop_column('ig_game_logs', 'odds_c');
        $this->dbforge->drop_column('ig_game_logs', 'odds_c2');
        $this->dbforge->drop_column('ig_game_logs', 'game_type');
        $this->dbforge->drop_column('ig_game_logs', 'game_info_id');
        // $this->db->trans_complete();
    }

}
