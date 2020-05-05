<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_mg_game_name_in_game_description_201510251929 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {

		$data = array(
			array(
				'game_code' => 'AvalonV90',
				'english_name' => 'Avalon 90',
			),
			array(
				'game_code' => 'TitansoftheSunTheia',
				'english_name' => 'Titans of the Sun - Theia',
			),
			array(
				'game_code' => 'TitansoftheSunHyperion',
				'english_name' => 'Titans of the Sun - Hyperion',
			),
			array(
				'game_code' => 'adventurepalace',
				'english_name' => 'Adventure Palace',
			),
			array(
				'game_code' => 'ThroneOfEgyptv90',
				'english_name' => 'Throne of Egypt 90',
			),
			array(
				'game_code' => 'RubyHitman',
				'english_name' => 'Hitman',
			),
			array(
				'game_code' => 'RubyBreakDaBankAgainV90',
				'english_name' => 'Break da Bank Again 90',
			),
			array(
				'game_code' => 'MSBreakDaBankAgain',
				'english_name' => 'Megaspin - Break da Bank Again',
			),
		);

		$this->db->update_batch($this->tableName, $data, 'game_code');

		//fix nt game code
		$this->dbforge->add_column($this->tableName, array(
			'external_game_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '300',
				'null' => true,
			),
		));

		// $this->dbforge->drop_column($this->tableName, 'icafe_chinese_simp_zh');
		// $this->dbforge->drop_column($this->tableName, 'icafe_chinese_trad_zhtw');
		// $this->dbforge->drop_column($this->tableName, 'translations');
		// $this->dbforge->drop_column($this->tableName, 'lang_en');
		// $this->dbforge->drop_column($this->tableName, 'lang_id');
		// $this->dbforge->drop_column($this->tableName, 'lang_vi');
		// $this->dbforge->drop_column($this->tableName, 'lang_fr');
		// $this->dbforge->drop_column($this->tableName, 'lang_de');
		// $this->dbforge->drop_column($this->tableName, 'lang_el');
		// $this->dbforge->drop_column($this->tableName, 'lang_it');
		// $this->dbforge->drop_column($this->tableName, 'lang_ja');
		// $this->dbforge->drop_column($this->tableName, 'lang_ko');
		// $this->dbforge->drop_column($this->tableName, 'lang_ru');
		// $this->dbforge->drop_column($this->tableName, 'lang_spanish');
		// $this->dbforge->drop_column($this->tableName, 'lang_tr');
		// $this->dbforge->drop_column($this->tableName, 'lang_pt_br');

		$this->db->query('update game_description set external_game_id=game_code');

		$this->db->query('update game_description set external_game_id=substr(game_name,10,3) where game_platform_id=' . NT_API);

	}

	public function down() {
	}
}