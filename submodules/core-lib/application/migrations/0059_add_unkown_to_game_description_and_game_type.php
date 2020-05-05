<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unkown_to_game_description_and_game_type extends CI_Migration {

	public function up() {
		$this->db->query("INSERT INTO `game_description` (`id`, `game_platform_id`, `game_type_id`, `game_name`, `game_code`, `dlc_enabled`, `progressive`, `flash_enabled`, `mobile_enabled`, `note`, `status`)
VALUES
	(335, 1, 11, 'pt.unknown', 'unknown', 0, '', 0, 0, '', 1),
	(336, 2, 12, 'ag.unknown', 'unknown', 0, '', 0, 0, '', 1);
");
		$this->db->query("INSERT INTO `game_type` (`id`, `game_platform_id`, `game_type`, `game_type_lang`, `note`, `status`)
VALUES
	(11, 1, 'unknown', 'pt.unknown', '', 1),
	(12, 2, 'unknown', 'ag.unknown', '', 1);
");
	}

	public function down() {
		$this->db->query("DELETE FROM game_description WHERE game_code = 'unknown';");
		$this->db->query("DELETE FROM game_type WHERE game_type = 'unknown';");
	}
}