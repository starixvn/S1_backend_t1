<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_fix_unknown_game_type_for_bbin_201602101631 extends CI_Migration {

	private $tableName = 'game_type';
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {
		//check first
		$this->db->from($this->tableName)->where('game_platform_id', BBIN_API)->where('game_type', 'unknown');
		$qry = $this->db->get();

		$gameTypeId = null;
		if ($qry && $qry->num_rows() > 0) {
			$row = $qry->row();
			if ($row) {
				$gameTypeId = $row->id;
			}
		}

		$data = array(
			'game_platform_id' => BBIN_API,
			'game_type' => 'unknown',
			'game_type_lang' => 'common.unknown',
			'status' => 1,
			'flag_show_in_site' => 0,
		);
		if (!empty($gameTypeId)) {
			//update
			$this->db->where('id', $gameTypeId)->update($this->tableName, $data);
		} else {
			//insert
			$this->db->insert($this->tableName, $data);
			$gameTypeId = $this->db->insert_id();
		}

		//update unknow bbin game
		//check if exists
		$this->db->from('game_description')->where('game_platform_id', BBIN_API)->where('game_code', 'unknown');

		$qry = $this->db->get();
		$gdId = null;
		if ($qry && $qry->num_rows() > 0) {
			$row = $qry->row();
			if ($row) {
				$gdId = $row->id;
			}
		}

		$data = array('game_platform_id' => BBIN_API,
			'game_name' => 'common.unknown',
			'game_code' => 'unknown',
			'dlc_enabled' => self::FLAG_FALSE,
			'flash_enabled' => self::FLAG_FALSE,
			'mobile_enabled' => self::FLAG_FALSE,
			'english_name' => 'Unknown BBIN Game',
			'external_game_id' => 'unknown',
			'flag_show_in_site' => self::FLAG_FALSE,
			'status' => self::FLAG_TRUE,
			'game_type_id' => $gameTypeId,
		);

		if (!empty($gdId)) {
			//update
			$this->db->where('id', $row->id)->update('game_description', $data);
		} else {
			//insert
			$this->db->insert('game_description', $data);
			$gdId = $this->db->insert_id();
		}

	}

	public function down() {
	}
}

///END OF FILE//////////