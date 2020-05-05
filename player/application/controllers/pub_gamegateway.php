<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/BaseController.php';

class Pub_gamegateway extends BaseController {

	const TAG_CODE_FISHING_GAME     = 'fishing_game';
    const TAG_CODE_SLOT             = 'slots';
    const TAG_CODE_LOTTERY          = 'lottery';
    const TAG_CODE_LIVE_DEALER      = 'live_dealer';
    const TAG_CODE_CASINO           = 'casino';
    const TAG_CODE_GAMBLE           = 'gamble';
    const TAG_CODE_TABLE_GAMES      = 'table_games';
    const TAG_CODE_TABLE_AND_CARDS  = 'table_and_cards';
    const TAG_CODE_CARD_GAMES       = 'card_games';
    const TAG_CODE_E_SPORTS         = 'e_sports';
    const TAG_CODE_FIXED_ODDS       = 'fixed_odds';
    const TAG_CODE_ARCADE           = 'arcade';
    const TAG_CODE_HORCE_RACING     = 'horce_racing';
    const TAG_CODE_PROGRESSIVES     = 'progressives';
    const TAG_CODE_SPORTS           = 'sports';
    const TAG_CODE_UNKNOWN_GAME     = 'unknown';
    const TAG_CODE_VIDEO_POKER      = 'video_poker';
    const TAG_CODE_POKER            = 'poker';
    const TAG_CODE_MINI_GAMES       = 'mini_games';
    const TAG_CODE_OTHER            = 'others';
    const TAG_CODE_SOFT_GAMES       = 'soft_games';
    const TAG_CODE_SCRATCH_CARD     = 'scratch_card';

    const GAME_PLATFORM_DESKTOP = "desktop";
    const GAME_PLATFORM_MOBILE = "mobile";

    const REQUIRED_FIELDS = ["operator","game_platform_id","platform"];

    const SUCCESS = 0;
    //ERROR CODE
    const INVALID_PLATFORM = 101;
    const INVALID_GAME_PLATFORM_ID = 102;
    const INVALID_URI = 103;
    const INVALID_GAME_TYPE = 104;
    const INVALID_METHOD = 105;
    const INVALID_REQUEST = 106;
    const INVALID_OPERATOR = 107;

	public function __construct() {
		parent::__construct();
        $this->load->library('game_list_lib');
        $this->CI->load->model(['game_description_model','game_type_model','external_system']);
	}

	public function get_frontend_games_get($game_platform_id = null,$game_platform = null, $game_type_code = null){
		$request = null;

		// if($this->isPostMethod()){
  //   		$this->output->set_header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
  //       	$this->output->set_header('Access-Control-Allow-Credentials: true');
  //       	$this->output->set_content_type('application/json');
  //       	$request = file_get_contents('php://input');
  //   	}
 
    	$data = $this->getGamelist($game_platform_id, $game_platform, $game_type_code, $request);
        # OUTPUT
        $this->returnJsonResult($data);
	}

