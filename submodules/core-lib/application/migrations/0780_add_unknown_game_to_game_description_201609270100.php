<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_unknown_game_to_game_description_201609270100 extends CI_Migration {

    const FLAG_TRUE = 1;
    const FLAG_FALSE = 0;

    public function up() {
        $this->db->trans_start();

        $data = array(
            GSMG_API => array(
                'game_type' => array(
                    'game_type' 		=> 'unknown',
                    'game_type_lang' 	=> 'gsmg.unknown',
                    'status' 			=> self::FLAG_TRUE,
                    'flag_show_in_site' => self::FLAG_FALSE,
                ),
                'game_description' => array(
                    'game_name' => 'gsmg.unknown',
                    'english_name' => 'Unknown GSMG Game',
                    'external_game_id' => 'unknown',
                    'game_code' => 'unknown',
                ),
            ),
            SEVEN77_API => array(
                'game_type' => array(
                    'game_type' 		=> 'unknown',
                    'game_type_lang' 	=> 'seven77.unknown',
                    'status' 			=> self::FLAG_TRUE,
                    'flag_show_in_site' => self::FLAG_FALSE,
                ),
                'game_description' => array(
                    'game_name' => 'seven77.unknown',
                    'english_name' => 'Unknown SEVEN77 Game',
                    'external_game_id' => 'unknown',
                    'game_code' => 'unknown',
                ),
            ),
            HRCC_API => array(
                'game_type' => array(
                    'game_type' 		=> 'unknown',
                    'game_type_lang' 	=> 'hrcc.unknown',
                    'status' 			=> self::FLAG_TRUE,
                    'flag_show_in_site' => self::FLAG_FALSE,
                ),
                'game_description' => array(
                    'game_name' => 'hrcc.unknown',
                    'english_name' => 'Unknown HRCC Game',
                    'external_game_id' => 'unknown',
                    'game_code' => 'unknown',
                ),
            ),
        );

        $game_description_list = array();
        foreach ($data as $game_platform_id => $game_type) {
            $game_type['game_type']['game_platform_id'] = $game_platform_id;
            $this->db->insert('game_type', $game_type['game_type']);
            $game_type_id = $this->db->insert_id();

            $game_type['game_description']['game_platform_id'] = $game_platform_id;
            $game_type['game_description']['game_type_id'] = $game_type_id;
            $this->db->insert('game_description', $game_type['game_description']);

        }

        $this->db->trans_complete();
    }

    public function down() {

        $game_platform_id = array(GSMG_API,SEVEN77_API,HRCC_API);

        $this->db->where_in('game_platform_id', $game_platform_id);
        $this->db->where('game_code', 'unknown');
        $this->db->delete('game_description');

        $this->db->where_in('game_platform_id', $game_platform_id);
        $this->db->where('game_type', 'unknown');
        $this->db->delete('game_type');
    }
}
