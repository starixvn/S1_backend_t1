<?php if (!defined('BASEPATH')) {

	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/abstract_importer.php';

class Importer_newrainbow extends Abstract_importer{

	function __construct() {
		parent::__construct();

		$this->import_player_csv_header=self::IMPORT_PLAYER_CSV_HEADER;
		
	}

	const IMPORT_PLAYER_CSV_HEADER=['username'];

	
	private function processPlayer(&$rltPlayer,&$playerMap){

		$this->CI->load->model(array('newrainbow_model','group_level'));
		$uploadCsvFilepath=$this->utils->getSharingUploadPath('/upload_temp_csv');
		$csv_file = rtrim($uploadCsvFilepath, '/').'/'.$rltPlayer['filename'];
		$ignore_first_row = true;	
		$controller = $this;	
		$newrainbow_model =  $this->CI->newrainbow_model;
		$group_level_model = $this->CI->group_level;
		$pass_length = 8;
        //$config['registry_date_range'] = ['from'=>'2018-03-01 00:00:00','to'=>'2018-03-31 00:00:00'];
		$registry_date_range = $this->utils->getConfig('registry_date_range');

		$this->loopCSV($csv_file, $ignore_first_row, $cnt, $message, function($cnt, $csv_row, $stop_flag) 
			use($controller,$group_level_model,$newrainbow_model,$pass_length,$registry_date_range,&$totalCount, &$failCount, &$rltPlayer) {

				//compare log consistency of columns
				$controller->utils->debug_log("compare column headings" , self::IMPORT_PLAYER_CSV_HEADER, $csv_row);
				$row = array_combine(self::IMPORT_PLAYER_CSV_HEADER, $csv_row);
				
				$externalId=null;
				$levelId='1';
				$username = $row['username'];
				$password = $controller->utils->generate_password_no_special_char($pass_length);
				
				$from = $registry_date_range['from'];
				$to= $registry_date_range['to'];
				$createdOn = $controller->utils->generateRandomDateTime($from,$to);

				print_r($createdOn);
				$newrainbow_model->startTrans();
				$failMessage = '';
				$importPlayerId = $newrainbow_model->importPlayer($group_level_model,$levelId, $username, $password,$createdOn);
				if ($newrainbow_model->isErrorInTrans()) {
					$failCount++;
					$csv_row['reason'] = 'Trans_error';
					array_push($rltPlayer['failed_list'], $csv_row); 
					$controller->utils->error_log("Import failed: [$failMessage]" , $csv_row);
				}
				$totalCount++;
				$newrainbow_model->endTrans();

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

