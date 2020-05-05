<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get game type data
 * * Update / Create data
 * * Get unknown game
 * * Shortcode PT/NT
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Game_type_model extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	const SHOW_IN_SITE = 1;
	const AUTO_ADD_TO_CASHBACK = 1;
    const UNKNOWN_GAME_TAG_ID = 16;
    const ACTIVE_GAME_TYPE = 1;

    const UNKNOWN_GAME_TYPE_CODE = 'unknown';

	protected $tableName = "game_type";

	const MAIN_GAME_TYPE_ATTRIBUTES = [
		'game_platform_id',
		'game_type',
		'game_type_lang',
		'related_game_type_id',
		'order_id',
		'auto_add_to_cashback',
		'auto_add_new_game',
		'game_type_code',
		'game_tag_id',
		'note',
		'status',
		'flag_show_in_site',
	];

    const GAME_TYPE_INT_FIELDS = [
		'related_game_type_id',
		'order_id',
		'auto_add_to_cashback',
		'auto_add_new_game',
		'status',
		'flag_show_in_site',
	];

	/**
	 * overview : get game type id
	 *
	 * @param 	string $gameType
	 * $param	null   $gamePlatformId
	 * @return 	int
	 */
	public function getGameTypeId($gameType, $gamePlatformId = null) {
		$this->db->like($this->tableName, $gameType);
		if ($gamePlatformId) {$this->db->where('game_platform_id', $gamePlatformId);}

		$strict_status_game_api_game_type = $this->utils->getConfig('strict_status_game_api_game_type');
		if (!empty($strict_status_game_api_game_type)) {
			if (in_array($gamePlatformId, $strict_status_game_api_game_type)) {
				$this->db->where('status', self::ACTIVE_GAME_TYPE);
			}
		}
		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'id');
	}

	/**
	 * overview : get game type list
	 *
	 * @param array $criteria
	 * @param string $orderby
	 * @param string $direction
	 * @return array
	 */
	public function getGameTypeList($criteria = array(), $orderby = 'order_id', $direction = 'asc') {
		return $this->db->from($this->tableName)
			->not_like('game_type', 'unknown')
			->not_like('game_type', 'Sidegames')
			->not_like('game_type', 'Live Games')
			->where($criteria)
			->order_by($orderby, $direction)
			->get()
			->result_array();
	}

	/**
	 * overview : get All Game Types
	 *
	 * @return array
	 */
	public function getGameTypesArray() {
		$qry = $this->db->get($this->tableName);
		return $qry->result_array();
	}
	/**
	 * overview : get All Game Types
	 *
	 *  @return array
	 */
	public function getGameTypes() {
		$qry = $this->db->get($this->tableName);
		return $qry->result();
	}

	/**
	 * overview : get game types
	 *
	 * @return array
	 */
	public function getGameTypesForDisplay() {
		$this->db
			->select($this->tableName . '.*, external_system.system_code as game_platform_name')
			->from($this->tableName)
			->join('external_system', $this->tableName . '.game_platform_id = external_system.id')
			->order_by('external_system.id')->order_by($this->tableName . '.order_id');
		return $this->db->get()->result_array();
	}

	 /**
	 * overview : get active game platform game types
	 *
	 * @return array
	 */
	public function getActiveGamePlatformGameTypes() {

		$where = array('external_system.status' => self::DB_TRUE, $this->tableName.'.status' => self::DB_TRUE);
		$this->db
		->select($this->tableName. '.*, external_system.system_code as game_platform_name')
		->from($this->tableName)
		->join('external_system', $this->tableName . '.game_platform_id = external_system.id')
		->where($where)
		->order_by('external_system.id')->order_by($this->tableName . '.id');
		$game_arr = $this->db->get()->result_array();

		$active_game_map = [];

		foreach ($game_arr as $v) {
			$active_game_map[$v['game_platform_id']][]  = $v;
		}

		return $active_game_map;
	}

	/**
	 * overview : get game type by id
	 *
	 * @param $id
	 * @return mixed
	 */
	public function getGameTypeById($id) {
		$query = $this->db->get_where($this->tableName, array('id' => $id));
		if ($query->num_rows() > 0) {
			return $query->result()[0]; // return only one result, as ID is unique
		}
	}

	/**
	 * overview : insert data in game type
	 *
	 * @param $data
	 * @return array
	 */
	public function create($data) {
        $this->processMd5FieldsSetFalseIfNotExist($data,self::MAIN_GAME_TYPE_ATTRIBUTES,self::GAME_TYPE_INT_FIELDS);
		if (!isset($data['md5_fields'])) {
	        $data['md5_fields'] = $this->generateMD5SumOneRow($data,self::MAIN_GAME_TYPE_ATTRIBUTES,self::GAME_TYPE_INT_FIELDS);
		}
		if (!isset($data['created_on'])) {
	        $data['created_on'] = $this->utils->getNowForMysql();
		}

		$this->db->insert($this->tableName, $data);
		$gameTypeId = $this->db->insert_id();
		if ($gameTypeId) {
			$this->processGameTypeHistory($data,self::ACTION_ADD,$gameTypeId);
		}
		return $gameTypeId;
	}

	/**
	 * overview : update data
	 * @param $data
	 * @return bool
	 */
	public function update($data) {
		$this->utils->debug_log("-------------------",$data);
		// $this->db->trans_start();
		$idQuery = array('id' => $data['id']);
		unset($data['id']);

		if (!isset($data['updated_at'])) {
	        $data['updated_at'] = $this->utils->getNowForMysql();
		}

        $this->processMd5FieldsSetFalseIfNotExist($data,self::MAIN_GAME_TYPE_ATTRIBUTES,self::GAME_TYPE_INT_FIELDS);

		$actionType = self::ACTION_UPDATE;
		if (isset($data['action']) && $data['action'] == self::ACTION_DELETE) {
			$select = '*';
			$actionType = self::ACTION_DELETE;

			$this->db->select('*')->where('id',$idQuery['id'])->from($this->tableName);
			$result = $this->runOneRowArray();
			$data = $result;
			$data['deleted_at'] = $this->utils->getNowForMysql();
			$data['game_type_code'] = 'del-'.$this->utils->getNowForMysql().'['.$result['game_type_code'].']';

			unset($result);
			unset($data['id']);
		}else{
			$this->db->select('md5_fields')->where('id',$idQuery['id'])->from($this->tableName);
			$result = $this->runOneRowArray();
		}

		if (!isset($data['md5_fields'])) {
	        $data['md5_fields'] = $this->generateMD5SumOneRow($data,self::MAIN_GAME_TYPE_ATTRIBUTES,[]);
		}

		if (empty($result['md5_fields']) || $result['md5_fields'] != $data['md5_fields']) {
			$success = $this->db->update($this->tableName, $data, $idQuery);
			if ($success) {
				$data['game_type_id'] = $idQuery['id'];
				$this->processGameTypeHistory($data,$actionType,$idQuery['id']);
			}
		}
		// $this->db->trans_complete();
		// return $this->db->trans_status(); // Returns TRUE when update executed successfully (even when no row updated)
		return true;
	}

	/**
	 * Add game type to game history table
	 * @param $data
	 * @return bool
	 */
	private function processGameTypeHistory($gameTypeDetails,$action,$gameTypeId = null){
		$gameTypeHistory = $gameTypeDetails;
		if (isset($gameTypeHistory['id']))
			unset($gameTypeHistory['id']);

		$gameTypeHistory['game_type_id'] = $gameTypeId;
        $gameTypeHistory['action'] = $action;

        $success = true;
        foreach (self::MAIN_GAME_TYPE_ATTRIBUTES as $column) {
        	if (!isset($gameTypeHistory[$column])) {
        		$success = false;
        	}
        }

        if ($success)
	        $this->insertData('game_type_history',$gameTypeHistory);

        unset($gameDetails);
    }

	/**
	 * overview : get unknown game type
	 *
	 * @param $systemId
	 * @return array
	 */
	public function getUnknownGameType($systemId) {
		$qry = $this->db->get_where($this->tableName, array('game_platform_id' => $systemId, 'game_type' => 'unknown'));
		return $this->getOneRow($qry);
	}

	/**
	 * overview : create game type
	 *
	 * @param $gameTypeStr
	 * @param $gamePlatformId
	 * @return array
	 */
	public function createGameType($gameTypeStr, $gamePlatformId, $extra) {
		$this->load->model('external_system');
		$system_code = $this->external_system->getNameById($gamePlatformId);

        $extra['game_type'] = ( ! empty($extra['game_type'])) ? $extra['game_type'] : 'unknown';
        $extra['game_type_code'] = ( ! empty($extra['game_type_code'])) ? $extra['game_type_code']: 'unknown';

        if (empty($extra['game_tag_id'])) {
            $extra['game_tag_id'] = $this->getTagIdByTagCode($extra['game_type_code']);
            if (is_array($extra['game_tag_id']) && empty($extra['game_tag_id'])) {
                $extra['game_tag_id'] = SELF::UNKNOWN_GAME_TAG_ID;
            }
        }

        $data = array(
            'game_platform_id' => $gamePlatformId,
            'game_type_lang' => $gameTypeStr,
            'game_type' => $gameTypeStr,
            'game_type_code' => !empty($extra['game_type_code']) ? $extra['game_type_code'] : strtolower(lang($extra['game_type'])),
            'game_tag_id' => !empty($extra['game_tag_id']['id']) ? $extra['game_tag_id']['id'] : $extra['game_tag_id'],
            'status' => self::DB_TRUE,
            'created_on' => $this->utils->getNowForMysql(),
            'flag_show_in_site' => self::DB_TRUE,
            'updated_at' => $this->utils->getNowForMysql(),
        );

        $game_type_id = $this->insertData($this->tableName, $data);

		if ($game_type_id) {
        	$this->processMd5FieldsSetFalseIfNotExist($data,self::MAIN_GAME_TYPE_ATTRIBUTES,self::GAME_TYPE_INT_FIELDS);
			$data['game_type_id'] = $game_type_id;
			if (!isset($data['md5_fields'])) {
		        $data['md5_fields'] = $this->generateMD5SumOneRow($data,self::MAIN_GAME_TYPE_ATTRIBUTES,[]);
			}

			$this->processGameTypeHistory($data,self::ACTION_ADD,$game_type_id);
		}

        return $game_type_id;
	}

	public function createGameTypePerPlatform($gameTypeStr, $gamePlatformId, $extra) {
		$this->load->model('external_system');
		$system_code = $this->external_system->getNameById($gamePlatformId);

        $extra['game_type'] = ( ! empty($extra['game_type'])) ? $extra['game_type'] : 'unknown';
        $extra['game_type_code'] = ( ! empty($extra['game_type_code'])) ? $extra['game_type_code']: 'unknown';

        if (empty($extra['game_tag_id'])) {
            $extra['game_tag_id'] = $this->getTagIdByTagCode($extra['game_type_code']);
            if (is_array($extra['game_tag_id']) && empty($extra['game_tag_id'])) {
                $extra['game_tag_id'] = SELF::UNKNOWN_GAME_TAG_ID;
            }
        }

        $data = array(
            'game_platform_id' => $gamePlatformId,
            'game_type_lang' => $gameTypeStr,
            'game_type' => $gameTypeStr,
            'game_type_code' => !empty($extra['game_type_code']) ? $extra['game_type_code'] : strtolower(lang($extra['game_type'])),
            'game_tag_id' => !empty($extra['game_tag_id']['id']) ? $extra['game_tag_id']['id'] : $extra['game_tag_id'],
            'status' => self::DB_TRUE,
            'created_on' => $this->utils->getNowForMysql(),
            'flag_show_in_site' => self::DB_TRUE,
            'updated_at' => $this->utils->getNowForMysql(),
        );

        $game_type_id = $this->insertData($this->tableName, $data);

		if ($game_type_id) {
        	$this->processMd5FieldsSetFalseIfNotExist($data,self::MAIN_GAME_TYPE_ATTRIBUTES,self::GAME_TYPE_INT_FIELDS);
			$data['game_type_id'] = $game_type_id;
			if (!isset($data['md5_fields'])) {
		        $data['md5_fields'] = $this->generateMD5SumOneRow($data,self::MAIN_GAME_TYPE_ATTRIBUTES,[]);
			}

			$this->processGameTypeHistory($data,self::ACTION_ADD,$game_type_id);
		}

        return $game_type_id;
	}

	/**
	 * overview : create unknown game type
	 *
	 * @param $gamePlatformId
	 * @return array
	 */
	public function createUnknownGameType($gamePlatformId) {
		$this->load->model('external_system');
		$system_code = $this->external_system->getNameById($gamePlatformId);

		$game_type = array(
			'game_platform_id' => $gamePlatformId,
			'game_type' => 'unknown',
			'game_type_lang' => 'unknown',
			'status' => self::DB_TRUE,
			'flag_show_in_site' => self::DB_FALSE,
            'created_on' => $this->utils->getNowForMysql(),
            'updated_at'=>$this->utils->getNowForMysql(),
		);

		$this->db->insert('game_type', $game_type);
		return $this->db->insert_id();
	}

	/**
	 * overview : check game type
	 *
	 * @param $gamePlatformId
	 * @param $gameTypeStr
	 * @return array|int
	 */
	public function checkGameType($gamePlatformId, $gameTypeStr, $extra) {
		$extra['game_platform_id'] = $data['game_platform_id'] = $gamePlatformId;

        $gameTypeId = null;
        #get game type id by game type code
		if (isset($extra['game_type_code'])) {
			$gameTypeId = $this->getGameTypeIdGametypeCode($gamePlatformId,$extra['game_type_code']);
		}

		if ($gameTypeId) {
			return $gameTypeId;
		}elseif(empty($gameTypeId)) {
	    	#double check if game type not exist
	    	$gameTypeId = $this->getGameTypeId($gameTypeStr, $gamePlatformId);
	    	if (empty($gameTypeId)) {
	    		#try get tag_code and get game type id
	    		$gameTag = $this->getGameTagsByTagName($gameTypeStr);
	    		if ($gameTag) {
					$gameTypeId = $this->getGameTypeIdGametypeCode($gamePlatformId,$gameTag['tag_code']);
	    		}
	    	}
	    }

        #if game type id still empty create it
        if (empty($gameTypeId)) {
            if (empty($gameTypeStr)) {
                $unknownGameType = $this->getUnknownGameType($gamePlatformId);

                if (empty($unknownGameType)) {
                    //create it
                    $gameTypeId = $this->createUnknownGameType($gamePlatformId);
                } else {
                    $gameTypeId = $unknownGameType->id;
                }

            } else {
                //create it
                $gameTypeId = $this->createGameType($gameTypeStr, $gamePlatformId, $extra);
            }
        }

        return $gameTypeId;
    }

	public function checkGameTypePerPlatform($gamePlatformId, $gameTypeStr, $extra) {
		$extra['game_platform_id'] = $data['game_platform_id'] = $gamePlatformId;

        $gameTypeId = null;
        #get game type id by game type code
		if (isset($extra['game_type_code'])) {
			$gameTypeId = $this->getGameTypeIdGametypeCode($gamePlatformId,$extra['game_type_code']);
		}

		if ($gameTypeId) {
			return $gameTypeId;
		}elseif(empty($gameTypeId)) {
	    	#double check if game type not exist
	    	$gameTypeId = $this->getGameTypeId($gameTypeStr, $gamePlatformId);
	    	if (empty($gameTypeId)) {
	    		#try get tag_code and get game type id
	    		$gameTag = $this->getGameTagsByTagName($gameTypeStr);
	    		if ($gameTag) {
					$gameTypeId = $this->getGameTypeIdGametypeCode($gamePlatformId,$gameTag['tag_code']);
	    		}
	    	}
	    }

        #if game type id still empty create it
        if (empty($gameTypeId)) {
            if (empty($gameTypeStr)) {
                $unknownGameType = $this->getUnknownGameType($gamePlatformId);

                if (empty($unknownGameType)) {
                    //create it
                    $gameTypeId = $this->createUnknownGameType($gamePlatformId);
                } else {
                    $gameTypeId = $unknownGameType->id;
                }

            } else {
                //create it
                $gameTypeId = $this->createGameTypePerPlatform($gameTypeStr, $gamePlatformId, $extra);
            }
        }

        return $gameTypeId;
    }

    /**
     * [getGameTagsByTagName description]
     * @param  [string] $tag_name [tag name]
     * @return [array]           [description]
     */
    public function getGameTagsByTagName($tag_name){
        $this->db->select("id, tag_name, tag_code")
        		->from('game_tags')
                ->where("tag_name",$tag_name);

        return $this->runOneRowArray();
    }

    /**
     * overview : get game type id by game type code
     * @param $gamePlatformId, $game_type_code
     * @return game type id or null
     */
    public function getGameTypeIdGametypeCode($game_platform_id,$game_type_code){
        $this->db->where(['game_platform_id' => $game_platform_id, 'game_type_code' => $game_type_code]);
        $query = $this->db->get($this->tableName);

        return is_array($query->row('id')) ? null : $query->row('id');
    }

	/**
	 * overview : get game type list
	 * @param $gamePlatformId
	 * @return array
	 */
	public function getGameTypeListByGamePlatformId($gamePlatformId, $bFilterFlagShowInSite = false) {
        $this->load->model('game_description_model');
        $hide_empty_game_type_on_game_tree = $this->utils->isEnabledFeature("hide_empty_game_type_on_game_tree");
        $hide_disabled_games_on_game_tree = $this->utils->isEnabledFeature("hide_disabled_games_on_game_tree");

        $this->db->select('game_type.*');
		$this->db->from($this->tableName)->where('game_type.game_platform_id', $gamePlatformId);

        if ($hide_disabled_games_on_game_tree)
            $this->db->where('game_type.status', GAME_DESCRIPTION_MODEL::ENABLED_GAME);

        if ($bFilterFlagShowInSite)
            $this->db->where('game_type.flag_show_in_site', GAME_DESCRIPTION_MODEL::ENABLED_GAME);

        if ($hide_empty_game_type_on_game_tree) {
        	$this->db->join('game_description','game_description.game_type_id = game_type.id');
            $this->db->where('game_description.status', GAME_DESCRIPTION_MODEL::ENABLED_GAME);
            $this->db->group_by('game_type.id');
        }

		return $this->runMultipleRowArray();
	}

    public function getAllGameTypeList() {
        $this->load->model('game_description_model');
        $this->db->from($this->tableName);

        return $this->runMultipleRowArray();
    }

	public function getTransGameTypeListByGamePlatformId($gamePlatformId) {

		$gameTypes=$this->getGameTypeListByGamePlatformId($gamePlatformId);
		if(!empty($gameTypes)){
			foreach ($gameTypes as &$gameType) {
				$gameType['game_type']=lang($gameType['game_type']);
				$gameType['game_type_lang']=lang($gameType['game_type_lang']);
			}
		}

		return $gameTypes;
	}

	public function getActiveGameTypeListByGamePlatformId($gamePlatformId) {
		$this->db->from($this->tableName);
		$this->db->where(['game_platform_id' => $gamePlatformId, 'flag_show_in_site' => self::SHOW_IN_SITE]);

		echo json_encode($this->runMultipleRowArray());
	}

	public function getAllGameType( $request, $is_export = false ){

		$i = 0;

		$columns = array();

		$columns[] = array(
			'alias' => 'deleted_at',
			'select' => 'game_type.deleted_at',
		);
		if( ! $is_export ){

			$columns[] = array(
				'dt' => $i++,
				'alias' => 'id',
				'select' => 'game_type.id',
				'formatter' => function ($d,$row) use ($is_export) {

					$output = '<a href="javascript:void(0)" title="' . lang("sys.gt23") . '" class="edit-gt" id="edit_gt-' . $d . '" data-row-id="' . $d . '" ><span class="glyphicon glyphicon-edit"></span></a>';

					$output .= '<a href="" title="' . lang("View Game Type") . '" class="viewGameTypeHistory-gt" data-toggle="modal" data-target="#viewGameTypeHistoryModal" id="viewGameHistory_gt-' . $d . '" data-row-id="' . $d . '" ><span class="glyphicon glyphicon-list-alt"></span></a>';

					if (empty($row['deleted_at'])) {
						$output .= '<a href="javascript:void(0)" title="' . lang('Delete this game type') . '" class="delete-gt" id="delete_gt-' . $d . '" data-row-id="'.$d.'"><span style="color:#ff3333" class="glyphicon glyphicon-trash"></span></a>';
					}
					return $output;

				}
			);

		}
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'game_platform',
			'name' => lang("sys.gt7"),
			'select' => 'external_system.system_name',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'game_type',
			'select' => 'game_type.game_type',
			'name' => lang("sys.gt6"),
			'formatter' => 'languageFormatter',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'language_code',
			'select' => 'game_type.game_type_lang',
			'name' => lang("sys.gt8"),
			'formatter' => 'languageFormatter',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'note',
			'name' => lang("sys.gt11"),
			'select' => 'game_type.note',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'game_type_code',
			'name' => lang("Game Type Code"),
			'select' => 'game_type.game_type_code',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'status',
			'select' => 'game_type.status',
			'name' => lang("sys.gt16"),
			'formatter' => function ($d) use ($is_export) {
				if( ! $is_export ){
					$checked = $d ? "checked" : "";
					return '<input disabled="disabled" ' . $checked . ' type="checkbox" class="checkWhite user-success" />';
				}else{
					return $d ? "✓" : "";
				}
			},
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'flag_show_in_site',
			'select' => 'game_type.flag_show_in_site',
			'name' => lang("sys.gt17"),
			'formatter' => function ($d) use ($is_export) {
				if( ! $is_export ){
					$checked = $d ? "checked" : "";
					return '<input disabled="disabled" ' . $checked . ' type="checkbox" class="checkWhite user-success" />';
				}else{
					return $d ? "✓" : "";
				}
			},
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'order_id',
			'select' => 'game_type.order_id',
			'name' => lang("sys.gt19"),
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'auto_add_new_game',
			'select' => 'game_type.auto_add_new_game',
			'name' => lang("sys.gt18"),
			'formatter' => function ($d) use ($is_export) {
				if( ! $is_export ){
					$checked = $d ? "checked" : "";
					return '<input disabled="disabled" ' . $checked . ' type="checkbox" class="checkWhite user-success" />';
				}else{
					return $d ? "✓" : "";
				}
			},
		);
		if(!$this->utils->isEnabledFeature('close_cashback')){
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'auto_add_to_cashback',
				'select' => 'game_type.auto_add_to_cashback',
				'name' => lang("sys.gt18"),
				'formatter' => function ($d) use ($is_export) {
					if( ! $is_export ){
						$checked = $d ? "checked" : "";
						return '<input disabled="disabled" ' . $checked . ' type="checkbox" class="checkWhite user-success" />';
					}else{
						return $d ? "✓" : "";
					}
				},
			);
		}
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'created_on',
			'name' => lang("pay.createdon"),
			'select' => 'game_type.created_on',
		);

		$table = 'game_type';
		$joins = array(
			'external_system' => 'external_system.id = game_type.game_platform_id',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();

		$this->load->library('data_tables');
		$input = $this->data_tables->extra_search($request);
		$where[] = "external_system.system_name <> '' ";
		if (isset($input['game_platform_id'])) {
			$where[] = "game_type.game_platform_id = ?";
			$values[] = $input['game_platform_id'];
		}else{
			//only for active api
			$apiArr=$this->utils->getAllCurrentGameSystemList();
			$where[] = "game_type.game_platform_id in ( ".implode(',', $apiArr)." )";
		}
		if (isset($input['game_type'])) {
			$where[] = "game_type.game_type LIKE '%".$input['game_type']."%' ";
			$values[] = $input['game_type'];
		}
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		return $result;

	}

	public function getAllGameTypeHistory( $request, $is_export = false ){

		$i = 0;

		$columns = array();

		$columns[] = array(
			'alias' => 'deleted_at',
			'select' => 'game_type_history.deleted_at',
		);
		$columns[] = array(
			'alias' => 'row_game_type_id',
			'select' => 'game_type_history.game_type_id',
		);
		if( ! $is_export ){

			$columns[] = array(
				'dt' => $i++,
				'alias' => 'id',
				'select' => 'game_type_history.id',
				'formatter' => function ($d,$row) use ($is_export) {

					$output = '<a href="" title="' . lang("View Game Type") . '" class="viewGameTypeHistory-gt" data-toggle="modal" data-target="#viewGameTypeHistoryModal" id="viewGameHistory_gt-' . $row['row_game_type_id'] . '" data-row-id="' . $row['row_game_type_id'] . '" ><span class="glyphicon glyphicon-list-alt"></span></a>';

					return $output;

				}
			);

		}
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'game_platform',
			'name' => lang("sys.gt7"),
			'select' => 'external_system.system_name',
			'formatter' => function ($d) {
				return lang($d);
			}
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'game_type_id',
			'name' => lang("Game Type ID"),
			'select' => 'game_type_history.game_type_id',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'action',
			'name' => lang("Action Type"),
			'select' => 'game_type_history.action',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'game_type',
			'select' => 'game_type_history.game_type',
			'name' => lang("sys.gt6"),
			'formatter' => 'languageFormatter',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'language_code',
			'select' => 'game_type_history.game_type_lang',
			'name' => lang("sys.gt8"),
			'formatter' => 'languageFormatter',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'note',
			'name' => lang("sys.gt11"),
			'select' => 'game_type_history.note',
			'formatter' => function ($d) {
				return $d ? $d : "N/A";
			},
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'game_type_code',
			'name' => lang("Game Type Code"),
			'select' => 'game_type_history.game_type_code',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'status',
			'select' => 'game_type_history.status',
			'name' => lang("sys.gt16"),
			'formatter' => function ($d) use ($is_export) {
				if( ! $is_export ){
					$checked = $d ? "checked" : "";
					return '<input disabled="disabled" ' . $checked . ' type="checkbox" class="checkWhite user-success" />';
				}else{
					return $d ? "✓" : "";
				}
			},
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'flag_show_in_site',
			'select' => 'game_type_history.flag_show_in_site',
			'name' => lang("sys.gt17"),
			'formatter' => function ($d) use ($is_export) {
				if( ! $is_export ){
					$checked = $d ? "checked" : "";
					return '<input disabled="disabled" ' . $checked . ' type="checkbox" class="checkWhite user-success" />';
				}else{
					return $d ? "✓" : "";
				}
			},
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'order_id',
			'select' => 'game_type_history.order_id',
			'name' => lang("sys.gt19"),
			'formatter' => function ($d) {
				return $d ? $d : "N/A";
			},
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'auto_add_new_game',
			'select' => 'game_type_history.auto_add_new_game',
			'name' => lang("sys.gt18"),
			'formatter' => function ($d) use ($is_export) {
				if( ! $is_export ){
					$checked = $d ? "checked" : "";
					return '<input disabled="disabled" ' . $checked . ' type="checkbox" class="checkWhite user-success" />';
				}else{
					return $d ? "✓" : "";
				}
			},
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'auto_add_to_cashback',
			'select' => 'game_type_history.auto_add_to_cashback',
			'name' => lang("sys.gt18"),
			'formatter' => function ($d) use ($is_export) {
				if( ! $is_export ){
					$checked = $d ? "checked" : "";
					return '<input disabled="disabled" ' . $checked . ' type="checkbox" class="checkWhite user-success" />';
				}else{
					return $d ? "✓" : "";
				}
			},
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'created_on',
			'name' => lang("pay.createdon"),
			'select' => 'game_type_history.created_on',
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'updated_at',
			'name' => lang("Updated At"),
			'select' => 'game_type_history.updated_at',
			'formatter' => function ($d) {
				return $d ? $d : "N/A";
			},
		);
		$columns[] = array(
			'dt' => $i++,
			'alias' => 'deleted_at',
			'name' => lang("Deleted At"),
			'select' => 'game_type_history.deleted_at',
			'formatter' => function ($d) {
				return $d!='0000-00-00 00:00:00' ? $d : "N/A";
			},
		);

		$table = 'game_type_history';
		$joins = array(
			'external_system' => 'external_system.id = game_type_history.game_platform_id',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();

		$this->load->library('data_tables');
		$input = $this->data_tables->extra_search($request);
		$where[] = "external_system.system_name <> '' ";
		if (isset($input['game_platform_id'])) {
			$where[] = "game_type_history.game_platform_id = ?";
			$values[] = $input['game_platform_id'];
		}else{
			//only for active api
			$apiArr=$this->utils->getAllCurrentGameSystemList();
			$where[] = "game_type_history.game_platform_id in ( ".implode(',', $apiArr)." )";
		}
		if (isset($input['game_type'])) {
			$where[] = "game_type_history.game_type LIKE '%".$input['game_type']."%' ";
			$values[] = $input['game_type'];
		}
		if (isset($input['action'])) {
			$where[] = "game_type_history.action = '" . $input['action'] . "' ";
			$values[] = $input['action'];
		}
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		return $result;

	}

	public function isGameTypeSetToAutoAddCashback($gameDescriptionId) {
		$this->db->select('auto_add_to_cashback');
		$this->db->from($this->tableName);
		$this->db->join('game_description', $this->tableName . '.id = game_description.game_type_id');
		$this->db->where('game_description.id', $gameDescriptionId);
		$this->db->where('auto_add_to_cashback', self::AUTO_ADD_TO_CASHBACK);

		return $this->runOneRowOneField('auto_add_to_cashback');
	}

	/**
	 *
	 * @param  array $gameTypes
	 * @return array code=>id
	 */
	public function syncGameTypes($gameTypes, $update_old_game_type = false){

		$codeMaps=[];

		if(!empty($gameTypes)){

			$message='';
			if(!$this->validateJsonTransArray($gameTypes, 'game_type', $message)){
				$this->utils->error_log('validateJsonTransArray game_type failed', $message);
				return false;
			}

			$message='';
			if(!$this->validateJsonTransArray($gameTypes, 'game_type_lang', $message)){
				$this->utils->error_log('validateJsonTransArray game_type_lang failed', $message);
				return false;
			}

			$now=$this->utils->getNowForMysql();

			foreach ($gameTypes as $gameTypeCode=>&$gameType) {

				if(!isset($gameType['game_type_lang'])){
					$gameType['game_type_lang']=$gameType['game_type'];
				}

				if(!isset($gameType['flag_show_in_site'])){
					$gameType['flag_show_in_site']= self::DB_TRUE;
				}
				if(!isset($gameType['auto_add_new_game'])){
					$gameType['auto_add_new_game']= self::DB_TRUE;
				}
				if(!isset($gameType['auto_add_to_cashback'])){
					$gameType['auto_add_to_cashback']= self::DB_TRUE;
				}
				if(!isset($gameType['status'])){
					$gameType['status']= self::DB_TRUE;
				}
				if(!isset($gameType['game_type_code'])){
					$gameType['game_type_code']= $gameTypeCode;
				}

                if (!empty($gameType['game_tag_code'])) {
                    $gameType['game_tag_id'] = $this->getTagIdByTagCode($gameType['game_tag_code']);
                    $gameType['game_tag_id'] = $gameType['game_tag_id']['id'];
                    unset($gameType['game_tag_code']);
                }else{
                    $gameType['game_tag_id'] = $this->getTagIdByTagCode(SYNC::TAG_CODE_UNKNOWN_GAME);
                    $gameType['game_tag_id'] = $gameType['game_tag_id']['id'];
                }


				//search by code
                #check if update old game type is enabled
                if(!empty($update_old_game_type)){
                    $this->db->select('id')->from($this->tableName)->where('game_type_code', $gameType['old_game_type_code'])
                    ->where('game_platform_id', $gameType['game_platform_id']);
                }else{
                    $this->db->select('id')->from($this->tableName)->where('game_type_code', $gameTypeCode)
                    ->where('game_platform_id', $gameType['game_platform_id']);
                }

                //check if old game type code is set
                if(!empty($gameType['old_game_type_code'])){
                    unset($gameType['old_game_type_code']);
                }

				$gameTypeId=$this->runOneRowOneField('id');
				if(!empty($gameTypeId)){
                    if(!isset($gameType['updated_at']))
                        $gameType['updated_at']= $now;

					$this->utils->debug_log('update game type', $gameType, $gameTypeId);
					//update
					$this->db->update($this->tableName, $gameType, ['id'=>$gameTypeId]);
				}else{
					//insert
                    if(!isset($gameType['created_on']))
                        $gameType['created_on']= $now;

                    if(array_key_exists($gameType['updated_at']))
                        unset($gameType['updated_at']);

					$this->utils->debug_log('insert game type', $gameType);
					$gameTypeId=$this->insertData($this->tableName, $gameType);
				}
				$codeMaps[$gameTypeCode]=$gameTypeId;
			}
		}

		return $codeMaps;

	}

    /*
        Get Game tag id for game type to specify where game category belongs to
     */
    public function getTagIdByTagCode($tag_code){
        $this->db->select("id")
                 ->where("tag_code",$tag_code);
        $game_tag = $this->db->get('game_tags');

        return json_decode(json_encode($game_tag->row()),true);
    }

    /*
        Get Game tag id and tag_name for game type to specify where game category belongs to
     */
    public function getGameTagsById($id){
        $this->db->select("id, tag_name, tag_code")
        		->from('game_tags')
                ->where("id",$id);

        return $this->runOneRowArray();
    }

    /*
	   Get all Game tag
	*/
    public function getAllGameTags(){
        $this->db->select("id,tag_name,tag_code")
        		->from('game_tags');

        return $this->runMultipleRowArray();
    }

    /*
    	Get all subwallet including game tag infos
    */
    public function getTagCategoryByGamePlatformId($gamePlatformId) {

		$gameTypes = $this->getGameTypeListByGamePlatformId($gamePlatformId);
		$response = array();
		$unknown = $this->getGameTagsById(self::UNKNOWN_GAME_TAG_ID);
		if(!empty($gameTypes)){
			foreach ($gameTypes as &$gameType) {
				$tagsInfo = $this->getGameTagsById($gameType['game_tag_id']);
				if(!empty($tagsInfo) && $tagsInfo['id'] != $unknown['id']){
					array_push($response, $tagsInfo['id']);
					foreach (array_keys($response, $unknown['id']) as $key) {
					    unset($response[$key]);
					}
				} else {
					if(empty($response)){
						array_push($response, $unknown['id']);
					}
				}
			}
		} else {
			array_push($response, $unknown['id']);
		}

		return $response;
	}

    public function getGameTagsByDescriptionId($game_description_id){

        $this->db->select("gtag.id, gtag.tag_name, gtag.tag_code, gd.game_name");
        $this->db->from('game_description as gd');
        $this->db->where("gd.id",$game_description_id);
        $this->db->join('game_type as gtype','gd.game_type_id = gtype.id','left');
        $this->db->join('game_tags as gtag','gtag.id = gtype.game_tag_id','left');
        return $this->runOneRowArray();
    }

    public function getGameTypeByFlagNewGames($game_platform_id){
        $this->db->select("*");
        $this->db->from('game_type as gt');
        $this->db->join('game_description as gd','gd.game_type_id = gt.id','left');
        $this->db->where("gd.game_platform_id",$game_platform_id);
        $this->db->where("gd.flag_new_game",true);
        $this->db->group_by('game_type');
        return $this->runMultipleRowArray();
    }

	public function searchGameTypeByList($list){
		$result=[];
		$this->db->select('external_system.system_code,game_type_lang')
		    ->from($this->tableName)->join('external_system', 'external_system.id=game_type.game_platform_id')
		    ->where_in('game_type.id', $list);
		$rows=$this->runMultipleRowArray();
		foreach ($rows as $row) {
			$result[]=lang($row['system_code']).'=>'.lang($row['game_type_lang']);
		}

		return $result;
	}

	public function deleteGameType($gameTypeId){
		$data = [
			'id' => $gameTypeId,
			'action' => self::ACTION_DELETE,
		];
        return $this->update($data);
	}

	public function getGameTypeHistoryById($gameTypeId){
		$this->db->select('*')->from($this->tableName . "_history")->where('game_type_id',$gameTypeId);
		$result = $this->runMultipleRowArray();
		return $result;
	}

	public function getGameTypeByQuery($select,$where = null,$join = null,$group_by = null,$having=null, $limit = 1000){
		$this->db->select($select);

		if ($where)
			$this->db->where($where);

		if ($join)
            $this->db->join($join['table'], $join['condition']);

        if ($group_by)
            $this->db->group_by($group_by);

        if ($having)
            $this->db->having($having);
        $this->db->from($this->tableName);
        $result = $this->runMultipleRowArray();

        return $result;
	}

	public function syncGameTag($gameProviderIds) {

		$this->db->select('game_type_code,id');
		$this->db->from('game_type');
		$this->db->where('game_platform_id',$gameProviderIds);
		$result= $this->runMultipleRowArray();

		if(!empty($result)) {
			foreach ($result as $key => $gametype) {

	  		$query = $this->db->select('id')->from('game_tags')->where('tag_code',$gametype['game_type_code']);
	  		$id = $this->db->get()->row()->id;
	  		$data = array('game_tag_id'=>$id);
	  		$result = $this->db->update('game_type', $data, array('id' => $gametype['id']));

	  		}
			return $result;
		}
	}

	/**
	 * overview : get game type id
	 *
	 * @param 	string $gameType
	 * $param	null   $gamePlatformId
	 * @return 	int
	 */
	public function getActiveGameTypeId($gameType, $gamePlatformId = null) {
		$this->db->like($this->tableName, $gameType);

		if ($gamePlatformId)
			$this->db->where('game_platform_id', $gamePlatformId);

		$this->db->where('status', self::DB_TRUE);

		$qry = $this->db->get($this->tableName);
		return $this->getOneRowOneField($qry, 'id');
	}

    public function queryByGamePlatformId($gamePlatformId, $gameTypeCode, &$sqlInfo=null){

        $this->db->select('game_type.game_platform_id, game_type.game_type_lang, game_type.game_type_code as game_type_unique_code, game_type.status as game_type_status')
            ->from('game_type')
            ->where('game_type.game_platform_id', $gamePlatformId);
        if(!empty($gameTypeCode)){
            $this->db->where('game_type.game_type_code', $gameTypeCode);
        }

        $rows=$this->runMultipleRowArray();
        //get last sql
        $sqlInfo=['sql'=>$this->db->last_query()];
        foreach ($rows as &$row) {
            //process game name and type name
            $row['game_type_name_detail']=$this->utils->extractLangJson($row['game_type_lang']);
            $row['game_type_status']=$row['game_type_status']==self::STATUS_NORMAL ? 'normal' : 'disabled';
            $row['game_platform_id']=intval($row['game_platform_id']);
            unset($row['game_name']);
            unset($row['game_type_lang']);
        }
        return $rows;
    }

    /**
     * sync game type list
     *
     *  gameTypeList format: ['game_platform_id'=>, 'game_type_unique_code'=>,
     *  'game_type_name_detail'=>, 'game_type_status'=>,]
     *
     * @param  array $gameTypeList
     * @return boolean
     */
    public function syncFrom($gamePlatformId, array $gameTypeList){
        if(empty($gameTypeList)){
            return false;
        }

        $success=false;
        foreach ($gameTypeList as $gameType) {
            //search by game_platform_id and game_type_unique_code
            $id=$this->queryGameTypeIdByCode($gameType['game_platform_id'], $gameType['game_type_unique_code']);
            $game_type_lang=convertLangDetailToJsonLangFormat($gameType['game_type_name_detail']);
            $data=[
                'game_platform_id'=>$gameType['game_platform_id'],
                'game_type'=>$game_type_lang,
                'game_type_lang'=>$game_type_lang,
                'status'=>self::DB_BOOL_STR_TO_INT[$gameType['game_type_status']],
                'game_type_code'=>$gameType['game_type_unique_code'],
                'updated_at'=>$this->utils->getNowForMysql(),
            ];
            if(empty($id)){
                $data['created_on']=$this->utils->getNowForMysql();
                //insert
                $success=!!$this->runInsertData('game_type', $data);
            }else{
                $this->db->where('id', $id)->set($data);
                $success=!!$this->runAnyUpdate('game_type');
            }
        }
        return $success;
    }

    public function queryGameTypeIdByCode($gamePlatformId, $code){
        $this->db->select('id')->from('game_type')->where('game_platform_id', $gamePlatformId)
            ->where('game_type_code', $code);
        return $this->runOneRowOneField('id');
    }

    public function queryIdMapByCode($gamePlatformId, array $codeArr){
        $this->db->select('id, game_type_code')->from('game_type')->where('game_platform_id', $gamePlatformId)
            ->where_in('game_type_code', $codeArr);
        $rows=$this->runMultipleRowArray();
        $result=[];
        if(!empty($rows)){
            foreach ($rows as $row) {
                $result[$row['game_type_code']]=$row['id'];
            }
        }

        return $result;
    }

}

///END OF FILE///////
