<?php
require_once dirname(__FILE__) . '/abstract_game_api.php';

/**
	* API NAME: Operator API Integration Guide
	* Document Number: SGS-GO-API v.1.2.5


	*
	* @category Game_platform
	* @version 1.8.10
	* @copyright 2013-2016 TripleOneTech
**/

class game_api_suncity extends Abstract_game_api {

	const TRANSFER_IN = 'IN';
	const TRANSFER_OUT = 'OUT';
	const POST = "POST";
	const GET = "GET";

	const PLAY_TYPES = [
		'baBank' => ['EN' => 'Banker', 'CN' => '庄'],
		'baDeal' => ['EN' => 'Player', 'CN' => '闲'],
		'baTie'  => ['EN' => 'Tie', 'CN' => '和'],
		'baPPa'  => ['EN' => 'Play Pair', 'CN' => '闲对'],
		'baBPa'  => ['EN' => 'Bank Pair', 'CN' => '庄对'],
		'baBig'  => ['EN' => 'Big', 'CN' => '大'],
		'baSm'   => ['EN' => 'Small', 'CN' => '小'],
		'baPOdd' => ['EN' => 'Player Odds', 'CN' => '闲单'],
		'baPEvn' => ['EN' => 'Player Even', 'CN' => '闲双'],
		'baBOdd' => ['EN' => 'Banker Odds', 'CN' => '庄单'],
		'baBEvn' => ['EN' => 'Banker Even', 'CN' => '庄双'],
		'BaBSix' => ['EN' => 'Super6 Banker', 'CN' => '超級六庄'],
		'BaSix'  => ['EN' => 'Super 6', 'CN' => '超級六'],
		'baJp'   => ['EN' => 'Jackpot', 'CN' => 'Jackpot'],
		'BpBank' => ['EN' => 'Banker', 'CN' => '庄'],
		'BpDeal' => ['EN' => 'Player', 'CN' => '闲'],
		'BpTie'  => ['EN' => 'Tie', 'CN' => '和'],
		'BpBPa'  => ['EN' => 'Bank Pair ', 'CN' => '庄对'],
		'BpPPa'  => ['EN' => 'Play Pair', 'CN' => '闲对'],
		'RoSi88' => ['EN' => 'Straight 0', 'CN' => '单号 0'],
		'RoSi01' => ['EN' => 'Straight 1', 'CN' => '单号 1'],
		'RoSi02' => ['EN' => 'Straight 2', 'CN' => '单号 2'],
		'RoSi03' => ['EN' => 'Straight 3', 'CN' => '单号 3'],
		'RoSi04' => ['EN' => 'Straight 4', 'CN' => '单号 4'],
		'RoSi05' => ['EN' => 'Straight 5', 'CN' => '单号 5'],
		'RoSi06' => ['EN' => 'Straight 6', 'CN' => '单号 6'],
		'RoSi07' => ['EN' => 'Straight 7', 'CN' => '单号 7'],
		'RoSi08' => ['EN' => 'Straight 8', 'CN' => '单号 8'],
		'RoSi09' => ['EN' => 'Straight 9', 'CN' => '单号 9'],
		'RoSi10' => ['EN' => 'Straight 10', 'CN' => '单号 10'],
		'RoSi11' => ['EN' => 'Straight 11', 'CN' => '单号 11'],
		'RoSi12' => ['EN' => 'Straight 12', 'CN' => '单号 12'],
		'RoSi13' => ['EN' => 'Straight 13', 'CN' => '单号 13'],
		'RoSi14' => ['EN' => 'Straight 14', 'CN' => '单号 14'],
		'RoSi15' => ['EN' => 'Straight 15', 'CN' => '单号 15'],
		'RoSi16' => ['EN' => 'Straight 16', 'CN' => '单号 16'],
		'RoSi17' => ['EN' => 'Straight 17', 'CN' => '单号 17'],
		'RoSi18' => ['EN' => 'Straight 18', 'CN' => '单号 18'],
		'RoSi19' => ['EN' => 'Straight 19', 'CN' => '单号 19'],
		'RoSi20' => ['EN' => 'Straight 20', 'CN' => '单号 20'],
		'RoSi21' => ['EN' => 'Straight 21', 'CN' => '单号 21'],
		'RoSi22' => ['EN' => 'Straight 22', 'CN' => '单号 22'],
		'RoSi23' => ['EN' => 'Straight 23', 'CN' => '单号 23'],
		'RoSi24' => ['EN' => 'Straight 24', 'CN' => '单号 24'],
		'RoSi25' => ['EN' => 'Straight 25', 'CN' => '单号 25'],
		'RoSi26' => ['EN' => 'Straight 26', 'CN' => '单号 26'],
		'RoSi27' => ['EN' => 'Straight 27', 'CN' => '单号 27'],
		'RoSi28' => ['EN' => 'Straight 28', 'CN' => '单号 28'],
		'RoSi29' => ['EN' => 'Straight 29', 'CN' => '单号 29'],
		'RoSi30' => ['EN' => 'Straight 30', 'CN' => '单号 30'],
		'RoSi31' => ['EN' => 'Straight 31', 'CN' => '单号 31'],
		'RoSi32' => ['EN' => 'Straight 32', 'CN' => '单号 32'],
		'RoSi33' => ['EN' => 'Straight 33', 'CN' => '单号 33'],
		'RoSi34' => ['EN' => 'Straight 34', 'CN' => '单号 34'],
		'RoSi35' => ['EN' => 'Straight 35', 'CN' => '单号 35'],
		'RoSi36' => ['EN' => 'Straight 36', 'CN' => '单号 36'],
		'RoDu_1' => ['EN' => 'Split (0,1)', 'CN' => '双号 0,1'],
		'RoDu_2' => ['EN' => 'Split (0,2)', 'CN' => '双号 0,2'],
		'RoDu_3' => ['EN' => 'Split (0,3)', 'CN' => '双号 0,3'],
		'RoDu12' => ['EN' => 'Split (1,2)', 'CN' => '双号 1,2'],
		'RoDu45' => ['EN' => 'Split (4,5)', 'CN' => '双号 4,5'],
		'RoDu78' => ['EN' => 'Split (7,8)', 'CN' => '双号 7,8'],
		'RoDu0a' => ['EN' => 'Split (10,11)', 'CN' => '双号 10,11'],
		'RoDucd' => ['EN' => 'Split (13,14)', 'CN' => '双号 13,14'],
		'RoDufg' => ['EN' => 'Split (16,17)', 'CN' => '双号 16,17'],
		'RoDuij' => ['EN' => 'Split (19,20)', 'CN' => '双号 19,20'],
		'RoDulm' => ['EN' => 'Split (22,23)', 'CN' => '双号 22,23'],
		'RoDuop' => ['EN' => 'Split (25,26)', 'CN' => '双号 25,26'],
		'RoDurs' => ['EN' => 'Split (28,29)', 'CN' => '双号 28,29'],
		'RoDuuv' => ['EN' => 'Split (31,32)', 'CN' => '双号 31,32'],
		'RoDuxy' => ['EN' => 'Split (34,35)', 'CN' => '双号 34,35'],
		'RoDu23' => ['EN' => 'Split (2,3)', 'CN' => '双号 2,3'],
		'RoDu56' => ['EN' => 'Split (5,6)', 'CN' => '双号 5,6'],
		'RoDu89' => ['EN' => 'Split (8,9)', 'CN' => '双号 8,9'],
		'RoDuab' => ['EN' => 'Split (11,12)', 'CN' => '双号 11,12'],
		'RoDude' => ['EN' => 'Split (14,15)', 'CN' => '双号 14,15'],
		'RoDugh' => ['EN' => 'Split (17,18)', 'CN' => '双号 17,18'],
		'RoDujk' => ['EN' => 'Split (20,21)', 'CN' => '双号 20,21'],
		'RoDumn' => ['EN' => 'Split (23,24)', 'CN' => '双号 23,24'],
		'RoDupq' => ['EN' => 'Split (26,27)', 'CN' => '双号 26,27'],
		'RoDust' => ['EN' => 'Split (29,30)', 'CN' => '双号 29,30'],
		'RoDuvw' => ['EN' => 'Split (32,33)', 'CN' => '双号 32,33'],
		'RoDuyz' => ['EN' => 'Split (35,36)', 'CN' => '双号 35,36'],
		'RoT_12' => ['EN' => 'Trio (0,1,2)', 'CN' => '三号 0,1,2'],
		'RoT_23' => ['EN' => 'Trio (0,2,3)', 'CN' => '三号 0,2,3'],
		'RoT123' => ['EN' => 'Street (1,2,3)', 'CN' => '三号 1,2,3'],
		'RoT456' => ['EN' => 'Street (4,5,6)', 'CN' => '三号 4,5,6'],
		'RoT789' => ['EN' => 'Street (7,8,9)', 'CN' => '三号 7,8,9'],
		'RoT0ab' => ['EN' => 'Street (10,11,12)', 'CN' => '三号 10,11,12'],
		'RoTcde' => ['EN' => 'Street (13,14,15)', 'CN' => '三号 13,14,15'],
		'RoTfgh' => ['EN' => 'Street (16,17,18)', 'CN' => '三号 16,17,18'],
		'RoTijk' => ['EN' => 'Street (19,20,21)', 'CN' => '三号 19,20,21'],
		'RoTlmn' => ['EN' => 'Street (22,23,24)', 'CN' => '三号 22,34,24'],
		'RoTopq' => ['EN' => 'Street (25,26,27)', 'CN' => '三号 25,26,27'],
		'RoTrst' => ['EN' => 'Street (28,29,30) ', 'CN' => '三号 28,29,30'],
		'RoTuvw' => ['EN' => 'Street (31,32,33)', 'CN' => '三号 31,32,33'],
		'RoTxyz' => ['EN' => 'Street (34,35,36)', 'CN' => '三号 34,35,36'],
		'Ro_123' => ['EN' => 'First Four', 'CN' => '四号 0,1,2,3'],
		'Ro4578' => ['EN' => 'Corner (4,5,7,8) ', 'CN' => '四号 4,5,7,8'],
		'Ro780a' => ['EN' => 'Corner (7,8,10,11)', 'CN' => '四号 7,8,10,11'],
		'Ro0acd' => ['EN' => 'Corner (10,11,13,14)', 'CN' => '四号 10,11,13,14'],
		'Rocdfg' => ['EN' => 'Corner (13,14,16,17)', 'CN' => '四号 13,14,16,17'],
		'Rofgij' => ['EN' => 'Corner (16,17,19,20)', 'CN' => '四号 16,17,19,20'],
		'Roijlm' => ['EN' => 'Corner (19,20,22,23)', 'CN' => '四号 19,20,22,23'],
		'Rolmop' => ['EN' => 'Corner (22,23,25,26)', 'CN' => '四号 22,23,25,26'],
		'Rooprs' => ['EN' => 'Corner (25,26,28,29)', 'CN' => '四号 25,26,28,29'],
		'Rorsuv' => ['EN' => 'Corner (28,29,31,32)', 'CN' => '四号 28,29,31,32'],
		'Rouvxy' => ['EN' => 'Corner (31,32,34,35)', 'CN' => '四号 31,32,34,35'],
		'Ro1245' => ['EN' => 'Corner (1,2,4,5) ', 'CN' => '四号 1,2,4,5'],
		'Ro890a' => ['EN' => 'Corner (8,9,10,11)', 'CN' => '四号 8,9,10,11'],
		'Ro2356' => ['EN' => 'Corner (2,3,5,6) ', 'CN' => '四号 2,3,5,6'],
		'Ro5689' => ['EN' => 'Corner (5,6,8,9) ', 'CN' => '四号 5,6,8,9'],
		'Ro89ab' => ['EN' => 'Corner (8,9,11,12)', 'CN' => '四号 8,9,11,12'],
		'Roabde' => ['EN' => 'Corner (11,12,14,15)', 'CN' => '四号 11,12,14,15'],
		'Rodegh' => ['EN' => 'Corner (14,15,17,18)', 'CN' => '四号 14,15,17,18'],
		'Roghjk' => ['EN' => 'Corner (17,18,20,21)', 'CN' => '四号 17,18,20,21'],
		'Rojkmn' => ['EN' => 'Corner (20,21,23,24)', 'CN' => '四号 20,21,23,24'],
		'Romnpq' => ['EN' => 'Corner (23,24,26,27)', 'CN' => '四号 23,24,26,27'],
		'Ropqst' => ['EN' => 'Corner (26,27,29,30)', 'CN' => '四号 26,27,29,30'],
		'Rostvw' => ['EN' => 'Corner (29,30,32,33)', 'CN' => '四号 29,30,32,33'],
		'Rovwyz' => ['EN' => 'Corner (32,33,35,36)', 'CN' => '四号 32,33,35,36'],
		'RoCol0' => ['EN' => 'Line (1- 6)', 'CN' => '六号 1,2,3,4,5,6'],
		'RoCol1' => ['EN' => 'Line (4- 9)', 'CN' => '六号 4,5,6,7,8,9'],
		'RoCol2' => ['EN' => 'Line (7- 12)', 'CN' => '六号 7,8,9,10,11,12'],
		'RoCol3' => ['EN' => 'Line (10-15)', 'CN' => '六号 10,11,12,13,14,15'],
		'RoCol4' => ['EN' => 'Line (13-18)', 'CN' => '六号 13,14,15,16,17,18'],
		'RoCol5' => ['EN' => 'Line (16-21)', 'CN' => '六号 16,17,18,19,20,21'],
		'RoCol6' => ['EN' => 'Line (19-24)', 'CN' => '六号 19,20,21,22,23,24'],
		'RoCol7' => ['EN' => 'Line (22-27)', 'CN' => '六号 22,23,24,25,26,27'],
		'RoCol8' => ['EN' => 'Line (25-30)', 'CN' => '六号25,26,27,28,29,30'],
		'RoCol9' => ['EN' => 'Line (28-33)', 'CN' => '六号28,29,30,31,32,33'],
		'RoCola' => ['EN' => 'Line (31-36)', 'CN' => '六号31,32,33,34,35,36'],
		'Ro12s1' => ['EN' => '1st12', 'CN' => '组1'],
		'Ro12s2' => ['EN' => '2nd12', 'CN' => '组2'],
		'Ro12s3' => ['EN' => '3rd12', 'CN' => '组3'],
		'RoRow1' => ['EN' => '2:1(1st)', 'CN' => '列1'],
		'RoRow2' => ['EN' => '2:1(2nd) ', 'CN' => '列2'],
		'RoRow3' => ['EN' => '2:1(3rd) ', 'CN' => '列3'],
		'RoOdd'  => ['EN' => 'Odd', 'CN' => '单'],
		'RoEven' => ['EN' => 'Even', 'CN' => '双'],
		'RoRed'  => ['EN' => 'Red', 'CN' => '红色'],
		'RoBak'  => ['EN' => 'Black', 'CN' => '黑色'],
		'RoBig'  => ['EN' => 'Big (19-36) ', 'CN' => '大'],
		'RoSm'   => ['EN' => 'Small (1-18) ', 'CN' => '小'],
		'DiBig'  => ['EN' => 'Big', 'CN' => '大'],
		'DiSm'   => ['EN' => 'Small', 'CN' => '小'],
		'DiOdd'  => ['EN' => 'Odd', 'CN' => '单'],
		'DiEven' => ['EN' => 'Even', 'CN' => '双'],
		'DiTri0' => ['EN' => 'Any Triple', 'CN' => '围骰'],
		'DiTri1' => ['EN' => 'Triple (1)', 'CN' => '围骰 1'],
		'DiTri2' => ['EN' => 'Triple (2)', 'CN' => '围骰 2'],
		'DiTri3' => ['EN' => 'Triple (3)', 'CN' => '围骰 3'],
		'DiTri4' => ['EN' => 'Triple (4)', 'CN' => '围骰 4'],
		'DiTri5' => ['EN' => 'Triple (5)', 'CN' => '围骰 5'],
		'DiTri6' => ['EN' => 'Triple (6)', 'CN' => '围骰 6'],
		'DiDou1' => ['EN' => 'Double (1)', 'CN' => '逢双 1'],
		'DiDou2' => ['EN' => 'Double (2)', 'CN' => '逢双 2'],
		'DiDou3' => ['EN' => 'Double (3)', 'CN' => '逢双 3'],
		'DiDou4' => ['EN' => 'Double (4)', 'CN' => '逢双 4'],
		'DiDou5' => ['EN' => 'Double (5)', 'CN' => '逢双 5'],
		'DiDou6' => ['EN' => 'Double (6)', 'CN' => '逢双 6'],
		'DiOne1' => ['EN' => 'Number (1)', 'CN' => '骰点 1'],
		'DiOne2' => ['EN' => 'Number (2)', 'CN' => '骰点 2'],
		'DiOne3' => ['EN' => 'Number (3)', 'CN' => '骰点 3'],
		'DiOne4' => ['EN' => 'Number (4)', 'CN' => '骰点 4'],
		'DiOne5' => ['EN' => 'Number (5)', 'CN' => '骰点 5'],
		'DiOne6' => ['EN' => 'Number (6)', 'CN' => '骰点 6'],
		'DiTo04' => ['EN' => 'Total (4)', 'CN' => '点数 4'],
		'DiTo05' => ['EN' => 'Total (5)', 'CN' => '点数 5'],
		'DiTo06' => ['EN' => 'Total (6)', 'CN' => '点数 6'],
		'DiTo07' => ['EN' => 'Total (7)', 'CN' => '点数 7'],
		'DiTo08' => ['EN' => 'Total (8)', 'CN' => '点数 8'],
		'DiTo09' => ['EN' => 'Total (9)', 'CN' => '点数 9'],
		'DiTo10' => ['EN' => 'Total (10)', 'CN' => '点数 10'],
		'DiTo11' => ['EN' => 'Total (11)', 'CN' => '点数 11'],
		'DiTo12' => ['EN' => 'Total (12)', 'CN' => '点数 12'],
		'DiTo13' => ['EN' => 'Total (13)', 'CN' => '点数 13'],
		'DiTo14' => ['EN' => 'Total (14)', 'CN' => '点数 14'],
		'DiTo15' => ['EN' => 'Total (15)', 'CN' => '点数 15'],
		'DiTo16' => ['EN' => 'Total (16)', 'CN' => '点数 16'],
		'DiTo17' => ['EN' => 'Total (17)', 'CN' => '点数 17'],
		'DiTw12' => ['EN' => 'Pair (1, 2)', 'CN' => '骰点 1, 2'],
		'DiTw13' => ['EN' => 'Pair (1, 3)', 'CN' => '骰点 1, 3'],
		'DiTw14' => ['EN' => 'Pair (1, 4)', 'CN' => '骰点 1, 4'],
		'DiTw15' => ['EN' => 'Pair (1, 5)', 'CN' => '骰点 1, 5'],
		'DiTw16' => ['EN' => 'Pair (1, 6)', 'CN' => '骰点 1, 6'],
		'DiTw23' => ['EN' => 'Pair (2, 3)', 'CN' => '骰点 2, 3'],
		'DiTw24' => ['EN' => 'Pair (2, 4)', 'CN' => '骰点 2, 4'],
		'DiTw25' => ['EN' => 'Pair (2, 5)', 'CN' => '骰点 2, 5'],
		'DiTw26' => ['EN' => 'Pair (2, 6)', 'CN' => '骰点 2, 6'],
		'DiTw34' => ['EN' => 'Pair (3, 4)', 'CN' => '骰点 3, 4'],
		'DiTw35' => ['EN' => 'Pair (3, 5)', 'CN' => '骰点 3, 5'],
		'DiTw36' => ['EN' => 'Pair (3, 6)', 'CN' => '骰点 3, 6'],
		'DiTw45' => ['EN' => 'Pair (4, 5)', 'CN' => '骰点 4, 5'],
		'DiTw46' => ['EN' => 'Pair (4, 6)', 'CN' => '骰点 4, 6'],
		'DiTw56' => ['EN' => 'Pair (5, 6)', 'CN' => '骰点 5, 6'],
		'DtDrag' => ['EN' => 'Dragon', 'CN' => '龙'],
		'DtTie'  => ['EN' => 'Tie', 'CN' => '和'],
		'DtTigr' => ['EN' => 'Tiger', 'CN' => '虎'],
		'CoB2Pa' => ['EN' => 'Bank 2 Pair', 'CN' => '庄两对'],
		'CoB3Ki' => ['EN' => 'Bank 3 Kind', 'CN' => '庄三条'],
		'CoBank' => ['EN' => 'Banker', 'CN' => '庄'],
		'CoBaS2' => ['EN' => 'Banker Security 2', 'CN' => '庄押金 2'],
		'CoBaS3' => ['EN' => 'Banker Security 3', 'CN' => '庄押金 3'],
		'CoDeal' => ['EN' => 'Player', 'CN' => '闲'],
		'CoDeS2' => ['EN' => 'Player Security 2', 'CN' => '闲押金 2'],
		'CoDeS3' => ['EN' => 'Player Security 3', 'CN' => '闲押金 3'],
		'CoP2Pa' => ['EN' => 'Play 2 Pair', 'CN' => '闲两对'],
		'CoP3Ki' => ['EN' => 'Play 3 Kind', 'CN' => '闲三条'],
		'MjC1'   => ['EN' => 'Player Sit 1', 'CN' => '闲门号 1'],
		'MjC2'   => ['EN' => 'Player Sit 2', 'CN' => '闲门号 2'],
		'MjC3'   => ['EN' => 'Player Sit 3', 'CN' => '闲门号 3'],
		'MjC4'   => ['EN' => 'Player Sit 4', 'CN' => '闲门号 4'],
		'MjC5'   => ['EN' => 'Player Sit 5', 'CN' => '闲门号 5'],
		'MjC6'   => ['EN' => 'Player Sit 6', 'CN' => '闲门号 6'],
		'3kP12'  => ['EN' => 'BPS12', 'CN' => '庄闲門12'],
		'3kP13'  => ['EN' => 'BPS13', 'CN' => '庄闲門13'],
		'3kP14'  => ['EN' => 'BPS14', 'CN' => '庄闲門14'],
		'3kP15'  => ['EN' => 'BPS15', 'CN' => '庄闲門15'],
		'3kP16'  => ['EN' => 'BPS16', 'CN' => '庄闲門16'],
		'3kP21'  => ['EN' => 'BPS21', 'CN' => '庄闲門21'],
		'3kP23'  => ['EN' => 'BPS23', 'CN' => '庄闲門23'],
		'3kP24'  => ['EN' => 'BPS24', 'CN' => '庄闲門24'],
		'3kP25'  => ['EN' => 'BPS25', 'CN' => '庄闲門25'],
		'3kP26'  => ['EN' => 'BPS26', 'CN' => '庄闲門26'],
		'3kP31'  => ['EN' => 'BPS31', 'CN' => '庄闲門31'],
		'3kP32'  => ['EN' => 'BPS32', 'CN' => '庄闲門32'],
		'3kP34'  => ['EN' => 'BPS34', 'CN' => '庄闲門34'],
		'3kP35'  => ['EN' => 'BPS35', 'CN' => '庄闲門35'],
		'3kP36'  => ['EN' => 'BPS36', 'CN' => '庄闲門36'],
		'3kP41'  => ['EN' => 'BPS41', 'CN' => '庄闲門41'],
		'3kP42'  => ['EN' => 'BPS42', 'CN' => '庄闲門42'],
		'3kP43'  => ['EN' => 'BPS43', 'CN' => '庄闲門43'],
		'3kP45'  => ['EN' => 'BPS45', 'CN' => '庄闲門45'],
		'3kP46'  => ['EN' => 'BPS46', 'CN' => '庄闲門46'],
		'3kP51'  => ['EN' => 'BPS51', 'CN' => '庄闲門51'],
		'3kP52'  => ['EN' => 'BPS52', 'CN' => '庄闲門52'],
		'3kP53'  => ['EN' => 'BPS53', 'CN' => '庄闲門53'],
		'3kP54'  => ['EN' => 'BPS54', 'CN' => '庄闲門54'],
		'3kP56'  => ['EN' => 'BPS56', 'CN' => '庄闲門56'],
		'3kP61'  => ['EN' => 'BPS61', 'CN' => '庄闲門61'],
		'3kP62'  => ['EN' => 'BPS62', 'CN' => '庄闲門62'],
		'3kP63'  => ['EN' => 'BPS63', 'CN' => '庄闲門63'],
		'3kP64'  => ['EN' => 'BPS64', 'CN' => '庄闲門64'],
		'3kP65'  => ['EN' => 'BPS65', 'CN' => '庄闲門65'],
	];

