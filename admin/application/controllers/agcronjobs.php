<?php

/**
 *
 * @deprecated
 *
 *
 */
class Agcronjobs extends CI_Controller {

	function __construct() {
		parent::__construct();
		//if (!$this->input->is_cli_request()) show_error('Direct access is not allowed');
		$this->load->helper('url');
		$this->load->library(array('api_functions', 'player_manager', 'payment_manager', 'report_functions'));
	}

	function index() {
		// set time limit
		set_time_limit(0);

		//get AG player data per day save to pt_player_details_per_day
		$this->getAGReports();
	}

	/**
	 * get ag report per minutes
	 *
	 * @return  void
	 */
	function getAGReports() {
		$ftp_host = "ftp.agingames.com";
		$ftp_user_name = "D27.hll999";
		$ftp_user_pass = "";

		//connecting server
		echo "<br />Connecting to $ftp_host via FTP...";
		$conn = ftp_connect($ftp_host);
		$login = ftp_login($conn, $ftp_user_name, $ftp_user_pass);

		//Enable PASV ( Note: must be done after ftp_login() )
		$mode = ftp_pasv($conn, TRUE);

		//login server?
		if ((!$conn) || (!$login) || (!$mode)) {
			die("FTP connection has failed !");
		}
		echo "<br />Login Ok.<br />";

		//get report
		//$fileDateTime = date("Ymd");
		//$fileDateTime = '20150305';

		$date = date("Ymd");
		$yesterdate = strtotime('-1 day', strtotime($date));
		$yesterdate = date('Ymj', $yesterdate);
		$data[] = $yesterdate;
		$data[] = date("Ymd");

		foreach ($data as $key => $row) {
			$server_file = "/AGIN/" . $row . "/";
			$file_list = ftp_nlist($conn, "/AGIN/" . $row); //get the file list
			if (!empty($file_list)) {
				foreach ($file_list as $file) {
					$fileName = $this->getXMLReportFileName($file);
					$local_file = 'resources/agreport/' . $fileName . '.txt';

					//download xml record to server
					if (ftp_get($conn, $local_file, $server_file . $fileName . '.xml', FTP_BINARY)) {
						//save and filter
						$this->saveAGRecord($fileName);
					} else {
						//echo "There was a problem\n";
					}
				}
			}
		}

		// $server_file = "/AGIN/".$fileDateTime."/";
		// $file_list = ftp_nlist($conn, "/AGIN/".$fileDateTime);
		// if(!empty($file_list)){
		//     foreach ($file_list as $file)
		//     {
		//       $fileName = $this->getXMLReportFileName($file);
		//       $local_file = 'resources/agreport/'.$fileName.'.txt';

		//       //download xml record to server
		//       if(ftp_get($conn, $local_file, $server_file.$fileName.'.xml', FTP_BINARY)) {
		//         //save and filter
		//         $this->saveAGRecord($fileName);
		//       }else{
		//         //echo "There was a problem\n";
		//       }
		//     }
		// }
	}

	/**
	 * save ag record
	 *
	 * @return  void
	 */
	function saveAGRecord($fileName) {
		if (!$this->api_functions->isAGRecordExists($fileName)) {
			//save to db now
			$this->extractXMLRecord($fileName);
		} else {
			//redo process (updates record)
			$this->api_functions->deleteAGExistingRecord($fileName);
			$this->saveAGRecord($fileName);
		}
	}

	/**
	 * get file name
	 *
	 * param string
	 *
	 * @return  void
	 */
	function getXMLReportFileName($val) {
		$filename = substr(strrchr($val, "/"), 1);
		$newFileName = current(explode(".", $filename));

		return $newFileName;
	}

