<?php

require_once dirname(__FILE__) . '/base_testing.php';

class Testing_lib_utils extends BaseTesting {

	public function init() {
		$this->load->library('utils');
		$this->test($this->utils != null, true, 'init utls');
	}

	public function testAll() {
		$this->init();
//		$this->testGetIpCity();
//		$this->testGetIpCityAndCountry();
//		$this->testLoadSystem();
//		$this->testTimezone();
		$this->testAutoAddCashback();
		// $this->testSyncCurrentExternalSystem();
	}

	public function testTarget($methodName) {
		$this->init();
		$this->$methodName();
	}

	private function testCompareFloat($a, $b) {
		$rlt = $this->utils->compareResultFloat($a, '=', $b);
		$this->utils->debug_log($a . '=' . $b . ' is ' . ($rlt ? 'true' : 'false'));

		$rlt = $this->utils->compareResultFloat($a, '<', $b);
		$this->utils->debug_log($a . '<' . $b . ' is ' . ($rlt ? 'true' : 'false'));

		$rlt = $this->utils->compareResultFloat($a, '>', $b);
		$this->utils->debug_log($a . '>' . $b . ' is ' . ($rlt ? 'true' : 'false'));

		$rlt = $this->utils->compareResultFloat($a, '<=', $b);
		$this->utils->debug_log($a . '<=' . $b . ' is ' . ($rlt ? 'true' : 'false'));

		$rlt = $this->utils->compareResultFloat($a, '>=', $b);
		$this->utils->debug_log($a . '>=' . $b . ' is ' . ($rlt ? 'true' : 'false'));

	}

	private function testAllCompareFloat() {
		$a = 1.0;
		$b = 1.0;

		$this->testCompareFloat($a, $b);

		$a = 1.0;
		$b = 1.1;

		$this->testCompareFloat($a, $b);

		$a = 1.1;
		$b = 1.0;

		$this->testCompareFloat($a, $b);

		$a = 0.9999999;
		$b = 1.0;

		$this->testCompareFloat($a, $b);

	}

	const AG_TIMEZONE = 'America/New_York';
	const SYSTEM_TIMEZONE = 'Asia/Hong_Kong';

	// public function testSyncCurrentExternalSystem() {
	// 	$this->load->model(array('external_system'));
	// 	$this->external_system->syncCurrentExternalSystem();
	// }

	public function testTimezone() {
		$dateTimeStr = '2015-09-01 06:00:01';
		$this->utils->debug_log('start', $dateTimeStr);
		$dateTimeStr = $this->utils->convertTimezone($dateTimeStr, self::AG_TIMEZONE, self::SYSTEM_TIMEZONE);
		$this->utils->debug_log('convert', $dateTimeStr);

	}

	public function testLoadSystem() {
		$api = $this->utils->loadExternalSystemLibObject(AG_API);
		$this->utils->debug_log('api==null', $api == null);
		// $apiManager = $this->utils->loadExternalSystemManagerObject(PT_API);
		// $this->utils->debug_log('testLoadSystem', $apiManager->getApi()->getPlatformCode());
		// $apiManager = $this->utils->loadExternalSystemManagerObject(AG_API);
		// $this->utils->debug_log('testLoadSystem', $apiManager->getApi()->getPlatformCode());
	}

	public function testGetIpCity() {
		$ip = '103.224.83.131'; //HK
		$city = $this->utils->getIpCity($ip);
		$this->test($city, 'Hong Kong', 'test ip:' . $ip);

		$ip = '61.130.97.212'; //china
		$city = $this->utils->getIpCity($ip);
		$this->test($city, 'Ningbo', 'test ip:' . $ip);

		$ip = '180.166.56.47';
		$city = $this->utils->getIpCity($ip);
		$this->test($city, 'Shanghai', 'test ip:' . $ip);

		$ip = 'bad ip';
		$city = $this->utils->getIpCity($ip);
		$this->test($city, '', 'test ip:' . $ip);
	}

	public function testGetIpCityAndCountry() {
		$ip = '103.224.83.131'; //HK
		list($city, $country) = $this->utils->getIpCityAndCountry($ip);
		$this->test($city, 'Hong Kong', 'test ip:' . $ip);
		$this->test($country, 'Hong Kong', 'test ip:' . $ip);

		$ip = '61.130.97.212'; //china
		list($city, $country) = $this->utils->getIpCityAndCountry($ip);
		$this->test($city, 'Ningbo', 'test ip:' . $ip);
		$this->test($country, 'China', 'test ip:' . $ip);

		$ip = '180.166.56.47';
		list($city, $country) = $this->utils->getIpCityAndCountry($ip);
		$this->test($city, 'Shanghai', 'test ip:' . $ip);
		$this->test($country, 'China', 'test ip:' . $ip);

		$ip = 'bad ip';
		list($city, $country) = $this->utils->getIpCityAndCountry($ip);
		$this->test($city, '', 'test ip:' . $ip);
		$this->test($country, '', 'test ip:' . $ip);
	}

