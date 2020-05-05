<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_seven_game_type_and_game_description_201609261753 extends CI_Migration {
    const FLAG_TRUE = 1;
    const FLAG_FALSE = 0;

    public function up() {

        // $this->db->trans_start();

        // $data = array(
        //     array(
        //         'game_type' => 'Slot',
        //         'game_type_lang' => 'slot',
        //         'status' => self::FLAG_TRUE,
        //         'flag_show_in_site' => self::FLAG_TRUE,
        //         'game_description_list' => array(
        //             array(
        //                 'game_name' => 'Baby Heart',
        //                 'english_name' => 'Baby Heart',
        //                 'external_game_id' => 229,
        //                 'game_code' => 229,
        //             ),
        //             array(
        //                 'game_name' => 'City Night',
        //                 'english_name' => 'City Night',
        //                 'external_game_id' => 230,
        //                 'game_code' => 230,
        //             ),
        //             array(
        //                 'game_name' => 'Rose and beauty',
        //                 'english_name' => 'Rose and beauty',
        //                 'external_game_id' => 231,
        //                 'game_code' => 231,
        //             ),
        //             array(
        //                 'game_name' => 'Constellation',
        //                 'english_name' => 'Constellation',
        //                 'external_game_id' => 232,
        //                 'game_code' => 232,
        //             ),
        //             array(
        //                 'game_name' => 'The Vikings',
        //                 'english_name' => 'The Vikings',
        //                 'external_game_id' => 242,
        //                 'game_code' => 242,
        //             ),
        //             array(
        //                 'game_name' => 'Happy Farm',
        //                 'english_name' => 'Happy Farm',
        //                 'external_game_id' => 244,
        //                 'game_code' => 244,
        //             ),
        //             array(
        //                 'game_name' => 'Egypt Style',
        //                 'english_name' => 'Egypt Style',
        //                 'external_game_id' => 26,
        //                 'game_code' => 26,
        //             ),
        //             array(
        //                 'game_name' => 'Egypt Style',
        //                 'english_name' => 'Egypt Style',
        //                 'external_game_id' => 26,
        //                 'game_code' => 26,
        //             ),
        //             array(
        //                 'game_name' => 'Egypt Style',
        //                 'english_name' => 'Egypt Style',
        //                 'external_game_id' => 26,
        //                 'game_code' => 26,
        //             ),
        //             array(
        //                 'game_name' => 'Alladin Treasure',
        //                 'english_name' =>  'Alladin Treasure',
        //                 'external_game_id' => 418,
        //                 'game_code' => 418,
        //             ),
        //             array(
        //                 'game_name' => 'Vampire Castle',
        //                 'english_name' => 'Vampire Castle',
        //                 'external_game_id' => 425,
        //                 'game_code' => 425,
        //             ),
        //             array(
        //                 'game_name' => 'Sexy Girl',
        //                 'english_name' => 'Sexy Girl',
        //                 'external_game_id' => 426,
        //                 'game_code' => 426,
        //             ),
        //             array(
        //                 'game_name' => 'Holiday Bikini',
        //                 'english_name' => 'Holiday Bikini',
        //                 'external_game_id' => 427,
        //                 'game_code' => 427,
        //             ),
        //             array(
        //                 'game_name' => 'Big Show',
        //                 'english_name' =>  'Big Show',
        //                 'external_game_id' => 429,
        //                 'game_code' => 429,
        //             ),
        //             array(
        //                 'game_name' => 'Pink lady',
        //                 'english_name' => 'Pink lady',
        //                 'external_game_id' => 43,
        //                 'game_code' => 43,
        //             ),
        //             array(
        //                 'game_name' => 'Hotel lady',
        //                 'english_name' => 'Hotel lady',
        //                 'external_game_id' => 44,
        //                 'game_code' => 44,
        //             ),
        //             array(
        //                 'game_name' => 'Beauty in bed',
        //                 'english_name' =>  'Beauty in bed',
        //                 'external_game_id' => 45,
        //                 'game_code' => 45,
        //             ),
        //             array(
        //                 'game_name' => 'Night Dancers',
        //                 'english_name' => 'Night Dancers',
        //                 'external_game_id' => 606,
        //                 'game_code' => 606,
        //             ),
        //             array(
        //                 'game_name' => 'Christmas Girl',
        //                 'english_name' => 'Christmas Girl',
        //                 'external_game_id' => 607,
        //                 'game_code' => 607,
        //             ),
        //             array(
        //                 'game_name' => 'Sex and zen',
        //                 'english_name' => 'Sex and zen',
        //                 'external_game_id' => 608,
        //                 'game_code' => 608,
        //             ),
        //             array(
        //                 'game_name' => 'Jinpingmei',
        //                 'english_name' => 'Jinpingmei',
        //                 'external_game_id' => 609,
        //                 'game_code' => 609,
        //             ),
        //         ),
        //     ),
        // );

        // $game_description_list = array();
        // foreach ($data as $game_type) {
        //     $this->db->insert('game_type', array(
        //         'game_platform_id' => SEVEN77_API,
        //         'game_type' => $game_type['game_type'],
        //         'game_type_lang' => $game_type['game_type_lang'],
        //         'status' => $game_type['status'],
        //         'flag_show_in_site' => $game_type['flag_show_in_site'],
        //     ));

        //     $game_type_id = $this->db->insert_id();
        //     foreach ($game_type['game_description_list'] as $game_description) {
        //         $game_description_list[] = array_merge(array(
        //             'game_platform_id' => SEVEN77_API,
        //             'game_type_id' => $game_type_id,
        //         ), $game_description);
        //     }
        // }

        // $this->db->insert_batch('game_description', $game_description_list);
        // $this->db->trans_complete();

    }

    public function down() {
        // $this->db->trans_start();
        // $this->db->delete('game_type', array('game_platform_id' =>  SEVEN77_API ));
        // $this->db->delete('game_description', array('game_platform_id' =>  SEVEN77_API ));


        // $this->db->trans_complete();
    }
}