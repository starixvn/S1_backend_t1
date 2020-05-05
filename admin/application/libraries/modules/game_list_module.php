<?php

trait game_list_module {

	/**
     * [sync_gamelist_from_json description]
     * @param  [string] $game_providers [can sync multiple game provider]
     * @return [json]                   [result of game sync]
     */
    public function sync_gamelist_from_json($game_provider)
    {
        $this->CI->load->model(['game_type_model','game_description_model']);
        
        $gamelist_json_directory = dirname(__FILE__) . '/../../../../submodules/game-lib/models/game_description/json_data';
        $gamelist_json_files = array_diff(scandir($gamelist_json_directory), array('..', '.'));
        
        $is_exist = false;
        
        if (count($gamelist_json_files) == 0) {
            $result = ["Note" => "No available game list."];
            return $this->showErrorMessage($result);
        }

        $game_provider = $this->filter_sub_game_api($game_provider);
       
        foreach ($gamelist_json_files as $file_name) 
        {
            $file_name_arr = explode("-", str_replace(".json", "", $file_name));
            
            $game_platform_id = $file_name_arr[1];

            # records action
            $this->utils->recordAction('game_list', 'dev_manual_sync_gamelist_from_json', "Manual Sync Game List: ".$game_platform_id);

            if (in_array($game_provider, $file_name_arr)) 
            {
                $is_exist = true;
                $game_list_data = file_get_contents($gamelist_json_directory . "/". $file_name);
                
                if (empty($this->validateGameJsonFormat($game_list_data))) continue;
                $game_list = json_decode($game_list_data,true);

                #get game_type_id ids
                $game_types = array_unique(array_column($game_list, 'game_type_code'));

                $temp_game_types = $this->process_game_type_id($game_types,$game_platform_id);

                $game_external_game_ids = [];
                foreach ($game_list as &$game) 
                {
                    #redefined game platform_id
                    $game['game_platform_id'] = $game_platform_id;

                    $game['game_type_id'] = @$temp_game_types[$game['game_type_code']]['game_type_id'];

                    if (empty($game['game_type_id'])) 
                    {
                        $this->utils->debug_log("ERROR game_type_code not valid =======>",$game['game_type_code']);
                        $game = null;
                        continue;
                    }
                    unset($game['game_type_code']);

                    if (!in_array($game['external_game_id'], $game_external_game_ids)) 
                    {
                        array_push($game_external_game_ids, $game['external_game_id']);

                    }else{
                        $this->utils->debug_log("ERROR duplicate game =======>",$game['external_game_id']);
                        $game=null;
                        continue;
                    }
                    
                    $isFieldsAreValid = $this->checkIfFieldsAreValid($game);

                    if (empty($isFieldsAreValid))
                        $game = null;

                }
                unset($game_external_game_ids);
                return json_encode($this->CI->game_description_model->devSyncGameDescriptionInGameGateway($game_list),JSON_PRETTY_PRINT);
            }
        }

        if (empty($is_exist)) {
            $result = [
                "Note"=>"ERROR Game Provider: [".$game_provider."] not found on game list!",
                "List of available Game provider"=>$gamelist_json_files
            ];
            return $this->showErrorMessage($result);
        }
    }

