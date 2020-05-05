<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get game description data by id, game code
 * * Get unknown game
 * * Remove/Get player favorite games
 * * Activate/Deactivate void bet
 * * Activate/Deactivate no cash back
 *
 * @category Game Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Game_description_model extends BaseModel {

	public function __construct() {
		parent::__construct();
	}

	protected $tableName = "game_description";

	const BBIN_GAMETYPE_SPORT_GAMES = 33;
	const BBIN_GAMETYPE_LOTTERY_GAMES = 34;
	const BBIN_GAMETYPE_3DHALL_GAMES = 35;
	const BBIN_GAMETYPE_LIVE_GAMES = 36;
	const BBIN_GAMETYPE_CASINO_GAMES = 37;

	const UNKNOWN_GAME_CODE = 'unknown';

    const MOBILE_ENABLED = 1;
    const MG_PLATFORM_ID = '6';
    const T1MG_PLATFORM_ID = '1008';
    const MG_NOTE = 'redirector';

    const ENABLED_GAME = 1;
	const DISABLED_GAME = 0;
	const STR_TRUE = "true";

	const GAME_ACTIVE = 1;
	const GAME_INACTIVE = 0;
	const GAME_NULL = NULL;


    const GAME_LIST_VALID_FIELDS_WITH_LIMIT = [
        "game_platform_id"     => [ "min" => 1, "max" => 2147483648],
        "related_game_desc_id" => [ "min" => 1, "max" => 2147483648],
        "game_type_id"         => [ "min" => 1, "max" => 4294967295],
        "game_order"           => [ "min" => 1, "max" => 2147483648],
        "game_name"            => [ "min" => 1, "max" => 500],
        "game_code"            => [ "min" => 1, "max" => 200],
        "note"                 => [ "min" => 0, "max" => 1000],
        "attributes"           => [ "min" => 0, "max" => 2000],
        "english_name"         => [ "min" => 2, "max" => 300],
        "external_game_id"     => [ "min" => 1, "max" => 300],
        "clientid"             => [ "min" => 1, "max" => 200],
        "moduleid"             => [ "min" => 1, "max" => 200],
        "sub_game_provider"    => [ "min" => 1, "max" => 100],
        "demo_link"            => [ "min" => 0, "max" => 500],
        "dlc_enabled"          => [ "min" => 0, "max" => 1],
        "progressive"          => [ "min" => 0, "max" => 1],
        "flash_enabled"        => [ "min" => 0, "max" => 1],
        "offline_enabled"      => [ "min" => 0, "max" => 1],
        "mobile_enabled"       => [ "min" => 0, "max" => 1],
        "status"               => [ "min" => 0, "max" => 1],
        "flag_show_in_site"    => [ "min" => 0, "max" => 1],
        "no_cash_back"         => [ "min" => 0, "max" => 1],
        "void_bet"             => [ "min" => 0, "max" => 1],
        "html_five_enabled"    => [ "min" => 0, "max" => 1],
        "enabled_on_android"   => [ "min" => 0, "max" => 1],
        "enabled_on_ios"       => [ "min" => 0, "max" => 1],
        "flag_new_game"        => [ "min" => 0, "max" => 1],
        "enabled_freespin"     => [ "min" => 0, "max" => 1],
    ];

    const GAME_LIST_REQUIRED_FIELDS = [
        "game_platform_id", "game_name", "game_code", "progressive", "flash_enabled", "offline_enabled", "mobile_enabled", "note", "status", "flag_show_in_site", "no_cash_back", "void_bet", "attributes", "game_order", "html_five_enabled", "english_name", "external_game_id", "enabled_freespin","enabled_on_android", "enabled_on_ios", "flag_new_game","game_type_id"
    ];

    const GAME_API_WITH_LOBBYS = [
        AGIN_API, BBIN_API, AB_API, IBC_API, EZUGI_API,EBET_API, VR_API, SPORTSBOOK_API, ENTWINE_API, ONEWORKS_API, IDN_API, AGBBIN_API, GSBBIN_API, AGSHABA_API, OG_API, PINNACLE_API, LD_CASINO_API, HG_API, LD_LOTTERY_API, EXTREME_LIVE_GAMING_API, EBET_AG_API, EBET_OPUS_API, SBTECH_API, SUNCITY_API, MWG_API, RWB_API,T1AGIN_API,TCG_API,T1OG_API,T1BBIN_API,GAMEPLAY_API,T1VR_API,T1DG_API, T1EZUGI_API, T1IDN_API, T1GGPOKER_GAME_API, T1ONEWORKS_API, T1HG_API, T1EBET_API,SBOBET_API,RG_API,NTTECH_CNY_B1_API,
        NTTECH_IDR_B1_API, NTTECH_CNY_B1_API, NTTECH_THB_B1_API, NTTECH_USD_B1_API, NTTECH_VND_B1_API, NTTECH_MYR_B1_API, NTTECH_V2_API, NTTECH_API, NTTECH_V2_IDR_B1_API,NTTECH_V2_CNY_B1_API,NTTECH_V2_THB_B1_API,NTTECH_V2_USD_B1_API,NTTECH_V2_VND_B1_API,NTTECH_V2_MYR_B1_API,
        AFB88_API, DG_API, DG_SEAMLESS_API, OG_V2_API, GOLDEN_RACE_GAMING_API, T1LOTTERY_API, ASIASTAR_API, HOGAMING_SEAMLESS_API, T1SUNCITY_API, OGPLUS_API, TIANHONG_MINI_GAMES_API, RGS_API, TGP_AG_API,
        SBTECH_BTI_API, T1SBTECH_BTI_API, WM_API,
    ];

    const MAIN_GAME_ATTRIBUTES = [
	    'game_platform_id',
	    'game_type_id',
	    'game_name',
	    'game_code',
	    'note',
	    'attributes',
	    'english_name',
	    'external_game_id',
	    'clientid',
	    'moduleid',
	    'related_game_desc_id',
	    'enabled_freespin',
	    'sub_game_provider',
	    'demo_link',
	    'game_order',
	    'enabled_on_android',
	    'enabled_on_ios',
	    'dlc_enabled',
	    'progressive',
	    'flash_enabled',
	    'offline_enabled',
	    'mobile_enabled',
	    // 'status',
	    // 'flag_show_in_site',
	    'no_cash_back',
	    'void_bet',
	    'html_five_enabled',
    ];

    const GAME_DESC_INT_FIELDS = [
    	'game_order',
	    'enabled_on_android',
	    'enabled_on_ios',
	    'enabled_freespin',
	    'dlc_enabled',
	    'progressive',
	    'flash_enabled',
	    'offline_enabled',
	    'mobile_enabled',
	    'status',
	    'flag_show_in_site',
	    'no_cash_back',
	    'void_bet',
	    'html_five_enabled',
	];

	/**
	 * overview : get game description id
	 *
	 * @param 	string $gameCode
	 * @return 	int
	 */
	public function getGameDescriptionId($gameCode) {
		$qry = $this->db->get_where($this->tableName, array('game_code' => $gameCode));
		return $this->getOneRowOneField($qry, 'id');
	}

	/**
	 * overview : get game description id and game type id
	 *
	 * @param $gameCode
	 * @return array
	 */
	public function getGameDescriptionIdAndGameTypeId($gameCode) {
		$qry = $this->db->get_where($this->tableName, array('game_code' => $gameCode));
		$row = $this->getOneRow($qry);
		if ($row) {
			return array($row->id, $row->game_type_id);
		}
		return array(null, null);
	}

	/**
     * overview : get game details by game_code
     *
     * @param $gameCode
     * @return array
     */
    public function getGameDetailsByGameCode($gameCode) {
        $this->db->select('gd.game_code,gd.id as game_description_id, gd.game_type_id,gd.game_name,gt.game_type');
        $this->db->join('game_type as gt', 'gt.id = gd.game_type_id');
        $qry = $this->db->get_where($this->tableName . ' as gd', array('game_code' => $gameCode));

        return $this->getOneRow($qry);
    }

    /**
	 * overview : get game details by game_code and by game platform id
	 *
     * @param $gameCode
	 * @param $gamePlatformId
	 * @return array
	 */
	public function getGameDetailsByGameCodeAndGamePlatform($gamePlatformId,$gameCode) {
		$this->db->select('gd.game_code,gd.id as game_description_id, gd.game_type_id,gd.game_name,gt.game_type');
		$this->db->join('game_type as gt', 'gt.id = gd.game_type_id');
		$qry = $this->db->get_where($this->tableName . ' as gd', array('game_code' => $gameCode,'gd.game_platform_id' => $gamePlatformId));

		return $this->getOneRow($qry);
	}

	/**
	 * overview : get game type id by game code
	 *
	 * @param $gameCode
	 * @return int
	 */
	public function getGameTypeIdByGameCode($gameCode) {
		$qry = $this->db->get_where($this->tableName, array('game_code' => $gameCode));
		return $this->getOneRowOneField($qry, 'game_type_id');
	}

	/**
	 * overview : get games data by game type id
	 *
	 * @param $gameTypeId
	 * @return aray
	 */
	public function getGamesByGameTypeId($gameTypeId) {
		$this->db->select('id');
		$qry = $this->db->get_where($this->tableName, array('game_type_id' => $gameTypeId));
		return $this->getMultipleRow($qry);
	}

	/**
	 * overview :get game code by game description id
	 *
	 * @param 	int $gameDescriptionId
	 * @return 	string
	 */
	public function getGameCodeByGameDescriptionId($gameDescriptionId) {
		$qry = $this->db->get_where($this->tableName, array('id' => $gameDescriptionId));
		return $this->getOneRowOneField($qry, 'game_code');
	}

	/**
	 * overview : get game platform id by game description id
	 *
	 * @param 	int $gameDescriptionId
	 * @return 	int
	 */
	public function getGamePlatformIdByGameDescriptionId($gameDescriptionId) {
		$qry = $this->db->get_where($this->tableName, array('id' => $gameDescriptionId));
		return $this->getOneRowOneField($qry, 'game_platform_id');
	}

	/**
	 * overview : get game type id by game description id
	 *
	 * @param 	int $gameDescriptionId
	 * @return 	int
	 */
	public function getGameTypeIdByGameDescriptionId($gameDescriptionId) {
		$qry = $this->db->get_where($this->tableName, array('id' => $gameDescriptionId));
		return $this->getOneRowOneField($qry, 'game_type_id');
	}

	/**
	 * overview : get game decription id by game code
	 *
	 * @param 	int $gameCode
	 * @return 	int
	 */
	public function getGameDescriptionIdByGameCode($gameCode) {
		$qry = $this->db->get_where($this->tableName, array('game_code' => $gameCode));
		return $this->getOneRowOneField($qry, 'id');
	}

	/**
	 * overview : get game platform id by game code
	 *
	 * @param 	int $gameCode
	 * @return 	int
	 */
	// public function getGamePlatformIdByGameCode($gameCode, $gamePlatformId) {
	// 	$qry = $this->db->get_where($this->tableName, array('game_code' => $gameCode, 'game_platform_id' => $gamePlatformId));
	// 	return $this->getOneRowOneField($qry, 'game_platform_id');
	// }

	/**
	 * overview : get game decription id by game platform ud
	 *
	 * @param 	int $gameCode
	 * @return 	int
	 */
	// public function getGameDescriptionIdByGamePlatformId($gamePlatformId) {
	// 	$qry = $this->db->get_where($this->tableName, array('game_platform_id' => $gamePlatformId));
	// 	return $this->getOneRowOneField($qry, 'id');
	// }

	/**
	 * overview : check if game desc id is valid
	 *
	 * @param 	int $gameDescriptionId
	 * @return 	int
	 */
	public function checkIfValidGameDescId($gameDescriptionId) {
		$qry = $this->db->get_where($this->tableName, array('id' => $gameDescriptionId));
		return $this->getOneRowOneField($qry, 'id');
	}

	/**
	 * overview : check if game desc id is valid
	 *
	 * @param 	int $gameCode
	 * @return 	int
	 */
	public function checkIfValidGameCode($gameCode) {
		$qry = $this->db->get_where($this->tableName, array('game_code' => $gameCode));
		return $this->getOneRowOneField($qry, 'game_code');
	}

	/**
	 * overview : get game description list
	 *
	 * @param array $criteria
	 * @param int $offset
	 * @param int $limit
	 * @param string $orderby
	 * @param string $direction
	 * @return array
	 */
	public function getGameDescriptionList($criteria = array(), $offset = 0, $limit = 10000, $orderby = 'game_description.id', $direction = 'asc') {

		if ($this->utils->getConfig('show_non_active_game_api_game_list')) {
			$this->load->model('external_system');
            $game_platform_ids = $game_platform_ids = implode(',', array_column($this->external_system->getAllActiveSytemGameApi(), 'id'));;

			$this->db->where('game_platform_id in ('.')');
		}

		$this->db->from($this->tableName)
			->join('game', 'game.gameId = game_description.game_platform_id')
			->join('game_type', 'game_type.id = game_description.game_type_id')
			->where('game_code !=', 'unknown')
			->where('flash_enabled', 1)
			->where($criteria)
			->limit($limit, $offset)
			->order_by($orderby, $direction)
			->order_by('game_description.game_name', 'asc');

		if ($this->utils->getConfig('show_non_active_game_api_game_list')) {
			$this->db->where('game_platform_id in ('.$game_platform_ids.')');
		}

		return $this->db->get()
			->result_array();
	}

	/**
	 * overview : get game description count
	 *
	 * @param array $criteria
	 * @return int
	 */
	public function getGameDescriptionCount($criteria = array()) {
		return $this->db->from($this->tableName)
			->join('game', 'game.gameId = game_description.game_platform_id')
			->join('game_type', 'game_type.id = game_description.game_type_id')
			->where('flash_enabled', 1)
			->where($criteria)
			->count_all_results();
	}

	/**
	 * overview : get unknown game
	 *
	 * @param $systemId
	 * @return array
	 */
	public function getUnknownGame($systemId) {
		$qry = $this->db->get_where($this->tableName, array('game_platform_id' => $systemId, 'game_code' => 'unknown'));

		return $this->getOneRow($qry);
	}

	/**
	 * overview : shortcode pt
	 *
	 * @param $systemId
	 * @param $gameCode
	 * @return null|array
	 */
	public function guessShortcodePT($systemId, $gameCode) {
		if (!empty($gameCode) && strpos($gameCode, '_') !== FALSE) {
			$arr = explode('_', $gameCode);
			$guessGameCode = $arr[0];
			$this->db->where(array('game_platform_id' => $systemId, 'game_code' => $guessGameCode));
			$qry = $this->db->get($this->tableName);

			return $this->getOneRow($qry);
		}
		return null;
	}

	/**
	 * overview : shortcode nt
	 *
	 * @param $systemId
	 * @param $gameCode
	 * @return null|array
	 */
	public function guessShortcodeNT($systemId, $gameCode) {
		if (!empty($gameCode) && strpos($gameCode, '_') !== FALSE) {
			$arr = explode('_', $gameCode);
			$guessGameCode = $arr[0];
			$this->db->where(array('game_platform_id' => $systemId, 'game_code' => $guessGameCode));
			$qry = $this->db->get($this->tableName);

			return $this->getOneRow($qry);
		}
		return null;
	}

	/**
	 * overview : update h88 data
	 */
	public function updateH88Data() {
		$h88_data = json_decode('{"list":{"fcgz":{"g":[1,2,4,7],"a":[],"b":0,"c":0,"d":0,"e":null,"t":{"t":"game","u":"fcgz","i":"4"}},"nian_k":{"g":[1,2,4,7],"a":[],"b":0,"c":0,"d":0,"e":null,"t":{"t":"game","u":"nian_k","i":"4"}},"thtk":{"g":[1,2,4,7],"a":[],"b":0,"c":0,"d":0,"e":null,"t":{"t":"game","u":"thtk","i":"4"}},"bld":{"g":[1,2,3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":1,"t":false},"fnfrj":{"g":[1,2,4,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":0,"t":{"t":"game","u":"fnfrj4","i":"1"}},"irm3":{"g":[1,2,3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":1,"t":false},"ashfmf":{"g":[1,3,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":2,"t":false},"avng":{"g":[1,3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":1,"t":false},"bld50":{"g":[1,3,7],"a":[5,6,7],"b":1,"c":0,"d":0,"e":1,"t":false},"cam":{"g":[1,3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":1,"t":false},"drd":{"g":[1,3,7],"a":[],"b":0,"c":0,"d":0,"e":null,"t":false},"dt2":{"g":[1,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"elr":{"g":[1,3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":1,"t":false},"fnf":{"g":[1,3,7],"a":[2,3,4],"b":1,"c":0,"d":1,"e":1,"t":false},"fnf50":{"g":[1,3,7],"a":[5,6,7],"b":1,"c":0,"d":0,"e":1,"t":false},"fxf":{"g":[1,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"glr":{"g":[1,3,4,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":2,"t":{"t":"game","u":"glrjj-1","i":"1"}},"gtscirsj":{"g":[1,3,7],"a":[2,3,4],"b":1,"c":0,"d":1,"e":2,"t":false},"gtsdgk":{"g":[1,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"gtsgoc":{"g":[1,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"gtspor":{"g":[1,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"hlk2":{"g":[1,3,7],"a":[2,3,4],"b":1,"c":0,"d":1,"e":1,"t":false},"hlk50":{"g":[1,3,7],"a":[5,6,7],"b":1,"c":0,"d":0,"e":1,"t":false},"irm50":{"g":[1,3,7],"a":[5,6,7],"b":1,"c":0,"d":0,"e":1,"t":false},"bib":{"g":[2,7],"a":[2,3,4],"b":0,"c":0,"d":1,"e":0,"t":false},"bt":{"g":[2,7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"c7":{"g":[2,7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"cm":{"g":[2,7],"a":[1],"b":0,"c":0,"d":1,"e":0,"t":false},"dlm":{"g":[2,3,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":2,"t":false},"dt":{"g":[2,3,7],"a":[2,3,4],"b":0,"c":0,"d":1,"e":2,"t":false},"eas":{"g":[2,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"er":{"g":[2,7],"a":[1],"b":0,"c":0,"d":1,"e":0,"t":false},"fff":{"g":[2,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"fm":{"g":[2,7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"gos":{"g":[2,7],"a":[1],"b":0,"c":0,"d":0,"e":2,"t":false},"gtshwkp":{"g":[2,7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"hlf":{"g":[2,7],"a":[2,3,4],"b":0,"c":0,"d":1,"e":0,"t":false},"sib":{"g":[2,3,7],"a":[1],"b":1,"c":0,"d":0,"e":2,"t":false},"ssp":{"g":[2,7],"a":[2,3,4],"b":0,"c":0,"d":1,"e":0,"t":false},"ashamw":{"g":[3,7],"a":[8],"b":0,"c":0,"d":0,"e":2,"t":false},"fsc":{"g":[3,5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"gtswg":{"g":[3,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":2,"t":false},"hsd":{"g":[3,5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"pbro":{"g":[3,5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"qbd":{"g":[3,5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"photk":{"g":[3,7],"a":[1],"b":1,"c":1,"d":0,"e":2,"t":false},"lvb":{"g":[3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":2,"t":false},"ct":{"g":[3,7],"a":[1],"b":1,"c":0,"d":1,"e":2,"t":false},"fbr":{"g":[3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":2,"t":false},"fow":{"g":[3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":2,"t":false},"hk":{"g":[3,7],"a":[1],"b":1,"c":0,"d":0,"e":2,"t":false},"ts":{"g":[3,7],"a":[5,6,7],"b":0,"c":0,"d":0,"e":2,"t":false},"gtsbayw":{"g":[3,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":2,"t":false},"gtssprs":{"g":[3,7],"a":[2,3,4],"b":0,"c":0,"d":1,"e":2,"t":false},"gtsjhw":{"g":[3,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":2,"t":false},"car":{"g":[3,4,8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":{"t":"game","u":"car","i":"1"}},"gtsmrln":{"g":[3,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":2,"t":false},"trm":{"g":[3,7],"a":[5,6,7],"b":1,"c":0,"d":0,"e":1,"t":false},"wvm":{"g":[3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":1,"t":false},"xmn":{"g":[3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":1,"t":false},"xmn50":{"g":[3,7],"a":[5,6,7],"b":1,"c":0,"d":0,"e":1,"t":false},"ghr":{"g":[3,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":1,"t":false},"irm3sc":{"g":[3,6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"irmn3sc":{"g":[3,6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"irmn3":{"g":[3,7],"a":[2,3,4],"b":1,"c":0,"d":1,"e":1,"t":false},"rom":{"g":[3,8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"rky":{"g":[3,7],"a":[2,3,4],"b":0,"c":1,"d":0,"e":2,"t":false},"gtssmdm":{"g":[3,7],"a":[5,6,7],"b":0,"c":0,"d":0,"e":2,"t":false},"kkgsc":{"g":[3,6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"mmy":{"g":[3,7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":2,"t":false},"gtscbl":{"g":[3,7],"a":[1],"b":0,"c":0,"d":0,"e":2,"t":false},"fdt":{"g":[3,7],"a":[2,3,4],"b":0,"c":1,"d":1,"e":2,"t":false},"tps":{"g":[3,5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"al":{"g":[3,7],"a":[1],"b":0,"c":0,"d":0,"e":2,"t":false},"gts5":{"g":[3,8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"ttc":{"g":[3,7],"a":[2,3,4],"b":0,"c":1,"d":0,"e":2,"t":false},"jb10p":{"g":[4,9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":{"t":"game","u":"jb10p","i":"1"}},"bl":{"g":[4,7],"a":[2,3,4],"b":1,"c":0,"d":1,"e":0,"t":{"t":"game","u":"bl","i":"1"}},"cifr":{"g":[4,7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":0,"t":{"t":"game","u":"cifr","i":"1"}},"grel":{"g":[4,7],"a":[1],"b":1,"c":0,"d":0,"e":0,"t":{"t":"game","u":"grel","i":"1"}},"ms":{"g":[4,7],"a":[1],"b":1,"c":0,"d":0,"e":0,"t":{"t":"game","u":"ms1","i":"1"}},"bls":{"g":[4,5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":{"t":"game","u":"bls","i":"1"}},"mj":{"g":[4,9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":{"t":"game","u":"mj1","i":"1"}},"qop":{"g":[4,7],"a":[1],"b":1,"c":0,"d":0,"e":0,"t":{"t":"game","u":"qop2","i":"1"}},"sc":{"g":[4,7],"a":[1],"b":1,"c":0,"d":0,"e":0,"t":{"t":"game","u":"sc3","i":"1"}},"wc":{"g":[4,6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":{"t":"game","u":"wc4","i":"1"}},"ghlj":{"g":[4,5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":{"t":"group","u":"ghlj","i":"4"}},"str":{"g":[4,8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":{"t":"group","u":"str_sb","i":"5"}},"wsffr":{"g":[4,7],"a":[1],"b":1,"c":0,"d":0,"e":0,"t":{"t":"group","u":"wsffr","i":"5"}},"wv":{"g":[4,8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":{"t":"group","u":"wv_s","i":"5"}},"atw":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"bowl":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"dctw":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"ghl":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"head":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"hr":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"kgdb":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"kn":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"lwh":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"mro":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"pop":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"pso":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"rcd":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"rps":{"g":[5],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"bbn":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"essc":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"fbm":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"lom":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"pks":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"sbj":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"scs":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"sro":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"ssa":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"tclsc":{"g":[6],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"ah2":{"g":[7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":0,"t":false},"cnpr":{"g":[7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":0,"t":false},"evj":{"g":[7],"a":[1],"b":1,"c":0,"d":0,"e":2,"t":false},"fmn":{"g":[7],"a":[1],"b":1,"c":0,"d":0,"e":0,"t":false},"gs":{"g":[7],"a":[1],"b":1,"c":0,"d":0,"e":0,"t":false},"hb":{"g":[7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":0,"t":false},"pyrrk":{"g":[7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":0,"t":false},"spidc":{"g":[7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":1,"t":false},"8bs":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"foy":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"gc":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"glg":{"g":[7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"gtsaod":{"g":[7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"gtsjzc":{"g":[7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"gtssmbr":{"g":[7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"gtsstg":{"g":[7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"gtswng":{"g":[7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"hh":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"jb":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"kkg":{"g":[7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"mcb":{"g":[7],"a":[2,3,4],"b":0,"c":0,"d":1,"e":0,"t":false},"nk":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"op":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"pl":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"sf":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"ssl":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"ta":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"tp":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"tr":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"tst":{"g":[7],"a":[5,6,7],"b":0,"c":0,"d":0,"e":0,"t":false},"ub":{"g":[7],"a":[1],"b":0,"c":0,"d":0,"e":0,"t":false},"whk":{"g":[7],"a":[5,6,7],"b":0,"c":0,"d":1,"e":0,"t":false},"wis":{"g":[7],"a":[2,3,4],"b":1,"c":0,"d":0,"e":2,"t":false},"wlg":{"g":[7],"a":[2,3,4],"b":0,"c":0,"d":0,"e":0,"t":false},"ba":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"cheaa":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"cr":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"frr":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"frr_g":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"gtsro3d":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"romw":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"pg":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"rd":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"ro":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"ro_g":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"ro3d":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"rodz":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"rodz_g":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"rop":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"rop_g":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"rouk":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"sb":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"tqp":{"g":[8],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"af":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"af25":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"af4":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"dw":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"dw4":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"hljb":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"jb4":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"jb50":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"jp":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"po":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false},"tob":{"g":[9],"a":[],"b":null,"c":null,"d":null,"e":null,"t":false}}}');

		$i = 1;
		foreach ($h88_data->list as $key => &$value) {

			$query = $this->db->where([
				'game_code' => $key,
				'game_platform_id' => PT_API,
			])->get('game_description');

			if ($result = $query->row()) {
				$this->db->set([
					'game_order' => $i++,
					'attributes' => json_encode($value),
				])->where([
					'game_code' => $key,
					'game_platform_id' => PT_API,
				])->update('game_description');
			}

		}

	}

	/**
	 * overview : get game description
	 * @param $gameDescId
	 * @return null
	 */
	public function getGameDescription($gameDescId) {
		$qry = $this->db->get_where($this->tableName, array('id' => $gameDescId));
		return $this->getOneRow($qry);
	}
	public function getGameName($gameDesc) {
		$name = lang($gameDesc->game_name);
		if (empty($name)) {
			$name = $gameDesc->english_name;
			if (empty($name)) {
				$name = $gameDesc->game_code;
			}
		}
		return $name;
	}

	/**
	 * overview : get all game descriptions
	 *
	 * @return array
	 */
    public function getAllGameDescriptions($activeGameApiOnly = null) {

        $this->db->select("GD.*,GT.game_type,ES.system_code as system_name")
                 ->from("game_description AS GD")
                 ->join("game_type AS GT","GT.id = GD.game_type_id","left")
                 ->join("external_system AS ES","ES.id = GD.game_platform_id","left");

        if ($activeGameApiOnly) {
            $this->db->where("ES.status = ". self::DB_TRUE);
        }

        return $this->runMultipleRowArray();
    }

	/**
	 * overview : get all game names
	 *
	 * @return array
	 */
	public function getAllGameNames() {

		$sql = "
			SELECT G.game,GD.game_code,GD.game_name,GD.game_code,GD.game_type_id,GT.game_type FROM game_description AS GD
			LEFT JOIN game AS G ON G.gameId = GD.game_platform_id
			LEFT JOIN game_type AS GT ON GT.id = GD.game_type_id
			ORDER BY GD.game_name
			";

		return $this->db->query($sql)->result_array();

	}

	/**
	 * overview : add game description
	 *
	 * @param $data
	 * @return bool
	 */
	public function addGameDescription($data) {

		try {

			$this->db->insert('game_description', $data);

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());

			} else {

				return TRUE;
			}

		} catch (Exception $e) {
			return TRUE;
		}

	}

	/**
	 * overview : update game descriptioni
	 *
	 * @param $data
	 * @param $id
	 * @return bool
	 */
	public function updateGameDescription($data, $id) {

		try {

			$this->db->where('id', $id);
			$this->db->update('game_description', $data);

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());

			} else {

				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}

	}

	/**
	 * overview : update game description
	 *
	 * @param $ids
	 * @return bool
	 */
	public function deleteGameDescription($ids) {

		try {
			$result = $this->db->get_where($this->tableName,['id' => $ids]);
			foreach ($result->result_array() as $key => $game) {
				$data = [
					'external_game_id' => 'del-'.$this->utils->getNowForMysql() . '[' . $game['external_game_id'] .']',
					'status' => self::DB_FALSE,
					'flag_show_in_site' => self::DB_FALSE,
					'deleted_at' => $this->utils->getNowForMysql(),
				];
				$this->update($game['id'],$data);
			}

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());

			} else {
				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}

	}

	/**
	 * overview : get games tree
	 *
	 * @param bool|true $showGameTree
	 * @return array
	 */
	public function getGamesTree($showGameTree = true) {
		// $gamePlatformList = $this->utils->getAllCurrentGameSystemList();
		// $rlt = array();
		// foreach ($gamePlatformList as $platformId) {
		// 	$this->getGameType($platformId);
		// 	if ($showGameTree) {

		// 	}
		// }

		$games = $this->getAllGames();
		$gameTree = array();
		foreach ($games as $gameInfo) {
			$gpId = $gameInfo->gamePlatformId;
			if (!array_key_exists($gpId, $gameTree)) {
				$gameTree[$gpId] = array('gamePlatformName' => $gameInfo->gamePlatformName,
					'gameTypeTree' => array());
			}

			$catId = $gameInfo->catId;
			if (!array_key_exists($catId, $gameTree[$gpId]['gameTypeTree'])) {
				$gameTree[$gpId]['gameTypeTree'][$catId] =
				array('gameType' => $gameInfo->gameType,
					'gameTypeLang' => $gameInfo->gameTypeLang,
					'gameList' => array());
			}

			$gdId = $gameInfo->gameDescriptionId;
			if (!array_key_exists($gdId, $gameTree[$gpId]['gameTypeTree'][$catId]['gameList'])) {
				$gameTree[$gpId]['gameTypeTree'][$catId]['gameList'][$gdId] = array();
			}

			$gameTree[$gpId]['gameTypeTree'][$catId]['gameList'][$gdId] = array(
				'gameName' => $gameInfo->gameName,
				'gameCode' => $gameInfo->gameCode,
			);

		}

		return $gameTree;
	}

	/**
	 * overview : getGameType
	 *
	 * @return	$array
	 */
	public function getAllGames($ignore_game_apis = null, $game_platform_id = null, $new_games_only = null, $not_include_unknown = null, $getActiveGames = false) {

		if ( ! empty($ignore_game_apis)) {
			if ($getActiveGames === true) {
				$this->db->select("gd.game_code")
						->from("game_description as gd")
						->where('gd.status =', self::ENABLED_GAME)
						->where('gd.flag_show_in_site =', self::ENABLED_GAME);

			} else {
				$this->db->select("gd.*, gt.game_type,gt.game_type_code, gt.game_tag_id")
						->from("game_description as gd")
						->join('game_type as gt','gt.id = gd.game_type_id')
						->order_by("gd.game_platform_id, gt.id desc, gd.id, gd.game_order desc");
			}

			if ( ! empty($game_platform_id)) {
				$this->db->where("gd.game_platform_id", $game_platform_id);
			}

			if ( ! empty($not_include_unknown)) {
				$this->db->where("gd.external_game_id !=", "unknown");
			}

		}else{
			$this->db->select(
					'gt.id as game_type_id,
                    gt.game_type as gameType,
                    gt.game_type_lang as gameTypeLang,
                    gt.game_platform_id as gameTypeId,
                    gd.id as gameDescriptionId,
                    gd.game_name as gameName,
                    gd.game_code as gameCode,
                    gd.game_platform_id as gamePlatformId,
                    external_system.system_code as gamePlatformName,
                    gd.no_cash_back,
                    gd.dlc_enabled,
                    gd.flash_enabled,
                    gd.offline_enabled,
                    gd.mobile_enabled,
                    gd.html_five_enabled,
                    gd.enabled_freespin,
                    gd.enabled_on_android,
                    gd.enabled_on_ios,
                    gd.flag_new_game'
			)
					->from('game_description as gd')
					->join('game_type as gt', 'gt.id = gd.game_type_id')
					->join('external_system', 'external_system.id = gd.game_platform_id')
					->where('gt.game_type !=', 'unknown')
					->not_like('gt.game_type','unknown')
					->order_by('gd.game_platform_id, gt.id, gd.game_order desc');


		}

		if (!empty($new_games_only)) {
			$this->db->where('gd.flag_new_game', true);
		}

		return $this->runMultipleRow();
	}

	/**
	 * overview : get unknown game list
	 *
	 * @return array
	 */
	public function getUnknownGameList() {
		$this->db->from($this->tableName)->where('game_code', 'unknown');
		$rows = $this->runMultipleRow();
		$rlt = array();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$rlt[] = $row->id;
			}
		}
		return $rlt;
	}

	/**
	 * overview : get game data
	 *
	 * @param $where
	 * @return array
	 */
	public function getGame($where) {
		$this->db->select('game_type.id as catId,
						   game_type.game_type as gameType,
						   game_type.game_type_lang as gameTypeLang,
						   game_description.game_type_id as gameTypeId,
						   game_description.id as gameDescriptionId,
						   game_description.game_name as gameName,
						   game_description.game_code as gameCode,
						   game_description.game_platform_id as gamePlatformId,
						   external_system.system_code as gamePlatformName')
			->from('game_description')
			->join('game_type', 'game_type.id = game_description.game_type_id')
			->join('external_system', 'external_system.id = game_description.game_platform_id')
			->where($where)
			->order_by('game_description.game_platform_id, game_type.id, game_description.game_order desc');
		return $this->runMultipleRow();
	}

	/**
	 * overview : update game description
	 *
	 * Date: 2019-06-28 (OGP-13029)
	 * Re: SBE Game List update
	 * updates:	this will now update all data from game list except the field for status,flag_new_game, flag_show_in_site
	 * 			by updating particular game list in sbe, it wont sync from gamegateway anymore due to locked feature
	 *
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	public function update($id, $data) {
		return $this->db->update($this->tableName, $data, array('id' => $id));
	}

	/**
	 * overview : check game description
	 *
	 * @param $gamePlatformId
	 * @param $gameNameStr
	 * @param $externalGameId
	 * @param $gameTypeStr
	 * @param null $extra
	 * @return array
	 */
	public function checkGameDesc($gamePlatformId, $gameNameStr, $externalGameId, $gameTypeStr, $extra = null) {
		$this->load->model('game_type_model');
		$gameTypeId = $this->game_type_model->checkGameType($gamePlatformId, $gameTypeStr, $extra);

		$this->db->from($this->tableName)->where('game_platform_id', $gamePlatformId)
			->where('external_game_id', $externalGameId);

		$gameDescId = $this->runOneRowOneField('id');
		if (empty($gameDescId)) {
			$data = array('game_platform_id' => $gamePlatformId,
				'game_type_id' => $gameTypeId, 'game_name' => $gameNameStr,
				'english_name' => $gameNameStr,
				'external_game_id' => $externalGameId,
				'note' => isset($extra['note'])?$extra['note']:'',
				'flash_enabled' => self::DB_TRUE,
				'status' => self::STATUS_NORMAL,
				'no_cash_back' => self::DB_FALSE,
                'void_bet' => self::DB_FALSE,
				'created_on' => $this->utils->getNowForMysql(),
			);
			if (!empty($extra)) {
				$data = array_merge($data, $extra);
			}
			if (array_key_exists("game_type",$data)){
				unset($data['game_type']);
			}
			if (array_key_exists("game_type_code",$data)){
	        	unset($data['game_type_code'] );
	        }
			if(isset($extra['game_type_id'])&&$extra['game_type_id']!=""){
				$data['game_type_id'] = $extra['game_type_id'];
			}
			$gameDescId = $this->insertData($this->tableName, $data);
			$this->utils->debug_log('add new game', $gameNameStr, $externalGameId, $gamePlatformId, 'game description id', $gameDescId);
			//write to group level
			$this->load->model(array('group_level'));
			$this->group_level->allowGameDescToAll($gameDescId);
		}
		return array($gameDescId, $gameTypeId);
	}

	/**
	 * overview : get game code map
	 *
	 * @param $gamePlatformId
	 * @return array
	 */
	public function getGameCodeMap($gamePlatformId) {
		$this->db->from($this->tableName)->where('game_platform_id', $gamePlatformId)
			->where('status', self::STATUS_NORMAL);

		$gameExternalIdMap = array();
		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$gameExternalIdMap[$row->game_code] = array('game_description_id' => $row->id,
					'game_type_id' => $row->game_type_id, 'void_bet' => $row->void_bet);
			}
		}

		return $gameExternalIdMap;
	}

	/**
	 * overview : get game external id map
	 *
	 * @param $gamePlatformId
	 * @return array
	 */
	public function getGameExternalIdMap($gamePlatformId) {
		$this->db->from($this->tableName)->where('game_platform_id', $gamePlatformId)
			->where('status', self::STATUS_NORMAL);

		$gameExternalIdMap = array();
		$rows = $this->runMultipleRow();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$gameExternalIdMap[$row->external_game_id] = array('game_description_id' => $row->id,
					'game_type_id' => $row->game_type_id, 'void_bet' => $row->void_bet);
			}
		}

		return $gameExternalIdMap;
	}

	/**
	 * overview : game tree
	 *
	 * @return array
	 */
	function getGameTree() {
		return $this->db
			->select('game_description.id')
			->select('game_description.game_name')
			->select('game_description.english_name')
			->select('game_description.game_platform_id')
			->select('game_description.game_type_id')
			->select('external_system.system_code')
			->select('game_type.game_type_lang')
			->select('game_type.game_type')
			->from('game_description')
			->join('external_system', 'external_system.id = game_description.game_platform_id')
			->join('game_type', 'game_type.id = game_description.game_type_id')
			->where('game_description.game_code !=', 'unknown')
			->where('game_type.game_type !=', 'unknown')
			->order_by('external_system.id')
			->order_by('game_type.id')
			->order_by('game_description.id')
			->get()
			->result();
	}

	/**
	 * overview : activate no cash back
	 * @param $id
	 * @return bool
	 */
	public function activateNoCashback($id) {
		try {

			$this->db->where('id', $id);
			$this->db->update('game_description', array('no_cash_back' => self::DB_TRUE));

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());

			} else {

				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}
	}

	/**
	 * overview : deactivate no cash back
	 *
	 * @param $id
	 * @return bool
	 */
	public function deactivateNoCashback($id) {
		try {

			$this->db->where('id', $id);
			$this->db->update('game_description', array('no_cash_back' => self::DB_FALSE));

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());

			} else {

				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}

	}

	/**
	 * overview : activate void bet
	 *
	 * @param $id
	 * @return bool
	 */
	public function activateVoidBet($id) {
		try {

			$this->db->where('id', $id);
			$this->db->update('game_description', array('void_bet' => self::DB_TRUE));

			if ($this->db->_error_message()) {
				throw new Exception($this->db->_error_message());

			} else {

				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}

	}

	/**
	 * overview : deactivate void bet
	 *
	 * @param $id
	 * @return bool
	 */
	public function deactivateVoidBet($id) {

		try {

			$this->db->where('id', $id);
			$this->db->update('game_description', array('void_bet' => self::DB_FALSE));

			if ($this->db->_error_message()) {
				return FALSE;
			} else {
				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}
	}

	/**
	 * overview : remove favorite game from player
	 *
	 * @param $gameDescriptionId
	 * @param $playerId
	 * @return bool
	 */
	public function removeFavoriteGameFromPlayer($gameDescriptionId, $playerId) {
		try {
			$this->db->where('game_description_id', $gameDescriptionId);
			$this->db->where('player_id', $playerId);
			$this->db->delete('player_favorites');

			if ($this->db->_error_message()) {
				return FALSE;
			} else {
				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}
	}

	/**
	 * overview : remove favorite game code
	 *
	 * @param $gameCode
	 * @param $playerId
	 * @return bool
	 */
	public function removeFavoriteGameCodeFromPlayer($gameCode, $playerId) {
		try {
			$this->db->where('game_code', $gameCode);
			$this->db->where('player_id', $playerId);
			$this->db->delete('player_favorites');

			if ($this->db->_error_message()) {
				return FALSE;
			} else {
				return TRUE;
			}

		} catch (Exception $e) {
			return FALSE;
		}
	}

	/**
	 * overview : get player favorite games
	 *
	 * @param $playerId
	 * @return null
	 */
	public function getPlayerFavoriteGames($playerId) {
		$this->db->select('id,game_code,game_type_id,game_description_id,game_platform_id')
			->from('player_favorites')
			->where('player_id', $playerId)
			->order_by('created_at', 'desc');
		return $this->runMultipleRow();
	}

	/**
	 * overview : get game description by code
	 *
	 * @param $gameCode
	 * @return null
	 */
	public function getGameDescriptionByCode($gameCode) {
		$qry = $this->db->get_where($this->tableName, array('game_code' => $gameCode));
		return $this->getOneRow($qry);
	}

	/**
	 * overview : rollback:
	 *
	 * detail : delete from game_description where game_platform_id=? and game_code!='unknown'
	 * 			delete from game_type where game_platform_id=? and game_code!='unknown'
	 *
	 * @param $fromApi
	 * @param $toApi
	 * @return bool
	 */
	public function batchCopyTo($fromApi, $toApi) {
		$unknown = self::UNKNOWN_GAME_CODE;

		//copy game type
		$sql = <<<EOD
insert into game_type(
game_platform_id,game_type,game_type_lang,note,status,
flag_show_in_site,order_id,auto_add_new_game,related_game_type_id)

select {$toApi},game_type,game_type_lang,note,status,
flag_show_in_site,order_id,auto_add_new_game,id
from game_type
where game_platform_id=? and game_type!="{$unknown}"
EOD;
		$this->db->query($sql, array($fromApi));

		$rlt = $this->getResultOfUpdate();

		$sql = <<<EOD
insert into game_description(
game_platform_id,game_type_id,game_name,game_code,
dlc_enabled,flash_enabled,offline_enabled,mobile_enabled,html_five_enabled,
progressive,note,status,flag_show_in_site,no_cash_back,void_bet,attributes,game_order,
english_name,external_game_id,clientid,moduleid,related_game_desc_id)

select {$toApi}, game_type.id,game_name,game_code,
dlc_enabled,flash_enabled,offline_enabled,mobile_enabled,html_five_enabled,
progressive,game_description.note,game_description.status,game_description.flag_show_in_site,
no_cash_back,void_bet,attributes,game_order,
english_name,external_game_id,clientid,moduleid,game_description.id

from game_description join game_type on game_description.game_type_id=game_type.related_game_type_id
where game_description.game_platform_id=? and game_description.game_code!="{$unknown}"
EOD;

		$this->db->query($sql, array($fromApi));

		return $this->getResultOfUpdate();
	}

	/**
	 * overview : get game type by id
	 *
	 * @param 	int $id		game_type_id
	 * @return 	string
	 */
	public function getGameTypeById($id) {
		$qry = $this->db->get_where("game_type", array('id' => $id));
		return $this->getOneRowOneField($qry, 'game_type');
	}

	/**
	 * overview : get game description id list
	 *
	 * @param $ids
	 * @return array
	 */
	public function getGameDescIdListByGameTypes($ids) {
        $this->db->from('game_description')->where_in('game_type_id', $ids);
        $rows = $this->runMultipleRowArray();
		$list = array();
		foreach ($rows as $row) {
			$list[] = $row['id'];
		}
		return $list;
	}

	/**
	 * overview : get game description by game type
	 *
	 * @param $ids
	 * @return array
	 */
	public function getGameDescInfoListByGameTypes($ids) {
		$this->db->from('game_description')->where_in('game_type_id', $ids);
		$rows = $this->runMultipleRowArray();
		$list = array();
		foreach ($rows as $row) {
			$list[] = array('id' => $row['id'],
				'game_type_id' => $row['game_type_id'],
				'game_platform_id' => $row['game_platform_id']);
		}
		return $list;
	}

	/**
	 * overview : get game code
	 *
	 * @param $gameName
	 * @param $game_platform_id
	 * @return array
	 */
	public function getGameCodeByGameName($gameName, $game_platform_id) {
		$qry = $this->db->get_where($this->tableName, array('english_name' => $gameName, 'game_platform_id' => $game_platform_id));
		return $this->getOneRowOneField($qry, 'game_code');
	}

	/**
	 * overview : get game description list
	 * @param $gamePlatformId
	 * @param $gameTypeId
	 * @return array
	 */
	public function getGameDescriptionListByGameType($gamePlatformId, $gameTypeId, $filterColumn=array()) {

        if ($this->utils->isEnabledFeature("hide_disabled_games_on_game_tree")) {
	        $this->db->from($this->tableName)
	            ->where('game_platform_id', $gamePlatformId)
	            ->where('game_type_id', $gameTypeId);

			if(count($filterColumn) > 0) {
				foreach ($filterColumn as $key => $value) {
					$this->db->where($key, $value);
				}
			}

            $this->db->where('status', self::ENABLED_GAME);
            // $this->db->where('flag_show_in_site', self::ENABLED_GAME);
			if(count($filterColumn) > 0) {
				foreach ($filterColumn as $key => $value) {
					$this->db->where($key, $value);
				}
			}
			return $this->runMultipleRowArray();
        }else{
	        $this->db->from($this->tableName)
	            ->where('game_platform_id', $gamePlatformId)
	            ->where('game_type_id', $gameTypeId);

			if(count($filterColumn) > 0) {
				foreach ($filterColumn as $key => $value) {
					$this->db->where($key, $value);
				}
			}
			return $this->runMultipleRowArray();
        }
	}

	/**
	 * getAllGameDescriptionList With GameTypeId List
	 * Ref. by self::getAllGameDescriptionList().
	 * @param array $game_type_id_list The game_type_id list.
	 * @return array The rows.
	 */
	public function getAllGameDescriptionListWithGameTypeIdList($game_type_id_list=null) {
		$this->db->from($this->tableName);
		if ($this->utils->isEnabledFeature("hide_disabled_games_on_game_tree")) {
			$this->db->where('status', self::ENABLED_GAME);
			// $this->db->where('flag_show_in_site', self::ENABLED_GAME);
		}
		if( ! is_null($game_type_id_list) ){
			$this->db->where_in('game_type_id', $game_type_id_list);
		}
		return $this->runMultipleRowArray();
	} // EOF getAllGameDescriptionListWithGameTypeIdList

    public function getAllGameDescriptionList($filterColumn=array()) {

        if ($this->utils->isEnabledFeature("hide_disabled_games_on_game_tree")) {
            $this->db->from($this->tableName);

            if(count($filterColumn) > 0) {
                foreach ($filterColumn as $key => $value) {
                    $this->db->where($key, $value);
                }
            }

            $this->db->where('status', self::ENABLED_GAME);
            // $this->db->where('flag_show_in_site', self::ENABLED_GAME);

            return $this->runMultipleRowArray();
        }else{
            $this->db->from($this->tableName);

            if(count($filterColumn) > 0) {
                foreach ($filterColumn as $key => $value) {
                    $this->db->where($key, $value);
                }
            }
            return $this->runMultipleRowArray();
        }
    }

	/**
	 * overview : get game description list by game platform id
	 *
	 * @param $gamePlatformId
	 * @param $searchType
	 * @param null $gameTypeId
	 * @return array
	 */
	public function getGameDescriptionListByGamePlatformId($gamePlatformId, $searchType, $gameTypeId = null) {
		$this->db->select($searchType);
		$this->db->from($this->tableName);
		$this->db->where('game_platform_id', $gamePlatformId);
		$this->db->where('external_game_id !=', 'unknown');
		if ($gameTypeId) {
			$this->db->where('game_type_id', $gameTypeId);
		}
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row[$searchType];
			}
			return $data;
		}
	}

	public function getGameDescriptionByGamePlatformIdAndGameCode($game_platform_id,$game_code) {
		$this->db->select('a.*,b.*,a.id as game_description_id');
		$this->db->where('a.game_platform_id', $game_platform_id);
	    $this->db->where('a.game_code', $game_code);
	    $this->db->from('game_description a');
	    $this->db->join('game_type b', 'b.id = a.game_type_id', 'left');
	    $query = $this->db->get();
	    return $query->result();
	}

	/**
	 * overview : get game tree array
	 *
	 * @param array $selectedGamePlatformArr
	 * @param array $selectedGameTypeInfoArr
	 * @param array $selectedGameDescInfoArr
	 * @param $percentage
	 * @param $showGameDescTree
	 * @return array
	 */
	public function getGameTreeArray($selectedGamePlatformArr = array(), $selectedGameTypeInfoArr = array(), $selectedGameDescInfoArr = array(), $percentage, $showGameDescTree, $filterColumn = array()) {
		$result = array();
		$this->load->model(array('external_system', 'game_type_model'));

		if (empty($selectedGamePlatformArr)) {
			$selectedGamePlatformArr = array();
		}
		if (empty($selectedGameTypeInfoArr)) {
			$selectedGameTypeInfoArr = array();
		}
		if (empty($selectedGameDescInfoArr)) {
			$selectedGameDescInfoArr = array();
		}

		$this->utils->debug_log('SELECTEDGAMEPLATFORMARR', count($selectedGamePlatformArr), 'selectedGameTypeInfoArr', count($selectedGameTypeInfoArr),
			'selectedGameDescInfoArr', count($selectedGameDescInfoArr), 'showGameDescTree', $showGameDescTree);

		//TODO try load cache first utils->getTextFromCache and saveTextToCache
		$gameApiList = $this->external_system->getAllActiveSytemGameApi();
		if (!empty($gameApiList)) {

			foreach ($gameApiList as $row) {
				$number = null;
				if (isset($selectedGamePlatformArr[$row['id']])) {
					$number = $selectedGamePlatformArr[$row['id']];
				}
				if (empty($number)) {
					$number = null;
				}
				$gameApiNode = array('id' => 'gp_' . $row['id'], 'text' => $row['system_code'],
					// 'state' => ["checked" => array_key_exists($row['id'], $selectedGamePlatformArr), "opened" => false],
					'set_number' => true, 'number' => $number, 'percentage' => $percentage);
				//load game type
				$gameTypeList = $this->game_type_model->getGameTypeListByGamePlatformId($row['id']);
				if (!empty($gameTypeList)) {

					foreach ($gameTypeList as $gameType) {
						$number = null;
						if (isset($selectedGameTypeInfoArr[$gameType['id']])) {
							$number = $selectedGameTypeInfoArr[$gameType['id']];
						}
						if (empty($number)) {
							$number = null;
						}
						$gameTypeNode = array('id' => 'gp_' . $row['id'] . '_gt_' . $gameType['id'], 'text' => lang($gameType['game_type_lang']),
							// 'state' => ["checked" => array_key_exists($gameType['id'], $selectedGameTypeInfoArr), "opened" => false],
							'set_number' => true, 'number' => $number, 'percentage' => $percentage);

						$showGameDescTree = true;
						if ($showGameDescTree) {
							//load game description
							$gameDescList = $this->getGameDescriptionListByGameType($row['id'], $gameType['id'], $filterColumn);
							// $this->utils->debug_log('===============================================gameDescList', count($gameDescList));
							if (!empty($gameDescList)) {
								foreach ($gameDescList as $gameDesc) {
									$number = null;
									if (isset($selectedGameDescInfoArr[$gameDesc['id']])) {
										$number = $selectedGameDescInfoArr[$gameDesc['id']];
									}
									if (empty($number)) {
										$number = null;
									}
									$gameDescNode = array(
										'id' => 'gp_' . $row['id'] . '_gt_' . $gameType['id'] . '_gd_' . $gameDesc['id'],
										'text' => lang($gameDesc['game_name']),
										'state' => [
											"checked" => array_key_exists($gameDesc['id'], $selectedGameDescInfoArr),
											"opened" => false,
											'filter_col' => array(
												'dlc_enabled' => $gameDesc['dlc_enabled'],
												'mobile_enabled' => $gameDesc['mobile_enabled'],
												'progressive' => $gameDesc['progressive'],
												'enabled_on_android' => $gameDesc['enabled_on_android'],
												'enabled_on_ios' => $gameDesc['enabled_on_ios'],
												'flash_enabled' => $gameDesc['flash_enabled'],
												'html_five_enabled' => $gameDesc['html_five_enabled']
											)
										],
										'set_number' => $showGameDescTree, 'number' => $number, 'percentage' => $percentage,

									);
									//add to game type
									$gameTypeNode['children'][] = $gameDescNode;
								}
							}
						} else {
							$gameTypeNode['state'] = ["checked" => array_key_exists($gameType['id'], $selectedGameTypeInfoArr), "opened" => false];
						}

						$gameApiNode['children'][] = $gameTypeNode;
					}
				}
				$result[] = $gameApiNode;
			}
		}
		return $result;
	}

	/**
	 * overview : game tree array
	 *
	 * @param array $selectedGamePlatformArr
	 * @param array $selectedGameTypeInfoArr
	 * @param array $selectedGameDescInfoArr
	 * @param $percentage
	 * @param $showGameDescTree
	 * @return array
	 */
	public function getGameTreeArray2($selectedGamePlatformArr = array(), $selectedGameTypeInfoArr = array(), $selectedGameDescInfoArr = array(), $percentage, $showGameDescTree) {
		$result = array();
		$this->load->model(array('external_system', 'game_type_model'));

		if (empty($selectedGamePlatformArr)) {
			$selectedGamePlatformArr = array();
		}
		if (empty($selectedGameTypeInfoArr)) {
			$selectedGameTypeInfoArr = array();
		}
		if (empty($selectedGameDescInfoArr)) {
			$selectedGameDescInfoArr = array();
		}

		$this->utils->debug_log('SELECTEDGAMEPLATFORMARR', $selectedGamePlatformArr, 'selectedGameTypeInfoArr', $selectedGameTypeInfoArr,
			'selectedGameDescInfoArr', count($selectedGameDescInfoArr), 'showGameDescTree', $showGameDescTree);

		//TODO try load cache first utils->getTextFromCache and saveTextToCache
		$gameApiList = $this->external_system->getAllActiveSytemGameApi();
		if (!empty($gameApiList)) {

			foreach ($gameApiList as $row) {
				$number = null;
				if (isset($selectedGamePlatformArr[$row['id']])) {
					$number = $selectedGamePlatformArr[$row['id']];
				}
				if (empty($number)) {
					$number = null;
				}
				$gameApiNode = array('id' => 'gp_' . $row['id'], 'text' => $row['system_code'],
					'state' => ["checked" => array_key_exists($row['id'], $selectedGamePlatformArr), "opened" => false],
					'set_number' => true, 'number' => $number, 'percentage' => $percentage);
				//load game type
				$gameTypeList = $this->game_type_model->getGameTypeListByGamePlatformId($row['id']);
				if (!empty($gameTypeList)) {

					foreach ($gameTypeList as $gameType) {
						$number = null;
						if (isset($selectedGameTypeInfoArr[$gameType['id']])) {
							$number = $selectedGameTypeInfoArr[$gameType['id']];
						}
						if (empty($number)) {
							$number = null;
						}
						$gameTypeNode = array('id' => $gameType['id'], 'text' => lang($gameType['game_type_lang']),
							'state' => ["checked" => array_key_exists($gameType['id'], $selectedGameTypeInfoArr), "opened" => false],
							'set_number' => true, 'number' => $number, 'percentage' => $percentage);
						$gameApiNode['children'][] = $gameTypeNode;
					}
				}
				$result[] = $gameApiNode;
			}
		}
		$this->utils->debug_log('jsTree', $result);
		return $result;
	}

	public function isGameTypeHasMobileVersion($gameTypeId) {
		$this->db->from($this->tableName)
				 ->where('game_type_id', $gameTypeId)
				 ->where('mobile_enabled', self::MOBILE_ENABLED);
		$query =  $this->runMultipleRowArray();

		$hasMobileVersion = false;
		if(count($query)) {
			$hasMobileVersion = true;
		}
		return $hasMobileVersion;
	}

    public function validateJsonTransArray($arr, $fieldName, &$message){
        $list_of_wrong_json_format = [];

        if(!empty($arr)){

            foreach ($arr as $val) {
                if(!empty($val[$fieldName])){
                        // print_r($val[$fieldName]);
                    if(!$this->validateJsonTrans($val[$fieldName])){
                        $temp_arr = [
                            "game_name" => $val["game_name"],
                            "external_game_id" => $val["external_game_id"],
                            "game_code" => $val["game_code"],
                        ];
                        array_push($list_of_wrong_json_format, $temp_arr);
                    }
                }
            }

        }

        return $list_of_wrong_json_format;
    }

    public function validateJsonTrans($str){

        $success=true;

        if(substr($str, 0, 6) === '_json:') {

            $jsonStr = substr($str, 6);
            $jsonArr = json_decode($jsonStr, true);
            //empty or found json error
            if(empty($jsonArr) || json_last_error() !== JSON_ERROR_NONE) {
                $success=false;
            }

        } else {
            return false;
        }
        return $success;

    }

    public function updateGameDescriptionStatus($id,$type,$status){
    	$data = [];
    	if($type == "flag_show_in_site"){
    		$data['flag_show_in_site'] = $status;
    	}elseif($type == "status"){
    		$data['status'] = $status;
    	}
    	return $this->db->update($this->tableName, $data, ['id' => $id]);
    }

    public function singleAddUpdateGame($gameDetails, $id = null, $status = null){
    	if (empty($gameDetails)) {
    		if(!empty($id)){
    			$this->db->select('game_platform_id, game_type_id, game_name, game_code, dlc_enabled, progressive, flash_enabled, offline_enabled, mobile_enabled, note, status, flag_show_in_site, no_cash_back, void_bet, attributes, game_order, html_five_enabled, english_name, external_game_id, clientid, moduleid, related_game_desc_id, enabled_freespin, sub_game_provider, enabled_on_android, enabled_on_ios, demo_link');
	    		$this->db->where('id',$id);
	    		$this->db->from($this->tableName);
	    		$gameDetails = $this->runOneRowArray();
	    		if ($status == "delete") {
					$gameDetails['external_game_id'] = 'del-'.$this->utils->getNowForMysql() . '[' . $gameDetails['external_game_id'] .']';
					$gameDetails['deleted_at'] = $this->utils->getNowForMysql();
					$gameDetails['status'] = self::DB_FALSE;
					$gameDetails['flag_show_in_site'] = self::DB_FALSE;

		        	$this->processGameDescriptionHistory($gameDetails,self::ACTION_DELETE,$id);
	    		}else{
		        	$this->processGameDescriptionHistory($gameDetails,self::ACTION_UPDATE,$id);
	    		}
	    		return $this->update($id,$gameDetails);
    		}
    		return false;
    	}

        $this->processMd5FieldsSetFalseIfNotExist($gameDetails,self::MAIN_GAME_ATTRIBUTES,self::GAME_DESC_INT_FIELDS);

        $gameDetails['md5_fields'] = $this->generateMD5SumOneRow($gameDetails,self::MAIN_GAME_ATTRIBUTES,self::GAME_DESC_INT_FIELDS);

    	if (empty($id)) {
			$gameDescId = $this->insertData($this->tableName, $gameDetails);
	        $this->processGameDescriptionHistory($gameDetails,self::ACTION_UPDATE,$gameDescId);
	        return $gameDescId;
    	}

    	if ($id) {
        	$success = $this->update($id,$gameDetails);
        	if ($success === true) {
	        	$this->processGameDescriptionHistory($gameDetails,self::ACTION_UPDATE,$id);
        	}
        	return $success;
    	}

    	return false;
    }

    private function processGameDescriptionHistory($gameDetails,$action,$game_description_id = null){
		$gameHistory = array_filter($gameDetails);
		if (isset($gameHistory['id']))
			unset($gameHistory['id']);

		if (isset($gameHistory['locked_flag']))
			unset($gameHistory['locked_flag']);

		$gameHistory['game_description_id'] = $game_description_id;
        $gameHistory['action'] = $action;
        $this->insertData('game_description_history',$gameHistory);

        unset($gameDetails);
    }

	public function syncGameDescription($gameDescArr,$cronjob = null, $ignore_validate_json = false, $hideDebugLog = null, $updateMobileAttributes = null,$extra = null){
        $cntInsert = $cntUpdate = 0;
        $data = $data['missing_game_type'] = [];
        $dont_allow_disabled_game_to_be_launched = $this->utils->isEnabledFeature('dont_allow_disabled_game_to_be_launched');
        $force_game_list_update = (isset($extra['force_game_list_update']) && $extra['force_game_list_update'] == self::STR_TRUE) ? true: null;

        if(!empty($gameDescArr)){

            $message='';
            if (empty($ignore_validate_json)) {
                $validateJsonTransArrayResult = $this->validateJsonTransArray($gameDescArr, 'game_name', $message);
                $data['validateJsonTransArrayResult'] = $validateJsonTransArrayResult;

                #dont insert wrong json format
                foreach ($validateJsonTransArrayResult as $key => $value) {
                    unset($gameDescArr[$key]);
                }

                if (!$hideDebugLog && $validateJsonTransArrayResult)
                    $this->utils->debug_log('validateJsonTransArray game_name failed', $validateJsonTransArrayResult);
            }

            $now=$this->utils->getNowForMysql();
            foreach ($gameDescArr as $key => $gameDesc) {
                if (empty($gameDesc)) continue;
               	if (empty($this->processSyncGameDescLostInfo($gameDesc,$data,$key))) continue;

                if(!isset($gameDesc['external_game_id'])){
                    $gameDesc['external_game_id']=$gameDesc['game_code'];
                }

                if(!isset($gameDesc['game_code'])){
                    $gameDesc['game_code']=$gameDesc['external_game_id'];
                }

                if(!isset($gameDesc['english_name'])){
                    $arr=$this->decodeJsonTrans($gameDesc['game_name']);
                    $gameDesc['english_name']=$arr['1'];
                }

                if ($updateMobileAttributes) {
                    if((isset($gameDesc['html_five_enabled']) && $gameDesc['html_five_enabled'] == self::DB_TRUE)
                        || (isset($gameDesc['mobile_enabled']) && $gameDesc['mobile_enabled'] == self::DB_TRUE)){
                        $gameDesc['mobile_enabled']=self::DB_TRUE;
                        $gameDesc['enabled_on_ios']=self::DB_TRUE;
                        $gameDesc['enabled_on_android']=self::DB_TRUE;
                    }
                }

                $this->processMd5FieldsSetFalseIfNotExist($gameDesc,self::MAIN_GAME_ATTRIBUTES,self::GAME_DESC_INT_FIELDS);
                $gameDesc['md5_fields'] = $this->generateMD5SumOneRow($gameDesc,self::MAIN_GAME_ATTRIBUTES,self::GAME_DESC_INT_FIELDS);

                //search by external_game_id
                $rows = $this->getGameByEODQuery('id,status,md5_fields,auto_sync_enable,external_game_id','external_game_id ="'.$gameDesc['external_game_id'].'" AND game_platform_id ='. $gameDesc['game_platform_id']);

                if(empty($rows)){
                    $gameDesc['created_on'] = $now;
                    $gameDesc['flag_show_in_site'] = false;
                    $gameDesc['auto_sync_enable'] = false;

                    # regenerate md5
	                $gameDesc['md5_fields'] = $this->generateMD5SumOneRow($gameDesc,self::MAIN_GAME_ATTRIBUTES,self::GAME_DESC_INT_FIELDS);

                    //insert
                    if (!$hideDebugLog)
                        $this->utils->debug_log('insert game description', $gameDesc);

                    $gameDescId=$this->insertData($this->tableName, $gameDesc);

                    if ($gameDescId) {
                    	$data['game_insert_success'] = true;
	        			$this->processGameDescriptionHistory($gameDesc,self::ACTION_ADD,$gameDescId);
                    }

                    $data['list_of_games']['inserted_games'][$key] = array(
                    	'game_name' => $gameDesc['game_name'],
                    	'game_code' => $gameDesc['external_game_id'],
                    	'game_type_id' => $gameDesc['game_type_id'],
                    );

                    $cntInsert++;
                }else{

                    if(!isset($gameDesc['updated_at']) || !empty($cronjob)){
                        $gameDesc['updated_at']=$now;
                    }

				    if (isset($extra['api_auto_update']) && $extra['api_auto_update'] == self::DB_FALSE) continue;

                    //update all
                    foreach ($rows as $row) {


                    	if (empty($row['md5_fields']) || $row['md5_fields'] != $gameDesc['md5_fields']) {

                			if (empty($force_game_list_update))
	                    		if (!empty($cronjob) && $row['auto_sync_enable'] == self::DB_FALSE) continue;

		                    if ($dont_allow_disabled_game_to_be_launched && $row['status'] == self::DISABLED_GAME) {
	                            $this->utils->debug_log('ignore game update ========>', $gameDesc);
	                            continue;
	                        }

	                        if (isset($gameDesc['flag_new_game']))
	                            unset($gameDesc['flag_new_game']);

	                        $gameDescId=$row['id'];

	                        if (!$hideDebugLog)
	                            $this->utils->debug_log('update game description', $gameDesc, $gameDescId);
	                        //update
	                        $gameUpdateSuccess = $this->db->update($this->tableName, $gameDesc, ['id'=>$gameDescId]);

	                        $data['list_of_games']['updated_games'][$key] = array(
		                    	'game_name' => $gameDesc['game_name'],
		                    	'game_code' => $gameDesc['external_game_id'],
		                    	'game_type_id' => $gameDesc['game_type_id'],
		                    );

	                        $cntUpdate++;

							if ($gameUpdateSuccess) {
                                $this->processGameDescriptionHistory($gameDesc,self::ACTION_UPDATE,$gameDescId);
                            }

	                    }

                        $data['game_update_success'] = true;
                    }
                }
            }
        }

        unset($gameDescArr);

        $data['Counts'] = array('insert'=>$cntInsert,'update'=>$cntUpdate);
		$data['success'] = false;
        if (isset($data['game_insert_success']) || isset($data['game_update_success']))
        	$data['success'] = true;

        if (!$hideDebugLog)
            $this->utils->debug_log('game_description_counts',$data['Counts']);
        // echo "<pre>";print_r($data);exit();
        return $data;
    }

    public function devSyncGameDescriptionInGameGateway($gameDescArr)
    {
        $cntInsert = $cntUpdate = 0;
        $data = $data['missing_game_type'] = [];
        $dont_allow_disabled_game_to_be_launched = $this->utils->isEnabledFeature('dont_allow_disabled_game_to_be_launched');

        if(!empty($gameDescArr))
        {
            $now = $this->utils->getNowForMysql();
            foreach ($gameDescArr as $key => $gameDesc)
            {
                if (empty($gameDesc)) continue;
               	if (empty($this->processSyncGameDescLostInfo($gameDesc,$data,$key))) continue;

                if(!isset($gameDesc['external_game_id'])){
                    $gameDesc['external_game_id'] = $gameDesc['game_code'];
                }

                if(!isset($gameDesc['game_code'])){
                    $gameDesc['game_code'] = $gameDesc['external_game_id'];
                }

                if(!isset($gameDesc['english_name'])){
                    $arr=$this->decodeJsonTrans($gameDesc['game_name']);
                    $gameDesc['english_name'] = $arr['1'];
                }

                $this->processMd5FieldsSetFalseIfNotExist($gameDesc,self::MAIN_GAME_ATTRIBUTES,self::GAME_DESC_INT_FIELDS);
                $gameDesc['md5_fields'] = $this->generateMD5SumOneRow($gameDesc,self::MAIN_GAME_ATTRIBUTES,self::GAME_DESC_INT_FIELDS);

                //search by external_game_id
                $rows = $this->getGameByEODQuery('id,status,md5_fields,auto_sync_enable,external_game_id','external_game_id ="'.$gameDesc['external_game_id'].'" AND game_platform_id ='. $gameDesc['game_platform_id']);

                if(empty($rows))
                {
                    $gameDesc['created_on'] = $now;
                    $gameDesc['flag_show_in_site'] = false;
                    $gameDesc['auto_sync_enable'] = false;

                    # regenerate md5
	                $gameDesc['md5_fields'] = $this->generateMD5SumOneRow($gameDesc,self::MAIN_GAME_ATTRIBUTES,self::GAME_DESC_INT_FIELDS);

                    $gameDescId = $this->insertData($this->tableName, $gameDesc);

                    if ($gameDescId)
                    {
                    	$data['game_insert_success'] = true;
	        			$this->processGameDescriptionHistory($gameDesc,self::ACTION_ADD,$gameDescId);
                    }

                    $data['list_of_games']['inserted_games'][$key] = [
												                    	'game_name' => $gameDesc['game_name'],
												                    	'game_code' => $gameDesc['external_game_id'],
												                    	'game_type_id' => $gameDesc['game_type_id'],
														             ];
                    $cntInsert++;

                }else{
                    if(!isset($gameDesc['updated_at']))
                    {
                        $gameDesc['updated_at'] = $now;
                    }

                    //update all
                    foreach ($rows as $row)
                    {

                    	if (empty($row['md5_fields']) || $row['md5_fields'] != $gameDesc['md5_fields'])
                    	{
		                    if ($dont_allow_disabled_game_to_be_launched && $row['status'] == self::DISABLED_GAME)
		                    {
	                            $this->utils->debug_log('ignore game update ========>', $gameDesc);
	                            continue;
	                        }

	                        # this filter will check if game description was sync manually
	                        # in sbe therefore it will skip the sync from gamegateway
	                        if (!$this->utils->getConfig("allow_sync_from_sbe_updates")){
	                            if($this->isManuallyUpdatedInSbe($gameDesc['game_platform_id'],$gameDesc['game_code'])){
	                            	continue;
	                            }
	                        }

	                        if (isset($gameDesc['flag_new_game'])){
	                            unset($gameDesc['flag_new_game']);
	                        }

	                        if (isset($gameDesc['status'])){
	                            unset($gameDesc['status']);
	                        }

	                        if (isset($gameDesc['flag_show_in_site'])){
	                            unset($gameDesc['flag_show_in_site']);
	                        }

	                        $gameDescId = $row['id'];

	                        //update
	                        $gameUpdateSuccess = $this->db->update($this->tableName, $gameDesc, ['id'=>$gameDescId]);

	                        $data['list_of_games']['updated_games'][$key] = array(
		                    	'game_name' => $gameDesc['game_name'],
		                    	'game_code' => $gameDesc['external_game_id'],
		                    	'game_type_id' => $gameDesc['game_type_id'],
		                    );

	                        $cntUpdate++;

							if ($gameUpdateSuccess)
							{
                                $this->processGameDescriptionHistory($gameDesc,self::ACTION_UPDATE,$gameDescId);
                            }
	                    }
                        $data['game_update_success'] = true;
                    }
                }
            }
        }

        unset($gameDescArr);

        $data['Counts'] = array('insert'=>$cntInsert,'update'=>$cntUpdate);
		$data['success'] = false;
        if (isset($data['game_insert_success']) || isset($data['game_update_success']))
        	$data['success'] = true;

        return $data;
    }

    public function isManuallyUpdatedInSbe($gamePlatformId,$gameCode){
    	$qry = $this->db->get_where($this->tableName, array('game_code' => $gameCode,
    														'game_platform_id' => $gamePlatformId,
    														'locked_flag' => self::DB_TRUE,
    														)
    								);
		return $this->getOneRow($qry);
    }

    private function processSyncGameDescLostInfo(&$gameDesc,&$data,$key){
    	$success = true;
		if(empty($gameDesc['external_game_id']) || empty($gameDesc['game_platform_id']) || empty($gameDesc['game_type_id'])){
            $this->utils->error_log('lost info', $gameDesc);

            if(!empty($gameDesc['game_type'])){

                $data['missing_game_type_id'][$key] = array(
	            	'game_name' => $gameDesc['game_name'],
	            	'game_code' => $gameDesc['external_game_id'],
	        	    'game_type' => $gameDesc['game_type']
	        	);
                unset($gameDesc['game_type']);
            }

    		$success = false;
        }

        return $success;
    }

	public function getFreeSpinGame(){

		$this->db->from($this->tableName)
				 ->where('enabled_freespin > ', 0);
		return $this->runMultipleRowArray();

	}

	public function getRecord( $game_code = '' ){

		$this->db->where('game_code', $game_code)
				 ->from('game_description');

		return $this->runOneRow();

	}

	/**
	 * overview : get game decription id by game platform id and game name
	 *
	 * @param $gamePlatformId
	 * @param $gameName
	 * @param null $is_mobile
	 * @return null
	 */
	 public function getGameDescriptionByGamePlatformIdAndGameName($gamePlatformId, $gameName, $is_mobile = null) {

		 $this->db->select('id,game_code,game_name');
		 $this->db->from($this->tableName);
		 $this->db->where('game_platform_id', $gamePlatformId);
		 $this->db->where('status', self::STATUS_NORMAL);
		 $this->db->like('game_name', $gameName);

		 if($is_mobile){
			 $this->db->where('mobile_enabled', self::MOBILE_ENABLED);
		 }

		 $game_descriptions = $this->runMultipleRowArray();

		 if ( ! empty($game_descriptions)) {
			 foreach ($game_descriptions as $key => $game_description){
				 $arr=$this->decodeJsonTrans($game_description['game_name']);
				 $game_descriptions[$key]['game_name_en']=$arr['1'];
				 $game_descriptions[$key]['game_name_cn']=$arr['2'];
				 unset($game_descriptions[$key]['game_name']);
			 }
		 }

	 	return $game_descriptions;
	 }

	/**
	 * overview : check if game code exist
	 *
	 * @param 	int $gamePlatformId, $gameCode
	 * @return 	int
	 */
	public function checkIfGameCodeExist($gamePlatformId, $gameCode) {
		$qry = $this->db->get_where($this->tableName, array('game_platform_id' => $gamePlatformId, 'game_code' => $gameCode));
		return $this->getOneRowOneField($qry, 'game_code');
	}

	public function getGameDescriptionListByGamePlatformIdAndGameCodeArray($game_platform_id,$game_code_arr) {
		$this->db->from('game_description')->where('game_platform_id', $game_platform_id)->where_in('game_code', $game_code_arr);

		$rows=$this->runMultipleRow();
		$map=[];
		if(!empty($rows)){
			foreach ($rows as $row) {
				$map[$row->game_code]=$row;
			}
		}

		return $map;
	}

	/**
	 * Get rows of game_description by game_platform_id list
	 *
	 * @param array $game_platform_id_list
	 * @return array The rows.
	 */
	public function getGameDescriptionListByGamePlatformIdList($game_platform_id_list) {
		$this->db->from('game_description')->where_in('game_platform_id', $game_platform_id_list);
		$rows=$this->runMultipleRowArray();
		return $rows;
	} // EOF getGameDescriptionListByGamePlatformIdList

    public function getNewGames(){
        $this->db->select('gd.id');
        $this->db->from('game_description as gd');
        $this->db->join('external_system as es','es.id = gd.game_platform_id','left');
        $this->db->where('gd.flag_new_game',true);
        $this->db->where('gd.status',false);
        $this->db->where('es.status',true);
        $new_games = $this->runMultipleRow();
        return $new_games;
    }

    public function getNewGamesCount(){
        $this->db->select("count(*) as count");
        $this->db->join('external_system as es','es.id = gd.game_platform_id','left');
        $this->db->where('gd.flag_new_game',true);
        $this->db->where('gd.status',false);
        $this->db->where('es.status',true);
        $count = $this->db->get('game_description as gd');
        return $count->row('count');
    }
    /**
     * overview : get game description list
     * @param $gamePlatformId
     * @param $gameTypeId
     * @return array
     */
    public function getGameDescriptionListByGameTypeAndFlagNewGame($gamePlatformId, $gameTypeId) {
        $this->db->from($this->tableName)
                 ->where('game_platform_id', $gamePlatformId)
                 ->where('game_type_id', $gameTypeId)
                 ->where('flag_new_game', true)
                 ->where('status', self::DISABLED_GAME);
        $data = $this->runMultipleRowArray();
        return $data;
    }

    /**
     * overview : get game tree array
     *
     * @param array $selectedGamePlatformArr
     * @param array $selectedGameTypeInfoArr
     * @param array $selectedGameDescInfoArr
     * @param $percentage
     * @param $showGameDescTree
     * @return array
     */
    public function getGameTreeArrayByFlag($percentage, $showGameDescTree) {
        $this->load->model(array('external_system', 'game_type_model'));
        $result = array();

        $where = ["game_description.flag_new_game" => self::TRUE,"game_description.status" => self::DISABLED_GAME];
        $group_by = "game_description.game_platform_id";

        $activeGameList = $this->getGame($where,$group_by);

        $activeGameList = json_decode(json_encode($activeGameList),true);

        $gameApiList = $this->external_system->getAllActiveSytemGameApi();
        $game_map = [];
        $game_platform_list = [];

        #FILTER ACTIVE GAME PROVIDER WITH NEW GAMES ONLY
        if (empty($activeGameList)) return false;

        foreach ($gameApiList as $row) {
            foreach ($activeGameList as $key => $game) {
                if ($row['id'] == $game['gamePlatformId']) {
                    if ( ! in_array($game['gamePlatformId'], $game_platform_list)) {
                        array_push($game_map, $row);
                        array_push($game_platform_list,$row['id']);
                    }
                }
            }
        }

        //TODO try load cache first utils->getTextFromCache and saveTextToCache
        if (!empty($game_map))
        {
            foreach ($game_map as $row) {
                $number = null;

                if (empty($number)) {
                    $number = null;
                }

                $gameApiNode = [
                				'id' => 'gp_' . $row['id'],
                				'text' => $row['system_code'],
                				'state' => ["opened" => true],
                    			'set_number' => true,
                				'number' => $number,
                				'percentage' => $percentage
                			   ];
                //load game type
                $gameTypeList = $this->game_type_model->getGameTypeByFlagNewGames($row['id']);
                if (!empty($gameTypeList))
                {
                    foreach ($gameTypeList as $gameType)
                    {
                        $number = null;

                        if (empty($number)) {
                            $number = null;
                        }
                        $gameTypeNode = [
                        				 'id' => 'gp_' . $row['id'] . '_gt_' . $gameType['id'],
                        				 'text' => lang($gameType['game_type_lang']),
                        				 'state' => ["opened" => true],
                            			 'set_number' => true,
                            			 'number' => $number
                            			];

                        //load game description
                        $gameDescList = $this->getGameDescriptionListByGameTypeAndFlagNewGame($row['id'], $gameType['game_type_id']);
                        if (!empty($gameDescList)) {
                            foreach ($gameDescList as $gameDesc) {
                                $number = null;
                                if (empty($number)) {
                                    $number = null;
                                }
                                $gameDescNode = ['id' => 'gp_' . $row['id'] . '_gt_' . $gameType['id'] . '_gd_' . $gameDesc['id'],
                                				 'text' => lang($gameDesc['game_name']),
                                    			 'set_number' => $showGameDescTree,
                                    			 'number' => $number,
                                    			 'percentage' => $percentage];
                                //add to game type
                                $gameTypeNode['children'][] = $gameDescNode;
                            }
                        }

                        $gameApiNode['children'][] = $gameTypeNode;
                    }
                }
                $result[] = $gameApiNode;
            }
        }

        return $result;
    }

    public function getGameByExternalGameId($game_platform_id, $external_game_id){
        $this->db->select('*')->where('game_platform_id',$game_platform_id)->where('external_game_id',$external_game_id);
        $result = $this->db->get('game_description');
        return $result->result_array();
    }

    public function getGameDescriptionById($id) {
        $qry = $this->db->get_where($this->tableName, array('id' => $id));
        return $this->getOneRowArray($qry);
    }

    public function getGameDescriptionByIdList($ids) {
        $this->db->from('game_description')->where_in('id', $ids);
        $rows = $this->runMultipleRowArray();
        return $rows;
    }

	public function searchGameDescriptionByList($list){
		$result=[];
		$this->db->select('external_system.system_code, game_type.game_type_lang,game_name')
		    ->from($this->tableName)
		    ->join('external_system', 'external_system.id=game_description.game_platform_id')
		    ->join('game_type', 'game_type.id=game_description.game_type_id')
		    ->where_in('game_description.id', $list);
		$rows=$this->runMultipleRowArray();
		foreach ($rows as $row) {
			$result[]=lang($row['system_code']).'=>'.lang($row['game_type_lang']).'=>'.lang($row['game_name']);
		}

		return $result;
	}

    public function getDuplicateGames($game_platform_id = null){

        $this->db->select('id,game_platform_id, english_name,game_name, external_game_id,game_code, count(id) as game_count');
        $this->db->group_by('external_game_id, game_platform_id');
        if (!empty($game_platform_id)) {
            $this->db->where('game_platform_id', $game_platform_id);
        }
        $this->db->where_not_in('game_platform_id',['',0]);
        $this->db->having('game_count > 1');
        $result = $this->db->get('game_description');
        $this->utils->debug_log("getDuplicateGames query========================>",$this->db->last_query());
        return $result->result_array();
    }

    public function getGameByQuery($querys,$where,$group_by=null,$join = null, $having = null, $order_by_field = null, $order_by = null){
        $this->db->select($querys);
        $this->db->where($where);

        if ($join)
            $this->db->join($join['table'], $join['condition']);

        if ($group_by)
            $this->db->group_by($group_by);

        if ($having)
            $this->db->having($having);

        if ($order_by_field){
        	$game_code = implode('","', $order_by_field).'';
            $this->db->order_by('FIELD ( game_description.game_code, "'.$game_code.'")', 'desc', FALSE);
        }

        if ($order_by){
            $this->db->order_by($order_by, 'asc');
        }

        $this->db->from('game_description');

        // $this->runMultipleRowArray();
        // echo $this->db->last_query();exit;
        // print_r($result->result_array());exit;
        return $this->runMultipleRowArray();
    }

    public function getGameLogsCountPerGame($game_id){
        $this->db->select("count(id) as game_count");
        $this->db->where("game_description_id",$game_id);
        $this->db->group_by("game_description_id");
        $result = $this->db->get('game_logs');
        // echo $this->db->last_query();exit;
        // return $result->result_array();
        return is_array($result->row('game_count')) ? 0:$result->row('game_count');
    }

    public function moveGameLogsToMinimumGameId($data){
        if ($data['duplicate_game_ids']) {
            $duplicate_game_ids = implode(' ,', $data['duplicate_game_ids']);
        }else{
            return false;
        }

        $this->db->set("game_description_id",$data['min_game_id']);
        $this->db->where("game_description_id in (" . $duplicate_game_ids . ")");
        return $this->db->update("game_logs");

    }

    public function deleteGameByGameId($data){
        $duplicate_game_ids = implode(' ,', $data['duplicate_game_ids']);

        $this->db->where("id in (" . $duplicate_game_ids . ")");
        $this->db->where("game_platform_id",$data['game_platform_id']);
        $result = $this->db->delete("game_description");
        return $result;

    }

    public function backupGameDescriptionTable(){
        $table_name = "game_description_bak_" . date("YmdHi");
        $table = $this->db->query("CREATE TABLE " . $table_name . " LIKE game_description");

        $this->utils->debug_log("isTableCreated===================>",$table);
        if ($table) {
            return $this->db->query("INSERT INTO " . $table_name . " SELECT * FROM game_description");
        }else{
            return false;
        }
    }

	public function getGamelistPerGameProviders($gameProviderIds) {
		$this->db->select('*');
		$this->db->where('game_platform_id in (' .$gameProviderIds . ')');
		$this->db->where('external_game_id != "unknown"');
		$result = $this->db->get($this->tableName);

		return $result->result_array();
	}

	public function processUnknownGame($gamePlatformId, $unknownGameTypeId, $gameNameStr, $externalGameId, $extra = null) {

        if (isset($extra['game_type_code'])) { unset($extra['game_type_code']);}
        if (isset($extra['game_type'])) { unset($extra['game_type']);}

		$this->db->from($this->tableName)->where('game_platform_id', $gamePlatformId)
			->where('external_game_id', $externalGameId);

		$gameDescId = $this->runOneRowOneField('id');
		if (empty($gameDescId)) {
			$data = array('game_platform_id' => $gamePlatformId,
				'game_type_id' => $unknownGameTypeId,
				'game_name' => $gameNameStr,
				'english_name' => $gameNameStr,
				'game_code'=>$externalGameId,
				'external_game_id' => $externalGameId,
				'flash_enabled' => self::DB_TRUE,
				'status' => self::STATUS_NORMAL,
				'no_cash_back' => self::DB_FALSE,
				'void_bet' => self::DB_FALSE,
				'flag_new_game'=> self::DB_TRUE,
			);

			if (!empty($extra)) {
				$data = array_merge($data, $extra);
			}
			$gameDescId = $this->insertData($this->tableName, $data);
			$this->utils->debug_log('add new game', $gameNameStr, $externalGameId, $gamePlatformId, $unknownGameTypeId, 'game description id', $gameDescId);
			//write to group level
			$this->load->model(array('group_level'));
			$this->group_level->allowGameDescToAll($gameDescId);
		}
		return $gameDescId;
	}

	public function batchGetGameDescIdByCode(array $batchGameCode){
    	$gameCodeGameDescIdMap=[];
		$keyStr='"'.implode('", "', $batchGameCode ).'"';

		$sql=<<<EOD
select id, game_type_id, concat(game_platform_id, "-", external_game_id) as gd_key
from game_description
where concat(game_platform_id, "-", external_game_id) in ({$keyStr})
EOD;
		$rows=$this->runRawSelectSQLArrayUnbuffered($sql);
		if(!empty($rows)){
    		foreach ($rows as $row) {
    			$gameCodeGameDescIdMap[$row['gd_key']]=[
    				'game_description_id'=>$row['id'],
    				'game_type_id'=>$row['game_type_id'],
    			];
    		}
		}
		return $gameCodeGameDescIdMap;
	}

	public function getMultipleUnknownGameMap(array $platformIdArray) {

		$this->db->select('game_platform_id, game_type_id, id')->from($this->tableName)->where_in('game_platform_id', $platformIdArray)
			->where('external_game_id', self::UNKNOWN_GAME_CODE);

		$rows=$this->runMultipleRowArrayUnbuffered();
		$map=[];

		if(!empty($rows)){
			foreach ($rows as $row) {
				$map[$row['game_platform_id']]=['game_description_id'=>$row['id'], 'game_type_id'=>$row['game_type_id']];
			}
		}

		return $map;
	}

    /**
     * [syncUnknownGame sync unknown games to available game apis in external_system_list.xml file]
     * @return [type]         [description]
     */
    public function syncUnknownGame(){
        $key_index = 0;#use key index for syncGameDescription: game must be in nested array
        $this->load->model('game_type_model');
        $unknown_game_type = $this->game_type_model->getGameTagsById(16);

        #set unknown game attributes
        $unknown_game[$key_index] = [
            'game_name' => $unknown_game_type['tag_name'],
            'game_code' => self::UNKNOWN_GAME_CODE,
            'external_game_id' => self::UNKNOWN_GAME_CODE,
            'status' => self::DB_TRUE,
            'flag_show_in_site' => self::DB_FALSE,
            'enabled_on_android' => self::DB_FALSE,
            'enabled_on_ios' => self::DB_FALSE,
            'dlc_enabled' => self::DB_FALSE,
            'flash_enabled' => self::DB_FALSE,
            'html_five_enabled' => self::DB_FALSE,
			'progressive' => self::DB_FALSE,
			'flash_enabled' => self::DB_FALSE,
			'offline_enabled' => self::DB_FALSE,
			'mobile_enabled' => self::DB_FALSE,
			'note' => null,
			'no_cash_back' => self::DB_FALSE,
			'void_bet' => self::DB_FALSE,
			'attributes' => self::DB_FALSE,
			'game_order' => self::DB_FALSE,
			'html_five_enabled' => self::DB_FALSE,
			'english_name' => 'Unknown',
			'clientid' => null,
			'moduleid' => null,
			'related_game_desc_id' => null,
			'enabled_freespin' => self::DB_FALSE,
			'sub_game_provider' => self::DB_FALSE,
			'demo_link' => null,
        ];
        #done

        #set game type attributes: game_type table
        $extra = [
            'game_type' => $unknown_game_type['tag_name'],
            'game_type_code' => $unknown_game_type['tag_code'],
        ];
        #end

        $directory = dirname(__FILE__) . '/../../../submodules/core-lib/application/config/external_system_list.xml';
        $xml = simplexml_load_file($directory);

        if ($xml) {
            $result = [];
            $cnt_success = $cnt_failed = 0;
            foreach ($xml as $row) {
                $row = $this->utils->xmlToArray($row);

                if (isset($row['system_type']) && $row['system_type'] == SYSTEM_GAME_API) {
                    $unknown_game[$key_index]['game_platform_id'] = $row['id'];
                    $unknown_game[$key_index]['game_type_id'] = $this->game_type_model->checkGameType($row['id'], $unknown_game_type['tag_name'], $extra);
                    $success = $this->syncGameDescription($unknown_game,null,null,true);
                    if (array_sum($success['Counts']) > 0 || isset($success['game_update_success'])) {
                        $result[$row['system_name']]['success'] = true;
                        $cnt_success++;
                    }else{
                        $result[$row['system_name']]['success'] = false;
                        $cnt_failed++;
                        $this->utils->debug_log('syncUnknownGame ============>[Failed add unknown game!]', $row);
                    }
                }
            }

            $this->utils->debug_log('syncUnknownGame ============>',['total_success' => $cnt_success, 'total_failed' => $cnt_failed]);
            $success = true;
        }else{
            $success = false;
            $this->utils->debug_log('syncUnknownGame ============>[XML not found!]');
        }

        return ["result"=>json_encode($result), 'count' => count($result),'success'=>$success];

    }

    /**
     * [checkGameIfLaunchable description]
     * @param  [int] $game_platform_id          [defined api id]
     * @param  [string] $game_code              [game launch code]
     * @param  [string] $game_id                [other attributes of the game]
     * @param  [string] $sub_game_provider      [sub game provider]
     * @param  [boolean] $check_attributes_only [check attributes only]
     * @return [boolean/array]                  [return error message or boolean]
     */
    public function checkGameIfLaunchable($game_platform_id, $game_code = null, $game_id = null, $sub_game_provider = null, $check_attributes_only = null){

        $where = "game_platform_id = " . $game_platform_id . " AND `game_code` = '" . $game_code . "' AND `status` = " . self::ENABLED_GAME;
        #check if when launching game requires game id
        #Game API: RTG
        if ($check_attributes_only) {
            $where = "game_platform_id = " . $game_platform_id;
            $result = $this->getGameByQuery("*", $where);

            $game_platform_ids_game_id_only = [PNG_API,YOPLAY_API];

            foreach ($result as $game_details) {
                if (empty($game_details['attributes'])) continue;
                $attributes = json_decode($game_details['attributes'],true);

                if (empty($attributes)) {
                    $this->utils->debug_log('Wrong json format =========>', $game_details);
                    continue;
                }

                if ($game_code)
                    $game_detail['game_code'] = array_search($game_code, $attributes);
                $game_detail['game_id'] = array_search($game_id, $attributes);

                if (count(array_filter($game_detail)) == 2 || (array_key_exists('game_id', $game_detail) && !empty($game_detail['game_id']) && in_array($game_platform_id, $game_platform_ids_game_id_only))) {

                    if ($game_details['status'] == self::ENABLED_GAME) {
                        return TRUE;
                    }else{
                        $this->utils->debug_log("checkGameIfLaunchable ==========>[game disabled]");
                        return FALSE;
                    }
                }
            }

            if ($this->utils->isEnabledFeature('allow_to_launch_non_existing_games_on_sbe')) {
                return true;
            }else{
                return ['error'=> "Game Not Found"];
            }


        }elseif($sub_game_provider){
            #check if when launching game requires sub game provider
            #Game API: Suncity
            $where .= " AND sub_game_provider = " . $sub_game_provider;
            $id = $this->getGameByQuery("id", $where);

            $extra["sub_game_provider"] = $sub_game_provider;
            if (empty($id))
                return $this->checkGameExistForGameLaunching($game_platform_id,$game_code,$extra);
        }else{
            $id = $this->getGameByQuery("id", $where);

            if (empty($id))
                return $this->checkGameExistForGameLaunching($game_platform_id,$game_code);
        }

        if ( ! empty($id)) {
            if (count($id) > 1) {
                $this->utils->debug_log("checkGameIfLaunchable ==========>[so many games]",$id);
                return FALSE;
            }
            return TRUE;
        }else{
            $this->utils->debug_log("checkGameIfLaunchable ==========>[game disabled]");
            return FALSE;
        }
    }

    protected function checkGameExistForGameLaunching($game_platform_id,$game_code,$extra = null){
        if ($this->utils->isEnabledFeature('allow_to_launch_non_existing_games_on_sbe')) {

            $where = "game_platform_id = " . $game_platform_id . " and game_code = '" . $game_code."'";
            if ( ! empty($extra['sub_game_provider']))
                $where .= " AND sub_game_provider = " . $extra['sub_game_provider'];

            $id = $this->getGameByQuery("id", $where);
            if (empty($id)) {
                $this->utils->debug_log("checkGameIfLaunchable ==========>[game not exist]",$game_code);
                return true;
            }
        }
    }

    public function getGameDescriptionHistory($gameDescriptionId){
    	$this->db->select('gdh.*,gt.game_type')
    		->from('game_description_history as gdh')
    		->join('game_type as gt', 'gt.game_platform_id = gdh.game_platform_id and gt.id = gdh.game_type_id','left')
    		->where('gdh.game_description_id',$gameDescriptionId);
    	$result = $this->runMultipleRowArray();
        $this->utils->debug_log("getGameDescriptionHistory ==========>[query]",$this->db->last_query());
    	return $result;
    }

    private function getGameByEODQuery($select,$where){
$sql = <<<EOD
    SELECT {$select} FROM game_description
    WHERE {$where}
EOD;
		$result = $this->db->query($sql)->result_array();
		return $result;
    }

    /**
	 * overview : activate new games from gamegateway, removed update md5 so the next time it sync it wont deactivate again
	 *
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	public function activate_new_games_from_gamegateway($id,$data)
	{
		return $this->db->update($this->tableName, $data, array('id' => $id));
	}

	/**
	 * Get the Game Name by current Language
	 *
	 * @param mixed $gameId
	 * @param int $game_platform_id
	 * @param string $unique_id
	 *
	 * @return mixed
	*/
	public function getGameNameByCurrentLang($gameId, $game_platform_id,$unique_id="game_code")
	{
		$sql = $this->db->select('game_name')
				->from($this->tableName)
				->where($unique_id, $gameId)
				->where('game_platform_id', $game_platform_id);

		 $result = $this->runOneRowArray();

		 if(isset($result["game_name"])){
			return $this->utils->text_from_json($result['game_name'], $this->CI->language_function->getCurrentLanguage());
		 }
	}

    public function queryByCode($gamePlatformId, $gameTypeCode=null, $gameCode=null, &$sqlInfo=null){

    	$this->db->select('game_description.game_platform_id, game_description.external_game_id as game_unique_code, game_description.game_name')
    		->select('game_description.flash_enabled as in_flash, game_description.html_five_enabled as in_html5, game_description.mobile_enabled as in_mobile, game_description.enabled_on_android as available_on_android, game_description.enabled_on_ios as available_on_ios')
    		->select('game_description.status as game_status, game_description.progressive, game_description.enabled_freespin')
    		->select('game_type.game_type_lang, game_type.game_type_code as game_type_unique_code, game_type.status as game_type_status')
    	    ->from('game_description')
    	    ->join('game_type', 'game_description.game_type_id=game_type.id')
    	    ->where('game_description.game_platform_id', $gamePlatformId);
    	if(!empty($gameTypeCode)){
    		$this->db->where('game_type.game_type_code', $gameTypeCode);
    	}
    	if(!empty($gameCode)){
    		$this->db->where('game_description.external_game_id', $gameCode);
    	}

    	$rows=$this->runMultipleRowArray();
    	//get last sql
    	$sqlInfo=['sql'=>$this->db->last_query()];
    	foreach ($rows as &$row) {
    		//process game name and type name
    		$row['game_name_detail']=$this->utils->extractLangJson($row['game_name']);
    		$row['game_type_name_detail']=$this->utils->extractLangJson($row['game_type_lang']);
    		$row['in_flash']=$row['in_flash']==self::DB_TRUE;
    		$row['in_html5']=$row['in_html5']==self::DB_TRUE;
    		$row['in_mobile']=$row['in_mobile']==self::DB_TRUE;
    		$row['available_on_android']=$row['available_on_android']==self::DB_TRUE;
    		$row['available_on_ios']=$row['available_on_ios']==self::DB_TRUE;
    		$row['enabled_freespin']=$row['enabled_freespin']==self::DB_TRUE;
    		$row['progressive']=$row['progressive']==self::DB_TRUE;
    		$row['game_status']=$row['game_status']==self::STATUS_NORMAL ? 'normal' : 'disabled';
    		$row['game_type_status']=$row['game_type_status']==self::STATUS_NORMAL ? 'normal' : 'disabled';
    		$row['game_platform_id']=intval($row['game_platform_id']);
    		unset($row['game_name']);
    		unset($row['game_type_lang']);
    	}
    	return $rows;
    }

    public function updateActiveGameList($gamePlatformId, $aGameCodes){

    	$aParamsSelect = [];
    	$aParamsUpdate = [];
    	$aParamsUpdateFlagShowInSite = [];

    	$iGamesCounted = count($aGameCodes);
    	$sQueryForIn = "(" . implode(',', array_fill(0, $iGamesCounted, '?')) . ")";

    	// 1.1 SELECT ALL GAMES that have 0 FLAG SHOW IN SITE base on the given GAME CODES FROM UPLOADED CSV FILE
    	array_push($aParamsSelect, self::GAME_NULL, $gamePlatformId, self::GAME_INACTIVE);
    	$aParamsSelect = array_merge($aParamsSelect, $aGameCodes);
        $sQuerySelect = "SELECT g.english_name as 'English_Name', g.game_code as 'Game_Code', e.system_name as 'Game_Platform' FROM game_description as g, external_system as e WHERE g.locked_flag IS ? AND g.game_platform_id = ? AND g.flag_show_in_site = ? AND e.id = g.game_platform_id AND g.game_code IN " . $sQueryForIn;
    	if ($gamePlatformId === self::MG_PLATFORM_ID || $gamePlatformId === self::T1MG_PLATFORM_ID) {
	    	array_push($aParamsSelect, self::MG_NOTE);
	        $sQuerySelect .= " AND g.note = ?";
    	}
     	$aResult = $this->runRawSelectSQLArray($sQuerySelect,$aParamsSelect);
     	// 1.1 END

     	// 1.2 SET FLAG_SHOW_IN_SITE = 0 TO ALL GAME ON THE GIVEN PLATFORM ID AND GAME CODES FROM UPLOADED CSV FILES TO MAKE SURE THAT ALL GAMES UNDER PLATFORM ID WILL HAVE 0 FLAG_SHOW_IN_SITE
    	array_push($aParamsUpdateFlagShowInSite, self::GAME_INACTIVE, $gamePlatformId, self::GAME_NULL);
    	$aParamsUpdateFlagShowInSite = array_merge($aParamsUpdateFlagShowInSite, $aGameCodes);
        $sQueryUpdateFlagShowInSite = "UPDATE game_description SET flag_show_in_site = ? WHERE game_platform_id = ? AND locked_flag IS ?";
        $this->runRawUpdateInsertSQL($sQueryUpdateFlagShowInSite,$aParamsUpdateFlagShowInSite);
        // 1.2 END

        // 1.3 UPDATE FLAG_SHOW_IN_SITE = 1 OF ALL GAMES ON GIVEN PLATFORM ID AND GAME CODES FROM UPLOADED CSV FILES TO MAKE SURE THAT ONLY ACTIVE GAMES WILL HAVE FLAG_SHOW_IN_SITE = 1
		array_push($aParamsUpdate, self::GAME_ACTIVE, self::GAME_ACTIVE,self::GAME_NULL ,$gamePlatformId);
    	$aParamsUpdate = array_merge($aParamsUpdate, $aGameCodes);
        $sQueryUpdate = "UPDATE game_description SET status = ?, flag_show_in_site = ? WHERE locked_flag IS ? AND game_platform_id = ? AND game_code IN " . $sQueryForIn;
        // 1.3.1 SPECIAL CASE FOR MG SINCE THE ONLY GAMES THAT WILL APPEAR ON FRONTEND IS ONLY THE MG GAMES THAT HAVE A NOTE VALUE = 'redirector'
        if ($gamePlatformId === self::MG_PLATFORM_ID || $gamePlatformId === self::T1MG_PLATFORM_ID) {
	        $sQueryUpdate .= " AND note = ?";
			array_push($aParamsUpdate, self::MG_NOTE);
        }
        // 1.3.1 END
        // 1.3 END
		$aAffected = $this->runRawUpdateInsertSQL($sQueryUpdate,$aParamsUpdate);
		return $aResult;
    }

    /**
     * sync game list
     *
     *  gameList format: ['game_platform_id'=>, 'game_unique_code'=>, 'game_name_detail'=>,
     *  'in_flash'=>, 'in_html5'=>, 'in_mobile'=>, 'available_on_android'=>, 'available_on_ios'=>,
     *  'game_status'=>, 'progressive'=>, 'enabled_freespin'=>, 'game_type_unique_code'=>, ]
     *
     * @param  array $gameList
     * @return boolean
     */
    public function syncFrom($gamePlatformId, array $gameList){
        if(empty($gameList)){
            return false;
        }

        $success=false;

        $gameTypeCodeArr=array_column($gameList, 'game_type_unique_code');
        $gameTypeCodeArr=array_unique($gameTypeCodeArr);
        $this->load->model(['game_type_model']);
        $unknownGameType=$this->game_type_model->getUnknownGameType($gamePlatformId);
        $gameTypeIdArr=$this->game_type_model->queryIdMapByCode($gamePlatformId, $gameTypeCodeArr);
        foreach ($gameList as $gameInfo) {
            //search by game_platform_id and game_type_unique_code
            $gameNameLang=convertLangDetailToJsonLangFormat($gameInfo['game_name_detail']);
            $gameTypeId=isset($gameTypeIdArr[$gameInfo['game_type_unique_code']]) ?
            	$gameTypeIdArr[$gameInfo['game_type_unique_code']] : $unknownGameType->id;
            $data=[
                'game_platform_id'=>$gamePlatformId,
                'game_type_id'=>$gameTypeId,
                'game_name'=>$gameNameLang,
                'game_code'=>$gameInfo['game_unique_code'],
                "dlc_enabled"=> self::DB_FALSE,
                "progressive"=> empty($gameInfo['progressive']) ? self::DB_FALSE : $gameInfo['progressive'],
                "flash_enabled"=> empty($gameInfo['in_flash']) ? self::DB_FALSE : $gameInfo['in_flash'],
                "offline_enabled"=> self::DB_FALSE,
                "mobile_enabled"=> empty($gameInfo['in_mobile']) ? self::DB_FALSE : $gameInfo['in_mobile'],
                "flag_show_in_site"=> self::DB_TRUE,
                "no_cash_back"=> self::DB_FALSE,
                "void_bet"=> self::DB_FALSE,
                "attributes"=> null,
                "game_order"=> null,
                "html_five_enabled"=> empty($gameInfo['in_html5']) ? self::DB_FALSE : $gameInfo['in_html5'],
                "english_name"=> $gameInfo['game_name_detail']['en'],
                "external_game_id"=> $gameInfo['game_unique_code'],
                "clientid"=> null,
                "moduleid"=> null,
                "related_game_desc_id"=> null,
                "enabled_freespin"=> empty($gameInfo['enabled_freespin']) ? self::DB_FALSE : $gameInfo['enabled_freespin'],
                "sub_game_provider"=> null,
                "enabled_on_android"=> empty($gameInfo['available_on_android']) ? self::DB_FALSE : $gameInfo['available_on_android'],
                "enabled_on_ios"=> empty($gameInfo['available_on_ios']) ? self::DB_FALSE : $gameInfo['available_on_ios'],
                "flag_new_game"=> self::DB_FALSE,

                'status'=>self::DB_BOOL_STR_TO_INT[$gameInfo['game_status']],
                'updated_at'=>$this->utils->getNowForMysql(),
            ];
            $id=$this->queryGameIdByCode($gamePlatformId, $gameInfo['game_unique_code']);
            if(empty($id)){
            	$data['created_on']=$this->utils->getNowForMysql();
                //insert
                $success=!!$this->runInsertData('game_description', $data);
            }else{
                $this->db->where('id', $id)->set($data);
                $success=!!$this->runAnyUpdate('game_description');
            }
        }
        return $success;
    }

    public function queryGameIdByCode($gamePlatformId, $code){
        $this->db->select('id')->from('game_description')->where('game_platform_id', $gamePlatformId)
            ->where('external_game_id', $code);
        return $this->runOneRowOneField('id');
    }

    public function queryExternalGameIdByCode($gamePlatformId ,$game_code){
        $this->db->select('external_game_id')->from('game_description')->where('game_platform_id', $gamePlatformId)
            ->where('game_code', $game_code);
        return $this->runOneRowOneField('external_game_id');
	}

	/**
     * Get The game_description.game_type_id list limit by game_platform_id list
     *
     * @param array $game_platform_id_list The  external_system(_list).id list.
     * @return array $game_type_id_list The game_description.game_type_id list.
     */
    public function getGameTypeIdByPlatformIdList($game_platform_id_list = []){
        $this->load->library(['og_utility']);
        $rows = $this->getGameDescriptionListByGamePlatformIdList($game_platform_id_list);
        $game_type_id_list = $this->og_utility->array_pluck($rows, 'game_type_id');
        return $game_type_id_list;
    }// EOF getGameTypeIdByPlatformIdList

    /**
     * Get The game_description.game_description_id list limit by game_platform_id list
     *
     * @param array $game_platform_id_list The  external_system(_list).id list.
     * @return array $game_description_id_list The game_description.game_description_id list.
     */
    public function getGameDescriptionIdByPlatformIdList($game_platform_id_list = []){
        $this->load->library(['og_utility']);
        $rows = $this->getGameDescriptionListByGamePlatformIdList($game_platform_id_list);
        $game_description_id_list = $this->og_utility->array_pluck($rows, 'id');
        return $game_description_id_list;
    } // EOF getGameDescriptionIdByPlatformIdList
}

///END OF FILE///////
