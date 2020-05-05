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

class Game_pt_api extends CI_Controller {
	//header api holder
	private $header = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session'));
		$this->ci->load->model(array('player'));
	}

	/**
	 * Index Page of PT API
	 *
	 *
	 * @return	void
	 */
	public function callApi($type = '') {
		$apiUrl = $this->ci->config->item('API_URL') . '' . $type;

		$header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive:timeout=5, max=100";
		$header[] = "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.3";
		$header[] = "Accept-Language:es-ES,es;q=0.8";
		$header[] = "Pragma: ";
		$header[] = "X_ENTITY_KEY: " . $this->ci->config->item('API_ENTITY_KEY');

		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $apiUrl);
		curl_setopt($tuCurl, CURLOPT_PORT, 443);
		curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($tuCurl, CURLOPT_SSLCERT, $this->ci->config->item('API_CERT_PATH') . 'kioskapi.mightypanda.644191.pem');
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_SSLKEY, $this->ci->config->item('API_CERT_PATH') . 'kioskapi.mightypanda.644191.key');

		$exec = curl_exec($tuCurl);

		if (!$exec) {
			print_r(curl_error($tuCurl));
		}

		/*echo API_CERT_PATH;*/

		curl_close($tuCurl);
		$data = json_decode($exec, TRUE);
		return $data;
		/*echo "<pre>";
	print_r($data);
	echo "</pre>";*/
	}

	/**
	 * Get specific reports
	 *
	 * @param   adminName
	 *
	 * @return json
	 */
	function getPlayerReports($player_name) {
		$data = 'customreport/getdata/reportname/PlayerStats/timeperiod/now/playername/' . $player_name . '/adminname/HLLCNYTLA/kioskname/HLLCNYTLK/entityname/HLLCNYTLE/platform/flash/reportby/day';
		$api_call = $this->callApi($data);

		return $api_call;
		/*echo '<pre>';
	print_r($api_call);
	echo '</pre>';*/
	}

	/**
	 * get online players
	 *
	 * @return json
	 */
	function getOnlinePlayers() {
		$data = 'player/list/online/1';
		$api_call = $this->callApi($data);

		return $api_call['result'];

		/*echo '<pre>';
	print_r($api_call);
	echo '</pre>';*/
	}

	/**
	 * logout player - force logout player or remove existing session
	 *
	 * @param playerName
	 *
	 * @return json
	 */
	function logoutPlayer($playerName) {
		$data = 'player/logout/playername/' . $playerName;
		$api_call = $this->callApi($data);

		return $api_call['result'];
	}

	/**
	 * check player is online or has existing login session
	 *
	 * @param playerName
	 *
	 * @return json
	 */
	function isPlayerOnline($playerName) {
		$data = 'player/online/playername/' . $playerName;
		$api_call = $this->callApi($data);

		return !$api_call['result'] ? null : $api_call['result'];
		/*echo '<pre>';
	print_r($api_call);
	echo '</pre>';*/
	}

	/**
	 * get player info
	 *
	 * @param   playerName str
	 *
	 * @return json
	 */
	public function getPlayerInfo($playerName) {
		$data = 'player/info/playername/' . $playerName;
		$api_call = $this->ci->game_pt_api->callApi($data);

		return $api_call;
	}
}