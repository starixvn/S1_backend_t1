<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_mg_mcid_201511141414 extends CI_Migration {

	public function up() {
		$data = array(
			'clientid' => "10001",
			'moduleid' => "104",
		);
		$this->db->where('external_game_id', "3 Card Poker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "129",
		);
		$this->db->where('external_game_id', "3 Card Poker (gold)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40301",
			'moduleid' => "10008",
		);
		$this->db->where('external_game_id', "5ReelDrive");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "27",
		);
		$this->db->where('external_game_id', "Aces & Faces");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "27506",
		);
		$this->db->where('external_game_id', "Aces & Faces (multiplayer 4 hands)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12702",
		);
		$this->db->where('external_game_id', "Alaskan Fishing");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Alaxe In Zombieland");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "73",
		);
		$this->db->where('external_game_id', "American");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Arctic Agents");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12675",
		);
		$this->db->where('external_game_id', "Arctic Fortune");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12844",
		);
		$this->db->where('external_game_id', "Asian Beauty");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "78",
		);
		$this->db->where('external_game_id', "Atlantic City");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "131",
		);
		$this->db->where('external_game_id', "Atlantic City Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10044",
		);
		$this->db->where('external_game_id', "Avalon");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10255",
		);
		$this->db->where('external_game_id', "Bonus Game - Avalon II");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11041",
		);
		$this->db->where('external_game_id', "Avalon 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "34",
		);
		$this->db->where('external_game_id', "Baccarat");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "63",
		);
		$this->db->where('external_game_id', "Baccarat Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12693",
		);
		$this->db->where('external_game_id', "Bars And Stripes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10213",
		);
		$this->db->where('external_game_id', "Battlestar Galactica");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10005",
			'moduleid' => "12",
		);
		$this->db->where('external_game_id', "Belissimo!");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11",
		);
		$this->db->where('external_game_id', "Big 5");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12512",
		);
		$this->db->where('external_game_id', "Big Kahuna");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "12820",
		);
		$this->db->where('external_game_id', "Big Kahuna 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10020",
		);
		$this->db->where('external_game_id', "Big Top");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10020",
		);
		$this->db->where('external_game_id', "Big Top move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "7",
		);
		$this->db->where('external_game_id', "Classic");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "13",
		);
		$this->db->where('external_game_id', "Blackjack Bonanza");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14036",
		);
		$this->db->where('external_game_id', "Bobby 7s");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10186",
		);
		$this->db->where('external_game_id', "Booty Time");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10206",
		);
		$this->db->where('external_game_id', "Break Away");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10001",
		);
		$this->db->where('external_game_id', "Break da Bank");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10235",
		);
		$this->db->where('external_game_id', "Bridezilla");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "212007",
		);
		$this->db->where('external_game_id', "Bubble Bonanza");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12518",
		);
		$this->db->where('external_game_id', "Bush Telegraph");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10231",
		);
		$this->db->where('external_game_id', "Bust the Bank");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14021",
		);
		$this->db->where('external_game_id', "ButterFlies");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40301",
			'moduleid' => "10020",
		);
		$this->db->where('external_game_id', "Carnaval");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "2",
			'moduleid' => "10056",
		);
		$this->db->where('external_game_id', "Carnaval 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12533",
		);
		$this->db->where('external_game_id', "Cashanova");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10006",
		);
		$this->db->where('external_game_id', "Cash Clams");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10004",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "Cash Crazy");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "47",
		);
		$this->db->where('external_game_id', "Cash Splash");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15004",
		);
		$this->db->where('external_game_id', "Cash Splash 5 Reel");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12529",
		);
		$this->db->where('external_game_id', "Cashville");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10294",
		);
		$this->db->where('external_game_id', "Castle Builder");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10005",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "Cherry Red");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14",
		);
		$this->db->where('external_game_id', "Chief's Magic");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10000",
		);
		$this->db->where('external_game_id', "Cool Buck");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10280",
		);
		$this->db->where('external_game_id', "Cool Wolf");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "49",
		);
		$this->db->where('external_game_id', "Cosmic Cat");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40301",
			'moduleid' => "10024",
		);
		$this->db->where('external_game_id', "Couch Potato");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10006",
		);
		$this->db->where('external_game_id', "Cracker Jack");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "9",
		);
		$this->db->where('external_game_id', "Craps");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10019",
		);
		$this->db->where('external_game_id', "Crazy Chameleons");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14003",
		);
		$this->db->where('external_game_id', "Crocodopolis");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "13",
		);
		$this->db->where('external_game_id', "Crazy Crocs");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210053",
		);
		$this->db->where('external_game_id', "Crown and Anchor");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "19",
		);
		$this->db->where('external_game_id', "Cyberstud Poker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "18",
		);
		$this->db->where('external_game_id', "Deuces & Joker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "27505",
		);
		$this->db->where('external_game_id', "Deuces & Joker (PP)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "17",
		);
		$this->db->where('external_game_id', "Deuces Wild");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "27502",
		);
		$this->db->where('external_game_id', "Deuces Wild (PP)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12527",
		);
		$this->db->where('external_game_id', "Dino Might");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "Double Magic");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14004",
		);
		$this->db->where('external_game_id', "Doctor Love");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10244",
		);
		$this->db->where('external_game_id', "Dolphin Quest");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "85",
		);
		$this->db->where('external_game_id', "Dbl Dbl Bonus");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "76",
		);
		$this->db->where('external_game_id', "Double Exposure");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "29",
		);
		$this->db->where('external_game_id', "Double Joker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "27504",
		);
		$this->db->where('external_game_id', "Double Joker (PP)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10023",
		);
		$this->db->where('external_game_id', "Double Wammy");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Drone Wars");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10232",
		);
		$this->db->where('external_game_id', "Dr Watts Up");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12817",
		);
		$this->db->where('external_game_id', "Eagles Wings");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14026",
		);
		$this->db->where('external_game_id', "Enchanted Mermaid");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210052",
		);
		$this->db->where('external_game_id', "Enchanted Woods");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "58",
		);
		$this->db->where('external_game_id', "European");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "134",
		);
		$this->db->where('external_game_id', "European Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "66",
		);
		$this->db->where('external_game_id', "European Blackjack Redeal Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "2",
		);
		$this->db->where('external_game_id', "European (Gold)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "3",
		);
		$this->db->where('external_game_id', "Fantastic 7's");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12611",
		);
		$this->db->where('external_game_id', "Fat Ladyings 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "10229",
		);
		$this->db->where('external_game_id', "Feathered Frenzy");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14046",
		);
		$this->db->where('external_game_id', "FireHawk");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10291",
		);
		$this->db->where('external_game_id', "Fish Party");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "8",
		);
		$this->db->where('external_game_id', "Flip Card");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10017",
		);
		$this->db->where('external_game_id', "Flower Power");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40301",
			'moduleid' => "10206",
		);
		$this->db->where('external_game_id', "Football Star");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "Fortune Cookie");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "93",
		);
		$this->db->where('external_game_id', "French");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15000",
		);
		$this->db->where('external_game_id', "Fruit Fiesta");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15008",
		);
		$this->db->where('external_game_id', "Fruit Fiesta (5 reel)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "4",
		);
		$this->db->where('external_game_id', "Fruit");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10224",
		);
		$this->db->where('external_game_id', "Galacticons");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "12",
		);
		$this->db->where('external_game_id', "Golden Dragon");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "41",
		);
		$this->db->where('external_game_id', "Genie's Gems");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210054",
		);
		$this->db->where('external_game_id', "Germinator");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "12530",
		);
		$this->db->where('external_game_id', "Gift Rap");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10238",
		);
		$this->db->where('external_game_id', "Girls With Guns - Jungle Heat");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10281",
		);
		$this->db->where('external_game_id', "Girls With Guns II - Frozen Dawn");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "15",
		);
		$this->db->where('external_game_id', "Gladiators Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10003",
		);
		$this->db->where('external_game_id', "Goblins Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "10154",
		);
		$this->db->where('external_game_id', "Gold Factory 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10007",
		);
		$this->db->where('external_game_id', "Gopher Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "11068",
		);
		$this->db->where('external_game_id', "Gopher Gold_ 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10106",
		);
		$this->db->where('external_game_id', "Great Griffin");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Haunted Nights");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Hells Grannies");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "212005",
		);
		$this->db->where('external_game_id', "Hexaline");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10024",
		);
		$this->db->where('external_game_id', "High Five");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "81",
		);
		$this->db->where('external_game_id', "High Limit Baccarat");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10265",
		);
		$this->db->where('external_game_id', "High Society");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "1001",
		);
		$this->db->where('external_game_id', "High Speed Poker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "58",
		);
		$this->db->where('external_game_id', "High Limit European");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10041",
		);
		$this->db->where('external_game_id', "Ho Ho Ho");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12782",
		);
		$this->db->where('external_game_id', "Hot Ink");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "30102",
		);
		$this->db->where('external_game_id', "House of Dragons");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10145",
		);
		$this->db->where('external_game_id', "Immortal Romance Video Slot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10289",
		);
		$this->db->where('external_game_id', "Immortal Romance 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "In It To Win It");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14001",
		);
		$this->db->where('external_game_id', "Irish Eyes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10043",
		);
		$this->db->where('external_game_id', "Isis    ");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10004",
			'moduleid' => "210010",
		);
		$this->db->where('external_game_id', "Instant Win Card Selector");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "25002",
		);
		$this->db->where('external_game_id', "Prog Poker - Jackpot Deuces");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "6",
		);
		$this->db->where('external_game_id', "Jacks or Better");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "27501",
		);
		$this->db->where('external_game_id', "Jacks or Better (PP)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10174",
		);
		$this->db->where('external_game_id', "Jason And The Golden Fleece");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Jekyll And Hyde");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "11",
		);
		$this->db->where('external_game_id', "Jester's Jackpot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12673",
		);
		$this->db->where('external_game_id', "Jewels Of The Orient");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12",
		);
		$this->db->where('external_game_id', "Jackpot Express");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "23",
		);
		$this->db->where('external_game_id', "Joker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Joy Of Six");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "48",
		);
		$this->db->where('external_game_id', "Jurassic Jackpot (big)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "48",
		);
		$this->db->where('external_game_id', "Jurassic Jackpot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10305",
		);
		$this->db->where('external_game_id', "Jurassic Park");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12855",
		);
		$this->db->where('external_game_id', "Karate Pig");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12855",
		);
		$this->db->where('external_game_id', "Karate Pig move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "20",
		);
		$this->db->where('external_game_id', "Keno");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "17500",
		);
		$this->db->where('external_game_id', "King CashaLot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12719",
		);
		$this->db->where('external_game_id', "Kings Of Cash");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40304",
			'moduleid' => "10025",
		);
		$this->db->where('external_game_id', "Ladies Nite");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "11028",
		);
		$this->db->where('external_game_id', "Lady In Red");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10193",
		);
		$this->db->where('external_game_id', "Leagues Of Fortune");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10004",
		);
		$this->db->where('external_game_id', "Lions Share");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12528",
		);
		$this->db->where('external_game_id', "Loaded");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10270",
		);
		$this->db->where('external_game_id', "Loose Cannon");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15007",
		);
		$this->db->where('external_game_id', "Lotsa Loot (5 reel)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10011",
		);
		$this->db->where('external_game_id', "Lotsaloot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "26",
		);
		$this->db->where('external_game_id', "Louisiana Double");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14047",
		);
		$this->db->where('external_game_id', "Love Bugs");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12500",
		);
		$this->db->where('external_game_id', "Lucky Charmer");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10263",
		);
		$this->db->where('external_game_id', "Lucky Koi");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10274",
		);
		$this->db->where('external_game_id', "Lucky Leprechan’s loot ");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Lucky Rabbits Loot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Lucky Streak");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10142",
		);
		$this->db->where('external_game_id', "Lucky Witch");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10142",
		);
		$this->db->where('external_game_id', "Lucky Witch move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Gold of Machu Picchu");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12532",
		);
		$this->db->where('external_game_id', "Mad Hatters");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Magic Boxes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Magic Charms");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "61",
		);
		$this->db->where('external_game_id', "Major Million");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15005",
		);
		$this->db->where('external_game_id', "Major Millions 5 Reel");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "18500",
		);
		$this->db->where('external_game_id', "Max Damage and the Alien Attack");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11052",
		);
		$this->db->where('external_game_id', "Mayan Princess");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Megadeth");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14042",
		);
		$this->db->where('external_game_id', "Merlin's Millions");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12506",
		);
		$this->db->where('external_game_id', "Mermaids Million");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12666",
		);
		$this->db->where('external_game_id', "Mermaids Millions 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "129",
		);
		$this->db->where('external_game_id', "3 Card Poker (multihand)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "131",
		);
		$this->db->where('external_game_id', "Atlantic City Gold (MH)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "78",
		);
		$this->db->where('external_game_id', "Atlantic City move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "56",
		);
		$this->db->where('external_game_id', "Classic (MH)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "134",
		);
		$this->db->where('external_game_id', "European Gold move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10005",
		);
		$this->db->where('external_game_id', "Monkey's Money");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10022",
		);
		$this->db->where('external_game_id', "Monster Mania");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10107",
		);
		$this->db->where('external_game_id', "Riviera Riches");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12667",
		);
		$this->db->where('external_game_id', "Moonshine 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Mount Olympus");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Loaded Slot Tournament");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Multiplayer Wheel Of Wealth");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10161",
		);
		$this->db->where('external_game_id', "Megaspin - Break da Bank Again");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10215",
		);
		$this->db->where('external_game_id', "Mugshot Madness");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Multiplayer Roulette");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "62",
		);
		$this->db->where('external_game_id', "Multi Wheel Roulette Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10126",
		);
		$this->db->where('external_game_id', "Mystic Dreams");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Mystique Grove");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10",
		);
		$this->db->where('external_game_id', "7 Oceans");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10211",
		);
		$this->db->where('external_game_id', "Octopays");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Orcs Battle");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10019",
		);
		$this->db->where('external_game_id', "Oriental Fortune");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10269",
		);
		$this->db->where('external_game_id', "0");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10018",
		);
		$this->db->where('external_game_id', "Party Time");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10205",
		);
		$this->db->where('external_game_id', "Phantom Cash");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "212000",
		);
		$this->db->where('external_game_id', "Pharaoh Bingo");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "3",
		);
		$this->db->where('external_game_id', "Pharaoh's Fortune");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10234",
		);
		$this->db->where('external_game_id', "Piggy fortunes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "3",
		);
		$this->db->where('external_game_id', "Pirates Paradise");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10251",
		);
		$this->db->where('external_game_id', "Playboy");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "25",
		);
		$this->db->where('external_game_id', "Poker Pursuit");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12530",
		);
		$this->db->where('external_game_id', "Pollen Nation");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "200022",
		);
		$this->db->where('external_game_id', "Premier Racing");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10006",
			'moduleid' => "2",
		);
		$this->db->where('external_game_id', "Premier Roulette Diamond Edition");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "200023",
		);
		$this->db->where('external_game_id', "Premier Trotting");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "30003",
		);
		$this->db->where('external_game_id', "Cyberstud");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12671",
		);
		$this->db->where('external_game_id', "Pure Platinum");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10267",
		);
		$this->db->where('external_game_id', "Racing For Pinks");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14007",
		);
		$this->db->where('external_game_id', "Ramesses Riches");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "42",
		);
		$this->db->where('external_game_id', "Red Dog");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10307",
		);
		$this->db->where('external_game_id', "Red Hot Devil");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11091",
		);
		$this->db->where('external_game_id', "Reel Gems");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "10031",
		);
		$this->db->where('external_game_id', "Reel Strike");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10008",
		);
		$this->db->where('external_game_id', "Reel Thunder");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11156",
		);
		$this->db->where('external_game_id', "Retro Reels");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11157",
		);
		$this->db->where('external_game_id', "Retro Reels - Diamond Glitz 2");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11158",
		);
		$this->db->where('external_game_id', "Retro Reels Extreme Heat");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10304",
		);
		$this->db->where('external_game_id', "Robo Jack");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10006",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "Rock The Boat");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Roller Derby");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "12",
		);
		$this->db->where('external_game_id', "Roman Riches");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "58",
		);
		$this->db->where('external_game_id', "European move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15",
		);
		$this->db->where('external_game_id', "Reels Royce");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10102",
		);
		$this->db->where('external_game_id', "Rhyming Reels Georgie Porgie");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10090",
		);
		$this->db->where('external_game_id', "RR Jack and Jill 96");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11169",
		);
		$this->db->where('external_game_id', "Old King Cole");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "88",
		);
		$this->db->where('external_game_id', "Aces and Eights");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10008",
			'moduleid' => "10025",
		);
		$this->db->where('external_game_id', "Agent Jane Blonde");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40302",
			'moduleid' => "12530",
		);
		$this->db->where('external_game_id', "Age of Discovery");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "128",
		);
		$this->db->where('external_game_id', "All Aces Poker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "89",
		);
		$this->db->where('external_game_id', "All American");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "30103",
		);
		$this->db->where('external_game_id', "Around the World");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10004",
		);
		$this->db->where('external_game_id', "Astronomical");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210018",
		);
		$this->db->where('external_game_id', "Ballistic Bingo");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10032",
		);
		$this->db->where('external_game_id', "Bar Bar Black Sheep");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210024",
		);
		$this->db->where('external_game_id', "Beer Fest");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12584",
		);
		$this->db->where('external_game_id', "Big Break");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "138",
		);
		$this->db->where('external_game_id', "Big Five Blackjack Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12592",
		);
		$this->db->where('external_game_id', "Big Kahuna - Snakes and Ladders");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210016",
		);
		$this->db->where('external_game_id', "Bingo Bonanza");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "90",
		);
		$this->db->where('external_game_id', "Bonus Deuces Wild");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "82",
		);
		$this->db->where('external_game_id', "Bonus Poker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "84",
		);
		$this->db->where('external_game_id', "Bonus Poker Deluxe");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210029",
		);
		$this->db->where('external_game_id', "Bowled Over");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11064",
		);
		$this->db->where('external_game_id', "Break da Bank Again 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "12510",
		);
		$this->db->where('external_game_id', "Bulls Eye");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210014",
		);
		$this->db->where('external_game_id', "Bunny Boiler");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210043",
		);
		$this->db->where('external_game_id', "Bunny Boiler Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11054",
		);
		$this->db->where('external_game_id', "Burning Desire 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12508",
		);
		$this->db->where('external_game_id', "Cabin Fever");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10006",
			'moduleid' => "12",
		);
		$this->db->where('external_game_id', "Captain Cash");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210040",
		);
		$this->db->where('external_game_id', "Card Climber");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11006",
		);
		$this->db->where('external_game_id', "Cashapillar");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11029",
		);
		$this->db->where('external_game_id', "Centre Court");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12526",
		);
		$this->db->where('external_game_id', "Chain Mail");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12501",
		);
		$this->db->where('external_game_id', "Chiefs Fortune");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10008",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "City of Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "135",
		);
		$this->db->where('external_game_id', "Classic Blackjack Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10004",
			'moduleid' => "10020",
		);
		$this->db->where('external_game_id', "Crazy 80s");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210022",
		);
		$this->db->where('external_game_id', "Crypt Crusade");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210050",
		);
		$this->db->where('external_game_id', "Crypt Crusade Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10005",
			'moduleid' => "3",
		);
		$this->db->where('external_game_id', "Cutesy Pie");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210007",
		);
		$this->db->where('external_game_id', "Dawn Of The Bread");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11020",
		);
		$this->db->where('external_game_id', "Deck The Halls");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11062",
		);
		$this->db->where('external_game_id', "Deck the Halls 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10006",
			'moduleid' => "3",
		);
		$this->db->where('external_game_id', "Diamond 7s");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12505",
		);
		$this->db->where('external_game_id', "Diamond Deal");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "12531",
		);
		$this->db->where('external_game_id', "Dogfather");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10011",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "DonDeal");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "83",
		);
		$this->db->where('external_game_id', "Double Bonus Poker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10007",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "Double Dose");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "76",
		);
		$this->db->where('external_game_id', "Double Exposure move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210012",
		);
		$this->db->where('external_game_id', "Dragons Fortune");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "12508",
		);
		$this->db->where('external_game_id', "Elementals");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10017",
		);
		$this->db->where('external_game_id', "Fairy Ring");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10009",
			'moduleid' => "3",
		);
		$this->db->where('external_game_id', "Floridita Fandango");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "11",
		);
		$this->db->where('external_game_id', "Flos Diner");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10006",
			'moduleid' => "10000",
		);
		$this->db->where('external_game_id', "Flying Ace");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210026",
		);
		$this->db->where('external_game_id', "Scratch Card-Foamy Fortunes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "10000",
		);
		$this->db->where('external_game_id', "Fortuna");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "12514",
		);
		$this->db->where('external_game_id', "Free Spirit -L- Wheel of Wealth");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210008",
		);
		$this->db->where('external_game_id', "Freezing Fuzzballs");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10021",
		);
		$this->db->where('external_game_id', "Froot Loot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10004",
			'moduleid' => "48",
		);
		$this->db->where('external_game_id', "Frost Bite");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "10017",
		);
		$this->db->where('external_game_id', "Fruit Salad");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10009",
			'moduleid' => "12",
		);
		$this->db->where('external_game_id', "Funhouse");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210032",
		);
		$this->db->where('external_game_id', "Game Set And Scratch");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10005",
			'moduleid' => "10000",
		);
		$this->db->where('external_game_id', "Gold Coast");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210017",
		);
		$this->db->where('external_game_id', "Golden Ghouls");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10026",
		);
		$this->db->where('external_game_id', "Good To Go");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10007",
			'moduleid' => "3",
		);
		$this->db->where('external_game_id', "Grand7s");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210011",
		);
		$this->db->where('external_game_id', "Granny Prix");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210021",
		);
		$this->db->where('external_game_id', "Hairy Fairies");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12531",
		);
		$this->db->where('external_game_id', "Halloweenies");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210037",
		);
		$this->db->where('external_game_id', "Hand to Hand Combat");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "10001",
		);
		$this->db->where('external_game_id', "Happy New Year");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10040",
		);
		$this->db->where('external_game_id', "Harveys");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10006",
			'moduleid' => "15",
		);
		$this->db->where('external_game_id', "Heavy Metal");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12732",
		);
		$this->db->where('external_game_id', "HellBoy");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "147",
		);
		$this->db->where('external_game_id', "High Streak European");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "146",
		);
		$this->db->where('external_game_id', "Hi Lo 13 European");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12547",
		);
		$this->db->where('external_game_id', "Hitman");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "10022",
		);
		$this->db->where('external_game_id', "Hot Shot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "210010",
		);
		$this->db->where('external_game_id', "Big Break Instant Win");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Bill and Ted’s Bogus Journey");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11006",
		);
		$this->db->where('external_game_id', "Cashapillar move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12531",
		);
		$this->db->where('external_game_id', "Halloweenies move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10005",
			'moduleid' => "10006",
		);
		$this->db->where('external_game_id', "Jack in the Box");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "10003",
		);
		$this->db->where('external_game_id', "Jewel Thief");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10000",
		);
		$this->db->where('external_game_id', "Jingle Bells");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "60030",
		);
		$this->db->where('external_game_id', "Joker 8000");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10039",
		);
		$this->db->where('external_game_id', "JungleJim");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210039",
		);
		$this->db->where('external_game_id', "Kashatoa");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10042",
		);
		$this->db->where('external_game_id', "Kathmandu");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210041",
		);
		$this->db->where('external_game_id', "Killer Clubs");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "30101",
		);
		$this->db->where('external_game_id', "King Arthur");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10001",
		);
		$this->db->where('external_game_id', "Legacy");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210020",
		);
		$this->db->where('external_game_id', "Lucky Numbers");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12538",
		);
		$this->db->where('external_game_id', "Magic Spell");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "127",
		);
		$this->db->where('external_game_id', "Holdem High");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "148",
		);
		$this->db->where('external_game_id', "Perfect Pairs European");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10012",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "Mocha Orange");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "30100",
		);
		$this->db->where('external_game_id', "Money Mad Monkey");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "56",
		);
		$this->db->where('external_game_id', "Multihand Classic");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "133",
		);
		$this->db->where('external_game_id', "Multi Vegas Downtown");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210015",
		);
		$this->db->where('external_game_id', "Mumbai Magic");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10041",
		);
		$this->db->where('external_game_id', "Munchkins");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210030",
		);
		$this->db->where('external_game_id', "Offside And Seek");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "147",
		);
		$this->db->where('external_game_id', "High Streak Blackjack");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "146",
		);
		$this->db->where('external_game_id', "Hi Lo 13 Blackjack");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10006",
			'moduleid' => "134",
		);
		$this->db->where('external_game_id', "Multi-Hand Blackjack");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "137",
		);
		$this->db->where('external_game_id', "Multi-Hand Bonus Blackjack");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "10009",
		);
		$this->db->where('external_game_id', "Peek-a-Boo");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "210024",
		);
		$this->db->where('external_game_id', "Pharaoh's Gems");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210027",
		);
		$this->db->where('external_game_id', "Plunder The Sea");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "11001",
			'moduleid' => "2",
		);
		$this->db->where('external_game_id', "Premier Roulette");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12686",
		);
		$this->db->where('external_game_id', "Prime Property 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10004",
			'moduleid' => "10000",
		);
		$this->db->where('external_game_id', "Rapid Reels");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10009",
		);
		$this->db->where('external_game_id', "Rings and Roses");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "200020",
		);
		$this->db->where('external_game_id', "Royal Derby");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10004",
			'moduleid' => "10003",
		);
		$this->db->where('external_game_id', "Samurai Sevens");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10049",
		);
		$this->db->where('external_game_id', "Santa Paws");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12670",
		);
		$this->db->where('external_game_id', "Scrooge");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210023",
		);
		$this->db->where('external_game_id', "Six Shooter Looter");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210048",
		);
		$this->db->where('external_game_id', "Six-Shooter Looter Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12509",
		);
		$this->db->where('external_game_id', "Sizzling Scorpions");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210013",
		);
		$this->db->where('external_game_id', "Slam Funk");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12773",
		);
		$this->db->where('external_game_id', "Soccer Safari");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10037",
		);
		$this->db->where('external_game_id', "Sonic Boom");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210028",
		);
		$this->db->where('external_game_id', "Space Evader");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210049",
		);
		$this->db->where('external_game_id', "Space Evader Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "140",
		);
		$this->db->where('external_game_id', "Spanish Blackjack Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10003",
		);
		$this->db->where('external_game_id', "Spell Bound");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10005",
			'moduleid' => "10025",
		);
		$this->db->where('external_game_id', "Summertime");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40301",
			'moduleid' => "10043",
		);
		$this->db->where('external_game_id', "Supe It Up");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210019",
		);
		$this->db->where('external_game_id', "Super Bonus Bingo");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210010",
		);
		$this->db->where('external_game_id', "Super Zeroes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40305",
			'moduleid' => "12528",
		);
		$this->db->where('external_game_id', "Sure Win");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12561",
		);
		$this->db->where('external_game_id', "The Osbournes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11013",
		);
		$this->db->where('external_game_id', "The Rat Pack");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10010",
			'moduleid' => "5",
		);
		$this->db->where('external_game_id', "Thousand Islands");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "38",
		);
		$this->db->where('external_game_id', "Three Wheeler");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12576",
		);
		$this->db->where('external_game_id', "Tomb Raider 2");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10007",
		);
		$this->db->where('external_game_id', "Totem Treasure");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "145",
		);
		$this->db->where('external_game_id', "Triple Pocket Hold'em");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210031",
		);
		$this->db->where('external_game_id', "Turtley Awesome");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "10041",
		);
		$this->db->where('external_game_id', "Twister");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "136",
		);
		$this->db->where('external_game_id', "Vegas Single Deck Blackjack GOLD");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "132",
		);
		$this->db->where('external_game_id', "Vegas Strip");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10004",
			'moduleid' => "12506",
		);
		$this->db->where('external_game_id', "Wasabi San");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210033",
		);
		$this->db->where('external_game_id', "Whack a Jackpot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "200021",
		);
		$this->db->where('external_game_id', "Wheel of Riches");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210009",
		);
		$this->db->where('external_game_id', "Wild Champions");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12511",
		);
		$this->db->where('external_game_id', "Witches Wealth");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10005",
			'moduleid' => "12506",
		);
		$this->db->where('external_game_id', "Worldcup Mania");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10085",
		);
		$this->db->where('external_game_id', "Santa's Wild Ride");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "8",
		);
		$this->db->where('external_game_id', "Scratch Card");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10026",
		);
		$this->db->where('external_game_id', "Secret Admirer");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10268",
		);
		$this->db->where('external_game_id', "Secret Santa");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "12856",
		);
		$this->db->where('external_game_id', "Shoot!");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "38",
		);
		$this->db->where('external_game_id', "Sic Bo");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11055",
		);
		$this->db->where('external_game_id', "Silver Fang");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12517",
		);
		$this->db->where('external_game_id', "Skull Duggery");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Snake Slot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Sovereign Of The Seven Seas");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "79",
		);
		$this->db->where('external_game_id', "Spanish");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12514",
		);
		$this->db->where('external_game_id', "Spectacular");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210056",
		);
		$this->db->where('external_game_id', "Spingo");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10025",
		);
		$this->db->where('external_game_id', "Feature Slot-Spring Break");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10216",
		);
		$this->db->where('external_game_id', "Starlight Kiss");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12536",
		);
		$this->db->where('external_game_id', "Starscape 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11014",
		);
		$this->db->where('external_game_id', "Stash of the Titans");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Steam Punk Heroes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11037",
		);
		$this->db->where('external_game_id', "Sterling Silver");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "10008",
		);
		$this->db->where('external_game_id', "SunQuest");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "77",
		);
		$this->db->where('external_game_id', "Super Fun 21");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Surf Safari");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10038",
		);
		$this->db->where('external_game_id', "Tally Ho");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "28",
		);
		$this->db->where('external_game_id', "Tens or Better");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "27507",
		);
		$this->db->where('external_game_id', "Tens or Better (PP)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10282",
		);
		$this->db->where('external_game_id', "Terminator II");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14038",
		);
		$this->db->where('external_game_id', "The Bermuda Mysteries");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10190",
		);
		$this->db->where('external_game_id', "The Finer Reels of Life");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "The Land of Lemuria");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "The Lost Princess Anastasia");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10162",
		);
		$this->db->where('external_game_id', "The Twisted Circus");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12845",
		);
		$this->db->where('external_game_id', "Throne Of Egypt");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10287",
		);
		$this->db->where('external_game_id', "Throne of Egypt 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40200",
			'moduleid' => "12772",
		);
		$this->db->where('external_game_id', "ThunderStruck II");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "12825",
		);
		$this->db->where('external_game_id', "Thunderstruck2 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Tiger Moon");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Tiger Vs Bear");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "12666",
		);
		$this->db->where('external_game_id', "Tomb Raider 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15001",
		);
		$this->db->where('external_game_id', "Treasure Nile");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "212006",
		);
		$this->db->where('external_game_id', "Triangulation");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "15",
		);
		$this->db->where('external_game_id', "Trick Or Treat");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10030",
		);
		$this->db->where('external_game_id', "Triple Magic");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15003",
		);
		$this->db->where('external_game_id', "Tunzamunni");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10184",
		);
		$this->db->where('external_game_id', "Untamed - Bengal Tiger");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10272",
		);
		$this->db->where('external_game_id', "Untamed - Crowned Eagle");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10194",
		);
		$this->db->where('external_game_id', "Untamed - Giant Panda");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10323",
		);
		$this->db->where('external_game_id', "Untamed - Giant Panda 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10214",
		);
		$this->db->where('external_game_id', "Untamed - Wolf Pack");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "75",
		);
		$this->db->where('external_game_id', "Vegas Downtown");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "132",
		);
		$this->db->where('external_game_id', "Vegas Strip move");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Victorian Villain");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10022",
		);
		$this->db->where('external_game_id', "Vinyl Countdown");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Western Frontier");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10020",
		);
		$this->db->where('external_game_id', "What A Hoot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12515",
		);
		$this->db->where('external_game_id', "What on Earth 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12688",
		);
		$this->db->where('external_game_id', "Wheel of WealthE 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "White buffalo");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10016",
		);
		$this->db->where('external_game_id', "Wow Pot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "41",
		);
		$this->db->where('external_game_id', "Winning Wizards");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10009",
		);
		$this->db->where('external_game_id', "Zany Zebra");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10275",
		);
		$this->db->where('external_game_id', "Scary Friends");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "10229",
		);
		$this->db->where('external_game_id', "Vegas Dreams");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10229",
		);
		$this->db->where('external_game_id', "Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10003",
			'moduleid' => "11016",
		);
		$this->db->where('external_game_id', "Lucky Firecracker");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40305",
			'moduleid' => "10025",
		);
		$this->db->where('external_game_id', "Adventure Palace");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10376",
		);
		$this->db->where('external_game_id', "Ariana");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10365",
		);
		$this->db->where('external_game_id', "Big Chef");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10309",
		);
		$this->db->where('external_game_id', "Forsaken Kingdom");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10396",
		);
		$this->db->where('external_game_id', "Hound Hotel");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10397",
		);
		$this->db->where('external_game_id', "Kitty Cabana");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10219",
		);
		$this->db->where('external_game_id', "Legend of Olympus");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10374",
		);
		$this->db->where('external_game_id', "Lucky Leprechaun");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10297",
		);
		$this->db->where('external_game_id', "Penguin Splash");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10277",
		);
		$this->db->where('external_game_id', "Pistoleras");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10346",
		);
		$this->db->where('external_game_id', "Rabbit In The Hat");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10400",
		);
		$this->db->where('external_game_id', "River of Riches");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10401",
		);
		$this->db->where('external_game_id', "Mega Moolah");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "17504",
		);
		$this->db->where('external_game_id', "Mega Moolah - Isis");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10401",
		);
		$this->db->where('external_game_id', "Hot as Hades");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12858",
		);
		$this->db->where('external_game_id', "Chain Mail New");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "15010",
		);
		$this->db->where('external_game_id', "The Dark Knight");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10250",
		);
		$this->db->where('external_game_id', "The Dark Knight Rises");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10424",
		);
		$this->db->where('external_game_id', "Bridesmaids");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10430",
		);
		$this->db->where('external_game_id', "Rugby Star");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "4",
			'moduleid' => "10044",
		);
		$this->db->where('external_game_id', "Lucky Zodiac");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10423",
		);
		$this->db->where('external_game_id', "Titans of the Sun - Theia");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10402",
		);
		$this->db->where('external_game_id', "Titans of the Sun - Hyperion");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10086",
		);
		$this->db->where('external_game_id', "Rhyming Reels Queen of Hearts 96");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12564",
		);
		$this->db->where('external_game_id', "Boogie Monsters");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Boogie Monsters 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "14023",
		);
		$this->db->where('external_game_id', "Gold Ahoy");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11004",
		);
		$this->db->where('external_game_id', "Break da Bank Again");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10380",
		);
		$this->db->where('external_game_id', "Golden Era");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "11172",
		);
		$this->db->where('external_game_id', "Rhyming Reels Old King Cole 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10266",
		);
		$this->db->where('external_game_id', "Wild Catch");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "First Past the Post 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10236",
		);
		$this->db->where('external_game_id', "Sweet Harvest");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Dragon Lady 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Dragons Loot 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10016",
		);
		$this->db->where('external_game_id', "Wowpot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40303",
			'moduleid' => "10206",
		);
		$this->db->where('external_game_id', "Cricket Star");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "10407",
		);
		$this->db->where('external_game_id', "Girls With Guns - Jungle Heat 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Age of Discovery 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15004",
		);
		$this->db->where('external_game_id', "Cash Splash 5");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11144",
		);
		$this->db->where('external_game_id', "Lion's Pride");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10126",
		);
		$this->db->where('external_game_id', "Mystic Dreams 96");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11167",
		);
		$this->db->where('external_game_id', "Party Island");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12621",
		);
		$this->db->where('external_game_id', "Summer Holiday");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12576",
		);
		$this->db->where('external_game_id', "Tomb Raider Secret of the Sword (Tomb Raider Secret of the Sword)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40303",
			'moduleid' => "10008",
		);
		$this->db->where('external_game_id', "Treasure Palace");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "73",
		);
		$this->db->where('external_game_id', "American Roulette Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "European Roulette Advanced");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40305",
			'moduleid' => "10043",
		);
		$this->db->where('external_game_id', "Beach Babes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "10316",
		);
		$this->db->where('external_game_id', "Break Away 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12856",
		);
		$this->db->where('external_game_id', "Gold Factory");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10025",
		);
		$this->db->where('external_game_id', "Thunderstruck");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "17501",
		);
		$this->db->where('external_game_id', "Progressive Mega");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "17504",
		);
		$this->db->where('external_game_id', "Progressive MM Isis");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "15010",
		);
		$this->db->where('external_game_id', "Progressive The Dark Knight");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11016",
		);
		$this->db->where('external_game_id', "Burning Desire");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15001",
		);
		$this->db->where('external_game_id', "Flash-Progressive Slots-Treasure Nile");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10008",
		);
		$this->db->where('external_game_id', "5 Reel Drive");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40301",
			'moduleid' => "10025",
		);
		$this->db->where('external_game_id', "Spring break");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "unknown");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "11151",
		);
		$this->db->where('external_game_id', "Spring Break 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "210051",
		);
		$this->db->where('external_game_id', "Pick 'n Switch");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Flash-Progressive Cyberstud");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "2",
		);
		$this->db->where('external_game_id', "European Roulette Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "11086",
		);
		$this->db->where('external_game_id', "5 Reel Drive 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Ladies Nite 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Double O Cash 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Big Kahuna - Snakes and Ladders (Big Kahuna - Snakes and Ladders)");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "12506",
		);
		$this->db->where('external_game_id', "Tomb Raider");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10403",
		);
		$this->db->where('external_game_id', "Bush Telegraph 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Sterlingilver 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Megaspin - Break da Bank Again 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10293",
		);
		$this->db->where('external_game_id', "Untamed - Bengal Tiger 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "12737",
		);
		$this->db->where('external_game_id', "Pure Platinum 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Super Zeroes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Granny Prix");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Slam Funk");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Freezing Fuzzballs");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Golden Ghouls");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Wild Champions");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Dawn of the Bread");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Mumbai Magic");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Cashapillar");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Halloweenies");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Card Selector - Big Break");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Summertime 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Stash of the Titans 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11173",
		);
		$this->db->where('external_game_id', "The Grand Journey");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Jurassic Big Reels");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Tomb Raide");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Alaskan Fishing 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Curry in a Hurry");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Gift Rap 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "15008",
		);
		$this->db->where('external_game_id', "Fruit Fiesta 5 Reel");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11092",
		);
		$this->db->where('external_game_id', "Tigers Eye");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12506",
		);
		$this->db->where('external_game_id', "Mermaids Millions");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Diamond Sevens");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11019",
		);
		$this->db->where('external_game_id', "Liquid Gold");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Cashapillar 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Karate Pig 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "12510",
		);
		$this->db->where('external_game_id', "Wheel Of Wealth");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Tally Ho 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "10011",
		);
		$this->db->where('external_game_id', "Lots of Loot");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Cash Splash Progressive");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Kathmandu 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Lucky Witch 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Isis 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Jolly Jester 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12817",
		);
		$this->db->where('external_game_id', "Eagle's Wings");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "12535",
		);
		$this->db->where('external_game_id', "Bars & Stripes");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Free Spirit");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10001",
			'moduleid' => "85",
		);
		$this->db->where('external_game_id', "Double Double Bonus");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "10072",
		);
		$this->db->where('external_game_id', "Buffet Bonanza 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Get Rocked 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Path of the Penguin");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Reel Gems 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Wealthpa 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Bars & Stripes 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "12630",
		);
		$this->db->where('external_game_id', "Alley Cats 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "CashOccino 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Big Top 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Crazy Chameleons 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "10002",
			'moduleid' => "41",
		);
		$this->db->where('external_game_id', "Genies Gems");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Thunderstruck 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Hot Shot 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Nutty Squirrel");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Quest for Beer 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Mystic Dreams 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Celtic Crown 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Love Potion 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Lions Pride 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Dance of the Masai 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "The Great Galaxy Grab 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "10381",
		);
		$this->db->where('external_game_id', "Dragons Myth");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "40300",
			'moduleid' => "11145",
		);
		$this->db->where('external_game_id', "Voila");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "1",
			'moduleid' => "12754",
		);
		$this->db->where('external_game_id', "Kings of Cash 90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "6",
			'moduleid' => "15",
		);
		$this->db->where('external_game_id', "One Arm Bandit");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "2",
			'moduleid' => "12531",
		);
		$this->db->where('external_game_id', "Dog Father");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

		$data = array(
			'clientid' => "null",
			'moduleid' => "null",
		);
		$this->db->where('external_game_id', "Franken Cash  90");
		$this->db->where('game_platform_id', 6);
		$this->db->update('game_description', $data);

	}

	public function down() {
//         $sql = <<<EOD
		// UPDATE game_description SET clientid = null, moduleid = null;
		// EOD;
		//         $this->db->query($sql);
	}
}