    /**
     * [checkIfFieldsAreValid
     *  - check if all required fields exist
     *  - check if all fields are valid
     *  - check min and max input per field
     * ]
     * @param  [array] &$game            [game attributes]
     * @return [boolean]                   [description]
     */
    private function checkIfFieldsAreValid(&$game)
    {
    	$fields_datatypes = [
					            "int" => [
					            			"game_type_id",
					            		  	"game_platform_id",
					            		  	"related_game_desc_id",
					            		  	"game_order"
					            		 ],
					            "boolean" => [
					            				"flag_show_in_site",
						                        "enabled_on_ios",
						                        "enabled_on_android",
						                        "progressive",
						                        "html_five_enabled",
						                        "flash_enabled",
						                        "flag_new_game",
						                        "dlc_enabled",
						                        "mobile_enabled",
						                        "enabled_freespin",
						                        "status",
						                        "void_bet",
						                        "no_cash_back"
					                      	 ],
					            "text" => [
					            			"game_name",
					            			"english_name",
					            			"attributes",
					            			"demo_link",
					            			"game_code",
					            			"external_game_id",
					            			"client_id",
					            			"moduleid",
					            			"sub_game_provider",
					            			"note"
					            		  ],
					        ];

        $success = true;
        $game_fields = array_keys($game);
        $missing_required_fields = $min_max_result = [];

        foreach (GAME_DESCRIPTION_MODEL::GAME_LIST_REQUIRED_FIELDS as $required_field) 
        {
            if (!in_array($required_field, $game_fields)) {
                array_push($missing_required_fields, $required_field);
            }
        }

        $game_list_valid_fields = array_keys(GAME_DESCRIPTION_MODEL::GAME_LIST_VALID_FIELDS_WITH_LIMIT);

        foreach ($game_fields as $field) {
            if ($field == "release_date") {
                //ignore
               continue;
            }
            if (!in_array($field,$game_list_valid_fields)){
                $success = false;
                $this->utils->debug_log("ERROR Invalid Field ======>[external_game_id=".$game['external_game_id']."]",$field);
            }
            
            if (in_array($field, $fields_datatypes['text'])) {
                if (strlen($game[$field]) > GAME_DESCRIPTION_MODEL::GAME_LIST_VALID_FIELDS_WITH_LIMIT[$field]['max']) {
                    array_push($min_max_result, [$field=>strlen($game[$field])]);
                    $success = false;
                }
            }

            if (in_array($field, $fields_datatypes['int']) || in_array($field, $fields_datatypes['boolean'])) {
                if ((int)$game[$field] > (int)GAME_DESCRIPTION_MODEL::GAME_LIST_VALID_FIELDS_WITH_LIMIT[$field]['max']) {
                    array_push($min_max_result, [$field=>$game[$field]]);
                    $success = false;
                }
                if ((int)$game[$field] < (int)GAME_DESCRIPTION_MODEL::GAME_LIST_VALID_FIELDS_WITH_LIMIT[$field]['min']) {
                    $game[$field]= false;
                }
            }

        }

        if (count($min_max_result) > 0)
            $this->utils->debug_log("ERROR Min/Max not met ======>[external_game_id=".$game['external_game_id']."]",$min_max_result);

        if (count($missing_required_fields) > 0)
            $this->utils->debug_log("ERROR Missing Required Field[s] ======>[external_game_id=".$game['external_game_id']."]",$missing_required_fields);

        unset($missing_required_fields,$min_max_result);
        return $success;
    }

    /**
     * [process_game_type_id description]
     * @param  [array] $game_types     [list of game types from game list]
     * @param  [int] $game_platform_id [defined system id]
     * @return [int]                   [returns game_type_id]
     */
    private function process_game_type_id($game_types,$game_platform_id)
    {
    	$game_tags = $this->CI->game_type_model->getAllGameTags();
        $temp_game_types = [];
        foreach ($game_types as $game_type) 
        {
            foreach ($game_tags as $key => $game_tag) 
            {
                if ($game_type == $game_tag['tag_code']) 
                {
                    $extra =[
                        'game_type' => $game_tag['tag_name'],
                        'game_type_code' => $game_tag['tag_code'],
                    ];

                    $temp_game_types[$game_type]=[
                        'game_type_id' => $this->CI->game_type_model->checkGameType($game_platform_id,$game_tag['tag_name'],$extra),
                    ];
                }
            }

            if ($game_type =='yoplay' && in_array($game_platform_id, [AGIN_API,T1AGIN_API])) 
            {
                $game_type_name = $extra['game_type'] = '_json:{"1":"Yoplay","2":"Yoplay","3":"Yoplay","4":"Yoplay","5":"Yoplay"}';
                $extra['game_type_code'] = "yoplay";
                
                $temp_game_types[$game_type] = [
                    'game_type_id' => $this->CI->game_type_model->checkGameType($game_platform_id,$game_type_name,$extra),
                ];
            }

            if ($game_type =='tip' && in_array($game_platform_id, [BBIN_API,GSBBIN_API,T1BBIN_API,AGBBIN_API])) 
            {
                $game_type_name = $extra['game_type'] = '_json:{"1":"BB Tip","2":"BB 小费","3":"BB Tip","4":"BB Tip","5":"BB Tip"}';
                
                $temp_game_types[$game_type] = [
                    'game_type_id' => $this->CI->game_type_model->checkGameType($game_platform_id,$game_type_name,$extra),
                ];
            }
        }

        return $temp_game_types;
    }

