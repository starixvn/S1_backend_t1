<?php
trait game_description_ag {

	public function sync_game_description_ag(){

		$cnt=0;

		$game_descriptions = array(
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'FRU', 
				'game_name' => '_json:{"1":"Fruit Slot","2":"水果拉霸"}', 
				"english_name" => "Fruit Slot", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'PKBJ', 
				'game_name' => '_json:{"1":"Video poker2 (jacks or better)","2":"㜘克高手"}', 
				"english_name" => "Video poker2 (jacks or better)", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SLM1', 
				'game_name' => '_json:{"1":"Beauty & Beach Volley","2":"美女沙排"}', 
				"english_name" => "Beauty & Beach Volley", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SLM2', 
				'game_name' => '_json:{"1":"The Wealthy Lamb","2":"新⸜忳財羊"}', 
				"english_name" => "The Wealthy Lamb", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SLM3', 
				'game_name' => '_json:{"1":"Legend of Warriors","2":"武聖傳"}', 
				"english_name" => "Legend of Warriors", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SC01', 
				'game_name' => '_json:{"1":"Lucky Slot","2":"幸忳老虎機"}', 
				"english_name" => "Lucky Slot", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'TGLW', 
				'game_name' => '_json:{"1":"Speedy Lucky Wheel","2":"極速幸忳輪"}', 
				"english_name" => "Speedy Lucky Wheel", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB01', 
				'game_name' => '_json:{"1":"Space Odyssey","2":"⣒空漫忲"}', 
				"english_name" => "Space Odyssey", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB02', 
				'game_name' => '_json:{"1":"Vintage Garden","2":"復古花園"}', 
				"english_name" => "Vintage Garden", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB03', 
				'game_name' => '_json:{"1":"Oden","2":"關㜙煮"}', 
				"english_name" => "Oden", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB04', 
				'game_name' => '_json:{"1":"Farm Café","2":"牧場咖啡"}', 
				"english_name" => "Farm Café", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB05', 
				'game_name' => '_json:{"1":"Sweets Home","2":"甜一甜屋"}', 
				"english_name" => "Sweets Home", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB06', 
				'game_name' => '_json:{"1":"Samurai","2":"日本武士"}', 
				"english_name" => "Samurai", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB07', 
				'game_name' => '_json:{"1":"Chinese Chess Slot","2":"象棋老虎機"}', 
				"english_name" => "Chinese Chess Slot", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB08', 
				'game_name' => '_json:{"1":"Mahjong Slot","2":"麻將老虎機"}', 
				"english_name" => "Mahjong Slot", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB09', 
				'game_name' => '_json:{"1":"Chess Slot","2":"西洋棋老虎機"}', 
				"english_name" => "Chess Slot", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB10', 
				'game_name' => '_json:{"1":"Happy Farm","2":"開心農場"}', 
				"english_name" => "Happy Farm", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB11', 
				'game_name' => '_json:{"1":"Summer Campsite","2":"夏日營地"}', 
				"english_name" => "Summer Campsite", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB12', 
				'game_name' => '_json:{"1":"Sea World Odyssey","2":"海底漫忲"}', 
				"english_name" => "Sea World Odyssey", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB13', 
				'game_name' => '_json:{"1":"Funny Clown","2":"鬼馬小丑"}', 
				"english_name" => "Funny Clown", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB14', 
				'game_name' => '_json:{"1":"Amazing Rides","2":"機動樂園"}', 
				"english_name" => "Amazing Rides", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB15', 
				'game_name' => '_json:{"1":"Spooky House","2":"驚嚇鬼屋"}', 
				"english_name" => "Spooky House", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB16', 
				'game_name' => '_json:{"1":"Crazy Circus","2":"瘋狂馬戲團"}', 
				"english_name" => "Crazy Circus", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB17', 
				'game_name' => '_json:{"1":"Ocean Theatre","2":"海洋劇場"}', 
				"english_name" => "Ocean Theatre", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB18', 
				'game_name' => '_json:{"1":"Wonderful Waterpark","2":"水ᶲ樂園"}', 
				"english_name" => "Wonderful Waterpark", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB19', 
				'game_name' => '_json:{"1":"Aerial Warfare","2":"空中戰爭"}', 
				"english_name" => "Aerial Warfare", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB20', 
				'game_name' => '_json:{"1":"Rock And Roll","2":"搖滾狂迷"}', 
				"english_name" => "Rock And Roll", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB21', 
				'game_name' => '_json:{"1":"Motor Gear","2":"越野機車"}', 
				"english_name" => "Motor Gear", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB22', 
				'game_name' => '_json:{"1":"Egypt Mystery","2":"埃及奧秘"}', 
				"english_name" => "Egypt Mystery", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB23', 
				'game_name' => '_json:{"1":"Happy Hour","2":"歡樂時光"}', 
				"english_name" => "Happy Hour", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB24', 
				'game_name' => '_json:{"1":"Jurassic Slot","2":"侏羅紀"}', 
				"english_name" => "Jurassic Slot", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB25', 
				'game_name' => '_json:{"1":"God of Land Fortune","2":"土地神"}', 
				"english_name" => "God of Land Fortune", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB26', 
				'game_name' => '_json:{"1":"God of Bag Monk Fortune","2":"布袋和尚"}', 
				"english_name" => "God of Bag Monk Fortune", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB27', 
				'game_name' => '_json:{"1":"God of Fortune","2":"正財神"}', 
				"english_name" => "God of Fortune", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB28', 
				'game_name' => '_json:{"1":"God of Wu Fortune","2":"武財神"}', 
				"english_name" => "God of Wu Fortune", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB29', 
				'game_name' => '_json:{"1":"God of Gamble Fortune","2":"偏財神"}', 
				"english_name" => "God of Gamble Fortune", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'AV01', 
				'game_name' => '_json:{"1":"Sexy Maid","2":"性感女僕"}', 
				"english_name" => "Sexy Maid", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'TGCW', 
				'game_name' => '_json:{"1":"Casino War","2":"岕場戰爭"}', 
				"english_name" => "Casino War", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG01', 
				'game_name' => '_json:{"1":"Dragons Pearl","2":"龍珠"}', 
				"english_name" => "Dragons Pearl", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG02', 
				'game_name' => '_json:{"1":"Lucky 8","2":"幸忳 8"}', 
				"english_name" => "Lucky 8", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG03', 
				'game_name' => '_json:{"1":"Bling Bling","2":"閃亮女郎"}', 
				"english_name" => "Bling Bling", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG04', 
				'game_name' => '_json:{"1":"Gold Fish","2":"金魚"}', 
				"english_name" => "Gold Fish", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG05', 
				'game_name' => '_json:{"1":"Chinese New Year","2":"中國新⸜"}', 
				"english_name" => "Chinese New Year", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG06', 
				'game_name' => '_json:{"1":"Pirates","2":"海盜王"}', 
				"english_name" => "Pirates", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG07', 
				'game_name' => '_json:{"1":"Fruitmania","2":"鮮果狂熱"}', 
				"english_name" => "Fruitmania", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG08', 
				'game_name' => '_json:{"1":"Red Panda","2":"小熊貓"}', 
				"english_name" => "Red Panda", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG09', 
				'game_name' => '_json:{"1":"High Roller","2":"大豪客"}', 
				"english_name" => "High Roller", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB30', 
				'game_name' => '_json:{"1":"Year of the Monkey","2":"靈猴獻瑞"}', 
				"english_name" => "Year of the Monkey", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'XG10', 
				'game_name' => '_json:{"1":"Dragon Boat Festival","2":"龍舟競渡"}', 
				"english_name" => "Dragon Boat Festival", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'PKBD', 
				'game_name' => '_json:{"1":"Deuces Wild","2":"百搭二王"}', 
				"english_name" => "Deuces Wild", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'PKBB', 
				'game_name' => '_json:{"1":"Bonus Deuces Wild","2":"紅利百搭"}', 
				"english_name" => "Bonus Deuces Wild", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB31', 
				'game_name' => '_json:{"1":"Sky Guardians","2":"天空守护者"}', 
				"english_name" => "Sky Guardians", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB32', 
				'game_name' => '_json:{"1":"Monkey King","2":"齊⣑大聖"}', 
				"english_name" => "Monkey King", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB33', 
				'game_name' => '_json:{"1":"Candy Quest","2":"糖果碰碰樂"}', 
				"english_name" => "Candy Quest", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB34', 
				'game_name' => '_json:{"1":"Ice Crush","2":"冰河世界"}', 
				"english_name" => "Ice Crush", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'FRU2', 
				'game_name' => '_json:{"1":"Fruit Slot 2","2":"水果拉霸 2"}', 
				"english_name" => "Fruit Slot 2", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'TG01', 
				'game_name' => '_json:{"1":"Black Jack (Slot Games)","2":"21 點 (電子忲 戲)"}', 
				"english_name" => "Black Jack (Slot Games)", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'TG02', 
				'game_name' => '_json:{"1":"Baccarat (Slot Games)","2":"百家樂 (電子忲戲)"}', 
				"english_name" => "Baccarat (Slot Games)", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'TG03', 
				'game_name' => '_json:{"1":"Roulette (Slot Games)","2":"輪盤 (電子忲戲)"}', 
				"english_name" => "Roulette (Slot Games)", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB35', 
				'game_name' => '_json:{"1":"Euro Football Champ","2":"歐洲列強爭霸"}', 
				"english_name" => "Euro Football Champ", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB36', 
				'game_name' => '_json:{"1":"Fish Hunter King","2":"捕魚王者"}', 
				"english_name" => "Fish Hunter King", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB37', 
				'game_name' => '_json:{"1":"Shanghai Bund","2":"ᶲ海百樂門"}', 
				"english_name" => "Shanghai Bund", 
			),
			array(
				'game_platform_id' => AG_API, 
				'game_code' => 'SB38', 
				'game_name' => '_json:{"1":"Rio Fever","2":"競技狂熱"}', 
				"english_name" => "Rio Fever", 
			),
		);

		$data = array();

		foreach ($game_descriptions as $game_list) {

			$game_code_exist = $this->db->select('COUNT(id) as count')
							 	->where('game_code', $game_list['game_code'])
							 	->where('game_platform_id', AG_API)
							 	->get('game_description')
					 		 	->row();

			if( $game_code_exist->count <= 0 ) continue;

			$this->db->where('game_code', $game_list['game_code']);
			$this->db->where('game_platform_id', AG_API);
			$this->db->update('game_description', $game_list);
			$cnt++;

		}

		return $cnt;
	}

}
