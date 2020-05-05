<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/modules/game_list_module.php';

class Game_list_lib {

    use game_list_module;
    const DEFAULT_EXEMPTED_GAME_TYPES_CODES_FORSYNC = [
                                                        'yoplay',
                                                        'tip',
                                                      ];
    const DEFAULT_GAMETYPE_KEYS_FOR_UNSET = [
                                             'id',
                                             'game_tag_id',
                                             'game_type_code',
                                             'game_type',
                                             'created_at',
                                             'updated_at',
                                             'md5_fields'
                                            ];
    public $_app_prefix;

    function __construct() {
        $this->CI = &get_instance();

        $this->_app_prefix=try_get_prefix();

        //get db name , if it's not og, use it, if it's og, use hostname
        // $default_db=config_item('db.default.database');
        // if($default_db!='og'){
        //     $this->_app_prefix=$default_db;
        // }else{
        //     static $_log;
        //     $_log = &load_class('Log');

        //     $this->_app_prefix=$_log->getHostname();
        // }

        // $is_staging=config_item('RUNTIME_ENVIRONMENT')=='staging';
        // if($is_staging && strpos($this->_app_prefix, 'staging')===false){
        //     //try append staging
        //     $this->_app_prefix.='_staging';
        // }

        $this->utils = $this->CI->utils;
        // $this->load = $this->CI->load;

    }

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

    const GAME_PLATFORM_HTML5 = "html5";
    const GAME_PLATFORM_IOS = "ios";
    const GAME_PLATFORM_FLASH = "flash";
    const GAME_PLATFORM_MOBILE = "mobile";
    const GAME_PLATFORM_ANDROID = "android";

    const ENGLISH_LANG_CODE = 1;
    const CHINESE_LANG_CODE = 2;
    const INDONESIAN_LANG_CODE = 3;
    const VIETNAMESE_LANG_CODE = 4;
    const KOREAN_LANG_CODE = 5;
    const THAI_LANG_CODE = 6;

    const FILTER_FLAG_SHOW_IN_SITE = true;

    /**
     * [getFrontEndGames generate available games game link
     *  How to use this function:
     *      - player.<client_site>/pub/get_front_end_games/<game_platform_id>/<game_type_code>
     *      - sample url: http://player.gamegateway.t1t.games/pub/get_frontend_games/1
     *      - when game_platform_id is empty, this function will show the available game provider with its details and you can use any game_platform_id value as parameter
     *      - as for the game type code, if you put a random text it will show the possible game type codes and you can use it as parameter
     *    System Feature: allow_generate_inactive_game_api_game_lists
     *      - this function have feature that only able the user to view the available game links with current active game api only
     *
     *  Fields description:
     *  'game_type_code' - can be use to determine which game type should be displayed
     *  'game_name' - full game name of the game
     *  'game_name_en' - english game name
     *  'game_name_cn' - chinese game name
     *  'game_name_indo' - indonesian game name
     *  'game_name_vn' - vietnamese game name
     *  'game_name_kr' - korean game name
     *  'provider_name' - Game provider full name
     *  'game_id_desktop' - game launch code for desktop
     *  'game_id_mobile' - game launch code for mobile
     *  'in_flash' - distinction if game is launchable on web only
     *  'in_html5' - distinction if game is launchable on mobile and web
     *  'mobile_enabled' - distinction if game is launchable on mobile only
     *  'downloadable' - distinction if game is downloadable
     *  'available_on_android' - distinction if game is launchable on android web version
     *  'available_on_ios' - distinction if game is launchable on ios web version
     *  'note' - note about the game
     *  'status' - status if game is enabled or not
     *  'top_game_order' - game order of the game most likely for top games
     *  'enabled_freespin' - distinction if game have free spin or not
     *  'sub_game_provider' - name of the game provider
     *  'flag_new_game' - status of the game if new or not
     *  'flag_show_in_site' - status of the game if can be viewable in site
     *  'progressive' - status if the game is/have jackpot or not
     *  'game_launch_url' - this is the available game links for the game
     *  'game_launch_code_other_settings' - this fields contains the other details for game link
     *  ]
     * @param  [int] $game_platform_id [defined game provider id]
     * @param  [string] $game_type_code   [show specific game type games only]
     * @return [type]                   [description]
     */
    public function getFrontEndGames($game_platform_id = null, $game_type_code = null, $game_platform = null, $request = null){
        $this->CI->load->model(['game_description_model','game_type_model','external_system']);

        $game_apis = $this->CI->external_system->getActivedGameApiList();

        if (empty($game_platform_id)) {
            $data = $this->getGameProviderDetails();
            if (!$this->CI->utils->isEnabledFeature('allow_generate_inactive_game_api_game_lists')) {
                $temp_data = [];
                foreach ($data['available_game_providers'] as $key => $game_provider) {
                    if(in_array($game_provider['game_platform_id'], $game_apis)){
                        $temp_data[$key] = $game_provider;
                    }
                }
                $data['available_game_providers'] = $temp_data;
                unset($temp_data);
            }

        }elseif(!empty($game_platform_id)){
            if (!$this->CI->utils->isEnabledFeature('allow_generate_inactive_game_api_game_lists')) {
                if (!in_array($game_platform_id, $game_apis))
                    return false;
            }
            $this->checkGameProviderGamelist($game_platform_id, $game_type_code, $data, $game_platform, $request);
        }

        if (isset($data['game_list'])) {
            $game_list = [];
            foreach ($data['game_list'] as $key => $game) {
                $game_list[$key] = $this->prepareFrontendGames($game);
            }
            $data['game_list'] = $game_list;
            unset($game_list);
        }
        //sorting of gamelist by game order and game unique id
        if(isset($data['game_list']))
        {
            $game_order_top =  array_column($data['game_list'], 'top_game_order');
            $game_unique_id = array_column($data['game_list'], 'game_unique_id');
            array_multisort($game_order_top, SORT_DESC, $game_unique_id, SORT_ASC, $data['game_list']);
        }
        return $data;

        # OUTPUT
        // $this->output->set_header('Access-Control-Allow-Origin: *');
        // $this->output->set_content_type('application/json');
        // $this->output->set_output(json_encode($data, JSON_PRETTY_PRINT));
    }

