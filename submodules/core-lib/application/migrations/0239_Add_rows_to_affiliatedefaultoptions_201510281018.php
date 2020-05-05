<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_affiliatedefaultoptions_201510281018 extends CI_Migration {

	public function up() {
		$this->db->trans_start();

		$this->db->query("INSERT INTO `affiliatedefaultoptions` (`affiliateDefaultOptionsId`, `gameId`,  `optionsType`, `optionsValue`,`createdOn`,`updatedOn`) VALUES
( 5, 6, 'percentage', 10, '0000-00-00 00:00:00', '2015-04-24 14:00:20' ),
( 6, 6, 'acive players', 5, '0000-00-00 00:00:00', '2015-04-24 14:00:20' ),
( 7, 7, 'percentage', 10, '0000-00-00 00:00:00', '2015-04-24 14:00:20' ),
( 8, 7, 'acive players', 5, '0000-00-00 00:00:00', '2015-04-24 14:00:20' )");

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			throw new Exception('failed');
		}

	}

	public function down() {
		$this->db->trans_start();

		$this->db->query("DELETE FROM affiliatedefaultoptions WHERE affiliateDefaultOptionsId = 5 ");
		$this->db->query("DELETE FROM affiliatedefaultoptions WHERE affiliateDefaultOptionsId = 6 ");
		$this->db->query("DELETE FROM affiliatedefaultoptions WHERE affiliateDefaultOptionsId = 7 ");
		$this->db->query("DELETE FROM affiliatedefaultoptions WHERE affiliateDefaultOptionsId = 8 ");
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			throw new Exception('Deletions  failed');
		}
	}
}