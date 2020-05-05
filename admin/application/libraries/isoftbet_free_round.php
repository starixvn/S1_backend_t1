<?php

class Isoftbet_free_round {
	
	protected $api_url = '';
	protected $licensee = '';
	protected $secret_key = '';

	public $params = array();

	public function __construct(){

		$this->CI = &get_instance();

		$this->api_url = $this->CI->config->item('isoftbet_api_url');
		$this->licensee = $this->CI->config->item('isoftbet_api_licensee');
		$this->secret_key = $this->CI->config->item('isoftbet_api_secret_key');

		$this->params['auth'] = array(
			'licensee' => $this->licensee,
		);

	}
	
	public function freerounds( $state = 'ACTIVE', $player_id = '', $operator_id = '', $skin_id = '' ){

		$params = array();

		if( ! empty($state) ) $params['state'] = $state;
		if( ! empty($player_id) ) $params['player_id'] = $player_id;
		if( ! empty($operator_id) ) $params['operator_id'] = $operator_id;
		if( ! empty($skin_id) ) $params['skin_id'] = $skin_id;

		return $this->do_request( __FUNCTION__, $params );

	}

	public function freerounds_create( $name = '', $operator_id = '', $games = array(), $supplier = '', $lines = array(), $line_bet = array(), $player_ids = array(), $limit_per_player = '', $promo_code = '', $max_players = '', $start_date = '', $end_date = '', $duration_relative = '', $coins = array(), $open_for_all){

		$params = array();
		$params['freeround'] = array(
			"name" => $name,          
			"operator_id" => $operator_id,      
			"games" => $games,  
			"lines" => $lines,          
			"line_bet" => $line_bet,
			"limit_per_player" => $limit_per_player,          
		);

		if( ! empty( $supplier ) ) $params['freeround']['supplier'] = $supplier;
		if( ! empty( $player_ids ) ) $params['freeround']['player_ids'] = $player_ids;
		if( ! empty( $promo_code ) ) $params['freeround']['promo_code'] = $promo_code;
		if( ! empty( $max_players  ) ) $params['freeround']['max_players '] = $max_players;
		if( ! empty( $start_date ) ) $params['freeround']['start_date'] = $start_date;
		if( ! empty( $end_date ) ) $params['freeround']['end_date'] = $end_date;
		if( ! empty( $duration_relative ) ) $params['freeround']['duration_relative'] = $duration_relative;
		if( ! empty( $coins ) ) $params['freeround']['coins'] = $coins;
		
		$params['freeround']['open_for_all'] = 0;
		
		return $this->do_request( __FUNCTION__, $params );

	}

	public function freerounds_cancel( $fround_id = '', $reason = '' ){

		$params = array(
			"fround_id" => $fround_id,        
			"reason" => $reason 
		);

		return $this->do_request( __FUNCTION__, $params );

	}

	public function freerounds_activate( $fround_id = '' ){
		
		$params = array(
			"fround_id" => $fround_id
		);

		return $this->do_request( __FUNCTION__, $params );

	}

	public function players( $fround_id = '' ){

		$params = array(
			"fround_id" => $fround_id
		);

		return $this->do_request( __FUNCTION__, $params );

	}

	public function players_register( $fround_id = '', $player_ids = array(), $promo_code = '' ){

		$params = array(
			"fround_id" => $fround_id,
			"player_ids" => $player_ids,
		);

		if( ! empty( $promo_code ) ) $params["promo_code"] = $promo_code;

		return $this->do_request( __FUNCTION__, $params );

	}

	public function players_remove( $fround_id = '', $player_ids = array() ){

		$params = array(
			"fround_id" => $fround_id,
			"player_ids" => $player_ids
		);

		return $this->do_request( __FUNCTION__, $params );

	}

	public function currencies( $fround_id = '' ){

		$params = array(
			"fround_id" => $fround_id
		);

		return $this->do_request( __FUNCTION__, $params );

	}

	public function currencies_add( $fround_id = '', $coins = array() ){

		$params = array(
			"fround_id" => $fround_id,
			"coins" => $coins
		);

		return $this->do_request( __FUNCTION__, $params );

	}

	public function currencies_remove( $fround_id = '', $coins = array() ){

		$params = array(
			"fround_id" => $fround_id,
			"coins" => $coins
		);

		return $this->do_request( __FUNCTION__, $params );

	}

	private function do_request( $api_uri = '', $data = array() ){

		$signature = $api_uri . '?' . http_build_query($data);
		$signature = hash_hmac('sha256', $signature, $this->secret_key); 

		$this->params['auth']['signature'] = $signature;
		$this->params['request'] = $data;

		$data_string = json_encode( $this->params, JSON_PRETTY_PRINT );	
		// echo "<pre>";
		// print_r($data_string);
		// exit;
		$target_url = $this->api_url . $api_uri;
		
		$ch = curl_init( $target_url ); 

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                              
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                               
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                      
		    'Content-Type: application/json',  
		    'Content-Length: ' . strlen($data_string))                                                                   
		);                

		$result = json_decode(curl_exec($ch));
		
		return $result;

	}

}