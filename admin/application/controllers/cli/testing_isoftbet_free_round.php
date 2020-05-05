<?php
echo "<pre>";
require_once dirname(__FILE__) . '/base_testing.php';

class Testing_isoftbet_free_round extends BaseTesting {

	public function init() {
        $this->load->library('isoftbet_free_round');

    }

    public function testAll() {

        $this->init();

        // $this->freerounds();
        $this->freerounds_create();
        // $this->freerounds_cancel();
        // $this->freerounds_activate();
        // $this->players();
        // $this->players_register();
        // $this->players_remove();
        // $this->currencies();
        // $this->currencies_add();
        // $this->currencies_remove();
        exit;

    }

    public function freerounds() {

        $state = 'ACTIVE';
        $operator_id = 0;

    	print_r($this->isoftbet_free_round->freerounds(null, null, $operator_id, 8));
    }

    public function freerounds_create() {

        $name = 'testing';
        $operator_id = "0";   
        $games = "1607";     
        $lines = "15";          
        $line_bet = "1";          
        $supplier = "1";          
        $player_ids = array("testjl", "testgizmo");          
        $limit_per_player = "3";          
        $promo_code = "TEST_PROMO";   
        $max_players = "2";    
        $coins = array(
            "1607" => array(
                array(
                    "currency" => "CNY",
                    "coin_value" => "0.07"
                )
            )
        );
        $open_for_all = 0;
        $start_date = '2017-02-30 16:21:00';
        $end_date = '2017-03-04 14:00:00';
        $duration_relative = 1;

    	print_r($this->isoftbet_free_round->freerounds_create( $name, $operator_id, $games, $supplier, $lines, $line_bet, $player_ids, $limit_per_player, $promo_code, $max_players, $start_date, $end_date, $duration_relative, $coins, $open_for_all ));
    }

    public function freerounds_cancel() {
        echo "<pre>";

        $fround_id = 37;
        $reason = "test";


    	print_r($this->isoftbet_free_round->freerounds_cancel( $fround_id, $reason ));
    }

    public function freerounds_activate() {
        
        echo "<pre>";
        $fround_id = 100;
    	print_r($this->isoftbet_free_round->freerounds_activate($fround_id));

    }

    public function players() {
        
    	echo "<pre>";
    	print_r($this->isoftbet_free_round->players());

    }

    public function players_register() {
        
    	echo "<pre>";
    	print_r($this->isoftbet_free_round->players_register());

    }

    public function players_remove() {
        
    	echo "<pre>";
    	print_r($this->isoftbet_free_round->players_remove());

    }

    public function currencies() {
        
    	echo "<pre>";
    	print_r($this->isoftbet_free_round->currencies());

    }

    public function currencies_add() {
        
    	echo "<pre>";

        $coins = array(
            'coin_value' => "10",
            'currency' => "CNY",
            'game_id' => "1607",
        );

    	print_r($this->isoftbet_free_round->currencies_add(10, $coins));

    }

    public function currencies_remove() {
        
    	echo "<pre>";
    	print_r($this->isoftbet_free_round->currencies_remove());

    }

}