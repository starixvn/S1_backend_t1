<?php
trait game_description_gamesos {

	public function sync_game_description_gamesos(){

		$cnt=0;

		$game_descriptions = array(
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_mrmonkeyslots', 
				'game_name' => '_json:{"1":"SL Mr. Monkey","2":"猴子先生"}', 
				'english_name' => 'SL Mr. Monkey',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_seaofgoldslots', 
				'game_name' => '_json:{"1":"SL Sea of Gold","2":"黄金海"}', 
				'english_name' => 'SL Sea of Gold',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_magicofozslots', 
				'game_name' => '_json:{"1":"SL Magic of OZ","2":"奥兹魔法"}', 
				'english_name' => 'SL Magic of OZ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_doublebonusslots', 
				'game_name' => '_json:{"1":"SL Double Bonus Slots","2":"双重奖励老虎机"}', 
				'english_name' => 'SL Double Bonus Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_mayawheelofluckslots', 
				'game_name' => '_json:{"1":"SL Maya Wheel of Luck","2":"玛雅幸运轮"}', 
				'english_name' => 'SL Maya Wheel of Luck',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_eastwindbattleslots', 
				'game_name' => '_json:{"1":"SL East Wind Battle","2":"战东风"}', 
				'english_name' => 'SL East Wind Battle',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_buffaloslot', 
				'game_name' => '_json:{"1":"SL By The Rives Of Buffalo","2":"水牛河畔"}', 
				'english_name' => 'SL By The Rives Of Buffalo',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_aztecslotsnewlhe', 
				'game_name' => '_json:{"1":"SL New Aztec Slots","2":"阿兹特克老虎机"}', 
				'english_name' => 'SL New Aztec Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_wildchinaslots', 
				'game_name' => '_json:{"1":"SL China Megawild","2":"狂野大中华"}', 
				'english_name' => 'SL China Megawild',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_gangsterslots', 
				'game_name' => '_json:{"1":"SL New York Gangs","2":"纽约黑帮"}', 
				'english_name' => 'SL New York Gangs',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_polartaleslots', 
				'game_name' => '_json:{"1":"SL Polar Tale","2":"极地故事"}', 
				'english_name' => 'SL Polar Tale',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_firecrackerslots', 
				'game_name' => '_json:{"1":"SL Midnight Lucky Sky Slots","2":"午夜幸运天"}', 
				'english_name' => 'SL Midnight Lucky Sky Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_karaokeslots', 
				'game_name' => '_json:{"1":"SL Karaoke Star Slots","2":"卡拉OK之星"}', 
				'english_name' => 'SL Karaoke Star Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_footballcupslots', 
				'game_name' => '_json:{"1":"SL Football Cup","2":"足球杯赛"}', 
				'english_name' => 'SL Football Cup',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_goldinbarsslotslhe', 
				'game_name' => '_json:{"1":"SL Gold in Bars","2":"金条老虎机"}', 
				'english_name' => 'SL Gold in Bars',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_summerdreamslots', 
				'game_name' => '_json:{"1":"SL Summer Dream","2":"夏日梦想"}', 
				'english_name' => 'SL Summer Dream',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_partynightslots', 
				'game_name' => '_json:{"1":"SL Party Night","2":"午夜派对"}', 
				'english_name' => 'SL Party Night',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_mysticslots', 
				'game_name' => '_json:{"1":"SL Mystic Slots","2":"神秘老虎机"}', 
				'english_name' => 'SL Mystic Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_mavericksaloonslots', 
				'game_name' => '_json:{"1":"SL Maverick saloon","2":"独行酒吧"}', 
				'english_name' => 'SL Maverick saloon',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_pipezillasslots', 
				'game_name' => '_json:{"1":"SL Pipezillas Slots","2":"管道精灵"}', 
				'english_name' => 'SL Pipezillas Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_vampireslayersslots', 
				'game_name' => '_json:{"1":" SL Vampire Slayers","2":"吸血鬼杀手"}', 
				'english_name' => ' SL Vampire Slayers',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_henhouseslots', 
				'game_name' => '_json:{"1":" SL Run Chicken Run","2":"小鸡快跑老虎机"}', 
				'english_name' => ' SL Run Chicken Run',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_gemsandthecityslots', 
				'game_name' => '_json:{"1":"SL Gems and the City","2":"宝石与城展"}', 
				'english_name' => 'SL Gems and the City',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_nonstoppartyslots', 
				'game_name' => '_json:{"1":"SL Non-Stop Party","2":"不间断聚会"}', 
				'english_name' => 'SL Non-Stop Party',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_cubanatropicanaslots', 
				'game_name' => '_json:{"1":"SL Cubana-Tropicana","2":"热带古巴"}', 
				'english_name' => 'SL Cubana-Tropicana',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_archipelagoslots', 
				'game_name' => '_json:{"1":"SL Archipelago","2":"爱琴海群岛"}', 
				'english_name' => 'SL Archipelago',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_freakyfruitsslots', 
				'game_name' => '_json:{"1":"SL Freaky Fruits","2":"多彩水果"}', 
				'english_name' => 'SL Freaky Fruits',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_rockthemouseslots', 
				'game_name' => '_json:{"1":" SL Rock the Mouse","2":"摇滚老鼠"}', 
				'english_name' => ' SL Rock the Mouse',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_magicpotslots', 
				'game_name' => '_json:{"1":"SL Magic Pot","2":"魔罐"}', 
				'english_name' => 'SL Magic Pot',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_freakycowboysslots', 
				'game_name' => '_json:{"1":" SL Freaky Cowboys","2":"奇异牛仔"}', 
				'english_name' => ' SL Freaky Cowboys',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_freakybanditsslots', 
				'game_name' => '_json:{"1":" SL Freaky Bandits ","2":"奇异土匪"}', 
				'english_name' => ' SL Freaky Bandits ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_freakygymslots', 
				'game_name' => '_json:{"1":"SL Freaky Gym","2":"多彩体操馆"}', 
				'english_name' => 'SL Freaky Gym',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_totemquestslots', 
				'game_name' => '_json:{"1":"SL Totem Quest","2":"图腾探秘"}', 
				'english_name' => 'SL Totem Quest',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_houseofscareslots', 
				'game_name' => '_json:{"1":"SL House of Scare","2":"恐怖屋"}', 
				'english_name' => 'SL House of Scare',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_goldenindiaslots', 
				'game_name' => '_json:{"1":"SL Golden India Slots","2":"黄金印度"}', 
				'english_name' => 'SL Golden India Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_cleopatratreasureslots', 
				'game_name' => '_json:{"1":"SL Cleopatra Treasure","2":"艳后宝藏"}', 
				'english_name' => 'SL Cleopatra Treasure',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_freakywildwestslots', 
				'game_name' => '_json:{"1":"SL Freaky Wild West","2":"多彩狂野西部"}', 
				'english_name' => 'SL Freaky Wild West',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_generoussantaslots', 
				'game_name' => '_json:{"1":"SL Generous Santa","2":"慷慨的圣诞老人"}', 
				'english_name' => 'SL Generous Santa',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_battlegroundslots', 
				'game_name' => '_json:{"1":"SL Battleground Spins","2":"生死战地老虎机"}', 
				'english_name' => 'SL Battleground Spins',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_spintheworldslots', 
				'game_name' => '_json:{"1":"SL Spin the World","2":"旋转世界"}', 
				'english_name' => 'SL Spin the World',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jourdelamourslots', 
				'game_name' => '_json:{"1":"SL Jour de l’Amour","2":"爱的日子"}', 
				'english_name' => 'SL Jour de l’Amour',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_pirateslots', 
				'game_name' => '_json:{"1":"SL Pirate Slots","2":"海盗老虎机"}', 
				'english_name' => 'SL Pirate Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_atlantisdiveslots', 
				'game_name' => '_json:{"1":"SL Atlantis Dive","2":"亚特兰蒂斯潜水"}', 
				'english_name' => 'SL Atlantis Dive',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_soccerslots', 
				'game_name' => '_json:{"1":"SL World-Cup Soccer Spins","2":"世界杯足球老虎机"}', 
				'english_name' => 'SL World-Cup Soccer Spins',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_easterfeastslots', 
				'game_name' => '_json:{"1":"SL Easter Feast","2":"复活节盛宴"}', 
				'english_name' => 'SL Easter Feast',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_farmslots', 
				'game_name' => '_json:{"1":"SL Farm Slot","2":"农场老虎机"}', 
				'english_name' => 'SL Farm Slot',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_freakycarsslots', 
				'game_name' => '_json:{"1":"SL Freaky Cars","2":"疯狂赛车"}', 
				'english_name' => 'SL Freaky Cars',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_fruitsaladjackpotslots', 
				'game_name' => '_json:{"1":"SL Fruit Salad Jackpot","2":"水果色拉老虎机"}', 
				'english_name' => 'SL Fruit Salad Jackpot',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_hot7sslotslhe', 
				'game_name' => '_json:{"1":"SL Hot 7’s","2":"热 7 老虎机"}', 
				'english_name' => 'SL Hot 7’s',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_mexicoslots', 
				'game_name' => '_json:{"1":"SL Mexican Slots","2":"墨西哥老虎机"}', 
				'english_name' => 'SL Mexican Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_olympicslots', 
				'game_name' => '_json:{"1":"SL Olympic Slots","2":"奥林匹克老虎机"}', 
				'english_name' => 'SL Olympic Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_spacerobbersslots', 
				'game_name' => '_json:{"1":"SL Space Robbers","2":"星际强盗"}', 
				'english_name' => 'SL Space Robbers',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_choochooslots', 
				'game_name' => '_json:{"1":"SL Choo-Choo Slots","2":"呜-呜老虎机"}', 
				'english_name' => 'SL Choo-Choo Slots',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_fortunecashout', 
				'game_name' => '_json:{"1":"AG Cash Out Fortune","2":"财富大冒险"}', 
				'english_name' => 'AG Cash Out Fortune',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_basebullscratch', 
				'game_name' => '_json:{"1":"AG Basebull Scratch ","2":"棒球刮刮乐"}', 
				'english_name' => 'AG Basebull Scratch ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_bingoscratch', 
				'game_name' => '_json:{"1":"AG Bingo Scratch","2":"宾果刮刮乐"}', 
				'english_name' => 'AG Bingo Scratch',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_cubanatropicanascratch', 
				'game_name' => '_json:{"1":"AG Cubana-Tropicana Scratch","2":"热带古巴刮刮乐游戏"}', 
				'english_name' => 'AG Cubana-Tropicana Scratch',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_easterfeastscratch', 
				'game_name' => '_json:{"1":"AG Easter Feast Scratch","2":"复活节刮刮乐"}', 
				'english_name' => 'AG Easter Feast Scratch',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_footballcupscratch', 
				'game_name' => '_json:{"1":"AG Football Cup Scratch","2":"足球杯赛刮刮乐"}', 
				'english_name' => 'AG Football Cup Scratch',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_formulascratch', 
				'game_name' => '_json:{"1":" AG Formula Scratch ","2":"方程式刮刮乐"}', 
				'english_name' => ' AG Formula Scratch ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_freakythimblesscratch', 
				'game_name' => '_json:{"1":"AG Freaky Thimbles ","2":"多彩猜豆"}', 
				'english_name' => 'AG Freaky Thimbles ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_gangstersscratch', 
				'game_name' => '_json:{"1":"AG Gangsters’ Loot ","2":"黑帮财宝"}', 
				'english_name' => 'AG Gangsters’ Loot ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_goldenscratch', 
				'game_name' => '_json:{"1":"AG Gold 999.9 ","2":"黄金 999.9"}', 
				'english_name' => 'AG Gold 999.9 ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_houseofscarescratch', 
				'game_name' => '_json:{"1":"AG House of Scare Scratch","2":"鬼屋刮刮乐"}', 
				'english_name' => 'AG House of Scare Scratch',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jourdelamourscratch', 
				'game_name' => '_json:{"1":"AG Jour de l’Amour Scratch","2":"爱的日子刮刮乐游戏"}', 
				'english_name' => 'AG Jour de l’Amour Scratch',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_keno', 
				'game_name' => '_json:{"1":"AG Keno","2":"基诺"}', 
				'english_name' => 'AG Keno',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_magicpotscratch', 
				'game_name' => '_json:{"1":"AG Magic Pot Scratch","2":"魔罐刮刮乐"}', 
				'english_name' => 'AG Magic Pot Scratch',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_monstersscratch', 
				'game_name' => '_json:{"1":" AG Monsters Scratch ","2":"怪兽刮刮乐"}', 
				'english_name' => ' AG Monsters Scratch ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_moonapolisscratch', 
				'game_name' => '_json:{"1":"AG Moonapolis ","2":"月球刮刮乐"}', 
				'english_name' => 'AG Moonapolis ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_nonstoppartyscratch', 
				'game_name' => '_json:{"1":"AG Non-Stop Party Scratch","2":"不间断聚会刮刮乐"}', 
				'english_name' => 'AG Non-Stop Party Scratch',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_teddyscratch', 
				'game_name' => '_json:{"1":" AG Teddy Scratch ","2":"泰迪刮刮乐"}', 
				'english_name' => ' AG Teddy Scratch ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_soccerscratch', 
				'game_name' => '_json:{"1":" AG Soccer Scratch ","2":"足球刮刮乐"}', 
				'english_name' => ' AG Soccer Scratch ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_sparkleladiesscratch', 
				'game_name' => '_json:{"1":"AG Sparkle Ladies ","2":"闪耀王后"}', 
				'english_name' => 'AG Sparkle Ladies ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_blackandwhite', 
				'game_name' => '_json:{"1":"XG Black and White","2":"黑与白"}', 
				'english_name' => 'XG Black and White',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_potshot', 
				'game_name' => '_json:{"1":"XG Hockey Potshot","2":"曲棍球随手射击"}', 
				'english_name' => 'XG Hockey Potshot',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_lotto', 
				'game_name' => '_json:{"1":"XG Instant Lotto","2":"即开彩票"}', 
				'english_name' => 'XG Instant Lotto',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jackpot7', 
				'game_name' => '_json:{"1":"XG Jackpot7Jackpot7 ","2":"老虎机"}', 
				'english_name' => 'XG Jackpot7Jackpot7 ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_luckywheel', 
				'game_name' => '_json:{"1":"XG Lucky Wheel","2":"幸运转盘"}', 
				'english_name' => 'XG Lucky Wheel',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_soccershot', 
				'game_name' => '_json:{"1":"XG Soccer Shot","2":"足球点射"}', 
				'english_name' => 'XG Soccer Shot',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_baccarat', 
				'game_name' => '_json:{"1":"CG Baccarat","2":"百家乐"}', 
				'english_name' => 'CG Baccarat',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_bj', 
				'game_name' => '_json:{"1":"CG Blackjack","2":"21 点"}', 
				'english_name' => 'CG Blackjack',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_blackjack', 
				'game_name' => '_json:{"1":"CG Blackjack Classic","2":"21 点经典"}', 
				'english_name' => 'CG Blackjack Classic',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_bj_progressive', 
				'game_name' => '_json:{"1":"CG Blackjack Progressive","2":"累积二十一点"}', 
				'english_name' => 'CG Blackjack Progressive',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_blackjack_progressive', 
				'game_name' => '_json:{"1":"CG Blackjack Progressive Classic","2":"累积二十一点经典"}', 
				'english_name' => 'CG Blackjack Progressive Classic',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_bj_s', 
				'game_name' => '_json:{"1":"CG Blackjack Surrender","2":"投降 21 点"}', 
				'english_name' => 'CG Blackjack Surrender',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_blackjack_s', 
				'game_name' => '_json:{"1":"CG Blackjack Surrender Classic","2":"投降 21 点经典"}', 
				'english_name' => 'CG Blackjack Surrender Classic',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_bj_switch', 
				'game_name' => '_json:{"1":"CG Blackjack Switch","2":"换牌21点"}', 
				'english_name' => 'CG Blackjack Switch',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_blackjack_switch', 
				'game_name' => '_json:{"1":"CG Blackjack Switch Classic","2":"换牌21点经典"}', 
				'english_name' => 'CG Blackjack Switch Classic',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_hold_em', 
				'game_name' => '_json:{"1":"CG Casino Hold’em","2":"赌场德州扑克"}', 
				'english_name' => 'CG Casino Hold’em',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_bj_faceup', 
				'game_name' => '_json:{"1":"CG Face Up 21 Blackjack","2":"全亮二十一点！"}', 
				'english_name' => 'CG Face Up 21 Blackjack',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_blackjack_faceup', 
				'game_name' => '_json:{"1":"CG Face Up 21 Blackjack Classic","2":"全亮二十一点！经典"}', 
				'english_name' => 'CG Face Up 21 Blackjack Classic',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_poker_oasis', 
				'game_name' => '_json:{"1":"CG Oasis Poker","2":"绿洲扑克（Oasis Poker）"}', 
				'english_name' => 'CG Oasis Poker',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_paigowpoker', 
				'game_name' => '_json:{"1":"CG Pai Gow Poker","2":"牌九扑克"}', 
				'english_name' => 'CG Pai Gow Poker',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_bj_perfectpairs', 
				'game_name' => '_json:{"1":"CG Perfect Pairs Blackjack","2":"完美对子二十一点"}', 
				'english_name' => 'CG Perfect Pairs Blackjack',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_blackjack_perfectpairs', 
				'game_name' => '_json:{"1":"CG Perfect Pairs Blackjack Classic","2":"完美对子二十一点经典"}', 
				'english_name' => 'CG Perfect Pairs Blackjack Classic',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_bj_pontoon', 
				'game_name' => '_json:{"1":"CG Pontoon","2":"英式二十一点"}', 
				'english_name' => 'CG Pontoon',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_blackjack_pontoon', 
				'game_name' => '_json:{"1":"CG Pontoon Classic","2":"英式二十一点经典"}', 
				'english_name' => 'CG Pontoon Classic',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_poker_russian', 
				'game_name' => '_json:{"1":"CG Russian Poker","2":"俄罗斯扑克（Russian Poker）"}', 
				'english_name' => 'CG Russian Poker',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_bj_spanish', 
				'game_name' => '_json:{"1":"CG Spanish Blackjack","2":"西班牙二十一点"}', 
				'english_name' => 'CG Spanish Blackjack',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_blackjack_spanish', 
				'game_name' => '_json:{"1":"CG Spanish Blackjack Classic","2":"西班牙二十一点经典"}', 
				'english_name' => 'CG Spanish Blackjack Classic',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_mhbaccarat', 
				'game_name' => '_json:{"1":"CG VIP Multi Hand Baccarat","2":"VIP百家乐"}', 
				'english_name' => 'CG VIP Multi Hand Baccarat',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_americanroulette', 
				'game_name' => '_json:{"1":"TG American Roulette","2":"美式轮盘赌"}', 
				'english_name' => 'TG American Roulette',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_craps', 
				'game_name' => '_json:{"1":"TG Craps","2":"掷骰子"}', 
				'english_name' => 'TG Craps',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_euroulette', 
				'game_name' => '_json:{"1":"TG European Roulette","2":"欧式轮盘赌"}', 
				'english_name' => 'TG European Roulette',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_euroulettepro', 
				'game_name' => '_json:{"1":"TG European Roulette PRO","2":"轮盘赌专家"}', 
				'english_name' => 'TG European Roulette PRO',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_sic_bo', 
				'game_name' => '_json:{"1":"TG Sic Bo","2":"骰宝"}', 
				'english_name' => 'TG Sic Bo',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_coliseum', 
				'game_name' => '_json:{"1":"VP Coliseum Poker","2":"竞技场扑克"}', 
				'english_name' => 'VP Coliseum Poker',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_coliseum10l', 
				'game_name' => '_json:{"1":"VP Coliseum Poker 10 Lines ","2":"10 行竞技场扑克"}', 
				'english_name' => 'VP Coliseum Poker 10 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_coliseum25l', 
				'game_name' => '_json:{"1":"VP Coliseum Poker 25 Lines ","2":"25 行竞技场扑克"}', 
				'english_name' => 'VP Coliseum Poker 25 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_coliseum4l', 
				'game_name' => '_json:{"1":"VP Coliseum Poker 4 Lines ","2":"4 线竞技场扑克"}', 
				'english_name' => 'VP Coliseum Poker 4 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_coliseum50l', 
				'game_name' => '_json:{"1":"VP Coliseum Poker 50 Lines ","2":"50 行竞技场扑克"}', 
				'english_name' => 'VP Coliseum Poker 50 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_deuceswild', 
				'game_name' => '_json:{"1":"VP Deuces Wild","2":"万能两点"}', 
				'english_name' => 'VP Deuces Wild',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_deuceswild10l', 
				'game_name' => '_json:{"1":"VP Deuces Wild 10 Lines ","2":"10 行万能两点"}', 
				'english_name' => 'VP Deuces Wild 10 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_deuceswild25l', 
				'game_name' => '_json:{"1":"VP Deuces Wild 25 Lines ","2":"25 行万能两点"}', 
				'english_name' => 'VP Deuces Wild 25 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_deuceswild4l', 
				'game_name' => '_json:{"1":"VP Deuces Wild 4 Lines","2":"4 线万能两点"}', 
				'english_name' => 'VP Deuces Wild 4 Lines',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_deuceswild50l', 
				'game_name' => '_json:{"1":"VP Deuces Wild 50 Lines ","2":"50 行万能两点"}', 
				'english_name' => 'VP Deuces Wild 50 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_faceace', 
				'game_name' => '_json:{"1":"VP Face the Ace","2":"花牌和 A"}', 
				'english_name' => 'VP Face the Ace',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_faceace10l', 
				'game_name' => '_json:{"1":"VP Face the Ace 10 Lines ","2":"10 行花牌和 A"}', 
				'english_name' => 'VP Face the Ace 10 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_faceace25l', 
				'game_name' => '_json:{"1":"VP Face the Ace 25 Lines ","2":"25 行花牌和 A"}', 
				'english_name' => 'VP Face the Ace 25 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_faceace4l', 
				'game_name' => '_json:{"1":"VP Face the Ace 4 Lines ","2":"4 行花牌和 A"}', 
				'english_name' => 'VP Face the Ace 4 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_faceace50l', 
				'game_name' => '_json:{"1":"VP Face the Ace 50 Lines ","2":"50 行花牌和 A"}', 
				'english_name' => 'VP Face the Ace 50 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_fiveaces', 
				'game_name' => '_json:{"1":"VP Five Aces","2":"五张 A"}', 
				'english_name' => 'VP Five Aces',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jacksorbetter', 
				'game_name' => '_json:{"1":"VP Jacks or Better","2":"好过杰克"}', 
				'english_name' => 'VP Jacks or Better',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jacksorbetter10l', 
				'game_name' => '_json:{"1":"VP Jacks or Better 10 Lines ","2":"10 行好过杰克"}', 
				'english_name' => 'VP Jacks or Better 10 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jacksorbetter25l', 
				'game_name' => '_json:{"1":"VP Jacks or Better 25 Lines ","2":"25 行好过杰克"}', 
				'english_name' => 'VP Jacks or Better 25 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jacksorbetter4l', 
				'game_name' => '_json:{"1":"VP Jacks or Better 4 Lines ","2":"4 线好过杰克"}', 
				'english_name' => 'VP Jacks or Better 4 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jacksorbetter50l', 
				'game_name' => '_json:{"1":"VP Jacks or Better 50 Lines ","2":"50 行好过杰克"}', 
				'english_name' => 'VP Jacks or Better 50 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jokerwild', 
				'game_name' => '_json:{"1":"VP Joker Wild","2":"万能小丑"}', 
				'english_name' => 'VP Joker Wild',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jokerwild10l', 
				'game_name' => '_json:{"1":"VP Joker Wild 10 Lines ","2":"10 线万能小丑"}', 
				'english_name' => 'VP Joker Wild 10 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jokerwild25l', 
				'game_name' => '_json:{"1":"VP Joker Wild 25 Lines ","2":"25 线万能小丑"}', 
				'english_name' => 'VP Joker Wild 25 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jokerwild4l', 
				'game_name' => '_json:{"1":"VP Joker Wild 4 Lines ","2":"4 线万能小丑"}', 
				'english_name' => 'VP Joker Wild 4 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_jokerwild50l', 
				'game_name' => '_json:{"1":"VP Joker Wild 50 Lines ","2":"50 线万能小丑"}', 
				'english_name' => 'VP Joker Wild 50 Lines ',
			), 
			array(
				'game_platform_id' => GAMESOS_API, 
				'game_code' => 'xc_shockwave', 
				'game_name' => '_json:{"1":"VP Shockwave Poker","2":"冲击波扑克"}', 
				'english_name' => 'VP Shockwave Poker',
			), 
		);

		$data = array();

		foreach ($game_descriptions as $game_list) {

			$game_code_exist = $this->db->select('COUNT(id) as count')
							 	->where('game_code', $game_list['game_code'])
							 	->where('game_platform_id', GAMESOS_API)
							 	->get('game_description')
					 		 	->row();

			if( $game_code_exist->count <= 0 ) continue;

			$this->db->where('game_code', $game_list['game_code']);
			$this->db->where('game_platform_id', GAMESOS_API);
			$this->db->update('game_description', $game_list);
			$cnt++;

		}

		return $cnt;
	}

}
