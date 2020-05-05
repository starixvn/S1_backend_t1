<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Game_API_functions
 *
 * Game_API_functions library for FHI OG System
 *
 * @package		Game_API_functions
 * @author		ASRII
 * @version		1.0.0
 */

class Api_functions {
	function __construct() {
		 $this->ci =& get_instance();
		 $this->ci->load->library(array('session'));
		 $this->ci->load->model(array('gameapi'));
	}

	/**
     * save player game profile
     *
     * @return
     */
	function savePlayerGameProfile($data) {
        $this->ci->gameapi->savePlayerGameProfile($data);
        //save pw to player table
	}

    /**
     * save player game profile
     *
     * @return
     */
    function savePlayerTransacDataPT($data) {
        return $this->ci->gameapi->savePlayerTransacDataPT($data);
    }


    /**
     * save game provider response result
     *
     * @param  data array
     * @return int
     */
    function saveGameProviderResponseResult($data) {
        return $this->ci->gameapi->saveGameProviderResponseResult($data);
    }

	/**
     * set report
     *
     * @return
     */
	function setReport($data) {
        $this->ci->gameapi->setReport($data);
        //save pw to player table
	}

	/**
     * get pt games
     *
     * @return
     */
	function getPTGames($gameType) {
        return $this->ci->gameapi->getPTGames($gameType);
        //save pw to player table
	}

    /**
     * get pt games
     *
     * @return
     */
    function getPTGamesForUser($gameType, $player_level) {
        return $this->ci->gameapi->getPTGamesForUser($gameType, $player_level);
        //save pw to player table
    }
}