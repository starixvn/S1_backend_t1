<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Redtiger_service_api extends BaseController {

	public function __construct() {

		parent::__construct();

		$this->api = $this->utils->loadExternalSystemLibObject(REDTIGER_SEAMLESS_API);

		$authorization = $this->input->get_request_header('Authorization');
		$authorization = explode(' ', $authorization);
		$authorization = end($authorization);

		if ($authorization != $this->api->api_key) {

			$success = FALSE;
			$data = [
				'message' => 'API аuthentication error',
				'code' => 100,
			];

			$this->output($success, $data);
			$this->output->_display();
			exit();

		}

	}

	public function index($temp1, $temp2 = null) {

		$success = FALSE;
		$data = [];

		$params = file_get_contents('php://input');
		$params = json_decode($params, TRUE);

		if (empty($params)) {
			$params = $this->input->get();
		}

		$function = implode('_', array_filter([$temp1, $temp2]));

		$this->utils->debug_log('REDTIGERSEAMLESS', $function, $params);

		$response_result_id = $this->saveRequest($function, $params);
		$player_id = NULL;

		try {


			list($success, $data) = $this->api->$function($params, $response_result_id, $player_id);
			

		} catch (Exception $e) {

			$success = FALSE;

			$data = [
				'message' => $e->getMessage(),
				'code' => $e->getCode(),
			];

		}

		return $this->output($success, $data, $response_result_id, $player_id);

	}

	private function saveRequest($request_api, $request_params = NULL) {

		$this->db->insert('response_results', array(
			'system_type_id' => $this->api->getPlatformCode(),
			'request_api' => $request_api,
			'request_params' => is_array($request_params) ? json_encode($request_params) : $request_params,
			'created_at' => date('Y-m-d H:i:s'),
		));

		return $this->db->insert_id();

	}

	private function output($success, $data = [], $response_result_id = NULL, $player_id = NULL) {

		$output = ['success' => $success];

		if ($output['success']) {
			$output['result'] = $data;
		} else {
			$output['error']  = $data;
		}

		$output = json_encode($output);

		if ($response_result_id) {

			$this->db->update('response_results', ['content' => $output, 'player_id' => $player_id], ['id' => $response_result_id]);

		}

		return $this->output->set_content_type('application/json')->set_output($output);

	}

}

///END OF FILE////////////