	const URI_MAP = array(
		self::API_createPlayer => 'api/player/authorize',
		self::API_login => 'api/player/authorize',
		self::API_logout => 'api/player/deauthorize',
		self::API_queryPlayerBalance => 'api/player/balance',
		self::API_isPlayerExist => 'api/player/balance',
        self::API_depositToGame => 'api/wallet/credit',
        self::API_withdrawFromGame => 'api/wallet/debit',
		self::API_syncGameRecords => 'api/report/bethistory',
		self::API_queryGameRecords => 'api/report/ProviderGameHistory'
	);

	public function __construct() {
		parent::__construct();
		$this->api_url = $this->getSystemInfo('url');
		$this->currency = $this->getSystemInfo('currency');
		$this->language = $this->getSystemInfo('language');
		$this->clientId = $this->getSystemInfo('clientId');
		$this->betlimitid = $this->getSystemInfo('betlimitid');
		$this->clientSecret = $this->getSystemInfo('clientSecret');
		$this->game_url = $this->getSystemInfo('game_url');
		$this->method = "POST"; # default as POST
        $this->agent_name = $this->getSystemInfo('agent_name');
        $this->api_key = $this->getSystemInfo('api_key');
        $this->update_original = $this->getSystemInfo('update_original_logs');
        $this->display_betid_as_round = $this->getSystemInfo('display_betid_as_round',false);
        $this->convert_match_type_game_logs = $this->getSystemInfo('convert_match_type_game_logs', false);
        $this->convert_match_type_game_logs_lang = $this->getSystemInfo('convert_match_type_game_logs_lang', 'EN');
	}

