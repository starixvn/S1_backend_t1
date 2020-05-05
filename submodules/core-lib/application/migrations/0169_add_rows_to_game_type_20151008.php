<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_game_type_20151008 extends CI_Migration {

	public function up() {
// 		$this->db->query("INSERT INTO `game_type` (`id`, `game_platform_id`, `game_type`, `game_type_lang`, `note`, `status`)
// VALUES
// 	(13, 6, 'Table Games', 'mg_table_games', '', 1),
// 	(14, 6, 'Slots', 'mg_slots', '', 1),
// 	(15, 6, 'Video Pokers', 'mg_video_pokers', '', 1),
// 	(16, 6, 'Progressives', 'mg_table_games', '', 1),
// 	(17, 6, 'Scratchcards', 'mg_scratch_cards', '', 1),
// 	(18, 6, 'Others', 'mg_others', '', 1);
// ");
	}

	public function down() {
		$this->db->query('DELETE FROM game_type WHERE id IN(13,14,15,16,17,18)');
	}
}