<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Affiliate
 *
 * This model represents game api data. It operates the following tables:
 * - game table
 *
 * @author  ASRII
 */

class Gameapi extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/**
	 * save player game profile
	 *
	 * @param  data array
	 *
	 * @return bool
	 */
	public function savePlayerGameProfile($data) {
		$this->db->insert('playergameprofile', $data);
		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * save player deposit details from PT
	 *
	 * @param  data array
	 *
	 * @return bool
	 */
	public function savePlayerTransacDataPT($data) {
		$this->db->insert('ptplayertransactiondetails', $data);
		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * set report
	 *
	 * @param  data array
	 *
	 * @return bool
	 */
	public function setReport($data) {
		$this->db->insert('issuereportptapi', $data);
		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * get pt games
	 *
	 * @param  id
	 *
	 * @return string
	 */
	public function getPTGamesForUser($gameType, $player_level) {
		$where = null;

		if ($gameType != '') {
			$where = "WHERE gameType = '" . $gameType . "'";
		}

		$query1 = $this->db->query("SELECT * FROM cmsgame
            $where
        ");

		$res1 = $query1->result_array();
		$games = array();

		foreach ($res1 as $key => $res1_value) {
			$query2 = $this->db->query("SELECT * FROM cmsgamecategory where cmsGameId = '" . $res1_value['cmsGameId'] . "'");
			$res2 = $query2->result_array();

			if (empty($res2)) {
				array_push($games, $res1_value);
			} else {
				foreach ($res2 as $key => $res2_value) {
					if ($res2_value['rankingLevelSettingId'] == $player_level) {
						array_push($games, $res1_value);
					}
				}
			}
		}

		if ($games > 0) {
			return $games;
		}
		return false;
	}

	/**
	 * set report
	 *
	 * @param  data array
	 *
	 * @return bool
	 */
	public function addAGFileNameReport($data) {
		$this->db->insert('agfilenamereport', $data);
		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * set report
	 *
	 * @param  data array
	 *
	 * @return bool
	 */
	public function addFtpRecord($data) {
		$this->db->insert('agftprecord', $data);
		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * saveGameRecordToDB
	 *
	 * @param  data array
	 *
	 * @return bool
	 */
	public function saveGameRecordToDB($data) {
		$this->db->insert('gameapirecord', $data);
		if ($this->db->affected_rows() == '1') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * get pt games
	 *
	 * @param  id
	 *
	 * @return string
	 */
	public function getPTGames($gameType) {
		$this->db->select('*')->from('cmsgame');
		$this->db->where('status', 'activated');
		if ($gameType != '') {
			$this->db->where('gameType', $gameType);
		}

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$data[] = $row;
			}
			//var_dump($data);exit();
			return $data;
		}
		return false;
	}

	/**
	 * check if record already exists
	 *
	 * @param $fileName  str
	 * @return Bool - TRUE or FALSE
	 */

	public function isAGRecordExists($fileName) {
		$this->db->select('reportName')->where('agftprecord.reportName', $fileName);
		$query = $this->db->get('agftprecord');

		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * check if billno already exists
	 *
	 * @param $fileName  str
	 * @return Bool - TRUE or FALSE
	 */

	public function isBillNoExists($billno) {
		$this->db->select('billNo')->where('agftprecord.billNo', $billno);
		$query = $this->db->get('agftprecord');

		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Will get delete existing record
	 *
	 * @param $transactionId - int
	 * @return array
	 */

	public function deleteAGExistingRecord($reportName) {
		$this->db->where('reportName', $reportName);
		$this->db->delete('agftprecord');
	}

	/**
	 * get game password
	 *
	 * @param  id
	 * @param  id
	 * @return string
	 */
	public function getPlayerPassword($player_id, $api_type) {
		$this->db->select('password')->from('game_provider_auth');
		$this->db->where('game_provider_auth.player_id', $player_id);
		$this->db->where('game_provider_auth.game_provider_id', $api_type);

		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return $query->row();
		}

		return NULL;
	}

	/**
	 * save game provider response result
	 *
	 * @param  data array
	 *
	 * @return id int
	 */
	public function saveGameProviderResponseResult($data) {
		$this->db->insert('response_results', $data);
		return $this->db->insert_id();
	}

}