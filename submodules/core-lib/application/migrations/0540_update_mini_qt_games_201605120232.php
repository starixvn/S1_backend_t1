<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_mini_qt_games_201605120232 extends CI_Migration {

	private $tableName = 'game_description';

	public function up() {
		$data = array(
			array('game_code' => 'OGS-bigfootmini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-bobby7smini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-cherryblossomsmini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-doctorlovemini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-irisheyesmini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-jokerjestermini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-lovebugsmini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-madmadmonkeymini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-medusamini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-merlinsmillionssuperbetmini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-oilmaniamini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-ramessesrichesmini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-thesnakecharmermini','attributes' => '{mini:true}'),
			array('game_code' => 'OGS-venetianrosemini','attributes' => '{mini:true}')
		);
		$this->db->update_batch($this->tableName, $data, 'game_code');
	}

	public function down() {
		$data = array(
			array('game_code' => 'OGS-bigfootmini','attributes' => null),
			array('game_code' => 'OGS-bobby7smini','attributes' => null),
			array('game_code' => 'OGS-cherryblossomsmini','attributes' => null),
			array('game_code' => 'OGS-doctorlovemini','attributes' => null),
			array('game_code' => 'OGS-irisheyesmini','attributes' => null),
			array('game_code' => 'OGS-jokerjestermini','attributes' => null),
			array('game_code' => 'OGS-lovebugsmini','attributes' => null),
			array('game_code' => 'OGS-madmadmonkeymini','attributes' => null),
			array('game_code' => 'OGS-medusamini','attributes' => null),
			array('game_code' => 'OGS-merlinsmillionssuperbetmini','attributes' => null),
			array('game_code' => 'OGS-oilmaniamini','attributes' => null),
			array('game_code' => 'OGS-ramessesrichesmini','attributes' => null),
			array('game_code' => 'OGS-thesnakecharmermini','attributes' => null),
			array('game_code' => 'OGS-venetianrosemini','attributes' => null)
		);
		$this->db->update_batch($this->tableName, $data, 'game_code');
	}
}