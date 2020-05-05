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
class Lequ_model extends BaseModel {


	public function __construct() {
		parent::__construct();

	}

	const REGISTERED_BY_IMPORTER = 'importer';


	public function importPlayer($csv_row,$group_level_model,$wallet_model,$player_model,$levelId,$username,$password,$balance,$details = null,$createdOn,$failCount,$rltPlayer) {

		$this->load->library(array('salt'));

		if (empty($username)) {
			$failCount++;
			$csv_row['reason'] = 'Empty username';
			array_push($rltPlayer['failed_list'], $csv_row);
			return false;
		}

		$this->db->select('playerId')->from('player')->where('username', $username);
		$playerId = $this->runOneRowOneField('playerId');

		$password = empty($password) ? '' : $this->salt->encrypt($password, $this->getDeskeyOG());

		# Basic player fields
		$data = array(
			'username' => $username,
			'gameName' => $username,
			//'password' => empty($password) ? '' : $this->salt->encrypt($password, $this->getDeskeyOG()),
			'password' => $password,
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

		# Playerdetails fields
		$this->db->select('playerDetailsId')->from('playerdetails')->where('playerId', $playerId);
		$playerDetailsId = $this->runOneRowOneField('playerDetailsId');
		$data = $details;
		$data['playerId'] = $playerId;
		if (!empty($playerDetailsId)) {
			$this->db->set($data)->where('playerDetailsId', $playerDetailsId)->update('playerdetails');
		} else {
			$this->db->set($data)->insert('playerdetails');
		}

		$currency = $player_model->getActiveCurrencyCode();
		$wallet_model->syncAllWallet($playerId, $balance, $currency);
		$wallet_model->moveAllToRealOnMainWallet($playerId);

		return $playerId;
	}



}