	public function get_frontend_games($game_platform_id = null,$game_platform = null, $game_type_code = null){

		$game_apis = $this->CI->external_system->getActivedGameApiList();

		try {

			// if(!$this->isPostMethod()){
			// 	$error = $this->getErrorCode(self::INVALID_METHOD);
			// 	throw new Exception(json_encode($error));
			// }

        	if(!isset($_POST) || empty($_POST)) { 
                if(!( $_GET )){
                    $this->output->set_content_type('application/json');
                    $request = file_get_contents('php://input');
                } else {
                    $request = $this->getJsonPData();
                }
        		
			} else {
				$request = $this->getFormData();
			}
            
        	$json_params = json_decode($request,true);
        	if(!$json_params){
        		$error = $this->getErrorCode(self::INVALID_REQUEST);
				throw new Exception(json_encode($error));
        	}

        	$keys = array_keys($json_params);
        	$required_field_exist = !array_diff(self::REQUIRED_FIELDS, $keys);
        	if(!$required_field_exist){
        		$error = $this->getErrorCode(self::INVALID_REQUEST);
				throw new Exception(json_encode($error));
        	}

        	$operator = isset($json_params['operator']) ? $json_params['operator'] : null;
        	$config = $this->config->item('gamelist_api_operator');		
        	if(empty($operator) || !$config || !array_key_exists($operator, $config)) {
        		$error = $this->getErrorCode(self::INVALID_OPERATOR);
				throw new Exception(json_encode($error));
        	}

        	$game_platform_id = isset($json_params['game_platform_id']) ? $json_params['game_platform_id'] : null;
        	$game_platform = isset($json_params['platform']) ? $json_params['platform'] : null;
        	$game_type_code = isset($json_params['game_type']) ? $json_params['game_type'] : null;

			if(empty($game_platform_id) || !in_array($game_platform_id, $config[$operator]['available_games'])) {
				$error = $this->getErrorCode(self::INVALID_GAME_PLATFORM_ID);
				throw new Exception(json_encode($error));
			}

			if(empty($game_platform) && ($game_platform != self::GAME_PLATFORM_DESKTOP && $game_platform != self::GAME_PLATFORM_MOBILE)) {
				$error = $this->getErrorCode(self::INVALID_PLATFORM);
				throw new Exception(json_encode($error));
			}
			$response = $this->getGamelist($game_platform_id, $game_platform, $game_type_code, $request);
		} catch (Exception $e) {
			$this->utils->debug_log('error',  $e->getMessage());
			$response = json_decode($e->getMessage());
		}
        if(!( $_GET )){
            $this->returnJsonResult($response);
        } else {
            $this->returnJsonpResult($response);
        }
		return;
	}

    private function getJsonPData(){
        $data = ( $_GET );
        $data = array(
            "operator" => $data['operator'],
            "game_platform_id" => $data['game_platform_id'],
            "platform" => $data['platform'],
            "game_type" => $data['game_type'],
        );
        return json_encode($data,true);
    }

	private function getFormData(){
		$data = array(
			"operator" => $this->input->post('operator'),
			"game_platform_id" => $this->input->post('game_platform_id'),
			"platform" => $this->input->post('platform'),
			"game_type" => $this->input->post('game_type'),
		);
		if($this->input->post('top_game_code')){
			if (is_array($this->input->post('top_game_code'))){
				$data['top_game_code'] = $this->input->post('top_game_code');
			} else {
				$data['top_game_code'] = json_decode($this->input->post('top_game_code'),true);
			}
		}

		if($this->input->post('order_by')){
			$data['order_by'] = $this->input->post('order_by');
		}
		return json_encode($data,true);
	}

	private function checkJsonParams($array){
		$required_fields = ["operator","game_platform_id","platform"];
		$keys = array_keys($array);
		
	}


	/**
	 * overview : get current game list and url 
	 *
	 * @param  int $game_platform_id
	 * @param  string $game_platform
	 * @param  string $game_type_code
	 */
	private function getGamelist($game_platform_id, $game_platform, $game_type_code,$request = null){
		$game_apis = $this->CI->external_system->getActivedGameApiList();
		$result = array();
		if(empty($game_platform_id) || empty($game_platform) ) {
			return $this->getErrorCode(self::INVALID_URI);
            // $data = $this->game_list_lib->getGameProviderDetails();
            // if (!$this->CI->utils->isEnabledFeature('allow_generate_inactive_game_api_game_lists')) {
            //     $temp_data = [];
            //     foreach ($data['available_game_providers'] as $key => $game_provider) {
            //         if(in_array($game_provider['game_platform_id'], $game_apis)){
            //             $temp_data[$key] = $game_provider;
            //         }
            //     }
            //     $data['available_game_providers'] = $temp_data;
            //     unset($temp_data);
            // }
        } else {
        	if(!$this->CI->utils->isEnabledFeature('allow_generate_inactive_game_api_game_lists')) {
                if (!in_array($game_platform_id, $game_apis))
                    return false;
            }
            $result = $this->processGamelist($game_platform_id, $game_platform, $game_type_code, $request);
        }

        return $result;
	}