    /**
     * [validateGameJsonFormat validate if game list json is valid]
     * @param  [string] $game_list [path of the file]
     */
    private function validateGameJsonFormat($game_list)
    {
        $game_list = json_decode($game_list);

        $success = false;
        switch (json_last_error()) 
        {
            case JSON_ERROR_NONE:
                $success = true;
            break;
            case JSON_ERROR_DEPTH:
                $message = ' - Maximum stack depth exceeded';
            break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = ' - Underflow or the modes mismatch';
            break;
            case JSON_ERROR_CTRL_CHAR:
                $message = ' - Unexpected control character found';
            break;
            case JSON_ERROR_SYNTAX:
                $message = ' - Syntax error, malformed JSON';
            break;
            case JSON_ERROR_UTF8:
                $message = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
            default:
                $message = ' - Unknown error';
            break;
        }
        if (!empty($message))
            $this->utils->debug_log("ERROR validateGameJsonFormat =======>",$message);

        return $success;
    }

    /*
     * This function filters sub game api if sub not found will return original game_platform_id
     * @param int $game_platform_id 
     */
    private function filter_sub_game_api($game_platform_id)
    {
    	switch ($game_platform_id) {
            case T1ONEWORKS_API:
                return ONEWORKS_API;
                break;

            case T1PT_API:
                return PT_API;
                break;

    		case GSPT_API:
    			return IMPT_API;
    			break;
    		
    		case T1JUMB_API:
    			return JUMB_GAMING_API;
    			break;

    		case EBET_MG_API:
    			return LAPIS_API;
    			break;

            case T1GG_API:
                return FISHINGGAME_API;
                break;

            case T1VR_API:
                return VR_API;
                break;

            case T1FG_ENTAPLAY_API:
    		case FG_ENTAPLAY_API:
    			return FG_API;
    			break;

    		case FADA_LD_LOTTERY_API:
    			return LD_LOTTERY_API;
    			break;

    		case EBET_THB_API:
    		case EBET_USD_API:
    			return EBET_API;
    			break;

    		case T1SBTECH_BTI_API:
            case SBTECH_BTI_API:
    			return SBTECH_API;
    			break;

            case T1EVOLUTION_API:
    			return EVOLUTION_GAMING_API;
                break;

            case EVOLUTION_SEAMLESS_THB1_API:
                return EVOLUTION_SEAMLESS_GAMING_API;
                 break;
                 
    		case MTECH_OG_API:
    			return OG_API;
    			break;

            case HABANERO_SEAMLESS_GAMING_API:
    		case MTECH_HB_API:
    		case HB_IDR1_API:
    		case HB_IDR2_API:
    		case HB_IDR3_API:
    		case HB_IDR4_API:
            case HB_IDR5_API:
            case HB_IDR6_API:
            case HB_IDR7_API:
    		case HB_THB1_API:
    		case HB_THB2_API:
    		case HB_VND1_API:
            case HB_VND2_API:
            case HB_VND3_API:
    		case HB_CNY1_API:
    		case HB_CNY2_API:
    		case HB_MYR1_API:
    		case HB_MYR2_API:
            case T1HB_API:
    			return HB_API;
    			break;

            case T1PRAGMATICPLAY_API:
    		case PRAGMATICPLAY_IDR1_API:
    		case PRAGMATICPLAY_IDR2_API:
    		case PRAGMATICPLAY_IDR3_API:
    		case PRAGMATICPLAY_IDR4_API:
            case PRAGMATICPLAY_IDR5_API:
            case PRAGMATICPLAY_IDR6_API:
            case PRAGMATICPLAY_IDR7_API:
    		case PRAGMATICPLAY_THB1_API:
    		case PRAGMATICPLAY_THB2_API:
    		case PRAGMATICPLAY_CNY1_API:
    		case PRAGMATICPLAY_CNY2_API:
    		case PRAGMATICPLAY_VND1_API:
            case PRAGMATICPLAY_VND2_API:
    		case PRAGMATICPLAY_VND3_API:
    		case PRAGMATICPLAY_MYR1_API:
            case PRAGMATICPLAY_MYR2_API:
            case PRAGMATICPLAY_SEAMLESS_THB1_API:
    			return PRAGMATICPLAY_API;
    			break;

            case T1MGPLUS_API:
    		case MGPLUS_IDR1_API:
    		case MGPLUS_IDR2_API:
    		case MGPLUS_IDR3_API:
    		case MGPLUS_IDR4_API:
            case MGPLUS_IDR5_API:
            case MGPLUS_IDR6_API:
            case MGPLUS_IDR7_API:
    		case MGPLUS_THB1_API:
    		case MGPLUS_THB2_API:
    		case MGPLUS_CNY1_API:
    		case MGPLUS_CNY2_API:
    		case MGPLUS_VND1_API:
    		case MGPLUS_VND2_API:
            case MGPLUS_VND3_API:
    		case MGPLUS_MYR1_API:
    		case MGPLUS_MYR2_API:
    			return MGPLUS_API;
    			break;

            case ISB_IDR1_API:
            case ISB_IDR2_API:
            case ISB_IDR3_API:
            case ISB_IDR4_API:
            case ISB_IDR5_API:
            case ISB_IDR6_API:
            case ISB_IDR7_API:
            case ISB_THB1_API:
            case ISB_THB2_API:
            case ISB_THB3_API:
            case ISB_THB4_API:
            case ISB_VND1_API:
            case ISB_VND2_API:
            case ISB_VND3_API:
            case ISB_VND4_API:
            case ISB_VND5_API:
            case ISB_CNY1_API:
            case ISB_CNY2_API:
            case ISB_CNY3_API:
            case ISB_CNY4_API:
            case ISB_MYR1_API:
            case ISB_MYR2_API:
            case ISB_MYR3_API:
            case ISB_MYR4_API:
            case T1ISB_API:
                return ISB_API;
                break;

            case MTECH_BBIN_API:
            case AGBBIN_API:
            case GSBBIN_API:
            case T1BBIN_API:
                return BBIN_API;
                break;

            case T1AB_API:
                return AB_API;
                break;

            case T1DG_API:
                return DG_API;
                break;

            case T1DT_API:
                return DT_API;
                break;

            case T1PNG_API:
                return PNG_API;
                break;

            case T1TTG_API:
                return TTG_API;
                break;

            case T1AGIN_API:
            case T1YOPLAY_API:
            case YOPLAY_API:
                return AGIN_API;
                break;

            case T1GD_API:
            case GD_SEAMLESS_API:
                return GD_API;
                break;

            case T1QT_API:
                return QT_API;
                break;

            case T1IPM_V2_SPORTS_API:
                return IPM_V2_SPORTS_API;
                break;

            case T1SA_GAMING_API:
                return SA_GAMING_API;
                break;

            case T1MWG_API:
                return MWG_API;
                break;

            case T1RTG_MASTER_API:
                return RTG_MASTER_API;
                break;

            case T1LE_GAMING_API:
                return LE_GAMING_API;
                break;

            case T1TIANHAO_API:
                return TIANHAO_API;
                break;

            case T1HG_API:
                return HG_API;
                break;

            case T1EVOLUTION_API:
                return EVOLUTION_GAMING_API;
                break;

            case T1LOTUS_API:
                return LOTUS_API;
                break;

            case T1SBTECH_BTI_API:
                return SBTECH_BTI_API;
                break;

            case T1CQ9_API:
                return CQ9_API;
                break;

            case T1OG_API:
            case T1MG_API:
                return MG_API;
                break;

            case T1SUNCITY_API:
                return SUNCITY_API;
                break;

            case T1KYCARD_API:
                return KYCARD_API;
                break;

            case TANGKAS1_IDR_API:
            case TANGKAS1_CNY_API:
            case TANGKAS1_USD_API:
            case TANGKAS1_VND_API:
            case TANGKAS1_MYR_API:
                return TANGKAS1_API;
                break;
            
            case T1SPADE_GAMING_API:
                return SPADE_GAMING_API;
                break;

            case HOGAMING_SEAMLESS_API:
            case HOGAMING_IDR_B1_API:
                return HOGAMING_API;
                break;
                
            case VIVOGAMING_IDR_B1_API:
            case VIVOGAMING_IDR_B1_ALADIN_API:
                return VIVOGAMING_API;
                break;

            case ISIN4D_IDR_B1_API:
            case ISIN4D_CNY_B1_API:
            case ISIN4D_THB_B1_API:
            case ISIN4D_USD_B1_API:
            case ISIN4D_VND_B1_API:
            case ISIN4D_MYR_B1_API:
                return ISIN4D_API;
                break;

            case ION_GAMING_IDR1_API:
                return ION_GAMING_API;

            case QQKENO_QQLOTTERY_IDR_B1_API:
            case QQKENO_QQLOTTERY_CNY_B1_API:
            case QQKENO_QQLOTTERY_THB_B1_API:
            case QQKENO_QQLOTTERY_USD_B1_API:
            case QQKENO_QQLOTTERY_VND_B1_API:
                return QQKENO_QQLOTTERY_API;
                break;

            case NTTECH_IDR_B1_API:
            case NTTECH_CNY_B1_API:
            case NTTECH_THB_B1_API:
            case NTTECH_USD_B1_API:
            case NTTECH_VND_B1_API:
                return NTTECH_API;
                break;

            case NTTECH_V2_IDR_B1_API:
            case NTTECH_V2_CNY_B1_API:
            case NTTECH_V2_THB_B1_API:
            case NTTECH_V2_USD_B1_API:
            case NTTECH_V2_VND_B1_API:
                return NTTECH_V2_API;
                break;

            case IBC_24TECH_IDR_B1_API:
            case IBC_24TECH_CNY_B1_API:
            case IBC_24TECH_THB_B1_API:
            case IBC_24TECH_USD_B1_API:
            case IBC_24TECH_VND_B1_API:
                return IBC_24TECH_API;
                break;

            case ONEBOOK_IDR_B1_API:
            case ONEBOOK_CNY_B1_API:
            case ONEBOOK_THB_B1_API:
            case ONEBOOK_USD_B1_API:
            case ONEBOOK_VND_B1_API:
                return ONEBOOK_API;
                break;

            case SBOBETGAME_IDR_B1_API:
            case SBOBETGAME_CNY_B1_API:
            case SBOBETGAME_THB_B1_API:
            case SBOBETGAME_USD_B1_API:
            case SBOBETGAME_VND_B1_API:
            case SBOBETV2_GAME_API:
            case SBOBET_SEAMLESS_GAME_API:
                return SBOBETGAME_API;
                break;

            case FLOW_GAMING_SEAMLESS_THB1_API:
                return FLOW_GAMING_SEAMLESS_API;
                break;

            case T1RG_API:
                return RG_API;
                break;

            case GENESIS_SEAMLESS_THB1_API:
                return GENESIS_SEAMLESS_API;
                break;

            case KING_MAKER_GAMING_THB_B1_API:
                return KING_MAKER_GAMING_API;
                break;

            case AG_SEAMLESS_THB1_API:
                return AG_SEAMLESS_GAME_API;
                break;
                
    		default:
    			return $game_platform_id;
    			break;
    	}
    }