    private function checkGameProviderGamelist($game_platform_id, $game_type_code, &$data, $game_platform, $request = null){
        $order_by_field = null;
        $order_by = null;
        if(!empty($request)){
            $request = json_decode($request,true);
            $order_by_field = isset($request['top_game_code']) ? $request['top_game_code'] : null;
            $available_order_by_field = array("game_code","game_name");
            if(isset($request['order_by']) && (in_array($request['order_by'], $available_order_by_field))){//optional
                $order_by = $request['order_by'];
            }
        }
        $select = "*";
        $where = "flag_show_in_site = 1 and status = 1 and external_game_id != 'unknown' and game_code != 'unknown' and game_platform_id = " . $game_platform_id;

        if (!in_array($game_platform, [self::GAME_PLATFORM_FLASH,self::GAME_PLATFORM_HTML5,self::GAME_PLATFORM_IOS,self::GAME_PLATFORM_ANDROID,self::GAME_PLATFORM_MOBILE,"all"])) {
            return $data = ["note"=>lang("Game platform not found!"),"avaialable_version"=>[self::GAME_PLATFORM_FLASH,self::GAME_PLATFORM_HTML5,self::GAME_PLATFORM_IOS,self::GAME_PLATFORM_ANDROID,self::GAME_PLATFORM_MOBILE]];
        }

        switch ($game_platform) {
            case self::GAME_PLATFORM_FLASH:
                $where.=" and flash_enabled = " . GAME_DESCRIPTION_MODEL::DB_TRUE;
                break;
            case self::GAME_PLATFORM_HTML5:
                $where.=" and html_five_enabled = " . GAME_DESCRIPTION_MODEL::DB_TRUE;
                break;
            case self::GAME_PLATFORM_IOS:
                $where.=" and enabled_on_ios = " . GAME_DESCRIPTION_MODEL::DB_TRUE;
                break;
            case self::GAME_PLATFORM_ANDROID:
                $where.=" and enabled_on_android = " . GAME_DESCRIPTION_MODEL::DB_TRUE;
                break;
            case self::GAME_PLATFORM_MOBILE:
                $where.=" and mobile_enabled = " . GAME_DESCRIPTION_MODEL::DB_TRUE;
                break;
            default:
                break;
        }

        # if client is ole, kycard will have lobby instead of url for individual games
        $providers_have_lobby = $this->utils->getConfig('allow_lobby_in_provider');

        if ( ! in_array($game_platform_id,GAME_DESCRIPTION_MODEL::GAME_API_WITH_LOBBYS) &&  ! in_array($game_platform_id,$providers_have_lobby)) {

            if (isset($this->getGameProviderDetails()['available_game_providers'][$game_platform_id])) {
                if ($game_type_code && !in_array($game_type_code, ['null','false'])) {
                    $game_type_id = $this->CI->game_type_model->getGameTypeIdGametypeCode($game_platform_id,$game_type_code);

                    if (empty($game_type_id)) {

                        #get all game types with active games only
                        $query = "game_type.game_type_code, count(game_type_id) as game_type_count";
                        $where = "game_type_code != 'unknown' and game_type.game_platform_id = " . $game_platform_id . " and game_description.status != " . GAME_DESCRIPTION_MODEL::DB_FALSE;
                        $group_by = "game_type_id";
                        $having = "game_type_code > 0";
                        $join = ["table"=>"game_type","condition"=>"game_type.id = game_description.game_type_id"];
                        $game_type_list = $this->CI->game_description_model->getGameByQuery($query,$where,$group_by,$join,null, $order_by_field,$order_by);
                        #end

                        $data['note'] = lang('Game Type Code not found!');
                        $data['available_game_type_codes'] = !empty($game_type_list) ? array_column($game_type_list, 'game_type_code'):"Game list is not Supported or Game list is not Synced!";
                    }else{
                        $where .= " and game_type_id = " . $game_type_id;
                        $data['game_list'] = $this->CI->game_description_model->getGameByQuery($select,$where,null,null,null, $order_by_field, $order_by);
                    }
                    return false;
                }
                $data['game_list'] = $this->CI->game_description_model->getGameByQuery($select,$where,null,null,null, $order_by_field, $order_by);
                // $data['game_list'] = $this->CI->game_description_model->getGameByQuery($select,$where);
            }else{
                $data['Error!!!'] = "Your Game platform id is not in the list. Please select the available game provider below:";
                $data['List'] = $this->getGameProviderDetails();
            }
            return false;
        }

        #Game provider belows have their own game lobby

        $game_api_details = $this->getGameProviderDetails();
        $data["Note"]= "This Game provider have their own game lobby: " . $game_api_details['available_game_providers'][$game_platform_id]['complete_name'];
        $game_launch_url = $game_api_details['available_game_providers'][$game_platform_id]['game_launch_url'];

        switch ($game_platform_id) {
            case AGIN_API:
            case T1AGIN_API:
                $game_types = ['slots', 'fishing_game', 'live_dealer', 'yoplay'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'slots' => ['web'=>8, 'mobile'=>8, 'type_lang' => $game_type_lang['slots']],
                    'fishing_game' => ['web'=>6, 'mobile'=>6, 'type_lang' => $game_type_lang['fishing_game']],
                    'live_dealer' => ['web'=>18, 'mobile'=>18, 'type_lang' => $game_type_lang['live_dealer']],
                    'yoplay' => ['web'=>'YP800', 'mobile'=>'YP800', 'type_lang' => $game_type_lang['yoplay']],
                    'main_lobby' => ['web'=>'0', 'mobile'=>'0'],
                ];
                break;
            case AGBBIN_API:
            case GSBBIN_API:
                $game_types = ['slots', 'fishing_game', 'live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'slots' => ['web'=>8, 'mobile'=>8, 'type_lang' => $game_type_lang['slots']],
                    'fishing_game' => ['web'=>6, 'mobile'=>6, 'type_lang' => $game_type_lang['fishing_game']],
                    'live_dealer' => ['web'=>6, 'mobile'=>18, 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
            case BBIN_API:
                $game_types = ['sports', 'lottery', 'live_dealer', 'slots', 'fishing_game'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $gamekind_sports = '1';
                $gamekind_live = '3';
                $gamekind_slots = '5';
                $gamekind_lottery = '12';
                $gamekind_fishing = '30';
                $game = [
                    'sports' => ['web'=>$gamekind_sports, 'mobile'=>$gamekind_sports, 'type_lang' => $game_type_lang['sports']],
                    'lottery' => ['web'=>$gamekind_lottery, 'mobile'=>$gamekind_lottery, 'type_lang' => $game_type_lang['lottery']],
                    'live_dealer' => ['web'=>$gamekind_live, 'mobile'=>$gamekind_live, 'type_lang' => $game_type_lang['live_dealer']],
                    'slots' => ['web'=>$gamekind_slots, 'mobile'=>$gamekind_slots, 'type_lang' => $game_type_lang['slots']],
                    'fishing_game' => ['web'=>$gamekind_fishing, 'mobile'=>$gamekind_fishing, 'type_lang' => $game_type_lang['fishing_game']]
                ];
                break;
            case T1BBIN_API:
                $game_types = ['sports', 'lottery', 'live_dealer', 'slots', 'fishing_game'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'sports' => ['web'=>1, 'mobile'=>1, 'type_lang' => $game_type_lang['sports']],
                    'lottery' => ['web'=>34, 'mobile'=>34, 'type_lang' => $game_type_lang['lottery']],
                    'live_dealer' => ['web'=>36, 'mobile'=>36, 'type_lang' => $game_type_lang['live_dealer']],
                    'slots' => ['web'=>37, 'mobile'=>37, 'type_lang' => $game_type_lang['slots']],
                    'fishing_game' => ['web'=>'fish', 'mobile'=>'fish', 'type_lang' => $game_type_lang['fishing_game']]
                ];
                break;
            case EZUGI_API:
            case ENTWINE_API:
            case OG_API:
            case T1OG_API:
            case AB_API:
            case LD_CASINO_API:
            case EBET_AG_API:
            case EBET_OPUS_API:
            case EBET_API:
                $game_types = ['live_dealer','slots'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ['type_lang' => $game_type_lang['live_dealer']],
                    'slots' => ['type_lang' => $game_type_lang['slots']]
                ];
                break;
            case T1VR_API:
            case VR_API:
                $game_types = ['lottery'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = ['lottery' => ["1", 'type_lang' => $game_type_lang['lottery']],];
                break;
            case IDN_API:
            case BAISON_GAME_API:
                $game_types = ['poker', 'fishing_game'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'poker' => ["1", 'type_lang' => $game_type_lang['poker']],
                    'fishing_game' => ["1", 'type_lang' => $game_type_lang['fishing_game']]
                ];
                break;
            case KYCARD_API:
                $game_types = ['table_and_cards'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = ['table_and_cards' => ["1","type_lang" => $game_type_lang['table_and_cards']],];
                break;
            case LE_GAMING_API:
                $game_types = ['table_and_cards'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = ['table_and_cards' => ["1","type_lang" => $game_type_lang['table_and_cards']],];
                break;
            case T1LOTTERY_API:
                $game_types = ['lottery'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = ['lottery' => ["","type_lang" => $game_type_lang['lottery']],];
                break;
            case IBC_API:
            case SPORTSBOOK_API:
            case ONEWORKS_API:
            case T1ONEWORKS_API:
            case SBTECH_API:
            case RWB_API:
                $game_types = ['sports'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = ['sports' => ["1", 'type_lang' => $game_type_lang['sports']],];
                break;
            case HG_API:
                $game_types = ['live', 'table', 'slots'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live' => ['game_type'=>'live', 'type_lang' => $game_type_lang['live']],
                    'table' => ['game_type'=>'table', 'type_lang' => $game_type_lang['table']],
                    'slots' => ['game_type'=>'slots', 'type_lang' => $game_type_lang['slots']]
                ];
                break;
            case EXTREME_LIVE_GAMING_API:
                $game_types = ['baccarat', 'roulette', 'blackjack'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'baccarat' => ['game_type'=>'baccarat', 'type_lang' => $game_type_lang['baccarat']],
                    'roulette' => ['game_type'=>'roulette', 'type_lang' => $game_type_lang['roulette']],
                    'blackjack' => ['game_type'=>'blackjack', 'type_lang' => $game_type_lang['blackjack']],
                ];
                break;
            case TCG_API:
            case LD_LOTTERY_API:
                $game_types = ['lottery'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = ['lottery' => ["", 'type_lang' => $game_type_lang['lottery']]];
                break;
            case TGP_AG_API:
                $game_type_id = $this->CI->game_type_model->getGameTypeIdGametypeCode($game_platform_id,"live_dealer");

                if ($game_type_id) {
                    $where .= " and game_type_id != " . $game_type_id;
                }

                $games = $this->CI->game_description_model->getGameByQuery($select,$where);
                $game_types = ['live_dealer', 'slots'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ["sub_game_provider" => "lobby","game_code" => "real", 'type_lang' => $game_type_lang['live_dealer'],
                    'games_dont_have_lobby' => $games],
                ];
                break;
            case T1SUNCITY_API:
            case SUNCITY_API:

                // $game_type_id = $this->CI->game_type_model->getGameTypeIdGametypeCode($game_platform_id,"live_dealer");

                // if ($game_type_id) {
                //     $where .= " and game_type_id != " . $game_type_id;
                // }

                // $games = $this->CI->game_description_model->getGameByQuery($select,$where);
                $game_types = ['live_dealer', 'slots'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ["sub_game_provider" => "SB","game_code" => "Sunbet_Lobby", 'type_lang' => $game_type_lang['live_dealer'],
                    //'games_dont_have_lobby' => $games
                    ],
                    'slots' => ["sub_game_provider" => "SBG","game_code" => "Sunbet_Lobby", 'type_lang' => $game_type_lang['slots'],
                    //'games_dont_have_lobby' => $games
                    ]
                ];
                break;
            case MWG_API:

                $games = $this->CI->game_description_model->getGameByQuery($select,$where);
                $game = [
                    'main_lobby' => ["main_lobby" => "lobby",],
                    'games_can_launch_without_lobby' => $games
                ];
                break;
            // case MG_API:

            //     $game_type_id = $this->CI->game_type_model->getGameTypeIdGametypeCode($game_platform_id,"live_dealer");
            //     $where .= $game_platform_id . " and game_type_id != " . $game_type_id;
            //     $games = $this->CI->game_description_model->getGameByQuery($select,$where);
            //     $game = [
            //         'live_dealer' => ['game_code' => '1'],
            //         'games_dont_have_lobby' => $games
            //     ];
            //     break;
            case SBOBET_API:
            case SBOBET_SEAMLESS_GAME_API:
                $game_types = ['sports', 'live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'sports' =>  ['game_type'=>'sports', 'type_lang' => $game_type_lang['sports']],
                    'live_dealer' =>  ['game_type'=>'live_dealer', 'type_lang' => $game_type_lang['live_dealer']],
                ];
                break;
            case GAMEPLAY_API:
                $game_types = ['live_dealer','lottery_keno', 'lottery_pk10', 'lottery_ladder', 'slots'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game_type_id = $this->CI->game_type_model->getGameTypeIdGametypeCode($game_platform_id,"slots");

                if ($game_type_id) {
                    $where .= " and game_type_id = " . $game_type_id;
                }

                $games = $this->CI->game_description_model->getGameByQuery($select,$where);

                $game = [
                    'live_dealer' => ["game_code" => "table", 'type_lang' => $game_type_lang['live_dealer']],
                    'lottery_keno' => ["game_code" => "keno", 'type_lang' => isset($game_type_lang['lottery_keno']) ? $game_type_lang['lottery_keno'] : null],
                    'lottery_pk10' => ["game_code" => "pk10", 'type_lang' => isset($game_type_lang['lottery_pk10']) ? $game_type_lang['lottery_pk10'] : null],
                    'lottery_ladder' => ["game_code" => "ladder", 'type_lang' => isset($game_type_lang['lottery_ladder']) ? $game_type_lang['lottery_ladder'] : null],
                    'slots' => ['game_code' => 'slots', 'type_lang' => $game_type_lang['slots']],
                    // 'games_dont_have_lobby' => $games
                ];
                break;
            case SOLID_GAMING_THB_API:
                $game_types = ['slots'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game_type_id = $this->CI->game_type_model->getGameTypeIdGametypeCode($game_platform_id,"slots");

                $game = [
                    'slots' => ["game_code" => "slots", 'type_lang' => $game_type_lang['slots']],
                ];
                break;
            case CQ9_API:
                $game = [
                    'slots' =>  ['game_type'=>'slots']
                ];
                break;
            case OG_V2_API:
                $game_types = ['live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ['game_type' => 'live_dealer', 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
            case HOGAMING_SEAMLESS_API:
                $game_types = ['live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ['game_type' => 'live_dealer', 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
            case AVIA_ESPORT_API:
                $game_types = ['sports'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'sports' =>  ['game_type'=>'sports', 'type_lang' => $game_type_lang['sports']]
                ];
                break;
            case PINNACLE_API:
                $game_types = ['sports'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'sports' => ['type_lang' => $game_type_lang['sports']]
                ];
                break;
            case GOLDEN_RACE_GAMING_API:
                $game_types = ['sports'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'sports' => ['type_lang' => $game_type_lang['sports']]
                ];
                break;
            case RG_API:
                $game_types = ['e_sports'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'e_sports' =>  ['game_type'=>'e_sports', 'type_lang' => $game_type_lang['e_sports']]
                ];
                break;
            case NTTECH_API:
            case NTTECH_IDR_B1_API:
            case NTTECH_CNY_B1_API:
            case NTTECH_THB_B1_API:
            case NTTECH_USD_B1_API:
            case NTTECH_VND_B1_API:
            case NTTECH_MYR_B1_API:
                $game_types = ['live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ['game_type' => 'live_dealer', 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
            case LUCKY_GAME_CHESS_POKER_API:
                $game_types = ['poker'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'poker' => ['game_type' => 'poker', 'type_lang' => $game_type_lang['poker']]
                ];
                break;
            case NTTECH_V2_API:
            case NTTECH_V2_IDR_B1_API:
            case NTTECH_V2_CNY_B1_API:
            case NTTECH_V2_THB_B1_API:
            case NTTECH_V2_USD_B1_API:
            case NTTECH_V2_VND_B1_API:
            case NTTECH_V2_MYR_B1_API:
                $game_types = ['live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ['game_type' => 'live_dealer', 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
            case DG_API:
            case DG_SEAMLESS_API:
                $game_types = ['live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ['game_type' => 'live_dealer', 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
            case AFB88_API:
                $game_types = ['sports'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'sports' => ['game_type' => 'sports', 'type_lang' => $game_type_lang['sports']]
                ];
                break;
            case SA_GAMING_API:
            case T1SA_GAMING_API:
                $game_types = ["slots","mini_games","lottery","live_dealer","fishing_game"];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'slots' => ['type_lang' => $game_type_lang['slots']],
                    'mini_games' => ['type_lang' => $game_type_lang['mini_games']],
                    'lottery' => ['type_lang' => $game_type_lang['lottery']],
                    "live_dealer" => ['type_lang' => $game_type_lang['live_dealer']],
                    "fishing_game" => ['type_lang' => $game_type_lang['fishing_game']]
                ];
                break;
            case ASIASTAR_API:
                $game_types = ['table_and_cards'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = ['table_and_cards' => ["1","type_lang" => $game_type_lang['table_and_cards']],];

                break;
            case OGPLUS_API:
                $game_types = ['live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ['game_type' => 'live_dealer', 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
            case TIANHONG_MINI_GAMES_API:
                $game_types = ['mini_games'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'mini_games' => ['game_type' => 'mini_games', 'type_lang' => $game_type_lang['mini_games']]
                ];
                break;
            case RGS_API:
                $game_types = ['sports'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'sports' => ['game_type' => 'sports', 'type_lang' => $game_type_lang['sports']]
                ];
                break;
            case WM_API:
                $game_types = ['live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ['game_type' => 'live_dealer', 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
            case T1SBTECH_BTI_API:
            case SBTECH_BTI_API:
                $game_types = ['sports'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'sports' => ['game_type' => 'sports', 'type_lang' => $game_type_lang['sports']]
                ];
                break;
            case AG_SEAMLESS_GAME_API:
            case AG_SEAMLESS_THB1_API:
                $game_types = ['slots','live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'slots' => ['game_type' => 'slots', 'type_lang' => $game_type_lang['slots']],
                    'live_dealer' => ['game_type' => 'live_dealer', 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
            case VIVOGAMING_API:
                $game_types = ['live_dealer'];
                $game_type_lang = $this->prepareGameTypeTranslation($game_platform_id,$game_types);
                $game = [
                    'live_dealer' => ['game_type' => 'live_dealer', 'type_lang' => $game_type_lang['live_dealer']]
                ];
                break;
        }

        if ( ! empty($game_type_code )) {
            if (isset($game[$game_type_code])) {
                $game[$game_type_code]['game_platform_id'] = $game_platform_id;
                $data["game_launch_url"] = $this->processGameUrls($game_launch_url,$game[$game_type_code]);
            }else{
                $data = $game;
            }
        }else{
            foreach ($game as $key => $game_launch_code) {

                if (in_array($key, ['stand_alone_game_links','games_dont_have_lobby','games_can_launch_without_lobby'])) continue;

                $game_launch_code['game_platform_id'] = $game_platform_id;
                $data["game_launch_url"][$key] = $this->processGameUrls($game_launch_url,$game_launch_code,$key);
            }

            $games = null;
            if (isset($game['games_dont_have_lobby']) ) {
                $games = $game['games_dont_have_lobby'];
                $games_dont_have_lobby = true;
            }elseif (isset($game['games_can_launch_without_lobby'])) {
                $games = $game['games_can_launch_without_lobby'];
                $games_can_launch_without_lobby = true;
            }

            if ( ! empty($games)) {
                foreach ($games as $key => $game) {
                    $game['game_platform_id'] = $game_platform_id;
                    $game_list[$key] = $this->prepareFrontendGames($game);
                }
                if (isset($games_can_launch_without_lobby)) {
                    $data['games_can_launch_without_lobby'] = $game_list;
                }elseif($games_dont_have_lobby){
                    $data['games_dont_have_lobby'] = $game_list;
                }
            }
        }
    }

    // Get the game_type_lang depends on the game_type of lobbies to be shown
    private function prepareGameTypeTranslation($game_platform_id, $game_type_codes){
        $iCountedGameCodes = count($game_type_codes);
        $game_type_lang = [];
        $lang = $this->utils->getPlayerCenterLanguage();
        $game_type_data = $this->CI->game_type_model->getGameTypeListByGamePlatformId($game_platform_id);
        foreach ($game_type_data as $game_type) {
            for ($iCounter = 0; $iCounter < $iCountedGameCodes ; $iCounter++) {
                switch ($game_type['game_type_code']) {
                    case $game_type_codes[$iCounter]:
                        $aGameTypeLang = [$game_type_codes[$iCounter] => $game_type['game_type_lang']];
                        $game_type_lang = array_merge($game_type_lang, $aGameTypeLang);
                        break;
                    default:
                        break;
                }
            }
        }
        return $game_type_lang;
    }

    private function trimString($string){
        return rtrim(preg_replace("/\(.+\)/", "", $string));
    }

     private function trimFlashOnly($string){
        return rtrim(str_replace(" (Flash)","",$string));;
    }

    private function processGameName(&$game_name){
        if(!empty($game_name)){
            foreach ($game_name as $key => $name) {
                $game_name[$key] = $this->trimString($name);
            }
        }
    }

    private function prepareFrontendGames($game){
        $game_api_details = $this->getGameProviderDetails();
        $game_name = json_decode(str_replace("_json:", "", $game['game_name']),true);
        $game_name_json = $game['game_name'];
        $game_type_code = json_decode(json_encode($this->CI->game_type_model->getGameTypeById($game['game_type_id'])),true)['game_type_code'];
        $game_launch_url = $game_api_details['available_game_providers'][$game['game_platform_id']]['game_launch_url'];

        if ($this->utils->getConfig('trim_game_name_on_gamelist_api')) {
            $this->processGameName($game_name);
        }
         if ($this->utils->getConfig('trim_game_name_flash_only')) {
            $game_name_json = $this->trimFlashOnly($game['game_name']);
        }
        $attributes = null;
        if (isset($game['attributes']))
            $attributes = $game['game_launch_code_other_settings'] = $game['attributes'];

        $game_list = [
            'game_type_code'    => $game_type_code,
            'game_name'         => $game_name_json,
            'game_name_en'      => $game_name[LANGUAGE_FUNCTION::INT_LANG_ENGLISH],
            'game_name_cn'      => $game_name[LANGUAGE_FUNCTION::INT_LANG_CHINESE],
            'game_name_indo'    => @$game_name[LANGUAGE_FUNCTION::INT_LANG_INDONESIAN],
            'game_name_vn'      => @$game_name[LANGUAGE_FUNCTION::INT_LANG_VIETNAMESE],
            'game_name_kr'      => @$game_name[LANGUAGE_FUNCTION::INT_LANG_KOREAN],
            'provider_name'     => $game_api_details['available_game_providers'][$game['game_platform_id']]['complete_name'],
            'provider_code'     => $game_api_details['available_game_providers'][$game['game_platform_id']]['game_provider_code'],
            // 'game_id_desktop'   => $game['game_code'],
            // 'game_id_mobile'    => $game['game_code'],
            'in_flash'          => $game['flash_enabled'],
            'in_html5'          => $game['html_five_enabled'],
            'in_mobile'         => $game['mobile_enabled'],
            // 'downloadable'      => $game['dlc_enabled'],
            'available_on_android'       => $game['enabled_on_android'],
            'available_on_ios'           => $game['enabled_on_ios'],
            'note'              => $game['note'],
            'status'            => $game['status'],
            'top_game_order'    => $game['game_order'],
            'enabled_freespin'  => $game['enabled_freespin'],
            'sub_game_provider' => $game['sub_game_provider'],
            'flag_new_game'     => $game['flag_new_game'],
            // 'flag_show_in_site' => $game['flag_show_in_site'],
            'progressive'       => $game['progressive'],
            'game_launch_url'   => $this->processGameUrls($game_launch_url,$game,$game_type_code,$attributes),
            'game_launch_code_other_settings'   => $attributes,
            'image_path' => $this->processGameImagePath($game)
        ];
        // echo "<pre>";print_r($game);exit;
        $game_code = $game['game_code'];
        $attributes = json_decode($attributes,true);
        if (isset($attributes['game_launch_code']))
            $game_code = $attributes['game_launch_code'];

        if ($game['mobile_enabled'])
            $game_list['game_id_mobile'] = $game_code;

        if ($game['flash_enabled'] || !$game['mobile_enabled'])
            $game_list['game_id_desktop'] = $game_code;

        if ($game['html_five_enabled'] || !$game['mobile_enabled']) {
            // $game_list['game_id_mobile'] = $game_code;
            $game_list['game_id_desktop'] = $game_code;
        }

        $game_list['game_unique_id'] = $game['external_game_id'];
        $game_list['release_date'] = isset($game['created_on']) ? $game['created_on'] : "N/A";

        return $game_list;
    }

    private function processGameImagePath($game){
        #sample path
        #cn path http://www.gamegateway.t1t.games/includes/images/cn/microgaming/Galacticons.png
        #en path http://www.gamegateway.t1t.games/includes/images/microgaming/Galacticons.png
        $url =  $this->utils->getConfig('game_list_image_path_url');
        $extension = ".jpg";
        $game_code = $game['game_code'];
        $file = $game_code.$extension;
        switch ($game['game_platform_id']) {
            case PT_API:
            case PT_V2_API:
            case T1PT_API:
                $dir ="playtech";
                break;
            case UC_API:
            case T1UC_API:
                $dir ="uc8";
                break;
            case TTG_API:
            case T1TTG_API:
                $dir ="ttg";
                break;
            case TGP_AG_API:
                $dir = "asia_gaming";
                break;
            case SUNCITY_API:
            case T1SUNCITY_API:
                $dir ="sunbet";
                break;
            case SPADE_GAMING_API:
            case T1SPADE_GAMING_API:
                $dir ="spadegaming";
                break;
            case SA_GAMING_API:
            case T1SA_GAMING_API:
                $dir ="sa";
                break;
            case RTG_API:
            case T1RTG_MASTER_API:
                $dir ="rtg";
                break;
            case QT_API:
            case T1QT_API:
                $dir ="qt";
                break;
            case PRAGMATICPLAY_API:
            case T1PRAGMATICPLAY_API:
            case PRAGMATICPLAY_SEAMLESS_THB1_API:
                $dir ="pragmatic";
                break;
            case PNG_API:
            case T1PNG_API:
                $game_code = json_decode($game['attributes'], TRUE)['game_launch_code'];
                $file = $game_code.$extension;
                $dir ="png";
                break;
            case PGSOFT_API:
                $game_launch_code = json_decode($game["attributes"],true)["game_launch_code"] ?: $game["game_code"];
                $file = $game_launch_code.$extension;
                $dir ="pg";
                break;
            case OPUS_API:
                $dir ="opus";
                break;
            case MG_API:
            case T1MG_API:
            case MG_DASHUR_API:
            case T1MGPLUS_API:
            case MGPLUS_API:
                $dir ="microgaming";
                break;
            case JUMB_GAMING_API:
            case T1JUMB_API:
                $dir ="jumbo";
                break;
            case ISB_API:
            case T1ISB_API:
                $dir ="isoftbet";
                break;
            case HABANERO_SEAMLESS_GAMING_API:
            case HB_API:
            case T1HB_API:
                $dir ="habanero";
                break;
            case ICONIC_SEAMLESS_API:
                $dir ="iconic";
                break;
            case GD_API:
            case T1GD_API:
            case GD_SEAMLESS_API:
                $dir ="gd";
                break;
            case GAMEPLAY_API:
                $dir ="gameplay";
                break;
            case DT_API:
            case T1DT_API:
                $dir ="dt";
                break;
            case CQ9_API:
            case T1CQ9_API:
                $dir ="cq9";
                break;
            case BETSOFT_API:
                $dir ="betsoft";
                break;
            case GOLDENF_PGSOFT_API:
                $dir ="goldenf_pgsoft";
                break;
            case BOOMING_SEAMLESS_API:
                $game_launch_code = $game["external_game_id"];
                $file = $game_launch_code.$extension;
                $dir ="booming";
                break;
            case AVIA_ESPORT_API:
                $dir ="avia_esport";
                break;
            case OG_V2_API:
                $dir ="og_v2";
                break;
            case AE_SLOTS_GAMING_API:
                $dir ="ae_slots_gaming";
                break;
            case FLOW_GAMING_SEAMLESS_API:
            case FLOW_GAMING_SEAMLESS_THB1_API:
                $dir ="flow_gaming";
                break;
            case REDRAKE_GAMING_API:
                $dir = "redrake_gaming";
                break;
            case TPG_API:
                $dir ="tpg";
                break;
            case ASIASTAR_API:
                $dir ="asiastar";
                break;
            case SOLID_GAMING_THB_API:
                $dir ="solid_gaming";
                break;
            case LAPIS_API:
                $dir ="microgaminglapis";
                break;
            case OGPLUS_API:
                $dir ="ogplus";
                break;
            case HUB88_API:
                $dir ="hub88";
                break;                
            case GENESIS_SEAMLESS_API:
            case GENESIS_SEAMLESS_THB1_API:
                $dir ="genesis";
                break;     
            case GPK_API:
                $dir ="gpk";
                break;           
            case JOKER_API:
                $dir ="joker";
                break;
	    case RGS_API:
                $dir = "rgs";
                break;
            case WM_API:
                $dir = "wm";
                break;
            case TIANHAO_API:
                $dir = "tianhao";
                break;                        
            default:
                $dir  = $game['game_platform_id'];
                break;
        }
        return array(
            "en" => "{$url}/includes/images/{$dir}/{$file}",
            "cn" => "{$url}/includes/images/cn/{$dir}/{$file}"
        );
    }

    private function processGameUrls($game_launch_url, $game, $game_type_code = null, $attributes = null){
        $lang = $this->utils->getPlayerCenterLanguage();
        $providers_have_lobby = $this->utils->getConfig('allow_lobby_in_provider');

        switch ($lang) {
            case self::CHINESE_LANG_CODE:
                $lang = "zh-cn";
                break;
            default:
                $lang = "en-us";
                break;
        }

        switch ($game['game_platform_id']) {
            case PT_API:
                if ($game_type_code == self::TAG_CODE_LIVE_DEALER)
                    $game_launch_url_arr['remarks'] = "Demo/Trial is not avaialable";
                if($game['html_five_enabled'] || $game['flash_enabled']){
                    $game_launch_url_arr['web'] = $game_launch_url . "/default/" . $game['external_game_id'];
                }

                if($game['mobile_enabled']){
                    $game_launch_url_arr['mobile']= $game_launch_url . "/default/" . $game['external_game_id'];
                }
                $game_launch_url_arr['sample'] = $game_launch_url . "/<siteName>/<game_launch_code>/<mode>/<is_mobile>";
                break;
            case PT_V2_API:
                if ($game_type_code == self::TAG_CODE_LIVE_DEALER)
                    $game_launch_url_arr['remarks'] = "Demo/Trial is not avaialable";
                if($game['html_five_enabled'] || $game['flash_enabled']){
                    $game_launch_url_arr['web'] = $game_launch_url . "/default/" . $game['external_game_id'];
                }

                if($game['mobile_enabled']){
                    $game_launch_url_arr['mobile']= $game_launch_url . "/default/" . $game['external_game_id'];
                }
                $game_launch_url_arr['sample'] = $game_launch_url . "/<siteName>/<game_launch_code>/<mode>/<is_mobile>";
                break;
            case MG_API:
                $type = ($game_type_code == self::TAG_CODE_LIVE_DEALER) ? 1:2;
                $game_code = $game['game_code'];
                if(($game_type_code == self::TAG_CODE_LIVE_DEALER)){
                    $game_launch_url_arr['web']['real'] = $game_launch_url . "/".$type."/" . "_mglivecasino/false/real/". $lang;
                    $game_launch_url_arr['web']['demo'] = "N/A";
                } else {
                    if($game['mobile_enabled']){
                        $game_launch_url_arr['mobile'] = $game_launch_url . "/".$type."/" . $game_code . "/true/real/". $lang;
                    } else {
                        $game['flash_enabled'] = true;
                        $game_launch_url_arr['web'] = $game_launch_url . "/".$type."/" . $game_code . "/false/real/". $lang;
                    }
                }
                $game_launch_url_arr['sample'] = $game_launch_url . "/<type>/<gamecode>/<lunchmobileorweb>/<mode><language>";
                break;
            case T1MGPLUS_API:
                $game_launch_url_arr['mobile'] = $game_launch_url ."/" . $game['game_platform_id'] ."/" . $game['game_code'] . "/real";
                $game_launch_url_arr['web'] = $game_launch_url ."/" . $game['game_platform_id'] ."/" . $game['game_code'] . "/real";
                break;
            case MGPLUS_API:
                $game_launch_url_arr['mobile'] = $game_launch_url ."/" . $game['game_platform_id'] ."/" . $game['game_code'] . "/real";
                $game_launch_url_arr['web'] = $game_launch_url ."/" . $game['game_platform_id'] ."/" . $game['game_code'] . "/real";
                break;
                // case NT_API:
                // $game_launch_url_arr['web'] = $game_launch_url . "/default/" . $game['external_game_id'];
                // break;
            case OPUS_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/default/" . $game['external_game_id'];
                break;
             case CQ9_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'];
                $game_launch_url_arr['mobile']  = $game_launch_url . "/" . $game['game_code'];
                $game_launch_url_arr['trial']  = $game_launch_url . "/" . $game['game_code'] . "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_launch_code>/<mode>";
                break;
            case EZUGI_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['live_dealer'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['live_dealer'] . "/true";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<provider>/mobile>";
                break;
            case ENTWINE_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . ENTWINE_API;
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . ENTWINE_API . "/true/true";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<extra>/<mobile>";
                break;
            case DG_API:
            case DG_SEAMLESS_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
            case LD_LOTTERY_API:
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['trial'] = $game_launch_url . "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<mode>";
                break;
            case OPUS_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/true/20";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/true/20";
                break;
            case ONESGAME_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" .  $game['game_code'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<game_type>/<language>";
                break;
            case GD_API:
            case GENESISM4_GAME_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" .  $game['game_code'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<game_type>/<game_mode>";
                break;
            case WFT_API:
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['sample'] = $game_launch_url . "/<languge>/<mobile>";
            case IPM_V2_SPORTS_API:
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['sample'] = $game_launch_url . "/<languge>";
            case IMPT_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/default/" . $game['game_code'] . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/default/" . $game['game_code'] . "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/default/" . $game['game_code']. "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<siteName>/<game_code>/<mode>";
                break;
            case TTG_API:
                $game_id = json_decode($attributes,true)['gameId'];
                $code = json_decode($game['game_launch_code_other_settings'],true);
                $game_launch_code = !empty($code['game_launch_code']) ? $code['game_launch_code'] : $game['game_code'];
                $game_type = isset($attributes['gameType']) ? json_decode($attributes,true)['gameType'] : 0;

                $game_launch_url_arr['web'] = $game_launch_url . "/".$game_id."/" . $game_launch_code ."/". $game_type . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/".$game_id."/" . $game_launch_code ."/". $game_type . "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/".$game_id."/" . $game_launch_code ."/". $game_type . "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_id>/<game_launch_code>/<game_type>/<mode>";
                break;
            case ONESGAME_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" .  $game['game_code'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<game_type>/<language>";
                break;
            case DT_API:
            case GAMESOS_API:
            case LAPIS_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'] . "/real/web";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'] . "/real/mobile";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<mode>/<web_platform>";
                break;
            case ISB_API:
                $code = json_decode($game['game_launch_code_other_settings'],true);
                $game_launch_code = !empty($code) ?$code['game_launch_code']:$game['game_code'];
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game_launch_code . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game_launch_code . "/real";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<mode>/";
                break;
            case FISHINGGAME_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'] . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'] . "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_code'] . "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<game_code>/";
                break;
            case HRCC_API:
                $game_launch_url_arr['web'] = "unknown";
                $game_launch_url_arr['mobile'] = "unknown";
                $game_launch_url_arr['sample'] = "unknown";
                break;
            case OPUS_SPORTSBOOK_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/OPUS_SPORTSBOOK_API";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/OPUS_SPORTSBOOK_API";
                break;
            case OPUS_KENO_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/keno";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/keno";
                break;
            case KUMA_API:
            case EBET_DT_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'] . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'] . "/real/true";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<mode>/<language>";
                break;
            case EBET_IMPT_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code']. "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code']. "/real";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<mode>/<type>";
                break;
            case EVOLUTION_GAMING_API:
                $game_launch_url_arr['web'] = $game_launch_url ."/". json_decode($game['game_launch_code_other_settings'],true)["game_type"] . "/" .$game['game_code'];
                $game_launch_url_arr['mobile'] = $game_launch_url ."/". json_decode($game['game_launch_code_other_settings'],true)["game_type"] . "/" .$game['game_code'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_type>/<game_code>/";
                break;
            case EVOLUTION_SEAMLESS_THB1_API:
                $game_launch_url_arr['web'] = $game_launch_url."/<game_code>"."/<mode>"."/<game_type>"."/<language>";
                $game_launch_url_arr['sample_web'] = $game_launch_url."/".$game['game_code']."/real"."/".json_decode($game['game_launch_code_other_settings'],true)["game_type"]."/null";
                $game_launch_url_arr['mobile'] = $game_launch_url."/<game_code>"."/<game_type>"."/<language>";
                $game_launch_url_arr['sample_mobile'] = $game_launch_url."/".$game['game_code']."/real"."/".json_decode($game['game_launch_code_other_settings'],true)["game_type"]."/null";
                break;
            case KING_MAKER_GAMING_API:
                $game_launch_url_arr['web'] = $game_launch_url."/<game_code>"."/<language>"."/<is_mobile>"."/<is_redirect>"."/<game_type>";
                $game_launch_url_arr['sample_web'] = $game_launch_url."/".$game["game_code"];
                $game_launch_url_arr['mobile'] = $game_launch_url."/<game_code>"."/<language>"."/<is_mobile>"."/<is_redirect>"."/<game_type>";
                $game_launch_url_arr['sample_mobile'] = $game_launch_url."/".$game["game_code"]."/en"."/true"."/true";
                break;
            case KING_MAKER_GAMING_THB_B1_API:
                $game_launch_url_arr['web'] = $game_launch_url."/<game_code>"."/<language>"."/<is_mobile>"."/<is_redirect>"."/<game_type>";
                $game_launch_url_arr['sample_web'] = $game_launch_url."/".$game["game_code"];
                $game_launch_url_arr['mobile'] = $game_launch_url."/<game_code>"."/<language>"."/<is_mobile>"."/<is_redirect>"."/<game_type>";
                $game_launch_url_arr['sample_mobile'] = $game_launch_url."/".$game["game_code"]."/en"."/true"."/true";
                break;
            case MG_QUICKFIRE_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code']. "/real/desktop";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code']. "/real/mobile";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<mode>/<device_type>/<language>";
                break;
            case EBET_QT_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code']. "/desktop/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code']. "/mobile/real";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<device_type>/<mode>/<language>";
                break;
            case ULTRAPLAY_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/desktop/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/mobile/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/mobile/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<device_type>/<mode>/<language>";
                break;
            case JUMB_GAMING_API:
                if (in_array($game['game_code'], [7001,7002,7003,7004])) {
                    $game_type_code = "fishing";
                }else{
                    if ($game_type_code == "card_games") {
                        $game_type_code = "table_and_cards";
                    } elseif ($game_type_code == "lottery") { //***
                        $game_type_code = "lottery";
                    } elseif ($game_type_code == "arcade") {
                        $game_type_code = "arcade";
                    } elseif ($game_type_code == "slots") {
                        $game_type_code = "slots";
                    }
                }

                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game_type_code . "/" . $game['game_code']. "/real/desktop";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game_type_code . "/" . $game['game_code']. "/real/desktop";
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game_type_code . "/" . $game['game_code']. "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_type>/<game_code>/<mode>/";
                break;
            case EBET_MG_API:
                if ($game_type_code == "live_dealer") {
                    $game_launch_url_arr['web'] = $game_launch_url . "/_mglivecasino/" . $game['game_code'] . "/real/flash";
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/_mglivecasino/" . $game['game_code'] . "/real/html5";
                }else{
                    $game_launch_url_arr['web'] = $game_launch_url . "/_null/" . $game['game_code'] . "/real/flash";
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/_null/" . $game['game_code'] . "/real/html5";
                }

                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_type>/<game_code>/<game_mode>/<category>";
                break;
            case QT_API:
            case FG_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/".$game['game_platform_id']."/" . $game['game_code'] . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/".$game['game_platform_id']."/" . $game['game_code'] . "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/".$game['game_platform_id']."/" . $game['game_code'] . "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_paltform_id>/<game_code>/<mode>";
                break;
            case T1ONEWORKS_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url . "/{$game['game_platform_id']}/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/{$game['game_platform_id']}/real";
                break;
            case ONEWORKS_API:
            case IBC_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url . "/1/";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/1/true";
                break;
            case BBIN_API:
            case GSBBIN_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['web'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['mobile'];
                $game_launch_url_arr['trial'] = $game_launch_url . "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_type_code>/<language>/<mobile>/<mode>";
                break;
            case MTECH_BBIN_API:
                if ($game_type_code == "live_dealer") {
                    $game_type_code = "live";
                } elseif ($game_type_code == "slots") {
                    $game_type_code = "game";
                } elseif ($game_type_code == "lottery") {
                    $game_type_code = "lottery";
                } elseif ($game_type_code == "sports") {
                    $game_type_code = "ball";
                }elseif ($game_type_code == "fishing_game") {
                    $game_type_code = "fisharea";
                }
                $game_launch_url_arr['web'] = $game_launch_url. "/" .$game_type_code;
                $game_launch_url_arr['mobile'] = $game_launch_url. "/" .$game_type_code;
                break;
            case GAMEPLAY_SBTECH_API:
            case SBTECH_API:
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_type";
                break;
            case AGIN_API:
            case AGBBIN_API:
            case AGSHABA_API:
                if(array_key_exists('type_lang', $game)) {
                    $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                }
                $game_launch_url_arr['web'] = $game_launch_url . "/default/" . $game['web'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/default/" . $game['mobile'] . "/true";
                $game_launch_url_arr['trial'] = $game_launch_url . "/default/" . $game['mobile'] . "/true/trial";
                $game_launch_url_arr['fun'] = $game_launch_url . "/default/" . $game['mobile'] . "/true/fun";
                break;
            case HABANERO_SEAMLESS_GAMING_API:
                $game_launch_url_arr['web'] = $game_launch_url . '/' . $game['game_code'] . "/real/";
                $game_launch_url_arr['mobile'] = $game_launch_url . '/' . $game['game_code'] . "/real/"; 
                $game_launch_url_arr['trial'] = $game_launch_url . '/' . $game['game_code'] . "/fun/";                
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<mode>";
                break;
            case ICONIC_SEAMLESS_API:
                $game_launch_url_arr['web'] = $game_launch_url . '/' . $game['game_code'] . "/real/";
                $game_launch_url_arr['mobile'] = $game_launch_url . '/' . $game['game_code'] . "/real/"; 
                $game_launch_url_arr['trial'] = $game_launch_url . '/' . $game['game_code'] . "/fun/";                
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<mode>";
                break;
            case HB_API:
            case IMSLOTS_API:
            case UC_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/real/" . $game['game_code'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/real/" . $game['game_code'];
                $game_launch_url_arr['trial'] = $game_launch_url . "/trial/" . $game['game_code'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<mode>/<game_code>";
                break;
            case SA_GAMING_API:
                if(! in_array(SA_GAMING_API,$providers_have_lobby)){
                    $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'];
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'];
                    $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>";
                    break;
                }else{
                    $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                    $game_launch_url_arr['web'] = $game_launch_url;
                    $game_launch_url_arr['sample'] = $game_launch_url;
                    $game_launch_url_arr['mobile'] = $game_launch_url;
                    break;
                }
            case YOPLAY_API:
            case T1YOPLAY_API:
            case EBET_KUMA_API:
            case EBET_GGFISHING_API:
            case KYCARD_API:
                if(! in_array(KYCARD_API,$providers_have_lobby)){
                    $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'];
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'];
                    $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>";
                    break;
                }else{
                    $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                    $game_launch_url_arr['web'] = $game_launch_url;
                    $game_launch_url_arr['sample'] = $game_launch_url;
                    $game_launch_url_arr['mobile'] = $game_launch_url;
                    break;
                }
            case LE_GAMING_API:
                if(! in_array(LE_GAMING_API,$providers_have_lobby)){
                    $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code']. "/real";
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code']. "/real";
                    $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_code']. "/trial";
                    $game_launch_url_arr['sample'] = $game_launch_url . "/<game_launch_code>/<mode>";
                    break;
                } else {
                    $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                    $game_launch_url_arr['web'] = $game_launch_url;
                    $game_launch_url_arr['sample'] = $game_launch_url;
                    $game_launch_url_arr['mobile'] = $game_launch_url;
                    break;
                }
            case SPORTSBOOK_API:
            case KENOGAME_API:
            case BETEAST_API:
            case IDN_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url . "/true";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<mobile>";
                break;
            case AB_API:
            case OG_API:
            case VR_API:
            case LB_API:
            case PINNACLE_API:
            case XHTDLOTTERY_API:
            case EBET_OPUS_API:
            case EBET_BBIN_API:
            case RWB_API:
            case TCG_API:
                $game_launch_url_arr['game_type_lang'] =  isset($game['type_lang']) ? $game['type_lang'] : "";
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                break;
            case EBET_API:
                $game_launch_url_arr['game_type_lang'] =  isset($game['type_lang']) ? $game['type_lang'] : "";
                if ($game_type_code == "slots") {
                    $game_launch_url_arr['web'] = $game_launch_url . "/real/false/5";
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/real/true/5";
                    $game_launch_url_arr['trial'] = $game_launch_url . "/real/false/6";
                } else {
                    $game_launch_url_arr['web'] = $game_launch_url . "/real/false/1";
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/real/true/1";
                }
                break;
            case SPADE_GAMING_API:
            case PRAGMATICPLAY_API:
            case PNG_API:
                $code = isset($game['game_launch_code_other_settings']) ? json_decode($game['game_launch_code_other_settings'],true) : "";
                $game_launch_code = !empty($code) ?$code['game_launch_code']:$game['game_code'];
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game_launch_code. "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game_launch_code. "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game_launch_code. "/fun";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_launch_code>/<mode>";
                break;
            case PRAGMATICPLAY_SEAMLESS_THB1_API:
                $code = isset($game['game_launch_code_other_settings']) ? json_decode($game['game_launch_code_other_settings'],true) : "";
                $game_launch_code = !empty($code) ?$code['game_launch_code']:$game['game_code'];
                $game_launch_url_arr['web'] = $game_launch_url . $game['game_platform_id'] . "/" . $game_launch_code. "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . $game['game_platform_id'] . "/" . $game_launch_code. "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . $game['game_platform_id'] . "/" . $game_launch_code. "/fun";
                $game_launch_url_arr['sample'] = $game_launch_url . $game['game_platform_id'] . "/<game_launch_code>/<mode>";
                break;
            case EBET_SPADE_GAMING_API:
            case EBET_BBTECH_API:
            case YUNGU_GAME_API:
            case LEBO_GAME_API:
            case ISB_SEAMLESS_API:
            case GOLDENF_PGSOFT_API:
            case PGSOFT_API:
                $code = json_decode($game['game_launch_code_other_settings'],true);
                $game_launch_code = !empty($code) ?$code['game_launch_code']:$game['game_code'];
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game_launch_code. "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game_launch_code. "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game_launch_code. "/fun";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_launch_code>/<mode>";
                break;
            case HG_API:
            case EBET_AG_API:
            case IG_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url . "/" .$game['game_type'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_type'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_type>";
                break;
            case EXTREME_LIVE_GAMING_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" .$game['game_type'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_type'] . "/true";
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_type'] . "/false/true";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_type>/<mobile>/<mode>";
                break;
            case TGP_AG_API:
                $code = json_decode(isset($game['game_launch_code_other_settings']),true);
                $game_launch_code = !empty($code) ? $code['game_launch_code']: $game['game_code'];
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url ;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['sample'] = $game_launch_url;
                break;
            case T1SUNCITY_API:
            case SUNCITY_API:

                $code = json_decode(isset($game['game_launch_code_other_settings']) ? 
                $game['game_launch_code_other_settings'] : '',true);
                $game_launch_code = !empty($code) ? $code['game_launch_code']: $game['game_code'];

                if(!in_array(SUNCITY_API,$providers_have_lobby)){
                    $game_launch_url_arr['web'] = $game_launch_url . "/" .$game['sub_game_provider'] . "/" . $game_launch_code;
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/" .$game['sub_game_provider'] . "/" . $game_launch_code;
                    $game_launch_url_arr['trial'] = $game_launch_url . "/" .$game['sub_game_provider'] . "/" . $game_launch_code . "/trial";
                    $game_launch_url_arr['sample'] = $game_launch_url . "/<game_provider_code>/<game_code>/<mode>";
                    break;

                }else{
                    $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                    $game_launch_url_arr['web'] = $game_launch_url . "/{$game['sub_game_provider']}/{$game['game_code']}";
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/{$game['sub_game_provider']}/{$game['game_code']}";
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/{$game['sub_game_provider']}/{$game['game_code']}/trial";
                    $game_launch_url_arr['sample'] = $game_launch_url . "/<game_provider_code>/<game_code>/<mode>";
                    break;
                }
            case MWG_API:
                $game_code = isset($game['game_code']) ? $game['game_code']: $game['main_lobby'];
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game_code;
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game_code;
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>";
                break;
            case RTG_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['sub_game_provider'] . "/" . $game['game_code'] . "/real" ;
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['sub_game_provider'] . "/" . $game['game_code'] . "/real" ;
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['sub_game_provider'] . "/" . $game['game_code'] . "/trial" ;
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_id>/<mach_id>/<mode>";
                break;
            case T1MG_API:
                $type = ($game_type_code == self::TAG_CODE_LIVE_DEALER) ? 1:2;
                $game_code = $game['game_code'];
                if(($game_type_code == self::TAG_CODE_LIVE_DEALER)){
                    $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'] . "/". $game_code;
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'] . "/". $game_code;

                } else {
                    if($game['mobile_enabled']){
                        $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'] . "/". $game_code;
                    } else {
                        $game['flash_enabled'] = true;
                        $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'] . "/". $game_code;
                    }
                }
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<game_code>";
                break;
            case T1PT_API:
                if ($game_type_code == self::TAG_CODE_LIVE_DEALER) {
                    $game_launch_url_arr['remarks'] = "Demo/Trial is not avaialable";
                }

                $game_launch_url_arr['web']= $game_launch_url . "/" . $game['game_platform_id'] . "/" .$game['external_game_id'] . '/real';
                $game_launch_url_arr['mobile']= $game_launch_url . "/" . $game['game_platform_id'] . "/" .$game['external_game_id'] . '/real';
                $game_launch_url_arr['trial']= $game_launch_url . "/" . $game['game_platform_id'] . "/" .$game['external_game_id'] . '/fun';
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<external_game_id>/<mode>";
                break;

            case T1YOPLAY_API:
            case T1AB_API:
            // case T1MG_API:
            case T1DG_API:
            case T1PNG_API:
                $game_launch_code = !empty($game['game_launch_code_other_settings']['game_launch_code'])?$game['game_launch_code_other_settings']['game_launch_code']:$game['game_code'];
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game_launch_code. "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game_launch_code. "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game_launch_code. "/fun";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_launch_code>/<mode>";
                break;

            case T1PRAGMATICPLAY_API:
            case T1TTG_API:
            case T1SPADE_GAMING_API:
            case T1HB_API:
            case T1EZUGI_API:
            case T1JUMB_API:
            case T1VR_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'] . "/";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'] . "/";
                break;
            case T1EBET_API:
                $game_launch_url_arr['game_type_lang'] =  isset($game['type_lang']) ? $game['type_lang'] : "";
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                if ($game_type_code == "slots") {
                    $game_launch_url_arr['web'] = $game_launch_url . "/real/false/6";
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/real/true/6";
                }
                break;
            case T1ISB_API:
            case T1LOTTERY_EXT_API:
            case T1UC_API:
            case T1DT_API:
            case T1IDN_API:
            case T1GD_API:
            case T1QT_API:
            case T1GGPOKER_GAME_API:
            case T1FG_ENTAPLAY_API:
            case T1ONEWORKS_API:
            case T1GG_API:
            case T1DG_API:
            case FG_ENTAPLAY_API:
                $game_id = isset($game['game_launch_code_other_settings']['game_id']) ? $game['game_launch_code_other_settings']['game_id']: false;
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['game_code'] . "/real/" . $game_id . "/";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['game_code'] . "/real/" . $game_id . "/";
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['game_code'] . "/trial/" . $game_id . "/";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<game_code>/<mode>/<game_id>/<language>/<redirect>";
                break;
            case T1AGIN_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['web'] . "/real" . "/" . $game['web'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['mobile'] . "/real" . "/" . $game['mobile'];
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['web'] . "/trial" . "/" . $game['web'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<game_code>/<mode>/<game_id>/<language>/<redirect>";
                break;
            case MG_DASHUR_API:
                if ($game_type_code == "live_dealer")
                    $game_type_code = "live";

                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game_type_code. "/" . $game['game_code'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game_type_code. "/" . $game['game_code'];
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game_type_code. "/" . $game['game_code'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<game_type>/<game_code>";
                break;
            case T1OG_API:
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                break;
            case T1BBIN_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['web'] . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['mobile'] . "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['web'] . "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<game_code>/<mode>/<game_id>/<language>/<redirect>";
                break;
            case BETSOFT_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/real/" . $game['game_code'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/real/" . $game['game_code'];
                break;

            // goto_betsoft_game($game_mode = 'real', $game_id='191',  $target = "iframe")
            case GAMEPLAY_API:
                // $platform = $game_type_code == "slots" ? "rslot" : ($game_type_code == 'live_dealer' ? $game_type_code = 'table' : $game_type_code);
                $platform = $game_type_code == "slots" ? "rslot" : ($game_type_code == 'live_dealer' ? $game_type_code = 'table' :  (strpos($game_type_code, 'lottery_') !== false) ? str_replace('lottery_', '', $game_type_code) : $game_type_code);
                $game_launch_code = $game_type_code == "slots" ? "d_lobby" : "null";

                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game_type_code . "/". $game_launch_code ."/". $platform . "/0";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game_type_code . "/". $game_launch_code ."/". $platform . "/0";
                if ($game_type_code != 'table') {
                    $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game_type_code . "/". $game_launch_code ."/". $platform ."/1/null";
                }
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<game_type>/<game_code>/<platform>/<game_mode>/<is_mobile>/<game_name>";
                break;
                // goto_gpgame($game_platform_id, $game_type = null, $game_code = null, $platform = null, $game_mode = 0, $is_mobile = null, $game_name = null)

            case SBOBET_API:
            case SBOBET_SEAMLESS_GAME_API:
                $game_type_code = $game_type_code == 'sports' ? 'sportsbook' : 'casino';
                // $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url . "/". $game_type_code;
                $game_launch_url_arr['mobile'] = $game_launch_url . "/". $game_type_code;
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_type";
                break;

            case REDTIGER_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/". $game['game_platform_id']."/<game_code>/<game_mode>/<sub_game_provider>";
                $game_launch_url_arr['sample_web'] = $game_launch_url . "/". $game['game_platform_id']."/".$game['game_code']."/real"."/".$game['sub_game_provider'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/". $game['game_platform_id']."/<game_code>/<game_mode>/<sub_game_provider>/<language>/<extra>/<is_redirect>/<is_mobile>";
                $game_launch_url_arr['sample_mobile'] = $game_launch_url . "/". $game['game_platform_id']."/".$game['game_code']."/real"."/".$game['sub_game_provider']."/null/en-US/null/false/true";

                $game_launch_url_arr['demo'] = $game_launch_url . "/". $game['game_platform_id'] . "/<game_code>/demo/<sub_game_provider>/<sub_game_api>/<language>";
                $game_launch_url_arr['sample_demo'] = $game_launch_url . "/". $game['game_platform_id'] . "/" . $game['game_code'] ."/demo"."/".$game['sub_game_provider']."/null/en-US";
                break;
            case HOGAMING_API:
                $game_launch_url_arr['web'] = $game_launch_url ."/". $game['game_platform_id']."/<game_type_id>/<lang>/<table_id>/<game_unique_id>/<bet_limit>";
                $game_launch_url_arr['sample_web'] = $game_launch_url ."/". $game['game_platform_id']."/en/0000000000000019/m777FH/1";
                $game_launch_url_arr['mobile'] = $game_launch_url ."/". $game['game_platform_id']."/<game_type_id>/<lang>/<table_id>/<game_mode>/<bet_limit>/<is_mobile>/<version>";
                $game_launch_url_arr['sample_mobile'] = $game_launch_url ."/". $game['game_platform_id']."/en/0000000000000001/null/real/1/true/V3";
                break;
            case HOGAMING_SEAMLESS_API:
                $game_launch_url_arr['web'] = $game_launch_url ."/". $game['game_platform_id']."/null/real/en";
                $game_launch_url_arr['mobile'] = $game_launch_url ."/". $game['game_platform_id']."/null/real/en";
                $game_launch_url_arr['trial'] = $game_launch_url ."/". $game['game_platform_id']."/null/trial/en";
                $game_launch_url_arr['sample'] = $game_launch_url ."/<game_platform_id>/<game_type>/<game_mode>/<language>";
                break;            
            case REDTIGER_SEAMLESS_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'] . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'] . "/real";
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_code'] . "/fun";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<game_mode>";
                break;
            case VIVOGAMING_API:
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['trial'] = $game_launch_url;                
                break;

            case ISIN4D_API:
            case ISIN4D_IDR_B1_API:
            case ISIN4D_CNY_B1_API:
            case ISIN4D_THB_B1_API:
            case ISIN4D_USD_B1_API:
            case ISIN4D_VND_B1_API:
            case ISIN4D_MYR_B1_API:
            case QQKENO_QQLOTTERY_API:
            case QQKENO_QQLOTTERY_CNY_B1_API:
            case QQKENO_QQLOTTERY_THB_B1_API:
            case QQKENO_QQLOTTERY_USD_B1_API:
            case QQKENO_QQLOTTERY_VND_B1_API:
            case QQKENO_QQLOTTERY_MYR_B1_API:
                $game_launch_url_arr['web'] =  $game_launch_url;
                break;
            case SOLID_GAMING_THB_API:
                $game_launch_url_arr['web'] =  $game_launch_url . $game['game_platform_id'] . '/' . $game['game_code'];
                $game_launch_url_arr['mobile'] =  $game_launch_url . $game['game_platform_id'] . '/' . $game['game_code'];
                $game_launch_url_arr['sample'] =  $game_launch_url . $game['game_platform_id'] . '/' . $game['game_code'] . '/DEMO';
                break;

            case NTTECH_API:
            case NTTECH_IDR_B1_API:
            case NTTECH_CNY_B1_API:
            case NTTECH_THB_B1_API:
            case NTTECH_USD_B1_API:
            case NTTECH_VND_B1_API:
            case NTTECH_MYR_B1_API:
                $game_language = $game['game_platform_id'] == NTTECH_IDR_B1_API ? "id" : 
                                    ($game['game_platform_id'] == NTTECH_CNY_B1_API ? "cn" : 
                                        ($game['game_platform_id'] == NTTECH_THB_B1_API ? "th" : 
                                            ($game['game_platform_id'] == NTTECH_VND_B1_API ? "vn" : "en")));
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] =  $game_launch_url."/" . $game_language;
                $game_launch_url_arr['mobile'] =  $game_launch_url."/" . $game_language . "/true";
                $game_launch_url_arr['sample'] = $game_launch_url."/<language>/<is_mobile>";
                break;
                
            case NTTECH_V2_API:
            case NTTECH_V2_IDR_B1_API:
            case NTTECH_V2_CNY_B1_API:
            case NTTECH_V2_THB_B1_API:
            case NTTECH_V2_USD_B1_API:
            case NTTECH_V2_VND_B1_API:
            case NTTECH_V2_MYR_B1_API:
                $game_language = $game['game_platform_id'] == NTTECH_V2_IDR_B1_API ? "id" : 
                                    ($game['game_platform_id'] == NTTECH_V2_CNY_B1_API ? "cn" : 
                                        ($game['game_platform_id'] == NTTECH_V2_THB_B1_API ? "th" : 
                                            ($game['game_platform_id'] == NTTECH_V2_VND_B1_API ? "vn" : "en")));
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] =  $game_launch_url."/". "LIVE/" . $game_language;
                $game_launch_url_arr['mobile'] =  $game_launch_url."/". "LIVE/" . $game_language . "/false" . "/true";
                $game_launch_url_arr['sample'] = $game_launch_url."/<gameType>/<language>/<is_mobile>/<is_redirect>";
                break;

            case ONEBOOK_API:
            case ONEBOOK_IDR_B1_API:
            case ONEBOOK_CNY_B1_API:
            case ONEBOOK_THB_B1_API:
            case ONEBOOK_USD_B1_API:
            case ONEBOOK_VND_B1_API:
            case ONEBOOK_MYR_B1_API:
                $game_launch_url_arr['web'] =  $game_launch_url."/<language>/<is_mobile>/<skin_id>/<oddstype>";
                $game_launch_url_arr['sample_web'] = $game_launch_url."/en/false/bl001/5";
                $game_launch_url_arr['mobile'] =  $game_launch_url."/<is_mobile>/<skin_id>/<oddstype>";
                $game_launch_url_arr['sample_mobile'] = $game_launch_url."/en/true/bl001/5";
                break;

            case SBOBETGAME_API:
            case SBOBETGAME_IDR_B1_API:
            case SBOBETGAME_CNY_B1_API:
            case SBOBETGAME_THB_B1_API:
            case SBOBETGAME_USD_B1_API:
            case SBOBETGAME_VND_B1_API:
            case SBOBETGAME_MYR_B1_API:
            case SBOBETV2_GAME_API:
                $game_launch_url_arr['web'] =  $game_launch_url."/<language>/<is_mobile>/<theme>/<oddstyle>";
                $game_launch_url_arr['sample_web'] = $game_launch_url."/en/false/blue/EU";
                $game_launch_url_arr['mobile'] =  $game_launch_url."<language>/<is_mobile>/<theme>/<oddstyle>/<is_redirect>";
                $game_launch_url_arr['sample_mobile'] = $game_launch_url."/en/true/blue/EU/true";
                break;

            case AE_SLOTS_GAMING_API:
                $game_launch_url_arr['web'] = $game_launch_url."/".$game['game_code'];
                $game_launch_url_arr['sample_web'] = $game_launch_url."/<game_code>/<game_mode>/<game_type>/<language>";
                $game_launch_url_arr['mobile'] = $game_launch_url."/".$game['game_code'];
                $game_launch_url_arr['sample_mobile'] = $game_launch_url."/<game_code>/<game_mode>/<game_type>/<language>";
                $game_launch_url_arr['trial'] = $game_launch_url."/".$game['game_code']."/request_demo_play";
                break;
            case REDRAKE_GAMING_API:
                $game_launch_url_arr['web'] = $game_launch_url."/<game_code>"."/<game_mode>"."/<game_type>"."/<language>";
                $game_launch_url_arr['sample_web'] = $game_launch_url."/".$game['game_code']."/real/null/en";
                $game_launch_url_arr['mobile'] = $game_launch_url."/<game_code>"."/<game_mode>"."/<game_type>"."/<language>";
                $game_launch_url_arr['sample_mobile'] = $game_launch_url."/".$game['game_code']."/real/null/en";
                $game_launch_url_arr['trial'] = $game_launch_url."/".$game['game_code']."/demo/null/en";
                break;
            case BAISON_GAME_API:
                if(! in_array(BAISON_GAME_API,$providers_have_lobby)){
                    $game_launch_url_arr['web'] = $game_launch_url . "/<game_code>";
                    $game_launch_url_arr['sample_web'] = $game_launch_url."/".$game['game_code'];
                    $game_launch_url_arr['mobile'] = $game_launch_url . "<game_code>";
                    $game_launch_url_arr['sample_mobile'] = $game_launch_url . "/" . $game['game_code'];
                    break;
                }else{
                    $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                    $game_launch_url_arr['web'] = $game_launch_url;
                    $game_launch_url_arr['sample'] = $game_launch_url;
                    $game_launch_url_arr['mobile'] = $game_launch_url;
                    break;
                }
            case BOOMING_SEAMLESS_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['external_game_id'] . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['external_game_id'] . "/real";
                $game_launch_url_arr['trial'] = $game_launch_url  . "/" . $game['game_platform_id'] . "/" . $game['external_game_id'] . "/demo";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<external_game_id>/<game_mode>";
                break;
            case AVIA_ESPORT_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['trial'] = $game_launch_url . "/trial";
                break;
            case OG_V2_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                break;
            case AFB88_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                break;
            case DONGSEN_ESPORTS_API:
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['sample'] = $game_launch_url;
                break;
            case DONGSEN_LOTTERY_API:
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['sample'] = $game_launch_url;
                break;
            case YGGDRASIL_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'] . '/false/real';
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'] . '/true/real';
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_code'] . '/false/trial';
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<is_mobile>/<mode>";
                break;
            case RG_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] =  $game_launch_url;
                $game_launch_url_arr['mobile'] =  $game_launch_url;
                break;
            case TPG_API:
                $game_launch_url_arr['web'] =  $game_launch_url . $game['game_code'];
                $game_launch_url_arr['mobile'] =  $game_launch_url . $game['game_code'];
                $game_launch_url_arr['sample'] = $game_launch_url . $game['game_code'];
                break;
            case LUCKY_GAME_CHESS_POKER_API:
                if(! in_array(LUCKY_GAME_CHESS_POKER_API,$providers_have_lobby)){
                    $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'];
                    $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'];
                    $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>";
                    break;
                } else {
                    $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                    $game_launch_url_arr['web'] =  $game_launch_url;
                    $game_launch_url_arr['mobile'] =  $game_launch_url;
                    break;
                }
            case FLOW_GAMING_SEAMLESS_API:
            case FLOW_GAMING_SEAMLESS_THB1_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . "/" . $game['game_code'] . "/real";
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'] . "/real";
                $game_launch_url_arr['trial'] = $game_launch_url  . "/" . $game['game_code'] . "/demo";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<game_mode>";
                break;  
            case GOLDEN_RACE_GAMING_API:
            case GD_SEAMLESS_API:
                $game_launch_url_arr['game_type_lang'] = isset($game['type_lang']) ? $game['type_lang'] : null;
                $game_launch_url_arr['mobile'] = $game_launch_url ."/" . $game['game_platform_id'];
                $game_launch_url_arr['web'] = $game_launch_url ."/" . $game['game_platform_id'];
                $game_launch_url_arr['trial'] = $game_launch_url ."/" . $game['game_platform_id'] . "/null/trial/null/en";
                $game_launch_url_arr['sample'] = $game_launch_url ."/<game platform id>/<game code>/<mode>/<game type>/<language>";
                break; 
            case SLOT_FACTORY_SEAMLESS_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['game_code'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['game_code'];
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_platform_id'] . "/" . $game['game_code'] . "/trial";
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>/<game_code>/<game_mode>";
                break;
            case TFGAMING_ESPORTS_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>";
                break;
            case T1LOTTERY_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                break;
            case GENESIS_SEAMLESS_API:
            case GENESIS_SEAMLESS_THB1_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'] . '/real';
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" .  $game['game_code'] . '/real';
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_code'] . '/play';
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<game_mode>";
                break; 

            case ASIASTAR_API:
                $game_launch_url_arr['game_type_lang'] = @$game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url ;
                break;

            case OGPLUS_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                break;
            case HUB88_API:
                $game_launch_url_arr['mobile'] = $game_launch_url ."/" . $game['external_game_id'] . '/real';
                $game_launch_url_arr['web'] = $game_launch_url ."/" . $game['external_game_id'] . '/real';
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['external_game_id'] . '/trial';
                $game_launch_url_arr['sample'] = $game_launch_url . "/<external_game_id>/<game_mode>";
                break;
            case KG_POKER_API:
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_platform_id'];
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_platform_id'];
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_platform_id>";
                break;
            case TIANHONG_MINI_GAMES_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                break;
            case GPK_API:
                $game_launch_type = $game_type_code === self::TAG_CODE_FISHING_GAME ? 'fishing' : 'slots';
                $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code'] . "/real/" . $game_launch_type;
                $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code'] . "/real/" . $game_launch_type;
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<game_mode>/<game_type>";
                break;
            case AG_SEAMLESS_GAME_API:
            case AG_SEAMLESS_THB1_API:
                if(! in_array(AG_SEAMLESS_THB1_API, $providers_have_lobby)){
                    $game_launch_url_arr['web'] = $game_launch_url."/".$game['game_code']."/true"."/null"."/zh-cn";
                    $game_launch_url_arr['sample_web'] = $game_launch_url."/<game_code>/<game_mode>/<game_type>/<language>";
                    $game_launch_url_arr['mobile'] = $game_launch_url."/".$game['game_code']."/true"."/null"."/zh-cn";
                    $game_launch_url_arr['sample_mobile'] = $game_launch_url."/<game_code>/<game_mode>/<game_type>/<language>";
                    //$game_launch_url_arr['trial'] = $game_launch_url."/".$game['game_code']."/false/null/zh-cn";
                    break;
                }else{
                    $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                    $game_launch_url_arr['web'] = $game_launch_url;
                    $game_launch_url_arr['mobile'] = $game_launch_url;
                    //$game_launch_url_arr['trial'] = $game_launch_url."/0/trial/null/zh-cn";
                    break;
                }
            case JOKER_API:
                $game_launch_url_arr['mobile'] = $game_launch_url ."/" . $game['game_code'] . '/real';
                $game_launch_url_arr['web'] = $game_launch_url ."/" . $game['game_code'] . '/real';
                $game_launch_url_arr['trial'] = $game_launch_url . "/" . $game['game_code'] . '/trial';
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<game_mode>";
                break;
            case RGS_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                break;
            case T1SBTECH_BTI_API:
            case SBTECH_BTI_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                break;
                break;  
            case WM_API:
                $game_launch_url_arr['game_type_lang'] = $game['type_lang'];
                $game_launch_url_arr['web'] = $game_launch_url;
                $game_launch_url_arr['mobile'] = $game_launch_url;
                $game_launch_url_arr['trial'] = $game_launch_url . "/null" . '/trial';
                break;
            case TIANHAO_API:
                $code = json_decode(isset($game['game_launch_code_other_settings']) ?  $game['game_launch_code_other_settings'] : '' , true);
                $game_launch_code = !empty($code) ? $code['game_launch_code']: $game['game_code'];

                $game_launch_url_arr['mobile'] = $game_launch_url ."/" . $game_launch_code;
                $game_launch_url_arr['web'] = $game_launch_url ."/" . $game_launch_code;
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>";
                break;   
            case ICONIC_SEAMLESS_API:
                $game_launch_url_arr['web'] = $game_launch_url . '/' . $game['game_code'] . "/real/";
                $game_launch_url_arr['mobile'] = $game_launch_url . '/' . $game['game_code'] . "/real/"; 
                $game_launch_url_arr['trial'] = $game_launch_url . '/' . $game['game_code'] . "/fun/";                
                $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<mode>";
                break;
            default:
                $game_launch_url_arr = "unknown";
                break;


            // case XYZBLUE_API:
            //     $game_launch_url_arr['web'] = $game_launch_url . "/" . $game['game_code']. "/real";
            //     $game_launch_url_arr['mobile'] = $game_launch_url . "/" . $game['game_code']. "/real";
            //     $game_launch_url_arr['sample'] = $game_launch_url . "/<game_code>/<mode>";
            //     break;
        }
        if (!empty($game['html_five_enabled']))
            return $game_launch_url_arr;

        if (array_key_exists('mobile_enabled',$game) && $game['mobile_enabled'] == GAME_DESCRIPTION_MODEL::DB_FALSE)
           unset($game_launch_url_arr['mobile']);

        if (array_key_exists('flash_enabled',$game) && $game['flash_enabled'] == GAME_DESCRIPTION_MODEL::DB_FALSE)
           unset($game_launch_url_arr['web']);

        return $game_launch_url_arr;
    }

    public function getGameProviderDetails()
    {
        $game_platforms['available_game_providers'] = [
            PT_API => $this->getGameApiDetails(PT_API,"PlayTech","Playtech","goto_ptgame", "PT"),
            MG_API => $this->getGameApiDetails(MG_API,"MG","Microgaming","goto_mggame", "MG"),
            NT_API => $this->getGameApiDetails(NT_API,"NT","NetEnt","goto_ntgame", "NT"),
            BBIN_API => $this->getGameApiDetails(BBIN_API,"BBIN","BBIN","goto_bbingame", "BBIN"),
            OPUS_API => $this->getGameApiDetails(OPUS_API,"OPUS","OPUS","goto_opusgame", "OPUS"),
            ONESGAME_API => $this->getGameApiDetails(ONESGAME_API,"ONESGAME","ONESGAME","goto_onesgame", "ONESGAME"),
            GSPT_API => $this->getGameApiDetails(GSPT_API,"GS PlayTech","GSPT","goto_gsptgame", "GSPT"),
            KENOGAME_API => $this->getGameApiDetails(KENOGAME_API,"KENOGAME","KENOGAME","goto_kenogamegame", "KENOGAME"),
            AB_API => $this->getGameApiDetails(AB_API,"AllBet","KENOGAME","goto_abgame", "AB"),
            IBC_API => $this->getGameApiDetails(IBC_API,"IBC","IBC","goto_ibc", "IBC"),
            GD_API => $this->getGameApiDetails(GD_API,"Gold Deluxe","GD","goto_gdgame", "GD"),
            XHTDLOTTERY_API => $this->getGameApiDetails(XHTDLOTTERY_API,"XHTDLOTTERY","XHTDLOTTERY","goto_xhtdlottery", "XHTDLOTTERY"),
            WFT_API => $this->getGameApiDetails(WFT_API,"WFT","WFT","goto_wftgame", "WFT"),
            HB_API => $this->getGameApiDetails(HB_API,"Habanero","HB","goto_habagame", "HB"),
            IMPT_API => $this->getGameApiDetails(IMPT_API,"Inplay Matrix Playtech","IMPT","goto_imptgame", "IMPT"),
            QT_API => $this->getGameApiDetails(QT_API,"Qtech","QT","goto_qtgame", "QT"),
            TTG_API => $this->getGameApiDetails(TTG_API,"Top Trend Gaming","TTG","goto_ttggame", "TTG"),
            ENTWINE_API => $this->getGameApiDetails(ENTWINE_API,"ENTWINE","ENTWINE","goto_entwine", "ENTWINE"),
            GAMESOS_API => $this->getGameApiDetails(GAMESOS_API,"GAMESOS","GAMESOS","goto_gamesosgame", "GAMESOS"),
            FG_API => $this->getGameApiDetails(FG_API,"Flow Gaming","FG","goto_fggame", "FG"),
            EBET_API => $this->getGameApiDetails(EBET_API,"EBET","EBET","goto_ebetgame", "EBET"),
            LAPIS_API => $this->getGameApiDetails(LAPIS_API,"Microgaming Lapis","LAPIS","goto_mglapis_game", "LAPIS"),
            ISB_API => $this->getGameApiDetails(ISB_API,"Isoftbet","ISB","goto_isb_game", "ISB"),
            ONEWORKS_API => $this->getGameApiDetails(ONEWORKS_API,"ONEWORKS","ONEWORKS","goto_oneworks_game", "ONEWORKS"),
            SPORTSBOOK_API => $this->getGameApiDetails(SPORTSBOOK_API,"IPM","SPORTSBOOK","goto_ipm", "SPORTSBOOK"),
            FISHINGGAME_API => $this->getGameApiDetails(FISHINGGAME_API,"Global Gaming","FISHINGGAME","goto_common_game/".FISHINGGAME_API, "FISHINGGAME"),
            IMSLOTS_API => $this->getGameApiDetails(IMSLOTS_API,"Global Gaming","IMSLOTS","goto_imslots", "IMSLOTS"),
            AGBBIN_API => $this->getGameApiDetails(AGBBIN_API,"AG BBIN","AGBBIN","goto_agbbingame", "AGBBIN"),
            AGSHABA_API => $this->getGameApiDetails(AGSHABA_API,"AGSHABA","AGSHABA","goto_agshabagame", "AGSHABA"),
            AGIN_API => $this->getGameApiDetails(AGIN_API,"AGIN","AGIN","goto_agingame", "AGIN"),
            HRCC_API => $this->getGameApiDetails(HRCC_API,"HRCC","HRCC","goto_hrcc_game", "HRCC"),
            BETEAST_API => $this->getGameApiDetails(BETEAST_API,"BETEAST","BETEAST","goto_beteastgame", "BETEAST"),
            UC_API => $this->getGameApiDetails(UC_API,"UC","UC","goto_beteastgame", "UC"),
            OPUS_SPORTSBOOK_API => $this->getGameApiDetails(OPUS_SPORTSBOOK_API,"OPUS SPORTSBOOK","OPUS_SPORTSBOOK","goto_opus", "OPUS_SPORTSBOOK"),
            OPUS_KENO_API => $this->getGameApiDetails(OPUS_KENO_API,"OPUS KENO","OPUS_KENO","goto_opus", "OPUS_KENO"),
            KUMA_API => $this->getGameApiDetails(KUMA_API,"KUMA","KUMA","goto_kumagame", "KUMA"),
            EZUGI_API => $this->getGameApiDetails(EZUGI_API,"EZUGI","EZUGI","goto_ezugigame", "EZUGI"),
            DT_API => $this->getGameApiDetails(DT_API,"Dream Tech","DT","goto_dtgame", "DT"),
            IDN_API => $this->getGameApiDetails(IDN_API,"IDN Play","IDN","goto_idngame", "IDN"),
            SA_GAMING_API => $this->getGameApiDetails(SA_GAMING_API,"SA Gaming","SA_GAMING","goto_sagaminggame", "SA_GAMING"),
            EBET_BBIN_API => $this->getGameApiDetails(EBET_BBIN_API,"EBET BBIN","EBET_BBIN","goto_ebetbbingame", "EBET_BBIN"),
            MG_QUICKFIRE_API => $this->getGameApiDetails(MG_QUICKFIRE_API,"MG QUICKFIRE","MG_QUICKFIRE","goto_mgquickfire_game", "MG_QUICKFIRE"),
            OG_API => $this->getGameApiDetails(OG_API,"Oriental Gaming","OG","goto_oggame", "OG"),
            JUMB_GAMING_API => $this->getGameApiDetails(JUMB_GAMING_API,"JUMB 168","JUMB_GAMING","goto_jumbogame", "JUMB_GAMING"),
            SPADE_GAMING_API => $this->getGameApiDetails(SPADE_GAMING_API,"Spade Gaming","SPADE_GAMING","goto_spadegame", "SPADE_GAMING"),
            PRAGMATICPLAY_API => $this->getGameApiDetails(PRAGMATICPLAY_API,"PragmaticPlay","PRAGMATICPLAY","goto_pragmaticplaygame", "PRAGMATICPLAY"),
            SBOBET_API => $this->getGameApiDetails(SBOBET_API,"SBO Bet","SBOBET","goto_sbobetgame", "SBOBET"),
            SBOBET_SEAMLESS_GAME_API => $this->getGameApiDetails(SBOBET_SEAMLESS_GAME_API,"SBOBET SEAMLESS","SBOBET_SEAMLESS_GAME_API","goto_sbobet_game", "SBOBET_SEAMLESS_GAME_API"),
            PNG_API => $this->getGameApiDetails(PNG_API,"Play N Go","PNG","goto_pnggame", "PNG"),
            ULTRAPLAY_API => $this->getGameApiDetails(ULTRAPLAY_API,"ULTRAPLAY","ULTRAPLAY","goto_ultraplay_game", "ULTRAPLAY"),
            PINNACLE_API => $this->getGameApiDetails(PINNACLE_API,"PINNACLE","PINNACLE","goto_pinnaclegame", "PINNACLE"),
            VR_API => $this->getGameApiDetails(VR_API,"Virtual Racing","VR","goto_vrgame", "VR"),
            EVOLUTION_GAMING_API => $this->getGameApiDetails(EVOLUTION_GAMING_API,"Evolution Gaming","EVOLUTION_GAMING","goto_evolution_game", "EVOLUTION_GAMING"),
            EBET_SPADE_GAMING_API => $this->getGameApiDetails(EBET_SPADE_GAMING_API,"Ebet Spade Gaming","EBET_SPADE_GAMING","goto_ebet_spadegame", "EBET_SPADE_GAMING"),
            EBET_BBTECH_API => $this->getGameApiDetails(EBET_BBTECH_API,"Ebet BBTECH","EBET_BBTECH","goto_ebetbbtech", "EBET_BBTECH"),
            EBET_IMPT_API => $this->getGameApiDetails(EBET_IMPT_API,"Ebet Inplay Matrix Playtech","EBET_IMPT","goto_ebetimpt", "EBET_IMPT"),
            EBET_MG_API => $this->getGameApiDetails(EBET_MG_API,"Ebet Microgaming","EBET_MG","goto_ebet_mg_game", "EBET_MG"),
            YOPLAY_API => $this->getGameApiDetails(YOPLAY_API,"YOPLAY","YOPLAY_API","goto_yoplaygame", "YOPLAY"),
            GAMEPLAY_SBTECH_API => $this->getGameApiDetails(GAMEPLAY_SBTECH_API,"GAMEPLAY SBTECH","GAMEPLAY_SBTECH","goto_gameplaySbtech", "GAMEPLAY_SBTECH"),
            EBET_KUMA_API => $this->getGameApiDetails(EBET_KUMA_API,"EBET KUMA","EBET_KUMA","goto_ebetkumagame", "EBET_KUMA"),
            EBET_QT_API => $this->getGameApiDetails(EBET_QT_API,"Ebet Qtech","EBET_QT","goto_ebet_qt_game", "EBET_QT"),
            YUNGU_GAME_API => $this->getGameApiDetails(YUNGU_GAME_API,"YUNGU GAME","YUNGU_GAME","goto_yungu_game", "YUNGU_GAME"),
            LEBO_GAME_API => $this->getGameApiDetails(LEBO_GAME_API,"LEBO GAME","LEBO_GAME","goto_lebo_game", "LEBO_GAME"),
            GSBBIN_API => $this->getGameApiDetails(GSBBIN_API,"GSBBIN","GSBBIN","goto_gsbbingame", "GSBBIN"),
            DG_API => $this->getGameApiDetails(DG_API,"Dream Game","DG","goto_dggame", "DG"),
            IPM_V2_SPORTS_API => $this->getGameApiDetails(IPM_V2_SPORTS_API,"IPM V2 SPORTS","IPM_V2_SPORTS","goto_ipm_v2_game", "IPM_V2_SPORTS"),
            XYZBLUE_API => $this->getGameApiDetails(XYZBLUE_API,"XYZBLUE","XYZBLUE","goto_xyzBlueMinigames", "XYZBLUE"),
            EBET_GGFISHING_API => $this->getGameApiDetails(EBET_GGFISHING_API,"EBET Global Gaming","EBET_GGFISHING","goto_ebetmwgfishing", "EBET_GGFISHING"),
            FINANCE_API => $this->getGameApiDetails(FINANCE_API,"FINANCE","FINANCE","goto_finance_game", "FINANCE"),
            LD_CASINO_API => $this->getGameApiDetails(LD_CASINO_API,"LD CASINO","LD_CASINO","goto_ld_casino_game", "LD_CASINO"),
            HG_API => $this->getGameApiDetails(HG_API,"HG","HG","goto_hg_game", "HG"),
            LD_LOTTERY_API => $this->getGameApiDetails(LD_LOTTERY_API,"LD LOTTERY","LD LOTTERY","goto_ct_lottery_game", "LD_LOTTERY"),
            EXTREME_LIVE_GAMING_API => $this->getGameApiDetails(EXTREME_LIVE_GAMING_API,"EXTREME LIVE GAMING","EXTREME_LIVE_GAMING","goto_extreme", "EXTREME_LIVE_GAMING"),
            EBET_AG_API => $this->getGameApiDetails(EBET_AG_API,"EBET AG","EBET_AG","goto_ebet_ag", "EBET_AG"),
            EBET_OPUS_API => $this->getGameApiDetails(EBET_OPUS_API,"EBET OPUS","EBET_OPUS","goto_ebet_opus", "EBET_OPUS"),
            EBET_DT_API => $this->getGameApiDetails(EBET_DT_API,"EBET DreamTech","EBET_DT","goto_ebet_dt_game", "EBET_DT"),
            IG_API => $this->getGameApiDetails(IG_API,"IG","IG","goto_ig_game", "IG"),
            GGPOKER_GAME_API => $this->getGameApiDetails(GGPOKER_GAME_API,"GGPOKER GAME","GGPOKER_GAME","goto_ggpoker", "GGPOKER_GAME"),
            GENESISM4_GAME_API => $this->getGameApiDetails(GENESISM4_GAME_API,"GENESISM4 GAME","GENESISM4_GAME","goto_genesism4", "GENESISM4_GAME"),
            SBTECH_API => $this->getGameApiDetails(SBTECH_API,"SBTECH","SBTECH","goto_ole777sports_game", "SBTECH"),
            ISB_SEAMLESS_API => $this->getGameApiDetails(ISB_SEAMLESS_API,"Isofbet SEAMLESS","ISB_SEAMLESS","goto_isbseamless_game", "ISB_SEAMLESS"),
            SUNCITY_API => $this->getGameApiDetails(SUNCITY_API,"Sun City","SUNCITY","goto_suncity", "SUNCITY"),
            T1SUNCITY_API => $this->getGameApiDetails(T1SUNCITY_API,"T1 Sun City","T1SUNCITY","goto_suncity", "T1SUNCITY"),
            MWG_API => $this->getGameApiDetails(MWG_API,"MWG","MWG","goto_mwg", "MWG"),
            RTG_API => $this->getGameApiDetails(RTG_API,"RTG","RTG","goto_rtg", "RTG"),
            RWB_API => $this->getGameApiDetails(RWB_API,"RWB","RWB","goto_rwb", "RWB"),
            FG_ENTAPLAY_API => $this->getGameApiDetails(FG_ENTAPLAY_API,"FG ENTAPLAY","FG_ENTAPLAY","goto_t1games", "FG_ENTAPLAY"),
            T1PT_API => $this->getGameApiDetails(T1PT_API,"T1 PlayTech","T1PT","goto_t1games", "T1PT"),
            T1YOPLAY_API => $this->getGameApiDetails(T1YOPLAY_API,"T1 YOPLAY","T1YOPLAY","goto_t1yoplaygame", "T1YOPLAY"),
            T1LOTTERY_API => $this->getGameApiDetails(T1LOTTERY_API,"T1 LOTTERY","T1LOTTERY","goto_t1lottery", "T1LOTTERY"),
            T1OG_API => $this->getGameApiDetails(T1OG_API,"T1 Oriental Gaming","T1OG","goto_t1games", "T1OG"),
            T1AB_API => $this->getGameApiDetails(T1AB_API,"T1 AllBet","T1AB","goto_t1games", "T1AB"),
            T1MG_API => $this->getGameApiDetails(T1MG_API,"T1 MicroGaming","T1MG","goto_t1games", "T1MG"),
            T1DG_API => $this->getGameApiDetails(T1DG_API,"T1 Dream Game","T1DG","goto_t1games", "T1DG"),
            T1PNG_API => $this->getGameApiDetails(T1PNG_API,"T1 Play N Go","T1PNG","goto_t1games", "T1PNG"),
            T1PRAGMATICPLAY_API => $this->getGameApiDetails(T1PRAGMATICPLAY_API,"T1 PragmayticPlay","T1PRAGMATICPLAY","goto_t1games", "T1PRAGMATICPLAY"),
            T1TTG_API => $this->getGameApiDetails(T1TTG_API,"T1 Top Trend Gaming","T1TTG","goto_t1games", "T1TTG"),
            T1AGIN_API => $this->getGameApiDetails(T1AGIN_API,"T1 AGIN","T1AGIN","goto_t1games", "T1AGIN"),
            T1EBET_API => $this->getGameApiDetails(T1EBET_API,"T1 EBET","T1EBET","goto_t1games", "T1EBET"),
            T1SPADE_GAMING_API => $this->getGameApiDetails(T1SPADE_GAMING_API,"T1 SPADE GAMING","T1SPADE_GAMING","goto_t1games", "T1SPADE_GAMING"),
            T1HB_API => $this->getGameApiDetails(T1HB_API,"T1 Habanero","T1HB","goto_t1games", "T1HB"),
            T1EZUGI_API => $this->getGameApiDetails(T1EZUGI_API,"T1 EZUGI","T1EZUGI","goto_t1games", "T1EZUGI"),
            T1JUMB_API => $this->getGameApiDetails(T1JUMB_API,"T1 JUMB","T1JUMB","goto_t1games", "T1JUMB"),
            T1VR_API => $this->getGameApiDetails(T1VR_API,"T1 Virtual Racing","T1VR","goto_t1games", "T1VR"),
            T1ISB_API => $this->getGameApiDetails(T1ISB_API,"T1 Isoftbet","T1ISB","goto_t1games", "T1ISB"),
            T1LOTTERY_EXT_API => $this->getGameApiDetails(T1LOTTERY_EXT_API,"T1 LOTTERY","T1LOTTERY_EXT","goto_t1games", "T1LOTTERY_EXT"),
            T1UC_API => $this->getGameApiDetails(T1UC_API,"T1 UC8","T1UC","goto_t1games", "T1UC"),
            T1DT_API => $this->getGameApiDetails(T1DT_API,"T1 DreamTech","T1DT","goto_t1games", "T1DT"),
            T1BBIN_API => $this->getGameApiDetails(T1BBIN_API,"T1BBIN","T1BBIN","goto_t1games", "T1BBIN"),
            T1IDN_API => $this->getGameApiDetails(T1IDN_API,"T1 IDN","T1IDN","goto_t1games", "T1IDN"),
            T1GD_API => $this->getGameApiDetails(T1GD_API,"T1 Gold Deluxe","T1GD","goto_t1games", "T1GD"),
            T1QT_API => $this->getGameApiDetails(T1QT_API,"T1 QTech","T1QT","goto_t1games", "T1QT"),
            T1GGPOKER_GAME_API => $this->getGameApiDetails(T1GGPOKER_GAME_API,"T1 GGPOKER GAME","T1GGPOKER_GAME","goto_t1games", "T1GGPOKER_GAME"),
            T1FG_ENTAPLAY_API => $this->getGameApiDetails(T1FG_ENTAPLAY_API,"T1 FG ENTAPLAY","T1FG_ENTAPLAY","goto_t1games", "T1FG_ENTAPLAY"),
            T1DG_API => $this->getGameApiDetails(T1DG_API,"T1 DG","T1DG","goto_t1games", "T1DG"),
            T1ONEWORKS_API => $this->getGameApiDetails(T1ONEWORKS_API,"T1 ONEWORKS","T1ONEWORKS","goto_t1games", "T1ONEWORKS"),
            T1GG_API => $this->getGameApiDetails(T1GG_API,"T1 Global Gaming","T1GG","goto_t1games", "T1GG"),
            ONE88_API => $this->getGameApiDetails(ONE88_API,"ONE88","ONE88","goto_", "ONE88"),
            GAMEPLAY_API => $this->getGameApiDetails(GAMEPLAY_API,"GAMEPLAY","GAMEPLAY","goto_gpgame", "GAMEPLAY"),
            BBTECHGSPOT_API => $this->getGameApiDetails(BBTECHGSPOT_API,"BBTECHGSPOT","BBTECHGSPOT","goto_", "BBTECHGSPOT"),
            GSKENO_API => $this->getGameApiDetails(GSKENO_API,"GSKENO","GSKENO","goto_", "GSKENO"),
            CROWN_API => $this->getGameApiDetails(CROWN_API,"CROWN","CROWN","goto_", "CROWN"),
            VIVO_API => $this->getGameApiDetails(VIVO_API,"VIVO","VIVO","goto_", "VIVO"),
            AGHG_API => $this->getGameApiDetails(AGHG_API,"AGHG","AGHG","goto_", "AGHG"),
            AGPT_API => $this->getGameApiDetails(AGPT_API,"AGPT","AGPT","goto_", "AGPT"),
            SEVEN77_API => $this->getGameApiDetails(SEVEN77_API,"SEVEN77","SEVEN77","goto_", "SEVEN77"),
            GSMG_API => $this->getGameApiDetails(GSMG_API,"GSMG","GSMG","goto_", "GSMG"),
            AGENCY_API => $this->getGameApiDetails(AGENCY_API,"AGENCY","AGENCY","goto_", "AGENCY"),
            EBET2_API => $this->getGameApiDetails(EBET2_API,"EBET2","EBET2","goto_", "EBET2"),
            PLAYSTAR_API => $this->getGameApiDetails(PLAYSTAR_API,"PLAYSTAR","PLAYSTAR","goto_", "PLAYSTAR"),
            SBTECH_GAMING_API => $this->getGameApiDetails(SBTECH_GAMING_API,"SBTECH Gaming","SBTECH_GAMING","goto_", "SBTECH_GAMING"),
            LADDER_GAMING_API => $this->getGameApiDetails(LADDER_GAMING_API,"LADDER Gaming","LADDER_GAMING","goto_", "LADDER_GAMING"),
            BETMASTER_API => $this->getGameApiDetails(BETMASTER_API,"BETMASTER","BETMASTER","goto_", "BETMASTER"),
            PT_KRW_API => $this->getGameApiDetails(PT_KRW_API,"PT KRW","PT_KRW","goto_", "PT_KRW"),
            TCG_API => $this->getGameApiDetails(TCG_API,"TCG","TCG","goto_tcg_lottery", "TCG"),
            GOLDENF_PGSOFT_API => $this->getGameApiDetails(GOLDENF_PGSOFT_API,"GOLDENF PGSOFT","GOLDENF_PGSOFT","goto_goldenf_pgsoft", "GOLDENF_PGSOFT"),
            MG_DASHUR_API => $this->getGameApiDetails(MG_DASHUR_API,"MG DASHUR","MG_DASHUR","goto_mg_dashur_game", "MG_DASHUR"),
            KYCARD_API => $this->getGameApiDetails(KYCARD_API,"KYCARD","KYCARD","goto_kycard", "KYCARD"),
            LE_GAMING_API => $this->getGameApiDetails(LE_GAMING_API,"LE GAMING","LE_GAMING","goto_legaming_game", "LE_GAMING"),
            BETSOFT_API => $this->getGameApiDetails(BETSOFT_API,"BETSOFT","BETSOFT","goto_betsoft_game", "BETSOFT"),
            CQ9_API => $this->getGameApiDetails(CQ9_API,"CQ9","CQ9","goto_cq9", "CQ9"),
            MTECH_BBIN_API => $this->getGameApiDetails(MTECH_BBIN_API,"MTECH BBIN","MTECH_BBIN","goto_mtech_bbin", "MTECH_BBIN"),
            T1CQ9_API => $this->getGameApiDetails(T1CQ9_API,"T1 CQ9","T1CQ9","goto_t1games", "T1CQ9"),
            REDTIGER_API => $this->getGameApiDetails(REDTIGER_API,"REDTIGER","REDTIGER","gotogame", "REDTIGER"),
            HOGAMING_API => $this->getGameApiDetails(HOGAMING_API,"HO GAMING","HOGAMING","goto_hggame", "HOGAMING"),
            MGPLUS_API => $this->getGameApiDetails(MGPLUS_API,"MG PLUS","MGPLUS","goto_common_game", "MGPLUS"),
            PGSOFT_API => $this->getGameApiDetails(PGSOFT_API,"PG SOFT","PGSOFT","goto_pgsoft", "PGSOFT"),
            T1MGPLUS_API => $this->getGameApiDetails(T1MGPLUS_API,"T1 MG PLUS","T1MGPLUS","goto_t1games", "T1MGPLUS"),
            ISIN4D_API => $this->getGameApiDetails(ISIN4D_API,"ISIN4D","ISIN4D","gotogame/".ISIN4D_API, "ISIN4D"),
            ISIN4D_IDR_B1_API => $this->getGameApiDetails(ISIN4D_IDR_B1_API,"ISIN4D","ISIN4D","goto_isin4d/".ISIN4D_IDR_B1_API, "ISIN4D_IDR_B1"),
            QQKENO_QQLOTTERY_API => $this->getGameApiDetails(QQKENO_QQLOTTERY_API,"QQKENOQQLOTTERY","QQKENO_QQLOTTERY","goto_qqkenolottery/".QQKENO_QQLOTTERY_API, "QQKENO_QQLOTTERY"),
            QQKENO_QQLOTTERY_THB_B1_API => $this->getGameApiDetails(QQKENO_QQLOTTERY_THB_B1_API,"QQKENOQQLOTTERY THB","QQKENO_QQLOTTERY_THB_B1","goto_qqkenolottery/".QQKENO_QQLOTTERY_THB_B1_API, "QQKENO_QQLOTTERY_THB_B1"),
            NTTECH_API => $this->getGameApiDetails(NTTECH_API,"NT Sexy Live","NTTECH","goto_nttech_game/".NTTECH_API, "NTTECH"),
            NTTECH_IDR_B1_API => $this->getGameApiDetails(NTTECH_IDR_B1_API,"NT Sexy Live","NTTECH","goto_nttech_game/".NTTECH_IDR_B1_API, "NTTECH_IDR_B1"),
            NTTECH_CNY_B1_API => $this->getGameApiDetails(NTTECH_CNY_B1_API,"NT Sexy Live","NTTECH","goto_nttech_game/".NTTECH_CNY_B1_API, "NTTECH_CNY_B1"),
            NTTECH_THB_B1_API => $this->getGameApiDetails(NTTECH_THB_B1_API,"NT Sexy Live","NTTECH","goto_nttech_game/".NTTECH_THB_B1_API, "NTTECH_THB_B1"),
            NTTECH_V2_API => $this->getGameApiDetails(NTTECH_V2_API,"NT Sexy Live","NTTECH","goto_nttechv2_game/".NTTECH_V2_API, "NTTECH"),
            NTTECH_V2_IDR_B1_API => $this->getGameApiDetails(NTTECH_V2_IDR_B1_API,"NT Sexy Live","NTTECH","goto_nttechv2_game/".NTTECH_V2_IDR_B1_API, "NTTECH_V2_IDR_B1_API"),
            NTTECH_V2_CNY_B1_API => $this->getGameApiDetails(NTTECH_V2_CNY_B1_API,"NT Sexy Live","NTTECH","goto_nttechv2_game/".NTTECH_V2_CNY_B1_API, "NTTECH_V2_CNY_B1_API"),
            NTTECH_V2_THB_B1_API => $this->getGameApiDetails(NTTECH_V2_THB_B1_API,"NT Sexy Live","NTTECH","goto_nttechv2_game/".NTTECH_V2_THB_B1_API, "NTTECH_V2_THB_B1_API"),            
            AE_SLOTS_GAMING_API => $this->getGameApiDetails(AE_SLOTS_GAMING_API,"AE SLOTS","AE_SLOTS_GAMING","goto_common_game/".AE_SLOTS_GAMING_API, "AE_SLOTS_GAMING"),
            REDRAKE_GAMING_API => $this->getGameApiDetails(REDRAKE_GAMING_API,"Red Rake","REDRAKE_GAMING_API","goto_common_game/".REDRAKE_GAMING_API, "REDRAKE_GAMING_API"),
            BAISON_GAME_API => $this->getGameApiDetails(BAISON_GAME_API,"Baison Game","BAISON","goto_common_game/".BAISON_GAME_API, "BAISON_GAME"),
            BOOMING_SEAMLESS_API => $this->getGameApiDetails(BOOMING_SEAMLESS_API,"BOOMING API","BOOMING API","goto_booming", "BOOMING_SEAMLESS"),
            AVIA_ESPORT_API => $this->getGameApiDetails(AVIA_ESPORT_API,"AVIA ESPORTS","AVIAESPORTS","goto_aviasport", "AVIA_ESPORT"),
            OG_V2_API => $this->getGameApiDetails(OG_V2_API,"OG V2","OGV2","goto_oggame_v2", "OG_V2"),
            IBC_24TECH_API => $this->getGameApiDetails(IBC_24TECH_API,"IBC 24TECH","IBC_24TECH","goto_24tech_game/".IBC_24TECH_API, "IBC_24TECH"),
            AFB88_API => $this->getGameApiDetails(AFB88_API,"AFB88","AFB88","goto_common_game/".AFB88_API, "AFB88"),
            DONGSEN_ESPORTS_API => $this->getGameApiDetails(DONGSEN_ESPORTS_API,"DONGSEN ESPORTS","DS ESPORTS","goto_common_game/".DONGSEN_ESPORTS_API, "DONGSEN_ESPORTS"),
            DONGSEN_LOTTERY_API => $this->getGameApiDetails(DONGSEN_LOTTERY_API,"DONGSEN LOTTERY","DS LOTTERY","goto_common_game/".DONGSEN_LOTTERY_API, "DONGSEN_LOTTERY"),
            YGGDRASIL_API => $this->getGameApiDetails(YGGDRASIL_API,"YGGDRASIL","YGGDRASIL","goto_yggdrasil", "YGGDRASIL"),
            PT_V2_API => $this->getGameApiDetails(PT_V2_API,"PlayTech","Playtech","goto_ptv2game", "PT V2"),
            SOLID_GAMING_THB_API => $this->getGameApiDetails(SOLID_GAMING_THB_API,"SOLID GAMING","SOLID GAMING","goto_common_game/", "SOLID_GAMING"),
            VIVOGAMING_API => $this->getGameApiDetails(VIVOGAMING_API,"VIVO GAMING","VIVOGAMING_API","goto_common_game/".VIVOGAMING_API, "VIVOGAMING_API"),
            EVOLUTION_SEAMLESS_THB1_API => $this->getGameApiDetails(EVOLUTION_SEAMLESS_THB1_API,"EVOLUTION SEAMLESS GAMING","EVOLUTION_SEAMLESS_THB1_API","goto_common_game/".EVOLUTION_SEAMLESS_THB1_API, "EVOLUTION_SEAMLESS_THB1_API"),
            REDTIGER_SEAMLESS_API => $this->getGameApiDetails(REDTIGER_SEAMLESS_API,"REDTIGER SEAMLESS","REDTIGER SEAMLESS","goto_redtigerseamless_game", "REDTIGER_SEAMLESS"),
            RG_API => $this->getGameApiDetails(RG_API,"Ray Gaming","RG","goto_rg_game", "RG"),
            ONEBOOK_API => $this->getGameApiDetails(ONEBOOK_API,"ONEBOOK","ONEBOOK SPORTSBOOK","goto_onebook_game/".ONEBOOK_API, "ONEBOOK_THB_B1_API"),
            ONEBOOK_THB_B1_API => $this->getGameApiDetails(ONEBOOK_THB_B1_API,"ONEBOOK","ONEBOOK SPORTSBOOK","goto_onebook_game/".ONEBOOK_THB_B1_API, "ONEBOOK_THB_B1_API"),
            DG_SEAMLESS_API => $this->getGameApiDetails(DG_SEAMLESS_API,"Dream Game Seamless","DG","goto_dggame_seamless", "DG"),
            LUCKY_GAME_CHESS_POKER_API => $this->getGameApiDetails(LUCKY_GAME_CHESS_POKER_API,"Lucky Game Chess Poker","LUCKY GAME","goto_common_game/".LUCKY_GAME_CHESS_POKER_API, "LUCKY_GAME_CHESS_POKER_API"),
            FLOW_GAMING_SEAMLESS_API => $this->getGameApiDetails(FLOW_GAMING_SEAMLESS_API,"FLOW GAMING SEAMLESS API","FLOW GAMING SEAMLESS API","goto_common_game/".FLOW_GAMING_SEAMLESS_API, "FLOW_GAMING_SEAMLESS"),
            PRAGMATICPLAY_SEAMLESS_THB1_API => $this->getGameApiDetails(PRAGMATICPLAY_SEAMLESS_THB1_API,"PragmaticPlay","PRAGMATICPLAY","goto_common_game/", "PRAGMATICPLAY"),
            SBOBETGAME_API => $this->getGameApiDetails(SBOBETGAME_API,"SBOBETGAME","SBOBETGAME SPORTSBOOK","goto_sbobetgame/".SBOBETGAME_API, "SBOBETGAME_API"),
            SBOBETGAME_THB_B1_API => $this->getGameApiDetails(SBOBETGAME_THB_B1_API,"SBOBETGAME","SBOBETGAME SPORTSBOOK","goto_sbobetgame/".SBOBETGAME_THB_B1_API, "SBOBETGAME_THB_B1_API"),
            SBOBETV2_GAME_API => $this->getGameApiDetails(SBOBETV2_GAME_API,"SBOBETGAME","SBOBETGAME SPORTSBOOK","goto_sbobetgame/".SBOBETV2_GAME_API, "SBOBETV2_GAME_API"),
            ASIASTAR_API => $this->getGameApiDetails(ASIASTAR_API,"ASIASTAR","ASIASTAR_API","goto_common_game/".ASIASTAR_API, "ASIASTAR_API"),
            GOLDEN_RACE_GAMING_API => $this->getGameApiDetails(GOLDEN_RACE_GAMING_API,"GOLDEN RACE GAMING","GOLDEN RACE GAMING","goto_common_game", "GOLDEN RACE GAMING"),
            HABANERO_SEAMLESS_GAMING_API => $this->getGameApiDetails(HABANERO_SEAMLESS_GAMING_API,"HABANERO","HABANERO","goto_common_game/".HABANERO_SEAMLESS_GAMING_API, "HABANERO"),
            FLOW_GAMING_SEAMLESS_THB1_API => $this->getGameApiDetails(FLOW_GAMING_SEAMLESS_THB1_API,"FLOW GAMING SEAMLESS API","FLOW GAMING SEAMLESS API","goto_common_game/".FLOW_GAMING_SEAMLESS_THB1_API, "FLOW_GAMING_SEAMLESS"),
            TPG_API => $this->getGameApiDetails(TPG_API,"TPG","TPG","goto_common_game/".TPG_API."/", "TPG_API"),
            SLOT_FACTORY_SEAMLESS_API => $this->getGameApiDetails(SLOT_FACTORY_SEAMLESS_API,"Slot Factory Seamless API", "SLOT_FACTORY_SEAMLESS_API", "goto_common_game", "SLOT_FACTORY_SEAMLESS_API"),
            TFGAMING_ESPORTS_API => $this->getGameApiDetails(TFGAMING_ESPORTS_API,"TFGaming Esports", "TFGAMING_ESPORTS_API", "goto_common_game", "TFGAMING_ESPORTS_API"),
            GD_SEAMLESS_API => $this->getGameApiDetails(GD_SEAMLESS_API,"Gold Deluxe", "GD_SEAMLESS_API", "goto_common_game", "GD_SEAMLESS_API"),
            GENESIS_SEAMLESS_API => $this->getGameApiDetails(GENESIS_SEAMLESS_API,"Genesis Seamless","Genesis Seamless","goto_common_game/".GENESIS_SEAMLESS_API, "GENESIS_SEAMLESS_API"),
            GENESIS_SEAMLESS_THB1_API => $this->getGameApiDetails(GENESIS_SEAMLESS_THB1_API,"Genesis Seamless","Genesis Seamless","goto_common_game/".GENESIS_SEAMLESS_THB1_API, "GENESIS_SEAMLESS_THB1_API"),
            LB_API => $this->getGameApiDetails(LB_API,"LB Keno","LB Keno","goto_common_game/".LB_API, "LB_API"),
            HOGAMING_SEAMLESS_API => $this->getGameApiDetails(HOGAMING_SEAMLESS_API,"HOGAMING SEAMLESS API","HOGAMING SEAMLESS API","goto_hgseamless_game/".HOGAMING_SEAMLESS_API, "HOGAMING_SEAMLESS"),
            KING_MAKER_GAMING_API => $this->getGameApiDetails(KING_MAKER_GAMING_API,"King Maker","KING_MAKER_GAMING_API","goto_kingmaker_game/".KING_MAKER_GAMING_API, "KING_MAKER_GAMING_API"),
            KING_MAKER_GAMING_THB_B1_API => $this->getGameApiDetails(KING_MAKER_GAMING_THB_B1_API,"King Maker","KING_MAKER_GAMING_THB_B1_API","goto_kingmaker_game/".KING_MAKER_GAMING_THB_B1_API, "KING_MAKER_GAMING_THB_B1_API"),
            TIANHONG_MINI_GAMES_API => $this->getGameApiDetails(TIANHONG_MINI_GAMES_API,"TianHong Mini Games","TIANHONG_MINI_GAMES_API","goto_common_game/".TIANHONG_MINI_GAMES_API, "TIANHONG_MINI_GAMES_API"),
            OGPLUS_API => $this->getGameApiDetails(OGPLUS_API,"OGPLUS","OGPLUS","goto_common_game/".OGPLUS_API, "OGPLUS_API"),
            HUB88_API => $this->getGameApiDetails(HUB88_API,"HUB88","HUB88","goto_hub88", "HUB88_API"),
            KG_POKER_API => $this->getGameApiDetails(KG_POKER_API, "KG Poker", "KG_POKER_API", "goto_common_game", "KG_POKER_API"),
            AG_SEAMLESS_GAME_API => $this->getGameApiDetails(AG_SEAMLESS_GAME_API, "AG SEAMLESS", "AG_SEAMLESS_GAME_API", "goto_common_game/".AG_SEAMLESS_GAME_API, "AG_SEAMLESS_GAME_API"),
            AG_SEAMLESS_THB1_API => $this->getGameApiDetails(AG_SEAMLESS_THB1_API, "AG SEAMLESS", "AG_SEAMLESS_THB1_API", "goto_common_game/".AG_SEAMLESS_THB1_API, "AG_SEAMLESS_THB1_API"),
            GPK_API => $this->getGameApiDetails(GPK_API, "TP", "TP_API", "goto_common_game", "TP_API"),
            JOKER_API => $this->getGameApiDetails(JOKER_API, "JOKER", "JOKER_API", "goto_common_game/".JOKER_API, "JOKER_API"),
            RGS_API => $this->getGameApiDetails(RGS_API, "RGS", "RGS_API", "goto_common_game/".RGS_API, "RGS_API"),
            TGP_AG_API => $this->getGameApiDetails(TGP_AG_API,"TGP AG API","TGP AG API","goto_common_game/".TGP_AG_API, "TGP_AG"),
            ICONIC_SEAMLESS_API => $this->getGameApiDetails(ICONIC_SEAMLESS_API,"ICONIC_API","ICONIC_API","goto_common_game/".ICONIC_SEAMLESS_API, "ICONIC_API"),
            SBTECH_BTI_API => $this->getGameApiDetails(SBTECH_BTI_API,"SBTECH BTI","SBTECH BTI","goto_sbtech_bti_game", "SBTECH_BTI_API"),
            T1SBTECH_BTI_API => $this->getGameApiDetails(T1SBTECH_BTI_API,"SBTECH BTI","SBTECH BTI","goto_sbtech_bti_game", "T1SBTECH_BTI_API"),
            WM_API => $this->getGameApiDetails(WM_API,"WM","WM_API","goto_common_game/".WM_API, "WM_API"),
            TIANHAO_API => $this->getGameApiDetails(TIANHAO_API,"TIANHAO API","TIANHAO_API","goto_tianhao", "TIANHAO_API"),
            ICONIC_SEAMLESS_API => $this->getGameApiDetails(ICONIC_SEAMLESS_API,"ICONIC_API","ICONIC_API","goto_common_game/".ICONIC_SEAMLESS_API, "ICONIC_API"),
        ];
        return $game_platforms;
    }


    private function getGameApiDetails($game_platform_id,$game_provider_name,$game_platform_name,$goto_game_method, $game_provider_code){
        return [
                'game_provider' => $game_platform_name,
                'complete_name' => $game_provider_name,
                'game_platform_id'=> $game_platform_id,
                'game_provider_code' => $game_provider_code,
                'game_launch_url' =>  'player_center/'.$goto_game_method
               ];
    }
}