	private function processGamelist($game_platform_id, $game_platform, $game_type_code, $request = null){

        if (!in_array($game_platform, [self::GAME_PLATFORM_MOBILE,self::GAME_PLATFORM_DESKTOP])) {
        	return $this->getErrorCode(self::INVALID_PLATFORM);
        }

		$order_by_field = null;
        $order_by = "game_name";//default order

        if(!empty($request)){
            $request = json_decode($request,true);

            if(isset($request['top_game_code'])){
            	 $order_by_field = isset($request['top_game_code']) ? $request['top_game_code'] : null;
            	 krsort($order_by_field);
            }
            
            $available_order_by_field = array("game_code","game_name");
            if(isset($request['order_by']) && (in_array($request['order_by'], $available_order_by_field))){//optional
                $order_by = $request['order_by'];
            }
        }

        $select = "*";
        $where = "flag_show_in_site = 1 and status = 1 and external_game_id != 'unknown' and game_code != 'unknown' and game_platform_id = " . $game_platform_id;

        switch ($game_platform) {
            case self::GAME_PLATFORM_MOBILE:
                $where.=" and mobile_enabled = " . GAME_DESCRIPTION_MODEL::DB_TRUE;
                break;
            case self::GAME_PLATFORM_DESKTOP:
                $where.=" and mobile_enabled != " . GAME_DESCRIPTION_MODEL::DB_TRUE;
                break;
            default:
                break;
        }
        $rows= array();
        if ( ! in_array($game_platform_id,GAME_DESCRIPTION_MODEL::GAME_API_WITH_LOBBYS)) {
        	if (isset($this->game_list_lib->getGameProviderDetails()['available_game_providers'][$game_platform_id])) {
        		if ($game_type_code && !in_array($game_type_code, ['null','false'])) {
        			$game_type_id = $this->CI->game_type_model->getGameTypeIdGametypeCode($game_platform_id,$game_type_code);
        			if (!empty($game_type_id)) {
        				$where .= " and game_type_id = " . $game_type_id;
                        $rows = $this->CI->game_description_model->getGameByQuery($select,$where,null,null,null, $order_by_field, $order_by);
        			} else {
        				return $this->getErrorCode(self::INVALID_GAME_TYPE);
        			}
        		}
        		$rows = $this->CI->game_description_model->getGameByQuery($select,$where,null,null,null, $order_by_field, $order_by);
        	}else{
                return $this->getErrorCode(self::INVALID_GAME_PLATFORM_ID);
            }
        }

        $this->preprocessGamelist($rows);
        return $rows;
	}

	private function preprocessGamelist(&$rows){
		$rows = $this->distinct($rows);
		if(!empty($rows)){
			foreach ($rows as $key => $value) {
				$rows[$key] = $this->prepareGamelist($value);
			}
		}
	}

	private function distinct($rows) {
		$result = [];
		for($i = 0; $i < sizeof($rows); $i++)
			if (!array_key_exists($rows[$i]['game_code'], $result))
				$result[$rows[$i]['game_code']] = $rows[$i];
            
		return $result;
	}
	

	private function prepareGamelist($game){
		$game_api_details = $this->game_list_lib->getGameProviderDetails();
		$game_type_code = json_decode(json_encode($this->CI->game_type_model->getGameTypeById($game['game_type_id'])),true)['game_type_code'];
		$game_name = json_decode(str_replace("_json:", "", $game['game_name']),true);
		$game_launch_url = $game_api_details['available_game_providers'][$game['game_platform_id']]['game_launch_url'];
		$this->processGameName($game_name);

		$attributes = null;
        if (isset($game['attributes']))
            $attributes = $game['game_launch_code_other_settings'] = json_decode($game['attributes'],true);

		$game_list = [
            'game_type_code'    => $game_type_code,
            'game_name'         => $game_name,
            'provider_name'     => $game_api_details['available_game_providers'][$game['game_platform_id']]['complete_name'],
            'in_flash'          => $game['flash_enabled'],
            'in_html5'          => $game['html_five_enabled'],
            'in_mobile'         => $game['mobile_enabled'],
            'status'            => $game['status'],
            'flag_new_game'     => $game['flag_new_game'],
            'progressive'       => $game['progressive'],
            'game_code'       	=> $game['game_code'],
            'sub_category' 		=> $game['attributes'],
            'game_launch_url'   => $this->processGameUrls($game_launch_url,$game,$game_type_code,$attributes),
        ];

		return $game_list;
	}