    /*
     * do_sync_game_list_from_gamegateway 
     * - this feature will sync game list from gamegateway to client database
     * @param  [string] $game_platform_id - canbe null, means all games
     */
	public function do_sync_game_list_from_gamegateway($game_platform_id=null, $processActiveGames = false)
	{    
        $this->CI->load->model(['game_description_model','game_type_model','external_system']);

        $active_game_apis = $this->CI->external_system->getAllActiveSytemGameApi();

        $game_api_to_sync_gamelist = $active_game_apis;
        if($game_platform_id)
        {
        	$game_api_to_sync_gamelist = $this->processValidGameApi($game_platform_id,$active_game_apis);	
        	if($game_api_to_sync_gamelist['success'])
        	{
        		$game_api_to_sync_gamelist = $game_api_to_sync_gamelist['game_api'];
        	}
        	else
        	{
        		echo json_encode($game_api_to_sync_gamelist['message']); return;
        	}
        }
        echo $this->processSyncGameList($game_api_to_sync_gamelist, $processActiveGames);
    }

    private function processSyncGameList($game_api_to_sync_gamelist, $processActiveGames = false)
    {
    	$exempted_game_type_codes_for_sync = $this->getExemptedGameTypeForSync();
    	$game_list_src_url = $this->CI->utils->getConfig("game_list_api_url")."/game_description/getAllGames";
        if ($processActiveGames === true) {
            $game_list_src_url = $this->CI->utils->getConfig("game_list_api_url")."/game_description/getAllActiveGames";
        }
    	$game_tags = $this->CI->game_type_model->getAllGameTags();
        $standard_game_type_codes = array_column($game_tags, 'tag_code');

        foreach ($game_api_to_sync_gamelist as $key => $row) 
        {
            $game_map = [];
            $game_provider_id = $row['id'];
            $orig_game_provider_id = $this->filter_sub_game_api($game_provider_id);
            $gamelist_api_url = $game_list_src_url ."/". $orig_game_provider_id;
            $game_gateway_games = file_get_contents($gamelist_api_url);
            $this->utils->debug_log("gamelist_api_url ==========>" . $gamelist_api_url);

            # Get game list from gamegateway
            $game_gateway_games_list = json_decode($game_gateway_games,true);

            if ($processActiveGames === true) {
                $aGameCodes = array_column($game_gateway_games_list, 'game_code');
                $this->utils->debug_log("gamelist_api_url ==========>" . $game_provider_id);
                $aResult = $this->CI->game_description_model->updateActiveGameList($game_api_to_sync_gamelist['game_platform']['id'], $aGameCodes);
                $iCountResult = count($aResult);
                $response = [
                             "Success" => true,
                             "Updated games count"   => $iCountResult,
                             "Games"  => $aResult
                            ];
                return json_encode($response);
            }

          
          	# check if game api is active
            $api = $this->utils->loadExternalSystemLibObject($game_provider_id);
            if (empty($api)) continue;

            # records action
            $this->utils->recordAction('game_list', 'dev_manual_sync_gamelist_from_gamegateway', "Manual Sync Game List: ".$row['system_name']);

            # Get current game list from client db
            $local_games = $api->getGameList(null,null,null,null,true);

            # check if game list from gamegateway is not empty
            if (empty($game_gateway_games_list)) continue;

            $this->utils->debug_log("sync_current_games ==========>" . $game_provider_id);

            # loop game list data from gamegateway
            foreach ($game_gateway_games_list as $key => $game)
            {
            	# check if game provide id is not empty
            	if (empty($game['game_platform_id'])) continue;

                $game['game_platform_id'] = $game_provider_id;

                # per game preparation
                $game['game_type_id'] = $this->CI->game_type_model->getGameTypeId($game['game_type'],$game['game_platform_id']);

                #don't sync if game type is not standard
                if (!in_array($game['game_type_code'], $exempted_game_type_codes_for_sync)) 
                {
                	if (!in_array($game['game_type_code'], $standard_game_type_codes)) continue;
                }

                if(empty($game['game_type_id']))
                {
                    $game['game_type_id'] = $this->CI->game_type_model->checkGameType($game['game_platform_id'],$game['game_type'],$game);
                }

                foreach (self::DEFAULT_GAMETYPE_KEYS_FOR_UNSET as $key) unset($game[$key]);
                
                # end, ready for game synchronizing
                array_push($game_map,$game);
            }
            
			unset($game_gateway_games_list,$game_gateway_games);

            $response = [
            			 "success"=>true,
            			 "result"=>$this->CI->game_description_model->devSyncGameDescriptionInGameGateway($game_map)
            			];
            return json_encode($response);
        }
    }