	public function sampleFunc($param1) {
		return $param1 . ' get';
	}

	public function testV8js() {
		$v8 = new V8Js();
		$v8->prop1 = array('prop1' => 'val');
		$v8->obj = $this;
		$JS = <<< EOT
len = 'Hello' + ' ' + 'World!' +PHP.prop1['prop1']+ PHP.obj.sampleFunc('xxx');
len;
EOT;

		$rlt = 'Hello World!valxxx get';
		try {
			$str = $v8->executeString($JS, 'basic.js');
			$this->utils->debug_log($str);
			$this->test($str, $rlt, 'test v8js');
		} catch (V8JsException $e) {
			$this->utils->debug_log($e);
		}

	}

	public function testBlockCountry() {
		$ip = '58.101.108.125';
		list($city, $country) = $this->utils->getIpCityAndCountry($ip);

		$this->utils->debug_log('city', $city, 'country', $country);
	}

	public function testDaterange() {
		$start_date = new DateTime('2016-03-25 00:00:00');
		$start_date->modify('-20 minutes');
		$end_date = new DateTime('2016-03-25 04:00:00');

		$dateTimeStr = $start_date->format('Y-m-d H:i:s');
		$modify = '-12 hours';
		$start_date = $this->utils->modifyDateTime($dateTimeStr, $modify);

		$dateTimeStr = $end_date->format('Y-m-d H:i:s');
		$modify = '-12 hours';
		$end_date = $this->utils->modifyDateTime($dateTimeStr, $modify);

		$dates = $this->utils->dateRange($start_date, $end_date);
		$this->utils->debug_log('dates', $dates, 'start_date', $start_date, 'end_date', $end_date);

	}

	public function testDateRangeWeek() {
		$today = '2016-04-25';
		list($from, $to) = $this->utils->getFromToByWeek($today);

		$this->utils->debug_log('today', $today, ' week from', $from, 'to', $to);

		$today = '2016-04-24';
		list($from, $to) = $this->utils->getFromToByWeek($today);

		$this->utils->debug_log('today', $today, ' week from', $from, 'to', $to);

		$today = '2016-04-24';
		list($from, $to) = $this->utils->getFromToByMonth($today);

		$this->utils->debug_log('today', $today, ' month from', $from, 'to', $to);

		$today = '2016-12-01';
		list($from, $to) = $this->utils->getFromToByMonth($today);

		$this->utils->debug_log('today', $today, ' month from', $from, 'to', $to);

		$today = '2016-02-24';
		list($from, $to) = $this->utils->getFromToByMonth($today);

		$this->utils->debug_log('today', $today, ' month from', $from, 'to', $to);

	}

	private function testYearMonth() {
		$this->load->model(array('affiliate_earnings'));
		$list = $this->affiliate_earnings->getYearMonthListToNow('201403');
		$this->utils->debug_log($list);
	}

	private function testXml(){
		$xml=<<<EOD
<?xml version="1.0" encoding="utf-8"?> <result info="15" msg=""/>
EOD;

		$resultXml = new SimpleXMLElement($xml);
		$attrName='info';

		$info=null;
		if(!empty($resultXml)){
			$result=$resultXml->xpath('/result');
			if(isset($result[0])){
				$attr=$result[0]->attributes();
				if(!empty($attr)){
					foreach ($attr as $key => $value) {
						if($key==$attrName){
							$info=''.$value;
						}
					}
				}
			}
		}

		// $info=$resultXml->xpath('/result');

		// foreach ($info[0]->attributes() as $key => $value) {
		// 	$this->utils->debug_log('info', $key, ''.$value);
		// }

		// $this->test();

		$this->utils->debug_log('info', $info);

	}

