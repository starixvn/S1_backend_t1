<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_inteplay_to_game_type_20160305 extends CI_Migration {

	const FLAG_TRUE = 1;
	const FLAG_FALSE = 0;

	public function up() {

		// $this->db->trans_start();

		// $data = array(
		// 	array(
		// 		'game_type' => 'Video Slots',
		// 		'game_type_lang' => 'inteplay_video_slots',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_name' => 'inteplay.NightClub',
		// 				'game_code' => 'NightClub',
		// 				'english_name' => 'Night Club',
		// 				'external_game_id' => 'NightClub',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.MoneyOnTree',
		// 				'game_code' => 'MoneyOnTree',
		// 				'english_name' => 'Money on Tree',
		// 				'external_game_id' => 'MoneyOnTree',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.JCDB',
		// 				'game_code' => 'JCDB',
		// 				'english_name' => 'Gem Hunter',
		// 				'external_game_id' => 'JCDB',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_CircusWagon',
		// 				'game_code' => 'H5_CircusWagon',
		// 				'english_name' => 'Circus Wagon',
		// 				'external_game_id' => 'H5_CircusWagon',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_LuxNight',
		// 				'game_code' => 'H5_LuxNight',
		// 				'english_name' => 'Lux Night',
		// 				'external_game_id' => 'H5_LuxNight',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_HalloweenNight',
		// 				'game_code' => 'H5_HalloweenNight',
		// 				'english_name' => 'Halloween Night',
		// 				'external_game_id' => 'H5_HalloweenNight',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_SexyBeach',
		// 				'game_code' => 'H5_SexyBeach',
		// 				'english_name' => 'Sexy Beach',
		// 				'external_game_id' => 'H5_SexyBeach',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_HotSweety',
		// 				'game_code' => 'H5_HotSweety',
		// 				'english_name' => 'Hot Sweety',
		// 				'external_game_id' => 'H5_HotSweety',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_Underground',
		// 				'game_code' => 'H5_Underground',
		// 				'english_name' => 'Underground',
		// 				'external_game_id' => 'H5_Underground',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_The3rdCentury',
		// 				'game_code' => 'H5_The3rdCentury',
		// 				'english_name' => 'The 3rd Century',
		// 				'external_game_id' => 'H5_The3rdCentury',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_NorthPole',
		// 				'game_code' => 'H5_NorthPole',
		// 				'english_name' => 'North Pole',
		// 				'external_game_id' => 'H5_NorthPole',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_MrBig',
		// 				'game_code' => 'H5_MrBig',
		// 				'english_name' => 'Mr Big',
		// 				'external_game_id' => 'H5_MrBig',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_Killer23',
		// 				'game_code' => 'H5_Killer23',
		// 				'english_name' => 'Killer 23',
		// 				'external_game_id' => 'H5_Killer23',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_HottestBachelor',
		// 				'game_code' => 'H5_HottestBachelor',
		// 				'english_name' => 'Hottest Bachelor',
		// 				'external_game_id' => 'H5_HottestBachelor',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_GhostHouse',
		// 				'game_code' => 'H5_GhostHouse',
		// 				'english_name' => 'Ghost House',
		// 				'external_game_id' => 'H5_GhostHouse',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_AngryPigs',
		// 				'game_code' => 'H5_AngryPigs',
		// 				'english_name' => 'Angry Pigs',
		// 				'external_game_id' => 'H5_AngryPigs',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_ShootOut',
		// 				'game_code' => 'H5_ShootOut',
		// 				'english_name' => 'Shoot Out',
		// 				'external_game_id' => 'H5_ShootOut',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_RoyalAscot',
		// 				'game_code' => 'H5_RoyalAscot',
		// 				'english_name' => 'Royal Ascot',
		// 				'external_game_id' => 'H5_RoyalAscot',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_HappyFarm',
		// 				'game_code' => 'H5_HappyFarm',
		// 				'english_name' => 'Happy Farm',
		// 				'external_game_id' => 'H5_HappyFarm',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_GoldMiner',
		// 				'game_code' => 'H5_GoldMiner',
		// 				'english_name' => 'Gold Miner',
		// 				'external_game_id' => 'H5_GoldMiner',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_LastDinosaurs',
		// 				'game_code' => 'H5_LastDinosaurs',
		// 				'english_name' => 'Last Dinosaurs',
		// 				'external_game_id' => 'H5_LastDinosaurs',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_RobotWorld',
		// 				'game_code' => 'H5_RobotWorld',
		// 				'english_name' => 'Robot World',
		// 				'external_game_id' => 'H5_RobotWorld',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_PetsWar',
		// 				'game_code' => 'H5_PetsWar',
		// 				'english_name' => 'Pets War',
		// 				'external_game_id' => 'H5_PetsWar',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_PenguinAdventure',
		// 				'game_code' => 'H5_PenguinAdventure',
		// 				'english_name' => 'Penguin Adventure',
		// 				'external_game_id' => 'H5_PenguinAdventure',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_MidAutumnFestival',
		// 				'game_code' => 'H5_MidAutumnFestival',
		// 				'english_name' => 'Mid-Autumn Festival',
		// 				'external_game_id' => 'H5_MidAutumnFestival',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_LostInAfrica',
		// 				'game_code' => 'H5_LostInAfrica',
		// 				'english_name' => 'Lost in Africa',
		// 				'external_game_id' => 'H5_LostInAfrica',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_LionDance',
		// 				'game_code' => 'H5_LionDance',
		// 				'english_name' => 'Lion Dance',
		// 				'external_game_id' => 'H5_LionDance',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_HurricaneSpeed',
		// 				'game_code' => 'H5_HurricaneSpeed',
		// 				'english_name' => 'Hurricane Speed',
		// 				'external_game_id' => 'H5_HurricaneSpeed',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_AfterDinner',
		// 				'game_code' => 'H5_AfterDinner',
		// 				'english_name' => 'After Dinner',
		// 				'external_game_id' => 'H5_AfterDinner',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_BadmintonChampion',
		// 				'game_code' => 'H5_BadmintonChampion',
		// 				'english_name' => 'Badminton Champion',
		// 				'external_game_id' => 'H5_BadmintonChampion',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_Anubis',
		// 				'game_code' => 'H5_Anubis',
		// 				'english_name' => 'Anubis',
		// 				'external_game_id' => 'H5_Anubis',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_CabooseMan',
		// 				'game_code' => 'H5_CabooseMan',
		// 				'english_name' => 'Caboose Man',
		// 				'external_game_id' => 'H5_CabooseMan',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_AgeOfDiscovery',
		// 				'game_code' => 'H5_AgeOfDiscovery',
		// 				'english_name' => 'Age of Discovery',
		// 				'external_game_id' => 'H5_AgeOfDiscovery',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.HalloweenNight',
		// 				'game_code' => 'HalloweenNight',
		// 				'english_name' => 'Halloween Night',
		// 				'external_game_id' => 'HalloweenNight',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.SexyBeach',
		// 				'game_code' => 'SexyBeach',
		// 				'english_name' => 'Sexy Beach',
		// 				'external_game_id' => 'SexyBeach',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.LuxNight',
		// 				'game_code' => 'LuxNight',
		// 				'english_name' => 'Lux Night',
		// 				'external_game_id' => 'LuxNight',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.NorthPole',
		// 				'game_code' => 'NorthPole',
		// 				'english_name' => 'North Pole',
		// 				'external_game_id' => 'NorthPole',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.Underground',
		// 				'game_code' => 'Underground',
		// 				'english_name' => 'Underground',
		// 				'external_game_id' => 'Underground',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.The3rdCentury',
		// 				'game_code' => 'The3rdCentury',
		// 				'english_name' => 'The 3rd Century',
		// 				'external_game_id' => 'The3rdCentury',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.MrBig',
		// 				'game_code' => 'MrBig',
		// 				'english_name' => 'Mr Big',
		// 				'external_game_id' => 'MrBig',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.Killer23',
		// 				'game_code' => 'Killer23',
		// 				'english_name' => 'Killer 23',
		// 				'external_game_id' => 'Killer23',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.HottestBachelor',
		// 				'game_code' => 'HottestBachelor',
		// 				'english_name' => 'Hottest Bachelor',
		// 				'external_game_id' => 'HottestBachelor',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.HotSweety',
		// 				'game_code' => 'HotSweety',
		// 				'english_name' => 'Hot Sweety',
		// 				'external_game_id' => 'HotSweety',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.GhostHouse',
		// 				'game_code' => 'GhostHouse',
		// 				'english_name' => 'Ghost House',
		// 				'external_game_id' => 'GhostHouse',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.AfterDinner',
		// 				'game_code' => 'AfterDinner',
		// 				'english_name' => 'After Dinner',
		// 				'external_game_id' => 'AfterDinner',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.AngryPigs',
		// 				'game_code' => 'AngryPigs',
		// 				'english_name' => 'Angry Pigs',
		// 				'external_game_id' => 'AngryPigs',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.BadmintonChampion',
		// 				'game_code' => 'BadmintonChampion',
		// 				'english_name' => 'Badminton Champion',
		// 				'external_game_id' => 'BadmintonChampion',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.ShootOut',
		// 				'game_code' => 'ShootOut',
		// 				'english_name' => 'Shoot Out',
		// 				'external_game_id' => 'ShootOut',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.RoyalAscot',
		// 				'game_code' => 'RoyalAscot',
		// 				'english_name' => 'Royal Ascot',
		// 				'external_game_id' => 'RoyalAscot',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.GoldMiner',
		// 				'game_code' => 'GoldMiner',
		// 				'english_name' => 'Gold Miner',
		// 				'external_game_id' => 'GoldMiner',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.HappyFarm',
		// 				'game_code' => 'HappyFarm',
		// 				'english_name' => 'Happy Farm',
		// 				'external_game_id' => 'HappyFarm',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.LastDinosaurs',
		// 				'game_code' => 'LastDinosaurs',
		// 				'english_name' => 'Last Dinosaurs',
		// 				'external_game_id' => 'LastDinosaurs',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.CabooseMan',
		// 				'game_code' => 'CabooseMan',
		// 				'english_name' => 'Caboose Man',
		// 				'external_game_id' => 'CabooseMan',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.CircusWagon',
		// 				'game_code' => 'CircusWagon',
		// 				'english_name' => 'Circus Wagon',
		// 				'external_game_id' => 'CircusWagon',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.PetsWar',
		// 				'game_code' => 'PetsWar',
		// 				'english_name' => 'Pets War',
		// 				'external_game_id' => 'PetsWar',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.MidAutumnFestival',
		// 				'game_code' => 'MidAutumnFestival',
		// 				'english_name' => 'Mid-Autumn Festival',
		// 				'external_game_id' => 'MidAutumnFestival',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.RobotWorld',
		// 				'game_code' => 'RobotWorld',
		// 				'english_name' => 'Robot World',
		// 				'external_game_id' => 'RobotWorld',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.Anubis',
		// 				'game_code' => 'Anubis',
		// 				'english_name' => 'Anubis',
		// 				'external_game_id' => 'Anubis',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.PenguinAdventure',
		// 				'game_code' => 'PenguinAdventure',
		// 				'english_name' => 'Penguin Adventure',
		// 				'external_game_id' => 'PenguinAdventure',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.HurricaneSpeed',
		// 				'game_code' => 'HurricaneSpeed',
		// 				'english_name' => 'Hurricane Speed',
		// 				'external_game_id' => 'HurricaneSpeed',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.LionDance',
		// 				'game_code' => 'LionDance',
		// 				'english_name' => 'Lion Dance',
		// 				'external_game_id' => 'LionDance',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.LostInAfrica',
		// 				'game_code' => 'LostInAfrica',
		// 				'english_name' => 'Lost in Africa',
		// 				'external_game_id' => 'LostInAfrica',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.AgeOfDiscovery',
		// 				'game_code' => 'AgeOfDiscovery',
		// 				'english_name' => 'Age of Discovery',
		// 				'external_game_id' => 'AgeOfDiscovery',
		// 			),
		// 		),
		// 	),
		// 	array(
		// 		'game_type' => 'Classic Slots',
		// 		'game_type_lang' => 'inteplay_classic_slots',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_name' => 'inteplay.H5_LoverMachine',
		// 				'game_code' => 'H5_LoverMachine',
		// 				'english_name' => 'Lover Machine',
		// 				'external_game_id' => 'H5_LoverMachine',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_MerryXmas',
		// 				'game_code' => 'H5_MerryXmas',
		// 				'english_name' => 'Merry Xmas',
		// 				'external_game_id' => 'H5_MerryXmas',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_MetalWar',
		// 				'game_code' => 'H5_MetalWar',
		// 				'english_name' => 'Metal War',
		// 				'external_game_id' => 'H5_MetalWar',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_LuckyCheery',
		// 				'game_code' => 'H5_LuckyCheery',
		// 				'english_name' => 'Lucky Cheery',
		// 				'external_game_id' => 'H5_LuckyCheery',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_LovelySmile',
		// 				'game_code' => 'H5_LovelySmile',
		// 				'english_name' => 'Lovely Smile',
		// 				'external_game_id' => 'H5_LovelySmile',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_HappyChicken',
		// 				'game_code' => 'H5_HappyChicken',
		// 				'english_name' => 'Happy Chicken',
		// 				'external_game_id' => 'H5_HappyChicken',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_SatansTreasure',
		// 				'game_code' => 'H5_SatansTreasure',
		// 				'english_name' => 'Satan’s Treasure',
		// 				'external_game_id' => 'H5_SatansTreasure',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_Cowboy',
		// 				'game_code' => 'H5_Cowboy',
		// 				'english_name' => 'Cowboy',
		// 				'external_game_id' => 'H5_Cowboy',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_AnimalsWar',
		// 				'game_code' => 'H5_AnimalsWar',
		// 				'english_name' => 'Animals’ War',
		// 				'external_game_id' => 'H5_AnimalsWar',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_SuperFood',
		// 				'game_code' => 'H5_SuperFood',
		// 				'english_name' => 'Super Food',
		// 				'external_game_id' => 'H5_SuperFood',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_SummerBeach',
		// 				'game_code' => 'H5_SummerBeach',
		// 				'english_name' => 'Summer Beach',
		// 				'external_game_id' => 'H5_SummerBeach',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_MayanCode',
		// 				'game_code' => 'H5_MayanCode',
		// 				'english_name' => 'Mayan Code',
		// 				'external_game_id' => 'H5_MayanCode',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_TheAliens',
		// 				'game_code' => 'H5_TheAliens',
		// 				'english_name' => 'The Aliens',
		// 				'external_game_id' => 'H5_TheAliens',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_Mahjong13',
		// 				'game_code' => 'H5_Mahjong13',
		// 				'english_name' => 'Mahjong 13',
		// 				'external_game_id' => 'H5_Mahjong13',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_Gangs',
		// 				'game_code' => 'H5_Gangs',
		// 				'english_name' => 'Gangs',
		// 				'external_game_id' => 'H5_Gangs',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_GhostCastle',
		// 				'game_code' => 'H5_GhostCastle',
		// 				'english_name' => 'Ghost Castle',
		// 				'external_game_id' => 'H5_GhostCastle',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_KingCard',
		// 				'game_code' => 'H5_KingCard',
		// 				'english_name' => 'King Card',
		// 				'external_game_id' => 'H5_KingCard',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_HappyJoker',
		// 				'game_code' => 'H5_HappyJoker',
		// 				'english_name' => 'Happy Joker',
		// 				'external_game_id' => 'H5_HappyJoker',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_DragonBall',
		// 				'game_code' => 'H5_DragonBall',
		// 				'english_name' => 'Dragon Ball',
		// 				'external_game_id' => 'H5_DragonBall',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_OrangePark',
		// 				'game_code' => 'H5_OrangePark',
		// 				'english_name' => 'Orange Park',
		// 				'external_game_id' => 'H5_OrangePark',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.LoverMachine',
		// 				'game_code' => 'LoverMachine',
		// 				'english_name' => 'Lover Machine',
		// 				'external_game_id' => 'LoverMachine',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.MerryXmas',
		// 				'game_code' => 'MerryXmas',
		// 				'english_name' => 'Merry Xmas',
		// 				'external_game_id' => 'MerryXmas',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.MetalWar',
		// 				'game_code' => 'MetalWar',
		// 				'english_name' => 'Metal War',
		// 				'external_game_id' => 'MetalWar',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.LuckyCheery',
		// 				'game_code' => 'LuckyCheery',
		// 				'english_name' => 'Lucky Cheery',
		// 				'external_game_id' => 'LuckyCheery',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.LovelySmile',
		// 				'game_code' => 'LovelySmile',
		// 				'english_name' => 'Lovely Smile',
		// 				'external_game_id' => 'LovelySmile',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.HappyChicken',
		// 				'game_code' => 'HappyChicken',
		// 				'english_name' => 'Happy Chicken',
		// 				'external_game_id' => 'HappyChicken',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.SatansTreasure',
		// 				'game_code' => 'SatansTreasure',
		// 				'english_name' => 'Satan’s Treasure',
		// 				'external_game_id' => 'SatansTreasure',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.Cowboy',
		// 				'game_code' => 'Cowboy',
		// 				'english_name' => 'Cowboy',
		// 				'external_game_id' => 'Cowboy',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.AnimalsWar',
		// 				'game_code' => 'AnimalsWar',
		// 				'english_name' => 'Animals’ War',
		// 				'external_game_id' => 'AnimalsWar',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.SuperFood',
		// 				'game_code' => 'SuperFood',
		// 				'english_name' => 'Super Food',
		// 				'external_game_id' => 'SuperFood',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.SummerBeach',
		// 				'game_code' => 'SummerBeach',
		// 				'english_name' => 'Summer Beach',
		// 				'external_game_id' => 'SummerBeach',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.MayanCode',
		// 				'game_code' => 'MayanCode',
		// 				'english_name' => 'Mayan Code',
		// 				'external_game_id' => 'MayanCode',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.TheAliens',
		// 				'game_code' => 'TheAliens',
		// 				'english_name' => 'The Aliens',
		// 				'external_game_id' => 'TheAliens',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.Mahjong13',
		// 				'game_code' => 'Mahjong13',
		// 				'english_name' => 'Mahjong 13',
		// 				'external_game_id' => 'Mahjong13',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.Gangs',
		// 				'game_code' => 'Gangs',
		// 				'english_name' => 'Gangs',
		// 				'external_game_id' => 'Gangs',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.GhostCastle',
		// 				'game_code' => 'GhostCastle',
		// 				'english_name' => 'Ghost Castle',
		// 				'external_game_id' => 'GhostCastle',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.KingCard',
		// 				'game_code' => 'KingCard',
		// 				'english_name' => 'King Card',
		// 				'external_game_id' => 'KingCard',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.HappyJoker',
		// 				'game_code' => 'HappyJoker',
		// 				'english_name' => 'Happy Joker',
		// 				'external_game_id' => 'HappyJoker',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.DragonBall',
		// 				'game_code' => 'DragonBall',
		// 				'english_name' => 'Dragon Ball',
		// 				'external_game_id' => 'DragonBall',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.OrangePark',
		// 				'game_code' => 'OrangePark',
		// 				'english_name' => 'Orange Park',
		// 				'external_game_id' => 'OrangePark',
		// 			),
		// 		)
		// 	),
		// 	array(
		// 		'game_type' => 'Table Games',
		// 		'game_type_lang' => 'inteplay_table_games',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_name' => 'inteplay.H5_EURouLette',
		// 				'game_code' => 'H5_EURouLette',
		// 				'english_name' => 'EU Roulette',
		// 				'external_game_id' => 'H5_EURouLette',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_USRoulette',
		// 				'game_code' => 'H5_USRoulette',
		// 				'english_name' => 'US Roulette',
		// 				'external_game_id' => 'H5_USRoulette',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_FishPrawnCrab',
		// 				'game_code' => 'H5_FishPrawnCrab',
		// 				'english_name' => 'Fish Prawn Crab',
		// 				'external_game_id' => 'H5_FishPrawnCrab',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_Belangkai',
		// 				'game_code' => 'H5_Belangkai',
		// 				'english_name' => 'Belangkai',
		// 				'external_game_id' => 'H5_Belangkai',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_SicBo',
		// 				'game_code' => 'H5_SicBo',
		// 				'english_name' => 'Sic Bo',
		// 				'external_game_id' => 'H5_SicBo',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.FishPrawnCrab',
		// 				'game_code' => 'FishPrawnCrab',
		// 				'english_name' => 'Fish Prawn Crab',
		// 				'external_game_id' => 'FishPrawnCrab',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.Belangkai',
		// 				'game_code' => 'Belangkai',
		// 				'english_name' => 'Belangkai',
		// 				'external_game_id' => 'Belangkai',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.SicBo',
		// 				'game_code' => 'SicBo',
		// 				'english_name' => 'Sic Bo',
		// 				'external_game_id' => 'SicBo',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.LuckyKnot',
		// 				'game_code' => 'LuckyKnot',
		// 				'english_name' => 'Lucky Knot Roulette',
		// 				'external_game_id' => 'LuckyKnot',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.EURoulette',
		// 				'game_code' => 'EURoulette',
		// 				'english_name' => 'EU Roulette',
		// 				'external_game_id' => 'EURoulette',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.USRoulette',
		// 				'game_code' => 'USRoulette',
		// 				'english_name' => 'US Roulette',
		// 				'external_game_id' => 'USRoulette',
		// 			),
		// 		)
		// 	),
		// 	array(
		// 		'game_type' => 'Card Games',
		// 		'game_type_lang' => 'inteplay_card_games',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_name' => 'inteplay.PaiGow',
		// 				'game_code' => 'PaiGow',
		// 				'english_name' => 'Pai Gow',
		// 				'external_game_id' => 'PaiGow',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_Blackjack',
		// 				'game_code' => 'H5_Blackjack',
		// 				'english_name' => 'Blackjack',
		// 				'external_game_id' => 'H5_Blackjack',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.H5_RedDog',
		// 				'game_code' => 'H5_RedDog',
		// 				'english_name' => 'Red Dog',
		// 				'external_game_id' => 'H5_RedDog',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.Blackjack',
		// 				'game_code' => 'Blackjack',
		// 				'english_name' => 'Blackjack',
		// 				'external_game_id' => 'Blackjack',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.RedDog',
		// 				'game_code' => 'RedDog',
		// 				'english_name' => 'Red Dog',
		// 				'external_game_id' => 'RedDog',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.Baccarat',
		// 				'game_code' => 'Baccarat',
		// 				'english_name' => 'Baccarat',
		// 				'external_game_id' => 'Baccarat',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.BaccaratPro',
		// 				'game_code' => 'BaccaratPro',
		// 				'english_name' => 'Baccarat',
		// 				'external_game_id' => 'BaccaratPro',
		// 			),
		// 		)
		// 	),
		// 	array(
		// 		'game_type' => 'Others',
		// 		'game_type_lang' => 'inteplay_others',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_name' => 'inteplay.BaccaratWheel',
		// 				'game_code' => 'BaccaratWheel',
		// 				'english_name' => 'Baccarat Wheel',
		// 				'external_game_id' => 'BaccaratWheel',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.CardLord',
		// 				'game_code' => 'CardLord',
		// 				'english_name' => 'Card Lord',
		// 				'external_game_id' => 'CardLord',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.FruitKing',
		// 				'game_code' => 'FruitKing',
		// 				'english_name' => 'King of Fruit',
		// 				'external_game_id' => 'FruitKing',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.JockeyClubGame3D',
		// 				'game_code' => 'JockeyClubGame3D',
		// 				'english_name' => 'Jockey Club 3D',
		// 				'external_game_id' => 'JockeyClubGame3D',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.JockeyClub',
		// 				'game_code' => 'JockeyClub',
		// 				'english_name' => 'Jockey Club',
		// 				'external_game_id' => 'JockeyClub',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.MultiBaccarat',
		// 				'game_code' => 'MultiBaccarat',
		// 				'english_name' => 'Multi Player Baccarat',
		// 				'external_game_id' => 'MultiBaccarat',
		// 			),
		// 		)
		// 	),
		// 	array(
		// 		'game_type' => 'Third Games',
		// 		'game_type_lang' => 'inteplay_third_games',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_TRUE,
		// 		'game_description_list' => array(
		// 			array(
		// 				'game_name' => 'inteplay.ROBYN',
		// 				'game_code' => 'ROBYN',
		// 				'english_name' => 'Robyn',
		// 				'external_game_id' => 'ROBYN',
		// 			),
		// 			array(
		// 				'game_name' => 'inteplay.OrionInteplayV1',
		// 				'game_code' => 'OrionInteplayV1',
		// 				'english_name' => 'Orion',
		// 				'external_game_id' => 'OrionInteplayV1',
		// 			),
		// 		)
		// 	),
		// 	array(
		// 		'game_type' => 'unknown',
		// 		'game_type_lang' => 'inteplay.unknown',
		// 		'status' => self::FLAG_TRUE,
		// 		'flag_show_in_site' => self::FLAG_FALSE,
		// 		'game_description_list' => array(

		// 		)
		// 	),
		// );

		// $game_description_list = array();
		// foreach ($data as $game_type) {

		// 	$this->db->insert('game_type', array(
		// 		'game_platform_id' => INTEPLAY_API,
		// 		'game_type' => $game_type['game_type'],
		// 		'game_type_lang' => $game_type['game_type_lang'],
		// 		'status' => $game_type['status'],
		// 		'flag_show_in_site' => $game_type['flag_show_in_site'],
		// 	));

		// 	$game_type_id = $this->db->insert_id();
		// 	foreach ($game_type['game_description_list'] as $game_description) {
		// 		$game_description_list[] = array_merge(array(
		// 			'game_platform_id' => INTEPLAY_API,
		// 			'game_type_id' => $game_type_id,
		// 		), $game_description);
		// 	}

		// }

		// $this->db->insert_batch('game_description', $game_description_list);
		// $this->db->trans_complete();

	}

	public function down() {
		// $this->db->trans_start();
		// $this->db->delete('game_type', array('game_platform_id' => INTEPLAY_API));
		// $this->db->delete('game_description', array('game_platform_id' => INTEPLAY_API));
		// $this->db->trans_complete();
	}
}