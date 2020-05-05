<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Kingrich_library {

	# HOST AND ENDPOINTS
	const HOST = 'https://api.globalcomrci.com';
	const ENDPOINT_AUTHORIZE = '/pagcor-auth/v1/authorize';
	const ENDPOINT_GAME_TRANSACTIONS = '/pagcor-api/v1/game-transactions';
	const ENDPOINT_GAME_TRANSACTIONS_AUDIT = '/pagcor-api/v1/game-transactions-audit';

	# AUTHORIZE PARAMETERS
	const RESPONSE_TYPE_CODE = 'code';
	const CLIENT_ID = '55b9633e-93f8-4ca1-861f-67f9874d8656';
	const CLIENT_SECRET = 'faf984e3-61ae-45db-9d65-121dbf0fca8c';

	# SUBMIT GAME RECORDS PARAMETERS
	const OPERATOR_CODE = 'OP00029';
	const PROVIDER_CODE = 'PR00049';

	# GAME TYPE
	const GAME_TYPE_LIVE_CASINO = 'CA';
	const GAME_TYPE_POKER = 'P2P';
	const GAME_TYPE_SLOTS = 'RNG';
	const GAME_TYPE_SPORTSBOOK = 'SB';

	# PLAYER TYPE
	const PLAYER_TYPE_REAL = 'Real';
	const PLAYER_TYPE_TEST = 'Test';

	# OTHERS
	const KEY_LAST_ID = 'test.last_id';
	const KEY_ACCESS_TOKEN = 'test.access_token';
	const LIMIT = 1000;
	const ALL_GAME_TYPE = 'all';
	const MIN_DEBIT_AMOUNT = 0;
	const MIN_CREDIT_AMOUNT = 0;

	private $headers = array('Content-Type: application/json');

	public function __construct() {
		$this->CI =& get_instance();
		$this->kingrich_gametypes = $this->CI->utils->getConfig('kingrich_gametypes_old');
		$this->api_host = $this->CI->utils->getConfig('kingrich_game_api')['api_host'];
		$this->client_key = $this->CI->utils->getConfig('kingrich_game_api')['client_key'];
		$this->secret_key = $this->CI->utils->getConfig('kingrich_game_api')['secret_key'];
	}
	
	public function get_access_token() {

		try {
			
			if ( TRUE) {

				$url_params = array(
					'response_type' => self::RESPONSE_TYPE_CODE,
					'client_id' => $this->client_key,
					'client_secret' => $this->secret_key,
				);


				$url  = $this->api_host . self::ENDPOINT_AUTHORIZE;
				$url .= '?' . http_build_query($url_params);
				
				$response = $this->curl($url, TRUE);

				$response_data = json_decode($response, TRUE);
				$this->CI->utils->info_log('<<====== Kingrich_library get_access_token response ======>>', $response_data);
				if ( ! isset($response_data['access_token'], $response_data['token_type'], $response_data['expires_in'])) {
					throw new Exception("Error Processing Request: " . $response, 1);
				}

				$access_token = $response_data['access_token'];
				$token_type = $response_data['token_type'];
				$expires_in = $response_data['expires_in'];

				$this->CI->cache->save(self::KEY_ACCESS_TOKEN, $access_token, $expires_in);
			}

			return $access_token;

		} catch (Exception $e) {
			$this->CI->utils->error_log($e->getMessage());
			echo "<pre>";
			print_r ($e->getMessage());
			echo "</pre>";
			return FALSE;
		}

    }

    public function submit_game_records($game_transactions) {
    	$response_data = false;
    	
    	if(!empty($game_transactions)){
    		$this->headers[] = 'Authorization: Bearer ' . $this->get_access_token();

			$url = $this->api_host . self::ENDPOINT_GAME_TRANSACTIONS;

			$this->CI->utils->info_log('<<====== Kingrich_library submit_game_records url ======>>', $url);
			
			$params = array(
				'provider_code' 	=> self::PROVIDER_CODE,
				'operator_code' 	=> self::OPERATOR_CODE,
				'aggregator_code' 	=> '',
				'game_transactions' => $game_transactions,
			);

			$this->CI->utils->debug_log('<<====== Kingrich_library submit_game_records url params  ======>>', $params);

	    	$response = $this->curl($url, $params);
	    	
	    	$response_data = json_decode($response, TRUE);
			$this->CI->utils->info_log('<<====== Kingrich_library submit_game_records response ======>>', $response_data);
    	}
    	
    	return $response_data;
    }

    public function get_game_transactions_list($status = NULL) {

    	$access_token = $this->get_access_token();

    	if ( ! empty($access_token)) {

	    	$this->headers[] = 'Authorization: Bearer ' . $access_token;

			$url = $this->api_host . self::ENDPOINT_GAME_TRANSACTIONS_AUDIT;

			if ($status) {
				$url .= '?' . http_build_query(array('status' => $status));
			}

	    	$response = $this->curl($url);

	    	$response_data = json_decode($response, TRUE);

	    	echo "<pre>";
	    	print_r ($url);
	    	echo "</pre>";

	    	echo "<pre>";
	    	print_r ($this->headers);
	    	echo "</pre>";

	    	echo "<pre>";
	    	print_r ($response_data);
	    	echo "</pre>";

	    	# TODO: What to do with these data?
	    	# foreach ($response_data['batch_info'] as $batch_info) {
	    	# 	$batch_transaction_id = $batch_info['id']; # 141e69f0-3112-11e8-9acb-33e42a3b6698
	    	# 	$status = $batch_info['status']; # posted
	    	# 	$created_date = $batch_info['createdDate']; # 2018-03-27T00:28:55.219
	    	# }

    	}

    }

    public function get_game_type($game_tag_id, $game_platform_id) {
    	
    	if($this->CI->utils->getConfig('enable_kingrich_gametypes_new')){
    		if( !empty( $game_tag_id ) && !empty( $game_platform_id ) ){
				$kingrich_gametypes = $this->CI->utils->getConfig('kingrich_gametypes');
				if(!empty($kingrich_gametypes)) {
					foreach ( $kingrich_gametypes as $key => $value ) {
						if( isset($value['tag_id']) ){
							if( !empty($value['tag_id']) ){
								foreach ( $value['tag_id'] as $tag_key => $tag_value ) {
									if( !empty($tag_value) ) {
										if($game_platform_id == $tag_key){
											if( in_array( $game_tag_id, $tag_value ) ){
												return $key;
											}
										}
									}
								}
							}
						}
					}
				}
	    	}
    	} else {
    	//OLD
			$kingrich_gametypes = $this->kingrich_gametypes;
			if(!empty($kingrich_gametypes)) {
				foreach ($kingrich_gametypes as $key => $value) {
					if(isset($value['tag_id'])){
						if(!empty($value['tag_id'])){
							if(in_array($game_tag_id, $value['tag_id'])){
								return $key;
							}
						}
					}
				}
			}
    	}

    	return null;

    }

	private function curl($url, $params = null) {

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		if (isset($params)) {
			curl_setopt($ch, CURLOPT_POST, TRUE);
			if (is_array($params)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
			}
		}
		
		if ( ! empty($this->headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		}

		if ($call_socks5_proxy = @$this->CI->utils->getConfig('kingrich_game_api')['call_socks5_proxy']) {
	        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
	        curl_setopt($ch, CURLOPT_PROXY, $call_socks5_proxy);
		}

		$response =  curl_exec($ch);

		curl_close($ch);
		
		return $response;

	}

}