	private function testArrayOrSingleXmlNoAttr(){

		$singleXml=<<<EOD
<BetDetails><MemberBetDetails>
<betId>61339477</betId><betTime>2016-08-10T00:40:47.1-04:00</betTime><memberCode>144_vpyousuck</memberCode><matchDateTime>2016-08-10T05:30:00-04:00</matchDateTime>
<sportsName>Soccer</sportsName><matchID>5289280</matchID><leagueName>Australia FFA Cup</leagueName><homeTeam>Brisbane Roar FC</homeTeam><awayTeam>Perth Glory FC</awayTeam><favouriteTeamFlag>H</favouriteTeamFlag>
<betType>OU</betType><selection>H</selection><handicap>2.75</handicap><oddsType>HK</oddsType><odds>0.7400</odds><currency>RMB</currency><betAmt>17.0000</betAmt>\n    <result>-17.0000</result>\n    <HTHomeScore>0</HTHomeScore>\n    <HTAwayScore>2</HTAwayScore>\n    <FTHomeScore>0</FTHomeScore>\n    <FTAwayScore>2</FTAwayScore><BetHomeScore>0</BetHomeScore><BetAwayScore>0</BetAwayScore><settled>1</settled><betCancelled>0</betCancelled><bettingMethod>INTERNET</bettingMethod><BTStatus>Pending</BTStatus><BTComission>0.00000000</BTComission>
</MemberBetDetails></BetDetails>
EOD;

		$obj=json_decode(json_encode(simplexml_load_string($singleXml)), true);

		$this->utils->debug_log('single:'.var_export( $obj, true),'MemberBetDetails', count($obj['MemberBetDetails']));

		if(!empty($obj['MemberBetDetails'])){
			//check index
			$is_single=array_key_exists('betId', $obj['MemberBetDetails']);
			$this->utils->debug_log('is_single', $is_single);
		}


		$arrXml=<<<EOD
<BetDetails>
<MemberBetDetails><betId>61336906</betId><betTime>2016-08-09T22:44:04.197-04:00</betTime><memberCode>144_vpcwn0409</memberCode><matchDateTime>2016-08-09T22:30:00-04:00</matchDateTime><sportsName>Soccer</sportsName><matchID>5289300</matchID><leagueName>Copa Sudamericana</leagueName><homeTeam>Universitario De Deportes</homeTeam><awayTeam>Club Sport Emelec</awayTeam><favouriteTeamFlag>H</favouriteTeamFlag><betType>RBOU</betType><selection>H</selection><handicap>2.00</handicap><oddsType>HK</oddsType><odds>1.0500</odds><currency>RMB</currency><betAmt>7.0000</betAmt><result>7.3500</result><HTHomeScore>0</HTHomeScore><HTAwayScore>0</HTAwayScore><FTHomeScore>0</FTHomeScore><FTAwayScore>3</FTAwayScore><BetHomeScore>0</BetHomeScore><BetAwayScore>0</BetAwayScore><settled>1</settled><betCancelled>0</betCancelled><bettingMethod>INTERNET</bettingMethod><BTStatus>Pending</BTStatus><BTComission>0.00000000</BTComission></MemberBetDetails>
<MemberBetDetails><betId>61337611</betId><betTime>2016-08-09T23:06:17.93-04:00</betTime><memberCode>144_vpabort</memberCode><matchDateTime>2016-08-09T22:00:00-04:00</matchDateTime><sportsName>Soccer</sportsName><matchID>5294227</matchID><leagueName>Copa Mexico</leagueName><homeTeam>Coras De Tepic</homeTeam><awayTeam>Leones Negros</awayTeam><favouriteTeamFlag>H</favouriteTeamFlag><betType>RB</betType><selection>A</selection><handicap>0.00</handicap><oddsType>HK</oddsType><odds>0.9800</odds><currency>RMB</currency><betAmt>5.0000</betAmt><result>-5.0000</result><HTHomeScore>0</HTHomeScore><HTAwayScore>0</HTAwayScore><FTHomeScore>1</FTHomeScore><FTAwayScore>0</FTAwayScore><BetHomeScore>0</BetHomeScore><BetAwayScore>0</BetAwayScore><settled>1</settled><betCancelled>0</betCancelled><bettingMethod>INTERNET</bettingMethod><BTStatus>Pending</BTStatus><BTComission>0.00000000</BTComission></MemberBetDetails>
</BetDetails>
EOD;

		$obj=json_decode(json_encode(simplexml_load_string($arrXml)), true);
		$this->utils->debug_log('arr:'. var_export( $obj, true) ,'MemberBetDetails', count($obj['MemberBetDetails']));

		if(!empty($obj['MemberBetDetails'])){
			//check index
			$is_single=array_key_exists('betId', $obj['MemberBetDetails']);
			$this->utils->debug_log('is_single', $is_single);
		}

	}

	public function testAutoAddCashback() {
		$gameDescriptionId = 5199;	// EBET API ( game_type : Baccarat (181 id) , game_description :5199 )

		$this->load->model(array('game_type_model', 'group_level'));

		$autoAddCashback = $this->game_type_model->isGameTypeSetToAutoAddCashback($gameDescriptionId);
		if($autoAddCashback) {
			$this->utils->debug_log('auto add cash back');
		}

		$this->group_level->allowGameDescToAll($gameDescriptionId);
	}

	public function testArrayChunk(){
		$arr=[];

		for ($i=0; $i < 1020; $i++) {
			$arr[]=['index'=>$i];
		}

		$this->utils->debug_log('size of arr', count($arr));

		$chunk_arr=array_chunk($arr, 500);

		foreach ($chunk_arr as $key => $value) {
			$this->utils->debug_log($key, 'size of ', count($value));
		}

	}

