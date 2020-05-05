<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2018-2018 TripleOneTech
 */
class NewRainbow_model extends BaseModel {


	public function __construct() {
		parent::__construct();
        
	}
    
    const REGISTERED_BY_IMPORTER = 'importer';
	
	/**
	 * Import newrainbow player from external source.
	 * Updated 20181011 for function import OLE
	 * @param model.    group_level_model
	 * @param int		$levelId
	 * @param string	$username
	 * @param string	$password
	 * @param string	$createdOn
	 * @return int
	 */
	public function importPlayer($group_level_model, $levelId, $username, $password,$createdOn) {

		$this->load->library(array('salt'));

		if (empty($username)) {
			$message = "Empty username: [$username]";
			return false;
		}
		if(empty($password)){
			$password='';
		}

		# Basic player fields
		$data = array(
			'username' => $username,
			'gameName' => $username,
			'password' => empty($password) ? '' : $this->salt->encrypt($password, $this->getDeskeyOG()),
			'active' => Player_model::OLD_STATUS_ACTIVE,
			'blocked' => Player_model::DB_FALSE,
			'status' => Player_model::OLD_STATUS_ACTIVE,
			'registered_by' => Player_model::REGISTERED_BY_WEBSITE,
			'enabled_withdrawal' => Player_model::DB_TRUE,
			'levelId' => $levelId,
			'codepass' => $password,
			'createdOn' => $createdOn,
			'updatedOn' => $this->utils->getTodayForMysql()
		);
		$this->db->select('playerId')->from('player')->where('username', $username);
		$playerId = $this->runOneRowOneField('playerId');
		
		$exists=false;
		# Create / Update the player record
		if (!empty($playerId)) {
			$exists=true;
			$this->db->set($data)->where('playerId', $playerId)->update('player');
		} else {
			$exists=false;
			$this->db->set($data)->insert('player');
			$playerId = $this->db->insert_id();
		}

		# Player level
		$group_level_model->adjustPlayerLevel($playerId, $levelId);

		return $playerId;
	}



}


