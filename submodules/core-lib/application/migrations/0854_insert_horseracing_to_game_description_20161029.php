<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_horseracing_to_game_description_20161029 extends CI_Migration {

	private $tableName = 'game_description';

	const ENGLISH_NAME = 'Horse Racing';

	public function up() {

	    $sql = "SELECT id FROM game_type where game_platform_id = ?";

	    $data = array(HRCC_API);

	    $query = $this->db->query( $sql, $data);
	    $get_game_type_id = $query->row();
	    $game_type_id = $get_game_type_id->id;

	    $this->db->insert($this->tableName, array(
	        "game_platform_id" => HRCC_API,
	        "english_name" => self::ENGLISH_NAME,
	        "game_name" => '_json:{"1":"Horse Racing","2":"赛马"}',
	        "game_type_id" => $game_type_id
	    ));
	}

	public function down() {

	    $data = array(
	        'game_platform_id' => HRCC_API,
	        'english_name' => self::ENGLISH_NAME
	    );

	    $this->db->delete($this->tableName, $data);

	}
}