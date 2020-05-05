<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_game_types extends CI_Migration {

	public function up() {
// 		$this->db->query("INSERT INTO `game_type` (`id`, `game_platform_id`, `game_type`, `game_type_lang`, `note`, `status`)
// VALUES
// 	(1, 2, 'EBR', 'ag_egame', '', 1),
// 	(2, 2, 'BR', 'ag_live', '', 1),
// 	(3, 1, 'Card Games', 'pt_card_games', '', 1),
// 	(4, 1, 'Fixed Odds', 'pt_fixed_odds', '', 1),
// 	(5, 1, 'Table Games', 'pt_table_games', '', 1),
// 	(6, 1, 'Live Games', 'pt_live_games', '', 1),
// 	(7, 1, 'Slot Machines', 'pt_slots', '', 1),
// 	(8, 1, 'Scratchcards', 'pt_scratch_cards', '', 1),
// 	(9, 1, 'Video Pokers', 'pt_video_pokers', '', 1),
// 	(10, 1, 'Sidegames', 'pt_sidegames', '', 1);
// ");
	}

	public function down() {
		// $this->db->query('DELETE * FROM `game_types');
	}
}