<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Game_API
 *
 * Game_API library for PT
 *
 * @deprecated
 *
 * @package		Game_API
 * @author		ASRII
 * @version		1.0.0
 */

class Game_ag_api extends CI_Controller {
	//header api holder
	private $header = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session', 'salt'));
		$this->ci->load->model(array('player'));
	}

	/**
	 * Index Page of PT API
	 *
	 *
	 * @return	void
	 */
	public function callApi($input) {
		$params = $this->ci->salt->encrypt($input, DESKEY_AG);
		$md5Key = MD5($params . MD5KEY_AG);

		// $header = array('Contect-Type:application/xml', 'Accept:application/xml');

		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		// curl_setopt($ch, CURLOPT_URL, INVOKEURL_AG.'doBusiness.do?params='.$params.'&key='.$md5Key);
		// curl_setopt($ch, CURLOPT_USERAGENT, 'WEB_LIB_GI_'.CAGENT_AG);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,1);

		// $html = curl_exec($ch);

		// $xml = new SimpleXMLElement($html);
		// $status = (string) $xml['info'];
		// //return $status;
		// //return $this->statusHandler($status);
		//  echo '<br/>info:'.$status."<br/>";
		//  print_r($xml);

		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, INVOKEURL_AG . 'doBusiness.do?params=' . $params . '&key=' . $md5Key);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-type: text/xml',
			));
			$content = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
			$obj = new SimpleXMLElement($content);
			//return $obj;
			//echo "<pre>";
			//$status = (string) $obj['info'];
			$status = $obj;
			//echo json_decode($obj, TRUE);
			return $status;
			//var_dump($status);
		} catch (Exception $e) {
			var_dump($e);exit;
		}
	}

	/**
	 * get player balance
	 *
	 * @param loginName str
	 * @param password str
	 * @param accountType str
	 * @param method str
	 * @param oddtype str
	 * @param currency str
	 *
	 * @return xml
	 */
	function createAGPlayer($loginName, $password, $accountType = 0, $method = 'lg', $oddtype = 'A', $currency = 'CNY') {
		$input = "cagent=" . CAGENT_AG . "/\\\\/loginname=" . $loginName . "/\\\\/method=" . $method . "/\\\\/actype=" . $accountType . "/\\\\/password=" . $password . "/\\\\/oddtype=" . $oddtype . "/\\\\/cur=" . $currency . "";
		$this->callApi($input);
	}

	/**
	 * get player balance
	 *
	 * @param loginName str
	 * @param password str
	 * @param accountType str
	 * @param method str
	 * @param currency str
	 *
	 * @return xml
	 */
	function getAGPlayerBalance($loginName, $password, $accountType = 0, $method = 'gb', $currency = 'CNY') {
		$input = "cagent=" . CAGENT_AG . "/\\\\/loginname=" . $loginName . "/\\\\/method=" . $method . "/\\\\/actype=" . $accountType . "/\\\\/password=" . $password . "/\\\\/cur=" . $currency . "";
		return $this->callApi($input);
	}

	/**
	 * transfer account
	 *
	 * @param loginName str
	 * @param password str
	 * @param accountType str
	 * @param method str
	 * @param currency str
	 * @param credit float
	 * @param type str (IN)
	 * @param billno (cagent+sequence)
	 *
	 * @return xml
	 */
	function agTranferCredit($loginName, $password, $accountType = 0, $method = 'tc', $currency = 'CNY', $credit, $billno, $type) {
		// echo '<br/>loginName: '.$loginName;
		// echo '<br/>password: '.$password;
		// echo '<br/>accountType: '.$accountType;
		// echo '<br/>method: '.$method;
		// echo '<br/>currency: '.$currency;
		// echo '<br/>credit: '.$credit;
		// echo '<br/>billno: '.$billno;
		// echo '<br/>type: '.$type;
		$input = "cagent=" . CAGENT_AG . "/\\\\/method=" . $method . "/\\\\/loginname=" . $loginName . "/\\\\/billno=" . $billno . "/\\\\/type=" . $type . "/\\\\/credit=" . $credit . "/\\\\/actype=" . $accountType . "/\\\\/password=" . $password . "/\\\\/cur=" . $currency . "";
		//$input = "cagent=".CAGENT_AG."/\\\\/loginname=asriiag/\\\\/method=tc/\\\\/billno=D27_AGIN681205102123348079/\\\\/type=IN/\\\\/credit=101.00/\\\\/actype=0/\\\\/password=asriiag";
		// $input = "cagent=D27_AGIN/\\\\/loginname=asriiag/\\\\/method=tc/\\\\/billno=D27_AGIN681205102123348078/\\\\/type=IN/\\\\/credit=100.00/\\\\/actype=0/\\\\/password=asriiag/\\\\/cur=CNY";
		return $this->callApi($input);
	}

	/**
	 * transfer account confirmation
	 *
	 * @param loginName str
	 * @param password str
	 * @param accountType str
	 * @param method str
	 * @param currency str
	 * @param credit float
	 * @param type str (IN)
	 * @param billno (cagent+sequence)
	 *
	 * @return xml
	 */
	function agTranferCreditConfirm($loginName, $password, $accountType = 0, $method = 'tcc', $currency = 'CNY', $credit, $billno, $type) {
		$input = "cagent=" . CAGENT_AG . "/\\\\/method=" . $method . "/\\\\/loginname=" . $loginName . "/\\\\/billno=" . $billno . "/\\\\/type=" . $type . "/\\\\/credit=" . $credit . "/\\\\/actype=" . $accountType . "/\\\\/flag=1/\\\\/password=" . $password . "/\\\\/cur=" . $currency . "";
		return $this->callApi($input);
	}

	/**
	 * query order status
	 *
	 * @param billno num
	 * @param method str
	 * @param accountType str
	 * @param currency str
	 *
	 * @return xml
	 */
	function queryOrderStatus($billno, $method = 'qos', $accountType = 0, $currency = 'CNY') {
		$input = "cagent=" . CAGENT_AG . "/\\\\/billno=" . $billno . "/\\\\/method=" . $method . "/\\\\/actype=" . $accountType . "/\\\\/cur=" . $currency . "";
		$this->callApi($input);
	}

	/**
	 * invoke game
	 *
	 * @param loginname str
	 * @param actype str
	 * @param password str
	 * @param dm str
	 * @param sid str
	 * @param lang str
	 * @param gameType str
	 * @param oddtype str
	 * @param cur str
	 *
	 * @return xml
	 */
	function invokeGame($loginname, $actype, $password, $dm, $sid, $lang, $gameType, $oddtype, $cur) {
		$input = "cagent=" . CAGENT_AG . "/\\\\/loginname=" . $loginname . "/\\\\/actype=" . $actype . "/\\\\/password=" . $password . "/\\\\/dm=" . $dm . "/\\\\/sid=" . $sid . "/\\\\/lang=" . $lang . "/\\\\/gameType=" . $gameType . "/\\\\/oddtype=" . $oddtype . "/\\\\/cur=" . $cur . "";
		$this->callGameApi($input);
	}

	/**
	 * handle status of api return
	 *
	 * @param status
	 *
	 * @return BOOLEAN
	 */
	function statusHandler($status) {
		switch ($status) {
			case 0:
				$data['status'] = 'success';
				return $data;
				break;
			case 'error':
				return 'error';
				break;

			default:
				# code...
				break;
		}
	}

	/**
	 * get random sequence for bill no
	 *
	 * @return number
	 */
	public function getRandomSequence() {
		$seed = str_split('0123456789123456'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$randomNum = '';
		foreach (array_rand($seed, 16) as $k) {
			$randomNum .= $seed[$k];
		}

		return $randomNum;
	}

	/**
	 * check player is online or has existing login session
	 *
	 * @param playerName
	 *
	 * @return json
	 */
	// function isPlayerOnline($playerName) {
	//     $data = 'player/online/playername/'.$playerName;
	//     $api_call = $this->ci->game_pt_api->callApi($data);

	//     return $api_call['result'];
	//     /*echo '<pre>';
	//     print_r($api_call);
	//     echo '</pre>';*/
	// }

	/**
	 * logout player - force logout player or remove existing session
	 *
	 * @param playerName
	 *
	 * @return json
	 */
	// function logoutPlayer($playerName) {
	//     $data = 'player/logout/playername/'.$playerName;
	//     $api_call = $this->ci->game_pt_api->callApi($data);

	//     return $api_call['result'];
	// }
}