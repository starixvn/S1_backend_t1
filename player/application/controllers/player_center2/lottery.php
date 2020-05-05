<?php
require_once 'PlayerCenterBaseController.php';
require_once APPPATH . 'controllers/modules/gotogame_module.php';

/**
 * Lists lottery
 */
class Lottery extends PlayerCenterBaseController {
    use gotogame_module;
    /**
     * @var Game_api_lottery_t1
     */
    protected $_api = NULL;

    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        $this->preloadLotteryVars();
    }

    private function preloadLotteryVars() {
        $this->load->model(array('game_provider_auth', 'external_system'));

        $game_platform_id = T1LOTTERY_API;

        if (!$this->external_system->isGameApiActive($game_platform_id)) {
            $this->utils->show_message('danger', NULL, sprintf(lang('gen.error.forbidden'), lang('system.word94')), $this->utils->getPlayerLoginUrl());
            return;
        }

        /** @var Game_api_lottery_t1 $api */
        $api = $this->utils->loadExternalSystemLibObject($game_platform_id);
        $this->_api = $api;

        $isLogged = $this->load->get_var('isLogged', FALSE);
        if(empty($isLogged)){
            return;
        }

        $player_id = $this->load->get_var('playerId');
        $player_name = $this->load->get_var('username');

        if($this->utils->blockLoginGame($player_id)){
            $this->goBlock();
            return;
        }

        $platformName = $this->external_system->getNameById($game_platform_id);
        $success = $this->prepareGotoGame($player_name, $player_id, $game_platform_id);
        if (!$success) {
            $this->utils->show_message('danger', NULL, lang('goto_game.error'), $this->utils->getPlayerLoginUrl());
            return;
        }
    }

    public function agent(){
        $player_id = $this->load->get_var('playerId');
        $player_name = $this->load->get_var('username');

        $result = $this->_api->queryForwardAgent($player_name, []);

        if($result['success'] && !empty($result['url'])){
            redirect($result['url']);
        }else{
            $this->utils->show_message('danger', NULL, $result['message']);
            return;
        }
    }

    public function fastbet(){
        $isLogged = $this->load->get_var('isLogged', FALSE);

        $player_id = NULL;
        $player_name = NULL;
        if($isLogged){
            $player_id = $this->load->get_var('playerId');
            $player_name = $this->load->get_var('username');
        }

        $result = $this->_api->queryForwardFastbet($player_name);

        if($result['success'] === FALSE || empty($result['url'])){
            $this->utils->show_message('danger', NULL, lang('Sorry, no permission'));
            return;
        }

        redirect($result['url']);
    }

    public function annoucement(){
        # GET LOGGED-IN PLAYER
        $playerName = $this->authentication->getUsername();
        $result = $this->_api->queryForwardAnnoucement($playerName);

        if($result['success'] === FALSE || empty($result['url'])){
            $this->utils->show_message('danger', NULL, lang('Sorry, no permission'));
            return;
        }

        redirect($result['url']);
    }

    public function award(){
        $result = $this->_api->queryForwardAward();

        if($result['success'] === FALSE || empty($result['url'])){
            $this->utils->show_message('danger', NULL, lang('Sorry, no permission'));
            return;
        }

        redirect($result['url']);
    }

    public function sdk(){
        $result = $this->_api->queryForwardSDK();

        if($result['success'] === FALSE || empty($result['url'])){
            show_404();
            return;
        }

        redirect($result['url']);
    }

    public function salary(){
        $player_id = $this->load->get_var('playerId');
        $player_name = $this->load->get_var('username');

        $result = $this->_api->queryForwardSalary($player_name, []);

        if($result['success'] && !empty($result['url'])){
            redirect($result['url']);
        }else{
            $this->utils->show_message('danger', NULL, $result['message']);
            return;
        }
    }

}
