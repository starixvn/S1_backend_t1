<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get error result
 * * Save reponse result
 * * Set/get response result
 * * Copy result
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Response_result extends BaseModel {

	private $today;

	function __construct() {
		parent::__construct();

		$this->today=date('Ymd');
	}

	protected $tableName = "response_results";

	const FLAG_NORMAL = 1;
	const FLAG_ERROR = 2;

	const STATUS_ERROR=2;

	/**
	 * overview : get error result
	 *
	 * @param DateTime $dateTimeFrom
	 * @param DateTime $dateTimeTo
	 * @return array
	 */
	public function getErrorResult(\DateTime $dateTimeFrom, \DateTime $dateTimeTo) {
		$this->db->from($this->tableName)
			->where('created_at >=', $this->utils->formatDateTimeForMysql($dateTimeFrom))
			->where('created_at <=', $this->utils->formatDateTimeForMysql($dateTimeTo))
			->where('flag', self::FLAG_ERROR);

		return $this->runMultipleRow();
	}

	/**
	 * overview : save response result for file
	 *
	 * @param $systemTypeId
	 * @param $flag
	 * @param $requstApi
	 * @param $requestParams
	 * @param $resultFilePath
	 * @param null $fields
	 * @return array
	 */
	public function saveResponseResultForFile($systemTypeId, $flag, $requstApi, $requestParams, $resultFilePath, $fields = null) {

		$insertData = array(
			"system_type_id" => $systemTypeId,
			"flag" => $flag,
			"filepath" => $resultFilePath,
			"request_api" => $requstApi,
			"request_params" => $requestParams,
			"created_at" => $this->utils->getNowForMysql(),
		);
		if (!empty($fields)) {
			$insertData = array_merge($insertData, $fields);
		}
		$this->db->insert($this->tableName, $insertData);
		return $this->db->insert_id();
	}

	public function writeContentToResponseFile($write_response_result_to_dir, $systemTypeId, $content) {

		$dateDir = $write_response_result_to_dir."/".$this->utils->getAppPrefix()."/" . date('Y-m-d');
		$dir = $dateDir . "/" . $systemTypeId . "/";
		//create dir
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
			//chmod
			@chmod($dateDir, 0777);
			@chmod($dir, 0777);
		}
		$filename = $this->utils->getDatetimeNow() . "_" . random_string('alnum', 8) . ".json";
		$f = $dir . $filename;
		file_put_contents($f, $content);
		return $dir . $filename;
	}

	/**
	 * Store Response Data into File/Partition Table, with initRespTable().
	 *
	 * Check write_response_result_to_dir of CI.Config
	 * If spec. write_response_result_to_dir
	 * 	then write Content into file.
	 * 	else into partition Table of database.
	 *
	 * The Partition Tables Name Rule,
	 * "resp_"+ Ymd, ex:"resp_20190621".
	 *
	 * @param integer $systemTypeId F.K. external_system.id
	 * @param integer $resultId F.K. response_results.id
	 * @param array $dataArr
	 * @param integer $status default self::STATUS_NORMAL,
	 *
	 * @return string|integer Path+Filename or insert_id for success, else void.
	 */
	public function saveResponseFile($systemTypeId, $resultId, $dataArr, $status=self::STATUS_NORMAL){

		// if( !$this->db->table_exists( $this->initRespTable() ){
		// 	$this->utils->debug_log('copyResultFile table `'.'resp_'.date('Ymd').'` not exist ');
		// 	return false;
		// }

		$write_response_result_to_dir=$this->utils->getConfig('write_response_result_to_dir');
		if(!empty($write_response_result_to_dir)){

			$dataArr['response_result_id']=$resultId;
			$dataArr['request_id']=$this->utils->getRequestId();

			$content=json_encode($dataArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

			//write to file
			return $this->writeContentToResponseFile($write_response_result_to_dir, $systemTypeId, $content);

		}else{

			$dataArr['response_result_id']=$resultId;
			$dataArr['request_id']=$this->utils->getRequestId();

			$content=json_encode($dataArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

			$max_allow_response_content=$this->utils->getConfig('max_allow_response_content');
			if(strlen($content)>$max_allow_response_content){
				$content=substr($content,0,$max_allow_response_content);
			}
			$respTable=$this->initRespTable();
			//add request_id later
			$id=$this->insertData($respTable, [
				'external_system_id'=>$systemTypeId,
				'response_result_id'=>$resultId,
				'content'=>$content,
				'status'=>$status,
				'created_at'=>$this->utils->getNowForMysql(),
			]);

			//update response_results
			if(!empty($id) && !empty($resultId)){
				$this->db->set('filepath', $respTable.'.'.$id)->where('id', $resultId);
				$this->runAnyUpdate($this->tableName);
			}
			unset($content);

			return $id;
		}

	}

	/**
	 * overview : save response result
	 *
	 * @param $systemTypeId
	 * @param $flag
	 * @param $requstApi
	 * @param $requestParams
	 * @param $resultText
	 * @param $statusCode
	 * @param $statusText
	 * @param $extra
	 * @param null $fields
	 * @param bool|false $dont_save_response_in_api
	 * @return mixed
	 */
	public function saveResponseResult($systemTypeId // #1
		, $flag // #2
		, $requstApi // #3
		, $requestParams // #4
		, $resultText // #5
		, $statusCode // #6
		, $statusText // #7
		, $extra // #8
		, $fields = [] // #9
		, $dont_save_response_in_api = false  // #10
	) {
		//save to file
		$filepath = null;
		// if (!$dont_save_response_in_api) {
		// 	$filepath = $this->utils->saveToResponseFile($systemTypeId, $resultText);
		// }
		if ($flag != self::FLAG_NORMAL && $flag != self::FLAG_ERROR) {
			$flag = self::FLAG_NORMAL;
		}

		$insertData = array(
			"system_type_id" => $systemTypeId,
			"flag" => $flag,
			"status_code" => $statusCode,
			"status_text" => $statusText,
			"request_api" => $requstApi,
			"request_params" => $requestParams,
			"extra" => $extra,
			"created_at" => $this->utils->getNowForMysql(),
			"request_id" => $this->utils->getRequestId(),
		);

		if (!empty($fields)) {
			$insertData = array_merge($insertData, $fields);
		}

		$write_response_result_to_dir=$this->utils->getConfig('write_response_result_to_dir');
		if(!empty($write_response_result_to_dir)){
			$insertData['resultText']=$resultText;
			// $insertData['resultText']=$resultText;
			$filepath=$this->saveResponseFile($systemTypeId, null, $insertData, $flag==self::FLAG_ERROR ? self::STATUS_ERROR : self::STATUS_NORMAL);
			unset($insertData['resultText']);
			$this->utils->debug_log('save reponse file to dir', $filepath);
		}

		$this->utils->debug_log('save result to ', $filepath, 'fields', count($fields), 'flag', $flag, 'dont_save_response_in_api', $dont_save_response_in_api);
		//update filepath
		$insertData["filepath"]=$filepath;
		$this->db->insert($this->tableName, $insertData);
		$id = $this->db->insert_id();
		$this->utils->debug_log('save response result', $id);

		if(!empty($id)){

			if(!$dont_save_response_in_api){
				$insertData['resultText']=$resultText;
			}

			if($flag==self::FLAG_ERROR){
				// if($systemTypeId == SMS_API){ #For SMS API
					// $insertData['resultText']=$resultText;
				// }
				$this->utils->error_log('error response result', $id, $flag);
				if(empty($write_response_result_to_dir)){
					$file_id=$this->saveResponseFile($systemTypeId, $id, $insertData, self::STATUS_ERROR);
					$this->utils->debug_log('save reponse file to db', $file_id);
				}
			}else{
				if(!$dont_save_response_in_api){
					// $insertData['resultText']=$resultText;
					if(empty($write_response_result_to_dir)){
						$file_id=$this->saveResponseFile($systemTypeId, $id, $insertData);
						$this->utils->debug_log('save reponse file to db', $file_id);
					}
				}else{
					$this->utils->debug_log('ignore response result file', $id);
				}
			}
		}

		return $id;
	}

	public function getResponseResultFileById($id, $table_name=null){
		$table_name= empty($table_name) ? $this->initRespTable() : $table_name ;
		$this->db->from($table_name)->where('id', $id);

		return $this->runOneRowArray();
	}

	public function getResponseResultFileByResultId($id, $table_name=null){
		$table_name= empty($table_name) ? $this->initRespTable() : $table_name ;
		$this->db->from($table_name)->where('response_result_id', $id);

		return $this->runOneRowArray();
	}

	public function copyResultFile($respRlt){
		if(!empty($respRlt)){

			$data=[
				'external_system_id'=>$respRlt['external_system_id'],
				'response_result_id'=>$respRlt['response_result_id'],
				'content'=>$respRlt['content'],
				'status'=>$respRlt['status'],
				'created_at'=>$respRlt['created_at'],
			];

			$table_name=$this->initRespTable();

			$row=$this->getResponseResultFileById($respRlt["id"], $table_name);
			if(empty($row)){
				//use old response id
				$data['id']=$respRlt["id"];
			}

			// if( !$this->db->table_exists( 'resp_'.date('Ymd') ) ){
			// 	$this->utils->debug_log('copyResultFile error table `'.'resp_'.date('Ymd').'` not exist ');
			// 	return false;
			// }

			return $this->insertData($table_name, $data);
		}
	}

	/**
	 * overview : set response result to error
	 *
	 * @param $responseResultId
	 * @return mixed
	 */
	public function setResponseResultToError($responseResultId) {
		$this->db->where('id', $responseResultId);
		return $this->db->update($this->tableName, array("flag" => self::FLAG_ERROR));
	}

	/**
	 * overview : get response result by id
	 *
	 * @param $id
	 * @return null
	 */
	public function getResponseResultById($id){
		$this->db->from($this->tableName)->where('id', $id);

		return $this->runOneRow();
	}

	/**
	 * overview : copy result
	 *
	 * @param $respRlt
	 * @return array
	 */
	public function copyResult($respRlt){
		$data=[
			"system_type_id" => $respRlt->system_type_id,
			"content" => $respRlt->content,
			"filepath" => $respRlt->filepath,
			"note" => $respRlt->note,
			"created_at" => $respRlt->created_at,
			"request_api" => $respRlt->request_api,
			"request_params" => $respRlt->request_params,
			"status_code" => $respRlt->status_code,
			"status_text" => $respRlt->status_text,
			"extra" => $respRlt->extra,
			"flag" => $respRlt->flag,
			"player_id" => $respRlt->player_id,
			"related_id1" => $respRlt->related_id1,
			"related_id2" => $respRlt->related_id2,
			"related_id3" => $respRlt->related_id3,
			"sync_id" => $respRlt->sync_id,
			"full_url" => $respRlt->full_url,
			"external_transaction_id" => $respRlt->external_transaction_id,
		];

		$row=$this->getResponseResultById($respRlt->id);
		if(empty($row)){
			//use old response id
			$data['id']=$respRlt->id;
		}

		return $this->insertData($this->tableName, $data);
	}

	/**
	 * overview : get response result information by id
	 *
	 * @param $id
	 * @return array
	 */
	public function getResponseResultInfoById($id){
		$this->db->from($this->tableName)->where('id', $id);

		return $this->runOneRowArray();
	}

	/**
	 * Get record form  `resp_XXX` and `response_results`.`id`
	 *
	 * @param array $id The string, resp_XXX+ "."+ `response_results`.`id` .
	 */
	public function getRespResultByTableField($id){
		$arr=explode('.', $id);
		if(count($arr)>1){

			$this->db->from($arr[0])->where('id', $arr[1]);
			return $this->runOneRowArray();

		}
		return null;
	}

	public function updateResponseResultContentByFilepath($filepath, $content) {
		$arr = explode('.', $filepath);
		if(count($arr) == 2){
			$this->db->where('id', $arr[1]);
			return $this->db->update($arr[0], array("content" => $content));
		}

		return null;
	}

	public function initRespTable(){
		return $this->utils->initRespTableByDate($this->today);
	}

	public function getMinCreatedAt(){
		$this->db->select_min('created_at')->from($this->tableName);

		return $this->runOneRowOneField('created_at');
	}

	public function getRespFileInfoByTableField($id){

		$row=$this->getRespResultByTableField($id);

		if(!empty($row)){

			$this->load->model(['player_model', 'external_system']);

			$row['content']=json_decode($row['content'], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR);
			$row['content']['decode_request_params']=json_decode($row['content']['request_params'], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR);
			// $row['content']['request_api'] = $row['content']['request_api']; // Patch for OGP-12954 在 payment api callback request 內加上可重複發送的機制.
			$row['content']['player']=$this->player_model->getUsernameById(@$row['content']['player_id']);
			$row['content']['system']=$this->external_system->getNameById($row['content']['system_type_id']);
		}

		return $row;
	}

	public function checkResendCallbackExists ($systemId, $orderId, $reSendBySecureId) {

        $this->db->from($this->tableName)
        		 ->where('system_type_id', $systemId)
        		 ->where('related_id1', $orderId)
        		 ->where('extra', 'reSendBySecureId='.$reSendBySecureId);

		if ($this->runExistsResult()) {
			return true;
		}

		$this->utils->error_log('resend callback does not exist');
		return false;
	}

}
