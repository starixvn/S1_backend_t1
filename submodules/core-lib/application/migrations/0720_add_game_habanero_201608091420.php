<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_game_habanero_201608091420 extends CI_Migration {
	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;
	public function up() {

// 		$response = <<<EOD
// {"GetGamesResult":{"Games":{"GameClientDbDTO":[{"BrandGameId":"50e36d20-a967-47b9-a4d5-1638b8c271a7","Name":"Sparta","KeyName":"SGSparta","IsNew":true,"DtAdded":"2016-08-08T07:49:28.293+00:00","DtUpdated":"2016-08-08T07:49:28.293+00:00","GameTypeId":11,"ReleaseStatusId":4,"GameTypeName":"Video Slots","MobileCapable":true,"MobiPlatformId":2,"WebPlatformId":2,"GameTypeDisplayName":"Slots","BaseGameTypeId":11,"ExProv":false,"LineDesc":"25 Lines","IsFeat":false,"RTP":"96.02","ReportName":"Sparta [96.02%] 25 Lines","TranslatedNames":{"GameTranslationDTO":[{"LanguageId":1,"Locale":"en","Translation":"Sparta"}]}}]}}}
// EOD;
// 		// $response = $client->GetGameTypes($params);
// 		$response = json_decode($response,true);
// 		$data = $response['GetGamesResult']['Games']['GameClientDbDTO'];
// 		foreach ($data as $game) {
// 			$game_item = array();
// 			$this->db->select('game_type.id');
// 			$this->db->from('game_type');
// 			$this->db->where('game_type.game_platform_id', HB_API);
// 			$this->db->where('game_type.game_type', $game['GameTypeName']);
// 			$query = $this->db->get();
// 			$game_type = $query->result_array();

// 			$game_type_id = $game_type[0]['id'];

// 			if(!empty($game_type_id)){
// 				$game_item['game_platform_id'] = HB_API;
// 				$game_item['game_type_id'] = $game_type_id;
// 				$game_item['game_name'] = $game['Name'];
// 				$game_item['game_code'] = $game['KeyName'];
// 				$game_item['english_name'] = $game['Name'];
// 				$game_item['external_game_id'] = $game['BrandGameId'];
// 				$this->db->insert('game_description', $game_item);
// 				$game_item['game_type'] = $game['GameTypeName'];

// 				 $json[] = $game_item;
// 			}
// 		}

// 		$this->utils->debug_log(json_encode($json));

	}
	public function down() {
		// $game_platform_id = HB_API;
		// $this->db->where('game_platform_id', $game_platform_id);
		// $this->db->where('game_name', 'Sparta');
		// $this->db->delete('game_description');
	}
}