	public function getPlatformCode() {
		return SUNCITY_API;
	}

	public function getHttpHeaders($params){
		$current_utc = gmdate("Y-m-d\TH:i:s\Z");
		$stringToSign = $this->clientSecret.$current_utc;
		$signature = base64_encode(hash_hmac('sha1', utf8_encode($stringToSign), utf8_encode($this->clientSecret),true));
		$authorization = "SGS ".$this->clientId.':'.$signature;

		$headers = array(
			"Accept" => "application/json",
			"Content-Type" => "application/json",
			"Authorization" => $authorization,
			"X-Sgs-Date" => $current_utc,
		);

		return $headers;
	}

	protected function isErrorCode($apiName, $params, $statusCode, $errCode, $error) {
        return $errCode || intval($statusCode, 10) >= 404;
    }

	protected function customHttpCall($ch, $params) {
		if($this->method == self::POST){
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params,true));
		}
	}

	public function generateUrl($apiName, $params) {
		$apiUri = self::URI_MAP[$apiName];
		$url = $this->api_url.$apiUri;
		if($this->method == self::GET){
			$url = $url.'?'.http_build_query($params);
		}
		// echo $url;
		return $url;
	}

	public function processResultBoolean($responseResultId, $resultArr, $playerName = null) {
		$success = false;
		if((isset($resultArr['err']) && $resultArr['err']==null) || !array_key_exists('err', $resultArr)){
			$success = true;
		}

		if (!$success) {
			$this->setResponseResultToError($responseResultId);
			$this->CI->utils->debug_log('Suncity Casino got error ', $responseResultId, 'playerName', $playerName, 'result', $resultArr);
		}
		return $success;
	}
    
	public function createPlayer($playerName, $playerId, $password, $email = null, $extra = []) {
        $this->method = self::POST;
		parent::createPlayer($playerName, $playerId, $password, $email, $extra);
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		if (array_key_exists('is_mobile', $extra)){
            if ($extra['is_mobile']) {
                $platformtype = 1;
            } else {
                $platformtype = 0;
            }
        } else {
            $platformtype = 0;
        }
		$is_demo = false;
		if(isset($extra['is_demo_flag'])&&$extra['is_demo_flag']==true){
			$is_demo = true;
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForCreatePlayer',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername,
			'is_demo_flag' => $is_demo,
			'playerId' => $playerId,
		);

		$key = md5($this->agent_name.$this->api_key);

		$params = array(
			'ipaddress' => $this->CI->input->ip_address(),
			'username' => $gameUsername,
			'userid' => $gameUsername,
			# 'tag' => array(), // used to include metadata about the player. This field may be included in player reports.
			'lang' => $this->language,//$gameUsername,
			'cur' => $this->currency,
			'betlimitid' => $this->betlimitid,#'1', # 1 Bronze - basic limits, 2 Silver - upgraded limits, 3 Gold - high limits, 4 platinum VIP, 5 Diamond - VVIP limits
			'istestplayer' => $is_demo, # boolean
			'platformtype' => $platformtype, #interger 0 desktop, 1 mobile (OGP-10807)
		);

		return $this->callApi(self::API_createPlayer, $params, $context);
	}

	public function processResultForCreatePlayer($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$is_demo_flag = $this->getVariableFromContext($params, 'is_demo_flag');
		$playerId = $this->getVariableFromContext($params, 'playerId');

		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);

		if($success){
			# update flag to registered = truer
        	$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE); 
		}

		$result = array(
			"player" => $gameUsername,
			"exists" => true
		);

		return array($success, $result);
	}

	public function getLauncherLanguage($language){
        $lang='';
        switch ($language) {
        	case 1:
            case 'en-us':
                $lang = 'en-US'; // english
                break;
            case 2:
            case 'zh-cn':
                $lang = 'zh-CN'; // chinese
                break;
            case 3:
            case 'id-id':
                $lang = 'id-ID'; // indonesia
                break;
            case 4:
            case 'vi-vn':
                $lang = 'vi-VN'; // vietnamese
                break;
            case 5:
            case 'ko-kr':
                $lang = 'ko-KR'; // korean
                break;
			case 6:
			case 'th-th':
			case 'th-TH':
				$lang = 'th-TH'; // thai
				break;
            default:
                $lang = 'en-US'; // default as english
                break;
        }
        return $lang;
    }

    public function login($playerName, $password = null, $extra = null) {
        $this->CI->load->model('game_provider_auth');
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $is_demo_account = $this->CI->game_provider_auth->isGameAccountDemoAccount($gameUsername, $this->getPlatformCode());
		if (array_key_exists('is_mobile', $extra)){
            if ($extra['is_mobile']) {
                $platformtype = 1;
            } else {
                $platformtype = 0;
            }
        } else {
            $platformtype = 0;
        }
		$is_demo = false;
		if((isset($extra['is_demo_flag'])&&$extra['is_demo_flag'])||(isset($extra['game_mode'])&&$extra['game_mode']!='real')){
			$is_demo = true;
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogin',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername
		);

		$params = array(
			'ipaddress' => $this->CI->input->ip_address(),
			'username' => $gameUsername,
			'userid' => $gameUsername,
			# 'tag' => array(), // used to include metadata about the player. This field may be included in player reports.
			'lang' => !empty($this->language) ? $this->language :   $this->getLauncherLanguage($extra['language']),//$gameUsername,
			'cur' => $this->currency,
			'betlimitid' => $this->betlimitid, # 1 Bronze - basic limits, 2 Silver - upgraded limits, 3 Gold - high limits, 4 platinum VIP, 5 Diamond - VVIP limits
			'istestplayer' => $is_demo_account, # true is player is demo account
			'platformtype' => $platformtype, #interger 0 desktop, 1 mobile (OGP-10807)
		);
        $this->method = self::POST;
		return $this->callApi(self::API_login, $params, $context);
	}

	public function processResultForLogin($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);

		return array($success, $resultArr);
	}

	public function logout($playerName, $password = null) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForLogout',
			'playerName' => $playerName,
			'gameUsername' => $gameUsername
		);

		$params = array(
			'userid' => $gameUsername
		);
        $this->method = self::POST;

		return $this->callApi(self::API_logout, $params, $context);
	}

	public function processResultForLogout($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);

		return array($success, $resultArr);
	}

	public function queryPlayerBalance($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForQueryPlayerBalance', 
			'playerName' => $playerName,
			'gameUsername' => $gameUsername
		);

		$params = array(
			'userid' => $gameUsername,
			'cur' => $this->currency,
		);
		$this->method = self::GET;

		return $this->callApi(self::API_queryPlayerBalance, $params, $context);
	}

	public function processResultForQueryPlayerBalance($params) {
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultArr = $this->getResultJsonFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);

		$result = array();
		if($success){
			$result['balance'] = @floatval($resultArr['bal']);
		}

		return array($success, $result);

	}

	public function isPlayerExist($playerName,$extra=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
		if($extra['is_demo_flag']){
			return $this->getExternalAccountIdByPlayerUsername($playerName);
		}

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForIsPlayerExist', 
			'playerName' => $playerName,
			'gameUsername' => $gameUsername
		);

        $params = array(
            'userid' => $gameUsername,
            'cur' => $this->currency,
        );
        $this->method = self::GET;

        return $this->callApi(self::API_isPlayerExist, $params, $context);
    }

    public function processResultForIsPlayerExist($params){

        $responseResultId = $this->getResponseResultIdFromParams($params);
      	$resultArr = $this->getResultJsonFromParams($params);
        $gameUsername = $this->getVariableFromContext($params, 'gameUsername');
        $playerName = $this->getVariableFromContext($params, 'playerName');
        $success = $this->processResultBoolean($responseResultId, $resultArr, $gameUsername);
        $playerId = $this->getPlayerIdInPlayer($playerName);

        if(empty($resultArr)){
        	$success = false;
        	$result = array('exists' => null);
        }else{
        	$success = true;
	        if ((isset($resultArr['err']) && $resultArr['err']=="") || !array_key_exists('err', $resultArr)) {
	        	$result = array('exists' => true); 
	        	# update flag to registered = true
	        	$this->updateRegisterFlag($playerId, Abstract_game_api::FLAG_TRUE); 
	        }else if(isset($resultArr['err']) && $resultArr['err']=="600"){
	            $result = array('exists' => false); # Player not found
	        }else{
	        	$result = array('exists' => null);
	        }
	    }

        return array($success, $result);
    }

	public function blockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->blockUsernameInDB($gameUsername);
        return array("success" => true);
    }

    public function unblockPlayer($playerName) {
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $success = $this->unblockUsernameInDB($gameUsername);
        return array("success" => true);
    }

	public function batchQueryPlayerBalance($playerNames, $syncId = null) {

        if (empty($playerNames)) {
            $playerNames = $this->getAllGameUsernames();
        }

        return $this->batchQueryPlayerBalanceOneByOne($playerNames, $syncId);

    }

	public function depositToGame($playerName, $amount, $transfer_secure_id=null){
		$type = self::TRANSFER_IN;
		return $this->transferCredit($playerName, $amount, $type, $transfer_secure_id);
	}

	public function withdrawFromGame($playerName, $amount, $transfer_secure_id=null){
		$type = self::TRANSFER_OUT;
		return $this->transferCredit($playerName, $amount, $type, $transfer_secure_id);
	}

	public function transferCredit($playerName, $amount, $type, $transfer_secure_id=null){
		$gameUsername = $this->getGameUsernameByPlayerUsername($playerName);

		$txid = $type.$gameUsername.date('YmdHis');

        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForTransferCredit',
            'gameUsername' => $gameUsername,
            'playerName' => $playerName,
            'amount' => $amount,
            'type' => $type,
			//'external_transaction_id' => $txid,
        );

		$params = array(
            "userid" => $gameUsername,
            "amt" => $amount,
            "cur" => $this->currency,
            "txid" => $txid,
            "timestamp" => gmdate("Y-m-d\TH:i:s\Z")
		);
		$callApiMethod = $type==self::TRANSFER_OUT?(self::API_withdrawFromGame):(self::API_depositToGame);
        $this->method = self::POST;

		return $this->callApi($callApiMethod, $params, $context);
	}

	public function processResultForTransferCredit($params) {
		//$external_transaction_id = $this->getVariableFromContext($params, 'external_transaction_id');
		$gameUsername = $this->getVariableFromContext($params, 'gameUsername');
		$playerName = $this->getVariableFromContext($params, 'playerName');
		$type = $this->getVariableFromContext($params, 'type');
		$amount = $this->getVariableFromContext($params, 'amount');
		$resultArr = $this->getResultJsonFromParams($params);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$success = $this->processResultBoolean($responseResultId, $resultArr,$playerName);

		$result = array(
			'response_result_id' => $responseResultId,
		//	'external_transaction_id'=>$external_transaction_id,
			'transfer_status'=>self::COMMON_TRANSACTION_STATUS_UNKNOWN,
			'reason_id'=>self::REASON_UNKNOWN
		);

		if ($success) {
            // $playerId = $this->getPlayerIdInGameProviderAuth($gameUsername);
            // if ($playerId) {
            //     if($type == self::TRANSFER_IN){ 
	           //      // Deposit
	           //      $this->insertTransactionToGameLogs($playerId, $gameUsername, null, $amount, $responseResultId,
	           //          $this->transTypeMainWalletToSubWallet());
            //     }else{ 
	           //      // Withdraw
	           //      $this->insertTransactionToGameLogs($playerId, $gameUsername, null, $amount, $responseResultId,
	           //          $this->transTypeSubWalletToMainWallet());
            //     }
            // } else {
            //     $this->CI->utils->debug_log('error', '=============== cannot get player id from '.$playerName.' getPlayerIdInGameProviderAuth');
            // }
			$result['transfer_status']=self::COMMON_TRANSACTION_STATUS_APPROVED;
			$result['didnot_insert_game_logs']=true;
        } else {

			$error_code = @$resultArr['err'];
			switch($error_code) {
				case '20' :
					$result['reason_id'] = self::REASON_GAME_ACCOUNT_LOCKED;
					break;
				case '60' :
					$result['reason_id'] = self::REASON_INVALID_TRANSFER_AMOUNT;
					break;
				case '70' :
					$result['reason_id'] = self::REASON_CURRENCY_ERROR;
					break;
				case '100' :
					$result['reason_id'] = self::REASON_NO_ENOUGH_BALANCE;
					break;
				case '103' :
					$result['reason_id'] = self::REASON_AGENT_NOT_EXISTED;
					break;
				case '104' :
					$result['reason_id'] = self::REASON_INVALID_KEY;
					break;
				case '300' :
					$result['reason_id'] = self::REASON_INCOMPLETE_INFORMATION;
					break;
				case '600' :
					$result['reason_id'] = self::REASON_NOT_FOUND_PLAYER;
					break;
				case '900' :
					$result['reason_id'] = self::REASON_INVALID_TRANSACTION_ID;
					break;
				case '999' :
					$result['reason_id'] = self::REASON_GAME_PROVIDER_INTERNAL_PROBLEM;
					break;
			}
			$result['transfer_status'] = self::COMMON_TRANSACTION_STATUS_DECLINED;
		}

        return array($success, $result);

	}

	public function queryForwardGame($playerName, $extra = null) {
		/*
			TGP Red Tiger
			SB Sunbet
			GB Globalbet
			GD Gold Deluxe
			LAX Laxino
			FC Fly Cow
		*/

        $resultArr = $this->login($playerName,null,$extra);
        if($extra['game_code'] == 'lobby'){
        	$game_url = $this->getSystemInfo('game_desktop_lobby_url');
        	if($extra['is_mobile']){ # if mobile 
        		$game_url = $this->getSystemInfo('game_mobile_lobby_url');
        	}
        	$url = $game_url.'?token='.$resultArr['authtoken'];
        } else if ($extra['is_demo_flag'] == true) {
        	$params = array(
	            'gpcode'    => $extra['game_type'],
	            'gcode'     => $extra['game_code'],
	            'lang'     => !empty($this->language) ? $this->language :   $this->getLauncherLanguage($extra['language']),
	        );

	        $params_http = http_build_query($params);
	        $url = $this->game_url.'/demolauncher?'.$params_http;
	        
        } else {
	        $params = array(
	            'gpcode'    => $extra['game_type'],
	            'gcode'     => $extra['game_code'],
	            #'platform'  => $extra['is_mobile'] ? 1 : 0, # Since the platform type is identified in player authorization method, the ‘platform’ parameter is not needed in game launcher method, and will be removed. (OGP-10807)
	            'token'     => $resultArr['authtoken'],
	        );

	        $params_http = http_build_query($params);
	        $url = $this->game_url.'/gamelauncher?'.$params_http;
	    }

        return array("success" =>$resultArr['success'],"url"=>$url);
	}

	public function syncOriginalGameLogs($token = false) {

		$startDate = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$endDate = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		$startDate = new DateTime($this->serverTimeToGameTime($startDate->format('Y-m-d H:i:s')));
    	$endDate = new DateTime($this->serverTimeToGameTime($endDate->format('Y-m-d H:i:s')));
    	$startDate->modify($this->getDatetimeAdjust());

		//observer the date format
		$startDate=$startDate->format('Y-m-d\TH:i:s');
		$endDate=$endDate->format('Y-m-d\TH:i:s');

		$context = array(
			'callback_obj' => $this,
			'callback_method' => 'processResultForSyncOriginalGameLogs',
			'startDate' => $startDate,
			'endDate' => $endDate
		);

		$params = array(
			#'userid' => $playerName, // not required.
			'startdate' => $startDate,
			'enddate' => $endDate,
			'includetestplayers' => false,
			'issettled' => true, // return settled bet only 
		);
		$this->method = self::GET;

		return $this->callApi(self::API_syncGameRecords, $params, $context);

	}

	public function processResultForSyncOriginalGameLogs($params) {
		$this->CI->load->model(array('suncity_game_logs'));
		//$startDate = $this->getVariableFromContext($params, 'startDate');
		//$endDate = $this->getVariableFromContext($params, 'endDate');
		$csvtext = $this->getResultTextFromParams($params);	
		$gameRecords = $this->convertResultCsvFromParams($csvtext);
		$responseResultId = $this->getResponseResultIdFromParams($params);
		//$success = $this->processResultBoolean($responseResultId, $resultArr);

		$success = false;
		if($params['statusCode'] == 200){
			$success = true;
		}

		$dataCount = 0;
        $existUgsBetIds = array();
		if(!empty($gameRecords)&&$success){
			// print_r($gameRecords);
			// exit();
            if(!$this->update_original){
                $gameRecords = $this->CI->suncity_game_logs->getAvailableRows($gameRecords);
            }else{
                $existingRecords = $this->CI->suncity_game_logs->getExistingRows($gameRecords);
                $existUgsBetIds = array_column($existingRecords, 'ugsbetid');
            }
			foreach ($gameRecords as $record) {
				if($record['roundstatus'] != "Closed"){
					continue;
				}
				$insertRecord = array();
				//Data from suncity API
				$insertRecord['ugsbetid'] = isset($record['ugsbetid']) ? $record['ugsbetid'] : NULL;
				$insertRecord['txid'] = isset($record['txid']) ? $record['txid'] : NULL;
				$insertRecord['betid'] = isset($record['betid']) ? $record['betid'] : NULL;
				$insertRecord['beton'] = isset($record['beton']) ?$this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['beton']))) : NULL;
				$insertRecord['betclosedon'] = isset($record['betclosedon']) ?$this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['betclosedon']))) : NULL;
				$insertRecord['betupdatedon'] = isset($record['betupdatedon']) ?$this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['betupdatedon']))) : NULL;
				$insertRecord['timestamp'] = isset($record['timestamp']) ?$this->gameTimeToServerTime(date('Y-m-d H:i:s', strtotime($record['timestamp']))) : NULL;
				$insertRecord['roundid'] = isset($record['roundid']) ? $record['roundid'] : NULL;
				$insertRecord['roundstatus'] = isset($record['roundstatus']) ? $record['roundstatus'] : NULL;
				$insertRecord['userid'] = isset($record['userid']) ? $record['userid'] : NULL;
				$insertRecord['username'] = isset($record['username']) ? $record['username'] : NULL;
				$insertRecord['riskamt'] = isset($record['riskamt']) ? $record['riskamt'] : NULL;
				$insertRecord['winamt'] = isset($record['winamt']) ? $record['winamt'] : NULL;
				$insertRecord['winloss'] = isset($record['winloss']) ? $record['winloss'] : NULL;
				$insertRecord['beforebal'] = isset($record['beforebal']) ? $record['beforebal'] : NULL;
				$insertRecord['postbal'] = isset($record['postbal']) ? $record['postbal'] : NULL;
				$insertRecord['cur'] = isset($record['cur']) ? $record['cur'] : NULL;
				$insertRecord['gameprovider'] = isset($record['gameprovider']) ? $record['gameprovider'] : NULL;
				$insertRecord['gameprovidercode'] = isset($record['gameprovidercode']) ? $record['gameprovidercode'] : NULL;
				$insertRecord['gamename'] = isset($record['gamename']) ? $record['gamename'] : NULL;
				$insertRecord['gameid'] = isset($record['gameid']) ? $record['gameprovidercode'].$record['gameid'] : NULL;
				$insertRecord['platformtype'] = isset($record['platformtype']) ? $record['platformtype'] : NULL;
				$insertRecord['ipaddress'] = isset($record['ipaddress']) ? $record['ipaddress'] : NULL;
				$insertRecord['bettype'] = isset($record['bettype']) ? $record['bettype'] : NULL;
				$insertRecord['playtype'] = isset($record['playtype']) ? $record['playtype'] : NULL;
				$insertRecord['playertype'] = isset($record['playertype']) ? $record['playertype'] : NULL;
				$insertRecord['turnover'] = isset($record['turnover']) ? $record['turnover'] : NULL;
				$insertRecord['validbet'] = isset($record['validbet']) ? $record['validbet'] : NULL;
				$game_details = $this->getGameHistory($record['roundid'], $record['username'],$insertRecord['gameprovidercode']);
				$insertRecord['match_detail'] = isset($game_details['url']) ? $game_details['url'] : NULL;

				//extra info from SBE
				$insertRecord['uniqueid'] = isset($record['ugsbetid']) ? $record['ugsbetid'] : NULL;
				$insertRecord['external_uniqueid'] = isset($record['ugsbetid']) ? $record['ugsbetid'] : NULL;
				$insertRecord['response_result_id'] = $responseResultId;
				$insertRecord['updated_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
				//insert data to Suncity gamelogs table database
				//$this->CI->suncity_game_logs->insertGameLogs($insertRecord);

                if($this->update_original && in_array($insertRecord['ugsbetid'], $existUgsBetIds)){
                    $this->CI->suncity_game_logs->updateGameLogs($insertRecord);
                } else {
                    $insertRecord['created_at'] = $this->utils->getNowDateTime()->format('Y-m-d H:i:s');
                    $this->CI->suncity_game_logs->insertGameLogs($insertRecord);
                }
				$dataCount++;
			}
		}
		

		$result['data_count'] = $dataCount;

		return array($success, $result);
	}

	public function syncMergeToGameLogs($token) {

		$this->CI->load->model(array('game_logs', 'player_model', 'suncity_game_logs'));

		$dateTimeFrom = clone parent::getValueFromSyncInfo($token, 'dateTimeFrom');
		$dateTimeFrom->modify($this->getDatetimeAdjust());
		$dateTimeTo = clone parent::getValueFromSyncInfo($token, 'dateTimeTo');

		//observer the date format
		$startDate = $dateTimeFrom->format('Y-m-d H:i:s');
		$endDate = $dateTimeTo->format('Y-m-d H:i:s');

		$rlt = array('success' => true);

		$result = $this->CI->suncity_game_logs->getGameLogStatistics($startDate, $endDate);

		$cnt = 0;
		if (!empty($result)) {

			$unknownGame = $this->getUnknownGame();
			foreach ($result as $row) {
				$cnt++;

				$game_description_id = $row->game_description_id;
				$game_type_id = $row->game_type_id;

				if(empty($row->game_type_id)&&empty($row->game_description_id)){
					list($game_description_id, $game_type_id) = $this->getGameDescriptionInfo($row, $unknownGame);
				}
				$round = $row->round_id;
				if($this->display_betid_as_round){
					if(isset($row->betid)){
						$round = $row->betid;
					}
				}
				$extra = array(
					'bet_for_cashback' => $row->valid_bet_amount,
					'trans_amount' => $row->bet_amount,
					'table' => $round,
					'bet_type' => $row->bettype,
					'match_type' => $row->playtype,
					'bet_details' => array('url' => $row->match_detail),
				);

				$this->syncGameLogs(
					$game_type_id,
					$game_description_id,
					$row->game_code,
					$row->game_type,
					$row->game,
					$row->player_id,
					$row->userid,
					$row->valid_bet_amount,
					$row->result_amount,
					null, # win_amount
					null, # loss_amount
					$row->after_balance, # after_balance
					0, # has_both_side
					$row->external_uniqueid,
					$row->beton, //start
					$row->betclosedon, //end
					$row->response_result_id,
					Game_logs::FLAG_GAME,
                    $extra
				);

			}
		}

		$this->CI->utils->debug_log('suncity PLAY API =========================>', 'startDate: ', $startDate,'EndDate: ', $endDate);
		$this->CI->utils->debug_log('syncMergeToGameLogs monitor', 'count', $cnt);
		return $rlt;
	}

	private function getGameDescriptionInfo($row, $unknownGame) {
		$game_description_id = null;

		$external_game_id = $row->gameid;
        $extra = array('game_code' => $external_game_id,'game_name' => $row->gamename);

        $game_type_id = $unknownGame->game_type_id;
        $game_type = $unknownGame->game_name;

		return $this->processUnknownGame(
			$game_description_id, $game_type_id,
			$external_game_id, $game_type, $external_game_id, $extra,
			$unknownGame);
	}

	private function getRoundIdKey($roundid){
    	return 'game-api-'.$this->getPlatformCode().'-roundid-'.$roundid;
    }

	public function getGameHistory($roundid = null, $gameUsername = null,$gpcode){
		$leagueKey=$this->getRoundIdKey($roundid);
        $rlt=$this->CI->utils->getJsonFromCache($leagueKey);
    	if(!empty($rlt)){
    		return $rlt;
    	}

		$context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetGameHistory',
        	
        );

        $params = array(
        	'userid' => $gameUsername,
        	'gpcode' => !is_null($gpcode) ? $gpcode : 'SB',
        	'roundid' => $roundid,
        );
        $this->method = self::GET;
        $rlt = $this->callApi(self::API_queryGameRecords, $params, $context);

        if($rlt['success']){
        	$this->CI->utils->saveJsonToCache($leagueKey, $rlt);
        } 
        return $rlt;
	}

	public function processResultForGetGameHistory($params){
		$responseResultId = $this->getResponseResultIdFromParams($params);
		$resultJsonArr = $this->getResultJsonFromParams($params);
		$this->CI->utils->debug_log("==========================result=========================> ",$resultJsonArr);

		// $url=  isset($resultJsonArr['url']) :$resultJsonArr['url'] : null;
		// echo "<pre>";
		// print_r($resultJsonArr);exit();
		// return $resultJsonArr;
		return array(true, $resultJsonArr);
	
	}

	public function convertMatchTypeCodeToReadable($match_type) {
		if ($this->convert_match_type_game_logs) {
			$new_match_type = $match_type;
			foreach (self::PLAY_TYPES as $key => $value) {
				if (strtoupper($match_type) === strtoupper($key)) {
					$new_match_type = $value[$this->convert_match_type_game_logs_lang];
				}
			}
			$this->CI->utils->debug_log('MATCH TYPE ===> ', $new_match_type, 'FROM MATCH TYPE ===> ', $match_type);
			return $new_match_type;
		} else {
			return $match_type;
		}
	}

	public function queryTransaction($transactionId, $extra) {
		return $this->returnUnimplemented();
	}

	public function syncPlayerAccount($playerName, $password, $playerId) {
		return $this->returnUnimplemented();
	}

	public function queryPlayerInfo($playerName) {
		return $this->returnUnimplemented();
	}

	public function updatePlayerInfo($playerName, $infos) {
		return $this->returnUnimplemented();
	}

	public function queryPlayerDailyBalance($playerName, $playerId, $dateFrom = null, $dateTo = null) {
		return $this->returnUnimplemented();
	}

	public function queryGameRecords($dateFrom, $dateTo, $playerName = null) {
		return $this->returnUnimplemented();
	}

	public function checkLoginStatus($playerName) {
		return $this->returnUnimplemented();
	}

	public function checkLoginToken($playerName, $token) {
		return $this->returnUnimplemented();
	}

	public function totalBettingAmount($playerName, $dateTimeFrom, $dateTimeTo) {
		return $this->returnUnimplemented();
	}

	public function changePassword($playerName, $oldPassword = null, $newPassword) {
		return $this->returnUnimplemented();
	}
}

/*end of file*/
