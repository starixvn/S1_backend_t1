<?php
require_once dirname(__FILE__) . '/BaseController.php';

/**
 *
 * base report
 *
 *
 * @category report_management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class BaseReport extends BaseController {

	const HTTP_REQEUST_PARAM='__request__';

	public function __construct(){
		parent::__construct();
	}

	/**
	 *
	 * @return array request_params is real parameters of report
	 */
	public function getReportFormatAndType(){

		$export_format=$this->input->get('export_format');
		$export_type=$this->input->get('export_type');

		if(empty($export_format)){
			$export_format=$this->input->post('export_format');
		}
		if(empty($export_type)){
			$export_type=$this->input->post('export_type');
		}


		$request=$this->input->post('json_search');
		if(!empty($request)){
			$request=$this->utils->decodeJson($request);

			if(isset($request['export_format'])){
				//overwrite from json field
				$export_format=$request['export_format'];
			}
			if(isset($request['export_type'])){
				//overwrite from json field
				$export_type=$request['export_type'];
			}
		}

		return ['export_format'=>$export_format, 'export_type'=>$export_type, 'request_params'=>$request];

	}

	public function exportData($funcName, $extra_params, $callerType, $caller, $state, $lang=null, $managementName = null, $actionName = null){
		$current_url = &get_instance();
		if(is_null($managementName)){
			$managementName = $current_url->router->fetch_class();
		}

		if(is_null($actionName)){
			$actionName = $current_url->router->fetch_method();
		}

		$this->load->library(['lib_queue']);

		$exportParams=$this->getReportFormatAndType();

		$this->utils->debug_log('exporting ', $exportParams, '-----extra_params-----', $extra_params);

		$success=true;
		//for remote use
		$exportParams['request_params']['extra_search']['target_func_name'] = $funcName;
		//request_params is always first one
		$params=[$exportParams['request_params']];// array_merge([], $extra_params);
		if(!empty($extra_params)){
			foreach ($extra_params as $value) {
				if(is_string($value) && $value === self::HTTP_REQEUST_PARAM){
					$params[]=$exportParams['request_params'];
					//delete first one
					array_splice($params, 0, 1);
				}else{
					$params[]=$value;
				}
			}
		}
		$this->utils->debug_log('final params ', $params);

		$isCsv=$exportParams['export_format']==Report_model::EXPORT_FORMAT_CSV;

		if($exportParams['export_type']==Report_model::EXPORT_TYPE_QUEUE){

			if(empty($lang)){
				$this->load->library(['language_function']);
				$lang=$this->language_function->getCurrentLanguage();
			}

			if($isCsv){
				$token=$this->lib_queue->addExportCsvJob($funcName, $params, $callerType, $caller, $state, $lang);
			}else{
				$token=$this->lib_queue->addExportExcelJob($funcName, $params, $callerType, $caller, $state, $lang);
			}

			$link=site_url('/export_data/queue/'.$token);

			$this->utils->recordAction($managementName, $actionName, $link);

			return ['success'=>$success, 'link'=>$link, 'token'=>$token];
		}else{

			if(method_exists($this->report_model, $funcName)){

				//call
				$result = call_user_func_array(array($this->report_model, $funcName), $params);
				$d = new DateTime();
				$filename=$funcName.'_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999);
				if($isCsv){
					$link = $this->utils->create_csv($result, $filename);
				}else{
					$from_DT_ajax=true;
					$link = $this->utils->create_excel($result, $filename, $from_DT_ajax);
				}

			}else{
				$success=false;
				$link=null;
				$filename=null;
			}

			$this->utils->recordAction($managementName, $actionName, $link);

			return ['success'=>$success, 'link'=>$link, 'filename'=>$filename];
		}
	}

	// public function exportByData($funcName, $result){

	// 	$this->load->library(['lib_queue']);

	// 	$exportParams=$this->getReportFormatAndType();

	// 	$isCsv=$exportParams['export_type']==Report_model::EXPORT_FORMAT_CSV;

	// 	$d = new DateTime();
	// 	$filename=$funcName.'_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999);
	// 	if($isCsv){
	// 		$link = $this->utils->create_csv($result, $filename);
	// 	}else{
	// 		$from_DT_ajax=true;
	// 		$link = $this->utils->create_excel($result, $filename, $from_DT_ajax);
	// 	}

	// 	return ['link'=>$link, 'filename'=>$filename];
	// }

}

