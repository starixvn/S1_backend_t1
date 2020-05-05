<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_game_type_add_order_08272005 extends CI_Migration {

	public function up() {

		$this->db->query("DELETE FROM `game_type`");
		$this->db->query("INSERT INTO `game_type` (`id`, `game_platform_id`, `game_type`, `game_type_lang`, `note`, `status`, `flag_show_in_site`, `order_id`)
VALUES
	(1, 2, 'EBR', 'ag_egame', '', 1, 1, NULL),
	(2, 2, 'BR', 'ag_live', '', 1, 1, NULL),
	(3, 1, 'Card Games', 'pt_card_games', '', 1, 1, 5),
	(4, 1, 'Fixed Odds', 'pt_fixed_odds', '', 1, 1, 3),
	(5, 1, 'Table Games', 'pt_table_games', '', 1, 1, 4),
	(6, 1, 'Live Games', 'pt_live_games', '', 1, 1, NULL),
	(7, 1, 'Slot Machines', 'pt_slots', '', 1, 1, 6),
	(8, 1, 'Scratchcards', 'pt_scratch_cards', '', 1, 1, 1),
	(9, 1, 'Video Pokers', 'pt_video_pokers', '', 1, 1, 2),
	(10, 1, 'Sidegames', 'pt_sidegames', '', 1, 1, NULL),
	(11, 1, 'unknown', 'pt.unknown', '', 1, 1, NULL),
	(12, 2, 'unknown', 'ag.unknown', '', 1, 1, NULL);

			");

	}

	public function down() {
		$this->dbforge->drop_table('game_type');
	}
}
