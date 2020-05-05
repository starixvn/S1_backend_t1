<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 *
 * importer
 *
 */
abstract class Abstract_importer {

	function __construct() {
		$this->CI = &get_instance();
		$this->utils=$this->CI->utils;
	}

    public function loopCSV($csv_file, $ignore_first_row, &$cnt, &$message, $callback){

		$cnt=0;
    	$success=false;

    	if(!file_exists($csv_file)){
    		return false;
    	}

		$file = fopen($csv_file, "r");
		if ($file !== false) {

			try{
				while (!feof($file)) {
					$tmpData = fgetcsv($file);

					// $this->debug_log('debug every row', $tmpData);

					if($cnt==0 && $ignore_first_row){
						//ignore header
						$this->CI->utils->debug_log('ignore first row', $tmpData);
						$cnt++;
						continue;
					}

					if (empty($tmpData)) {
						$this->CI->utils->debug_log('ignore empty row');
						$cnt++;
						continue;
					}

					$stop_flag=false;

					$success=$callback($cnt, $tmpData, $stop_flag);
					$cnt++;

					if($stop_flag){
						break;
					}
//					if(!$success){
//						break;
//					}
				}
			} finally {
				fclose($file);
			}

		} else {
			$this->CI->utils->error_log('open csv failed');
			$message=lang('Open CSV File Failed');
		}

		return $success;
    }

    public function getHeaderFromCSV($csv_file, &$message){
		$cnt=0;
    	$header=null;

    	if(!is_readable($csv_file)){
    		return false;
    	}

		$file = fopen($csv_file, "r");
		if ($file !== false) {
			try{
				if(!feof($file)) {
					$header = fgetcsv($file);
				}
			} finally {
				fclose($file);
			}
		} else {
			$this->CI->utils->error_log('getHeaderFromCSV open csv failed');
			$message=lang('Open CSV File Failed');
		}

		return $header;

	}

	public function countRowFromCSV($csv_file, &$message){
		$cnt=0;

    	if(!is_readable($csv_file)){
    		return false;
    	}

    	try{
			$file = new SplFileObject($csv_file, 'r');
			try{
				$file->seek(PHP_INT_MAX);
				//without header
				$cnt=$file->key();
			} finally {
				unset($file);
			}
		}catch(Exception $e) {
			$this->CI->utils->error_log('countRowFromCSV open csv failed', $e);
			$message=lang('Open CSV File Failed');
		}

		return $cnt;
	}

	public function validPlayerCSV($filepath, &$summary, &$message){
		$success=true;
		$header=$this->getHeaderFromCSV($filepath, $message);
		$count_of_row=$this->countRowFromCSV($filepath, $message);

		$summary=[
			'column_count'=>count($header),
			'row_count'=>$count_of_row,
		];

		return $success;
	}

	public function validAffCSV($filepath, &$summary, &$message){
		$success=true;
		$header=$this->getHeaderFromCSV($filepath, $message);
		$count_of_row=$this->countRowFromCSV($filepath, $message);

		$summary=[
			'column_count'=>count($header),
			'row_count'=>$count_of_row,
		];

		return $success;

	}

	public function validAffContactCSV($filepath, &$summary, &$message){
		$success=true;
		$header=$this->getHeaderFromCSV($filepath, $message);
		$count_of_row=$this->countRowFromCSV($filepath, $message);

		$summary=[
			'column_count'=>count($header),
			'row_count'=>$count_of_row,
		];

		return $success;

	}

	public function validPlayerContactCSV($filepath, &$summary, &$message){
		$success=true;
		$header=$this->getHeaderFromCSV($filepath, $message);
		$count_of_row=$this->countRowFromCSV($filepath, $message);

		$summary=[
			'column_count'=>count($header),
			'row_count'=>$count_of_row,
		];

		return $success;

	}

	public function validPlayerBankCSV($filepath, &$summary, &$message){
		$success=true;
		$header=$this->getHeaderFromCSV($filepath, $message);
		$count_of_row=$this->countRowFromCSV($filepath, $message);

		$summary=[
			'column_count'=>count($header),
			'row_count'=>$count_of_row,
		];

		return $success;

	}

	public function validAgencyCSV($filepath, &$summary, &$message){
		$success=true;
		$header=$this->getHeaderFromCSV($filepath, $message);
		$count_of_row=$this->countRowFromCSV($filepath, $message);

		$summary=[
			'column_count'=>count($header),
			'row_count'=>$count_of_row,
		];

		return $success;

	}

	public function validAgencyContactCSV($filepath, &$summary, &$message){
		$success=true;
		$header=$this->getHeaderFromCSV($filepath, $message);
		$count_of_row=$this->countRowFromCSV($filepath, $message);

		$summary=[
			'column_count'=>count($header),
			'row_count'=>$count_of_row,
		];

		return $success;

	}

	abstract public function importCSV(array $files, &$summary, &$message);

}