    private function processValidGameApi($game_platform_id,$active_game_apis)
    {
    	$game_apis = [];
    	if($game_platform_id && !in_array($game_platform_id, ['null','false'])) {
        	$available_game_providers = array_column($active_game_apis, 'id');
        	$game_api_name = null;

        	// gets the game_api_name here
        	array_walk($active_game_apis,function($k,$v)use($game_platform_id,&$game_api_name){
        		if($k['id'] == $game_platform_id) $game_api_name = $k['system_name'];
        	});

        	if(!in_array($game_platform_id, $available_game_providers)) {
	        	return [
	        			"success"=>false,
	        			"message"=>"Error: Game platform ID Not found!"
	        		   ];
        	}else{
        		$game_apis = ['game_platform'=>['id'=>$game_platform_id,"system_name"=>$game_api_name]];
        	}

        }
        return ["success"=>true,"game_api"=>$game_apis];
    }

    private function getExemptedGameTypeForSync()
    {
    	$exempted_game_type_codes_for_sync = self::DEFAULT_EXEMPTED_GAME_TYPES_CODES_FORSYNC;
    	if (!empty($this->utils->getConfig('exempted_game_type_codes_for_sync')))
    		$exempted_game_type_codes_for_sync = $this->CI->utils->getConfig('exempted_game_type_codes_for_sync');

    	return $exempted_game_type_codes_for_sync;
    }

    private function showErrorMessage($result)
    {
        $this->utils->debug_log("ERROR",$result);
        $result['success'] = true;
        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
