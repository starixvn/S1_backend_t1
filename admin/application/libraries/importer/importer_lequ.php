<?php if (!defined('BASEPATH')) {

	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/abstract_importer.php';

class Importer_lequ extends Abstract_importer{

	function __construct() {
		parent::__construct();

		$this->import_player_csv_header=self::IMPORT_PLAYER_CSV_HEADER;

	}

	const IMPORT_PLAYER_CSV_HEADER=['agent','username','userpassword','balance','realpassword'];


	private function processPlayer(&$rltPlayer,&$playerMap){

		$this->CI->load->model(array('lequ_model','group_level','wallet_model'));
		$uploadCsvFilepath=$this->utils->getSharingUploadPath('/upload_temp_csv');
		$csv_file = rtrim($uploadCsvFilepath, '/').'/'.$rltPlayer['filename'];
		$ignore_first_row = true;
		$controller = $this;
		$player_model =  $this->CI->player_model;
		$lequ_model =  $this->CI->lequ_model;
		$wallet_model = $this->CI->wallet_model;
		$group_level_model = $this->CI->group_level;
		$pass_length = 8;

        $player_importer_password_choice = $this->utils->getConfig('player_importer_password_choice');
        //$config['player_importer_password_choice'] = 'use_csv_password'; //"use_csv_password,generate_password"
		//$player_importer_password_choice = 'use_csv_password';

		$importer_lequ_balance_rate=$this->utils->getConfig('importer_ole_balance_rate');
		if(empty($importer_lequ_balance_rate)){
			$importer_lequ_balance_rate=1;
		}

		$this->loopCSV($csv_file, $ignore_first_row, $cnt, $message, function($cnt, $csv_row, $stop_flag)
			use($controller,$group_level_model,$wallet_model, $player_model,$lequ_model,$importer_lequ_balance_rate,$pass_length,
				$player_importer_password_choice,
				&$totalCount, &$failCount, &$rltPlayer) {

				//compare log consistency of columns
				$controller->utils->debug_log("compare column headings" , self::IMPORT_PLAYER_CSV_HEADER, $csv_row);
				$row = array_combine(self::IMPORT_PLAYER_CSV_HEADER, $csv_row);

				$externalId=null;
				$levelId='1';
				$username = $row['username'];
				$balance =  $row['balance'];
				$temppass =  $row['userpassword'];
				//convert balance
				$balance = round($balance * $importer_lequ_balance_rate, 4);

				//leave it empty option3
				switch ($player_importer_password_choice) {
					case 'use_csv_password':
						$password = $row['realpassword'];
						break;
					case 'generate_password':
						$password = $controller->utils->generate_password_no_special_char($pass_length);
						break;

					default:
						$password = '';
						break;
				}

				$details = array(
					'firstName' =>  "",
					'lastName' => "",
					'temppass' => $temppass
				);

				$createdOn = $controller->utils->getNowForMysql();
				$lequ_model->startTrans();
				$failMessage = '';
				$importPlayerId = $lequ_model->importPlayer($csv_row,$group_level_model, $wallet_model, $player_model,$levelId, $username, $password,$balance,$details,$createdOn,$failCount,$rltPlayer);

				if ($lequ_model->isErrorInTrans()) {
					$failCount++;
					$csv_row['reason'] = 'Trans_error';
					array_push($rltPlayer['failed_list'], $csv_row);
					$controller->utils->error_log("Import failed: [$failMessage]" , $csv_row);
				}
				$totalCount++;
				$lequ_model->endTrans();

			});
		$rltPlayer['success_count'] = $totalCount - $failCount;
		$rltPlayer['row_count'] = $totalCount;
		$rltPlayer['failed_count'] = $failCount;
		$rltPlayer['column_count'] = count(self::IMPORT_PLAYER_CSV_HEADER);
		$rltPlayer['success']=true;
		$this->CI->utils->debug_log('process player:'.$rltPlayer['filename']);
	}

	public function importCSV(array $files, &$summary, &$message){
		// import_player_csv_file, import_aff_csv_file, import_aff_contact_csv_file, import_player_contact_csv_file, import_player_bank_csv_file

		$success=true;

		$rltPlayer=[
			'filename'=>$files['import_player_csv_file'],
			'success'=>true,
			'failed_list'=>[],
			'failed_count'=>0,
			'success_count'=>0,
			'column_count'=>0,
			'row_count'=>0,
		];
		$playerMap=[];
		$this->processPlayer($rltPlayer, $playerMap);


		$summary=[
			'import_player_csv_file'=>$rltPlayer,
		];
		$this->CI->utils->debug_log('Import Summary', $summary);
		$message=null;

		return $success;
	}

}