	/**
	 * extract file name
	 *
	 * param xmlFileRecord string
	 *
	 * @return  void
	 */
	function extractXMLRecord($xmlFileRecord) {
		$source = FTP_SAVEREPORTPATH . $xmlFileRecord . '.txt';

		$xmlData = '<rows>' . file_get_contents($source, true) . '</rows>';
		$reportData = simplexml_load_string($xmlData);

		//print_r($reportData);
		foreach ($reportData as $key => $value) {
			//$result[] = $value['dataType'];
			if ($value['dataType'] == 'TR') {
				$result['reportName'] = $xmlFileRecord;
				$result['dataType'] = (string) $value['dataType'];
				$result['id'] = (string) $value['ID'];
				$result['agentCode'] = (string) $value['agentCode'];
				$result['transferId'] = (string) $value['transferId'];
				$result['tradeNo'] = (int) $value['tradeNo'];
				$result['platformType'] = (string) $value['platformType'];
				$result['playerName'] = (string) $value['playerName'];
				$result['transferType'] = (string) $value['transferType'];
				$result['transferAmount'] = (float) $value['transferAmount'];
				$result['previousAmount'] = (float) $value['previousAmount'];
				$result['currentAmount'] = (float) $value['currentAmount'];
				$result['currency'] = (string) $value['currency'];
				$result['exchangeRate'] = (string) $value['exchangeRate'];
				$result['ip'] = (string) $value['IP'];
				$result['flag'] = (string) $value['flag'];
				$result['creationTime'] = (string) $value['creationTime'];
				$result['gameCode'] = (string) $value['gameCode'];

				$this->saveToDB($result);
			} elseif ($value['dataType'] == 'BR') {
				if (!$this->isBillNoExists($value['billNo'])) {
					$result['reportName'] = $xmlFileRecord;
					$result['dataType'] = (string) $value['dataType'];
					$result['billNo'] = (string) $value['billNo'];
					$result['playerName'] = (string) $value['playerName'];
					$result['agentCode'] = (string) $value['agentCode'];
					$result['gameCode'] = (string) $value['gameCode'];
					$result['netAmount'] = (float) $value['netAmount'];
					$result['betTime'] = (string) $value['betTime'];
					$result['gameType'] = (string) $value['gameType'];
					$result['betAmount'] = (float) $value['betAmount'];
					$result['validBetAmount'] = (float) $value['validBetAmount'];
					$result['flag'] = (string) $value['flag'];
					$result['currency'] = (string) $value['currency'];
					$result['tableCode'] = (string) $value['tableCode'];
					$result['loginIP'] = (string) $value['loginIP'];
					$result['recalcuTime'] = (string) $value['recalcuTime'];
					$result['platformType'] = (string) $value['platformType'];
					$result['remark'] = (string) $value['remark'];
					$result['round'] = (string) $value['round'];
					$result['result'] = (string) $value['result'];
					$result['beforeCredit'] = (string) $value['beforeCredit'];

					$this->saveToDB($result);
				}
			} elseif ($value['dataType'] == 'EBR') {
				if (!$this->isBillNoExists($value['billNo'])) {
					$result['reportName'] = $xmlFileRecord;
					$result['dataType'] = (string) $value['dataType'];
					$result['billNo'] = (string) $value['billNo'];
					$result['playerName'] = (string) $value['playerName'];
					$result['agentCode'] = (string) $value['agentCode'];
					$result['gameCode'] = (string) $value['gameCode'];
					$result['netAmount'] = (float) $value['netAmount'];
					$result['betTime'] = (string) $value['betTime'];
					$result['gameType'] = (string) $value['gameType'];
					$result['betAmount'] = (float) $value['betAmount'];
					$result['validBetAmount'] = (float) $value['validBetAmount'];
					$result['flag'] = (string) $value['flag'];
					$result['playType'] = (string) $value['playType'];
					$result['currency'] = (string) $value['currency'];
					$result['tableCode'] = (string) $value['tableCode'];
					$result['loginIP'] = (string) $value['loginIP'];
					$result['recalcuTime'] = (string) $value['recalcuTime'];
					$result['platformType'] = (string) $value['platformType'];
					$result['remark'] = (string) $value['remark'];
					$result['round'] = (string) $value['round'];
					$result['slottype'] = (string) $value['slottype'];
					$result['result'] = (string) $value['result'];
					$result['mainbillno'] = (string) $value['mainbillno'];
					$result['beforeCredit'] = (string) $value['beforeCredit'];

					$this->saveToDB($result);
				}

			} elseif ($value['dataType'] == 'GR') {

				$result['reportName'] = $xmlFileRecord;
				$result['dataType'] = (string) $value['dataType'];
				$result['gmcode'] = (string) $value['gmcode'];
				$result['tableCode'] = (string) $value['tableCode'];
				$result['beginTime'] = (string) $value['beginTime'];
				$result['closeTime'] = (string) $value['closeTime'];
				$result['dealer'] = (string) $value['dealer'];
				$result['shoecode'] = (string) $value['shoecode'];
				$result['flag'] = (string) $value['flag'];
				$result['bankerPoint'] = (string) $value['bankerPoint'];
				$result['playerPoint'] = (string) $value['playerPoint'];
				$result['cardNum'] = (string) $value['cardNum'];
				$result['pair'] = (string) $value['pair'];
				$result['gametype'] = (string) $value['gametype'];
				$result['dragonpoint'] = (string) $value['dragonpoint'];
				$result['tigerpoint'] = (string) $value['tigerpoint'];
				$result['cardlist'] = (string) $value['cardlist'];
				$result['vid'] = (string) $value['vid'];
				$result['platformType'] = (string) $value['platformtype'];

				$this->saveToDB($result);
			}
		}

		//echo 'Done Saving AG Records: '.$cnt;
		$data = array(
			'type' => 'per min',
			'subject' => 'AG Server Records: ' . $xmlFileRecord,
			'description' => 'Done Saving AG Records',
			'status' => 'success',
			'date' => date('Y-m-d H:i:s'),
		);
		$string = $this->convertArrayToString($data);
		$this->writeToLog($string);
	}

	/**
	 * save ag record
	 *
	 * @return  void
	 */
	function isBillNoExists($billNo) {
		return $this->api_functions->isBillNoExists($billNo);
	}

	/**
	 * writes log
	 *
	 * param data array
	 *
	 * @return  void
	 */
	public function writeToLog($string) {
		$path = realpath(APPPATH . "../public");
		$file = $path . '/log-crondaily.txt';
		$content = $string . ";\n";
		file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
	}

	/**
	 * converts array to string
	 *
	 * param data array
	 *
	 * @return  void
	 */
	public function convertArrayToString($array) {
		$string = implode(",", $array);
		return $string;
	}

	/**
	 * save record to db
	 *
	 * param data array
	 *
	 * @return  void
	 */
	function saveToDB($data) {
		$this->api_functions->addFtpRecord($data);

		//var_dump($data);
		$data2 = array(
			'type' => 'per min save to db',
			'subject' => 'Save Record to db ',
			'description' => 'Done Saving AG Records',
			'status' => 'success',
			'date' => date('Y-m-d H:i:s'),
		);
		$string = $this->convertArrayToString($data2);
		$this->writeToLog($string);
	}
}
