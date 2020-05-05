<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/BaseController.php';

class Ggpoker_service_api extends BaseController {

	public function __construct() {
		parent::__construct();
		$this->load->model(array('wallet_model','game_provider_auth','common_token'));
		$this->game_api = $this->utils->loadExternalSystemLibObject(GGPOKER_GAME_API);

	}
	# TRANSACTION_TYPE
	const TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET = 5;
	const TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET = 6;

	public function cashier(){
		//parameters
		$param_token = filter_input(INPUT_GET,"token",FILTER_SANITIZE_STRING);
		$param_date = filter_input(INPUT_GET,"date",FILTER_SANITIZE_STRING);
		$param_username = filter_input(INPUT_GET,"username",FILTER_SANITIZE_STRING);
		//game info
		$sbe_name = $this->game_api->getPlayerUsernameByGameUsername($param_username);
		$game_name = $this->game_api->getGameUsernameByPlayerUsername($sbe_name);
		$playerInfo =$this->game_api->getPlayerInfoByUsername($sbe_name);
		$secretKey = $this->game_api->secretKey;
		$generated_token = MD5($secretKey.$game_name.strrev($secretKey).$param_date);
		if($generated_token == $param_token){
			$this->load->model(array('common_token'));
			$player_token = $this->common_token->getPlayerToken($playerInfo->playerId);
			$player_url = $this->utils->getSystemUrl('player','/iframe/auth/login_with_token/' . $player_token);
			redirect($player_url, 'refresh');
		}
		else{
			$url = $this->utils->getSystemUrl('player','/iframe/auth/login');
			redirect($url, 'refresh');
		}
	}

	public function register(){
		
		$register_url = $this->utils->getSystemUrl('player','/player_center/iframe_register');
		redirect($register_url, 'refresh');
	}

	public function forgotPassword(){
		$forgotPassword_url = $this->utils->getSystemUrl('player','/player_center/forgotPassword');
		redirect($forgotPassword_url, 'refresh');
	}
}

///END OF FILE////////////