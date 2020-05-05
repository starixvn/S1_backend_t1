<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Game_API
 *
 * Game_API library for AG
 *
 * @deprecated
 *
 * @package		Game_API
 * @author		ASRII
 * @version		1.0.0
 */

class Game_ag_ftp extends CI_Controller {
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
		$apiUrl = API_URL . '' . $type;

		$header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive:timeout=5, max=100";
		$header[] = "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.3";
		$header[] = "Accept-Language:es-ES,es;q=0.8";
		$header[] = "Pragma: ";
		$header[] = "X_ENTITY_KEY: " . API_ENTITY_KEY;

		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $apiUrl);
		curl_setopt($tuCurl, CURLOPT_PORT, 443);
		curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
		curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($tuCurl, CURLOPT_SSLCERT, API_CERT_PATH . 'kioskapi.mightypanda.644191.pem');
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_SSLKEY, API_CERT_PATH . 'kioskapi.mightypanda.644191.key');

		$exec = curl_exec($tuCurl);

		curl_close($tuCurl);
		$data = json_decode($exec, TRUE);
		return $data;
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}

	/**
	 * Get specific reports
	 *
	 * @param   type
	 *
	 * @return json
	 */
	function getFtpDirectories() {
		// set up basic connection
		$ftp_server = "ftp.agingames.com";
		$ftp_user_name = "D27.hll999";
		$ftp_user_pass = ""; //don't use ftp

		//Connect
		echo "<br />Connecting to $ftp_server via FTP...";
		$conn = ftp_connect(FTP_SERVER);
		$login = ftp_login($conn, FTP_USERNAME, FTP_PASSWORD);

		//Enable PASV ( Note: must be done after ftp_login() )
		$mode = ftp_pasv($conn, TRUE);

		//Login OK ?
		if ((!$conn) || (!$login) || (!$mode)) {
			die("FTP connection has failed !");
		}
		//echo "<br />Login Ok.<br />";

		//
		//Now run ftp_nlist()
		//
		// $file_list = ftp_nlist($conn, "");
		// foreach ($file_list as $file)
		// {
		//   echo "<br>$file";
		// }

		// to show list of directory
		// $filelist = $this->filecollect($conn);
		// echo "<pre>";
		//   print_r($filelist);
		// echo "</pre>";

		$local_file = 'resources/agreport/agreport.xml';
		$server_file = "/AGIN/20150226/201502260712.xml";
		// try to download $server_file and save to $local_file
		if (ftp_get($conn, $local_file, $server_file, FTP_BINARY)) {
			echo "Successfully written to $local_file\n";
		} else {
			echo "There was a problem\n";
		}

		//close
		ftp_close($conn);
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
}