	public function testGetFullCashbackPercentageMap(){
		$this->load->model(['group_level']);

		$map=$this->group_level->getFullCashbackPercentageMap();

		log_message('debug', var_export( $map, true));

		// $this->utils->debug_log('getFullCashbackPercentageMap', var_export( $map, true));
	}

	public function testGetPlayerRateFromLevel(){


		$this->load->model(['group_level']);

		$player_id=112;
		$levelId=1;
		$game_platform_id=2;
		$game_type_id=2;
		$game_description_id=11;
		$extra_info=[
			'commonRules'=>$this->group_level->getCommonCashbackRules(),
			'levelCashbackMap'=>$this->group_level->getFullCashbackPercentageMap(),
		];

		$row=$this->group_level->getPlayerRateFromLevel($player_id, $levelId,
			$game_platform_id, $game_type_id, $game_description_id, $extra_info);

		$this->utils->debug_log('row', $row);

		$game_description_id=14;

		$row=$this->group_level->getPlayerRateFromLevel($player_id, $levelId,
			$game_platform_id, $game_type_id, $game_description_id, $extra_info);

		$this->utils->debug_log('row', $row);

		$game_type_id=12;
		$row=$this->group_level->getPlayerRateFromLevel($player_id, $levelId,
			$game_platform_id, $game_type_id, $game_description_id, $extra_info);

		$this->utils->debug_log('row', $row);

		$game_platform_id=50;
		$row=$this->group_level->getPlayerRateFromLevel($player_id, $levelId,
			$game_platform_id, $game_type_id, $game_description_id, $extra_info);

		$this->utils->debug_log('row', $row);


	}

	public function testAllowGameDescToAll(){

		$gameDescId=4585;

		$this->load->model(['group_level']);
		$this->group_level->allowGameDescToAll($gameDescId);

	}

	public function testCompareIP(){
		$targetIp='116.93.126.130';
		$maskIp='116.93.126.130/32';

		$rlt=$this->utils->compareIP($targetIp, $maskIp);
		$this->test($rlt, true, 'compareIP:' . $targetIp.' , '.$maskIp);

		// $targetIp='116.93.126.129';
		// $maskIp='116.93.126.130/32';

		// $rlt=$this->utils->compareIP($targetIp, $maskIp);
		// $this->test($rlt, true, 'compareIP:' . $targetIp.' , '.$maskIp);

		$targetIp='116.93.126.130';
		$maskIp='116.93.126.130';

		$rlt=$this->utils->compareIP($targetIp, $maskIp);
		$this->test($rlt, true, 'compareIP:' . $targetIp.' , '.$maskIp);

	}

	public function testSendSms(){

		$contactNumber='1233242';
		$username='test002';

		if($this->utils->isEnabledFeature('send_sms_after_registration')){

			//send sms
			$searchArr=['{player_username}', '{player_center_url}'];
			$replaceArr=[ $username, $this->utils->getSystemUrl('player') ];
			$msg=$this->utils->generateSmsTemplate($searchArr, $replaceArr);
			$this->utils->debug_log('send registration sms to '.$contactNumber, $username, $msg);
			if(!empty($msg)){
				$this->utils->sendSmsByApi($contactNumber, $msg);
			}
		}


	}

	public function testLock(){

		$lock_it = $this->utils->lockResourceBy('testit', Utils::LOCK_ACTION_BALANCE, $lockedKey);

		$this->utils->debug_log('test lock', $lock_it, $lockedKey);

	}


	public function testAffDomain(){

		$this->load->model(['affiliatemodel']);

		$domain='8.ybao361.com';

		$rlt=$this->affiliatemodel->getTrackingCodeByMatchAffDomain($domain);

		$this->utils->debug_log('search '.$domain.' result code:'.$rlt);

		$domain='www.8.ybao361.com';

		$rlt=$this->affiliatemodel->getTrackingCodeByMatchAffDomain($domain);

		$this->utils->debug_log('search '.$domain.' result code:'.$rlt);

		$domain='player.8.ybao361.com';

		$rlt=$this->affiliatemodel->getTrackingCodeByMatchAffDomain($domain);

		$this->utils->debug_log('search '.$domain.' result code:'.$rlt);

		$domain='m.8.ybao361.com';

		$rlt=$this->affiliatemodel->getTrackingCodeByMatchAffDomain($domain);

		$this->utils->debug_log('search '.$domain.' result code:'.$rlt);

	}

	public function testCreateBigCsv(){

	    $sql='select * from player';

	    $dataResult=array(
            "draw" => '-1',
            "recordsFiltered" => null,
            "recordsTotal" => null,
            "data" => $sql,
            "header_data" => ['col1'],
        );

	    $filename='player_'.random_string('md5');

	    $this->utils->debug_log('create csv by '.$sql, $this->utils->create_csv($dataResult, $filename));

    }

}