	private function processGameUrls($game_launch_url, $game, $game_type_code = null, $attributes = null){
		$lang = $this->utils->getPlayerCenterLanguage();
        switch ($lang) {
            case LANGUAGE_FUNCTION::INT_LANG_CHINESE:
                $lang = "zh-cn";
                break;
            default:
                $lang = "en-us";
                break;
        }
		switch ($game['game_platform_id']) {
            case PT_API:
                if ($game_type_code == GAME_LIST_LIB::TAG_CODE_LIVE_DEALER)
                    $game_launch_url_arr['remarks'] = "Demo/Trial is not avaialable";
                if($game['html_five_enabled'] || $game['flash_enabled']){
                    $game_launch_url_arr['real'] = $game_launch_url . "/default/" . $game['external_game_id'] . "/real";
                    $game_launch_url_arr['demo'] = $game_launch_url . "/default/" . $game['external_game_id'] . "/trial";
                } 

                if($game['mobile_enabled']){
                    $game_launch_url_arr['real'] = $game_launch_url . "/default/" . $game['external_game_id'] . "/real";
                    $game_launch_url_arr['demo'] = $game_launch_url . "/default/" . $game['external_game_id'] . "/trial";
                }
                break;
            case MG_API:
                $type = ($game_type_code == GAME_LIST_LIB::TAG_CODE_LIVE_DEALER) ? 1:2;
                $game_code = $game['game_code'];
                if(($game_type_code == self::TAG_CODE_LIVE_DEALER)){
                    $game_launch_url_arr['real'] = $game_launch_url . "/".$type."/" . "_mglivecasino/false/real/". $lang;
                    $game_launch_url_arr['demo'] = "N/A";
                } else {
                    $game_launch_url_arr['real'] = $game_launch_url . "/".$type."/" . $game_code . "/true/real/". $lang;
                    $game_launch_url_arr['demo'] = $game_launch_url . "/".$type."/" . $game_code . "/true/fun/". $lang;
                }
                break;
            default:
                $game_launch_url_arr = "unknown";
                break;
        }
        return $game_launch_url_arr;
	}

	private function processGameName(&$game_name){
        if(!empty($game_name)){
            foreach ($game_name as $key => $name) {
                $game_name[$key] = $this->trimString($name);
            }
        } 
	}

	private function trimString($string){
		return rtrim(preg_replace("/\(.+\)/", "", $string));
	}

	private function getErrorCode($errorCode = null){

		switch ($errorCode) {
		    case self::INVALID_PLATFORM:
				$error =  array(
					"code" 		=> "INVALID_PLATFORM",
					"message" 	=> "Invalid platform or platform not exist."
				);
		        break;
		    case self::INVALID_GAME_PLATFORM_ID:
				$error =  array(
					"code" 		=> "INVALID_GAME_PLATFORM_ID",
					"message" 	=> "Invalid platform id or platform id not exist."
				);
		        break;
		    case self::INVALID_URI:
				$error =  array(
					"code" 		=> "INVALID_URI",
					"message" 	=> "Invalid Uri."
				);
		        break;
		    case self::INVALID_GAME_TYPE:
				$error =  array(
					"code" 		=> "INVALID_GAME_TYPE",
					"message" 	=> "Invalid game type."
				);
		        break;
		    case self::INVALID_METHOD:
				$error =  array(
					"code" 		=> "INVALID_METHOD",
					"message" 	=> "Invalid method."
				);
		        break;
		    case self::INVALID_REQUEST:
				$error =  array(
					"code" 		=> "INVALID_REQUEST",
					"message" 	=> "Invalid request."
				);
		        break;
		    case self::INVALID_OPERATOR:
				$error =  array(
					"code" 		=> "INVALID_OPERATOR",
					"message" 	=> "Invalid operator or operator not exist."
				);
		        break;
		    default:
				$error =  array(
					"code" 		=> "INTERNAL_ERROR",
					"message" 	=> "Internal error."
				);
		} 
		return $error;
	}
}

///END OF FILE/////////////////