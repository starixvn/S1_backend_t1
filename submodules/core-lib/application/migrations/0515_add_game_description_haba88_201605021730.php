<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_game_description_haba88_201605021730 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	public function up() {

		// $brandId = '';
		// $APIKey = '';
		// $wsdl_url = 'https://ws-a.insvr.com/hosted.asmx?WSDL';
		// $client = new SoapClient($wsdl_url);
		// $params = array(
		// 	'req' => array(
		// 		"BrandId" => $brandId,
		// 		"APIKey" => $APIKey,
		// 	),
		// );
		// $response = $client->GetGames($params);
		// // $response = $client->GetGameTypes($params);
		// $data = $response->GetGamesResult->Games->GameClientDbDTO;
		// foreach ($data as $game) {
		// 	$game_item = array();
		// 	$this->db->select('game_type.id');
		// 	$this->db->from('game_type');
		// 	$this->db->where('game_type.game_platform_id', HB_API);
		// 	$this->db->where('game_type.game_type', $game->GameTypeName);
		// 	$query = $this->db->get();
		// 	$game_type = $query->result_array();
		// 	$game_type_id = $game_type[0]['id'];
		// 	$game_item['game_platform_id'] = HB_API;
		// 	$game_item['game_type_id'] = $game_type_id;
		// 	$game_item['game_name'] = $game->Name;
		// 	$game_item['game_code'] = $game->KeyName;
		// 	$game_item['english_name'] = $game->Name;
		// 	$game_item['external_game_id'] = $game->BrandGameId;
		// 	$this->db->insert('game_description', $game_item);
		// 	$game_item['game_type'] = $game->GameTypeName;

		// 	/*$game_item = array();
		// 	$this->db->select('game_type.id');
		// 	$this->db->from('game_type');
		// 	$this->db->where('game_type.game_platform_id', HB_API);
		// 	$this->db->where('game_type.game_type', $game->GameTypeName);
		// 	$query = $this->db->get();
		// 	$game_type = $query->result_array();
		// 	echo $game->game_type.' '.print_r($game_type);exit;
		// 	$game_type_id = $game_type[0]['id'];
		// 	$game_item['game_platform_id'] = HB_API;
		// 	$game_item['game_type_id'] = $game_type_id;
		// 	$game_item['game_name'] = $game->Name;
		// 	$game_item['game_code'] = $game->game_code;
		// 	$game_item['english_name'] = $game->english_name;
		// 	$game_item['external_game_id'] = $game->external_game_id;
		// 	$this->db->insert('game_description', $game_item);*/
		// 	// $game_item['game_type'] = $game->GameTypeName;

		// 	// $json[] = $game_item;
		// }

		// $this->utils->debug_log(json_encode($json));

	}
	public function down() {
		$game_platform_id = HB_API;

		$this->db->where('game_platform_id', $game_platform_id);
		$this->db->delete('game_description');
	}
}