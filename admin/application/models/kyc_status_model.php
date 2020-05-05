<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get kyc Status
 * * Get player kyc Status
 * * Get/insert/update/ player kyc status
 *
 * @category Player KYC Status
 * @version 1.8.10
 * @author Jhunel L. Ebero
 * @copyright 2013-2017 TripleOneTech
 */
class kyc_status_model extends BaseModel {
	const CACHE_TTL = 3600; # 1 hour

	public function __construct() {
		parent::__construct();
		$this->load->model(array('player_model','player_kyc','player','system_feature'));
	}

	protected $tableName = 'kyc_status';

	private function getCacheKey($name) {
        return PRODUCTION_VERSION."|$this->tableName|$name";
    }

    private $playerInfoCache = array();
    private function getPlayerInfo($playerId, $use_cache = true) {
    	if(!array_key_exists($playerId, $this->playerInfoCache) || $use_cache == false) {
    		$this->playerInfoCache[$playerId] = $this->player_model->getPlayerInfoById($playerId);
    	}

		return $this->playerInfoCache[$playerId];
    }

	public function getAllKycStatus() {
		$result = $this->utils->getJsonFromCache($this->getCacheKey('all_kyc_status'));
		if(!empty($result)) {
			return $result;
		}

		$query = $this->db->query("SELECT * FROM kyc_status");
		$result = $query->result_array();

		$this->utils->saveJsonToCache($this->getCacheKey('all_kyc_status'), $result, self::CACHE_TTL);

		return $result;
	}

	public function getKycStatusInfo($kyc_id) {
		$result = $this->utils->getJsonFromCache($this->getCacheKey('kyc_status:'.$kyc_id));
		if(!empty($result)) {
			return $result;
		}

		$this->db->where('id', $kyc_id);
		$query = $this->db->get($this->tableName);
		$result = $query->row_array();

		$this->utils->saveJsonToCache($this->getCacheKey('kyc_status:'.$kyc_id), $result, self::CACHE_TTL);

		return $result;
	}

	public function addUpdateKyc($data){
		$response = false;
		if(!empty($data)){
			if(!empty($data['id'])){
				$this->utils->deleteCache($this->getCacheKey('kyc_status:'.$data['id']));
				$this->utils->deleteCache($this->getCacheKey('all_kyc_status'));
				//var_dump($data['kycId']);die();
				$this->db->where('id', $data['id']);
				$this->db->set($data);
				//var_dump($this->runAnyUpdate($this->tableName));die();
				return $this->runAnyUpdate($this->tableName);
			} else {
				return $this->insertData($this->tableName, $data);
			}
		}
		return $response;
	}

	public function removeKYCDetails($id) {
		if(!empty($id)){
			$this->utils->deleteCache($this->getCacheKey('kyc_status:'.$id));
			$this->utils->deleteCache($this->getCacheKey('all_kyc_status'));

			$this->db->where('id', $id);
			$this->db->delete($this->tableName);
			return $this->db->affected_rows();
		}
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Summitted documment validation
	 *
	 * details : Check if the uploaded documents match the information to the system, such as: register name and proof of address
	 *
	 * @param int $player_id	player_id
	 */
	public function player_valid_identity_and_proof_of_address($playerId) {
		$verified = $this->get_verification_info($playerId);
		$response = FALSE;
		if(!empty($verified)){
			if(isset($verified[self::Verification_Adress]['verified'])){
				$address = $verified[self::Verification_Adress]['verified'];
				if(isset($address['status']) && isset($address['auto_status'])){
					$response = ($address['status'] || $address['auto_status']) ? TRUE : FALSE ;
				}
			}
		}
		return $response;
	}

	public function get_verification_info($playerId){
		$this->set_default_value_proof_filename($playerId);
		$playerInfo = $this->getPlayerInfo($playerId);
		$data = array();
		if(!empty($playerInfo)){
			if(isset($playerInfo['proof_filename'])){
				$proofFilename = json_decode($playerInfo['proof_filename'],true);
				if(!empty($proofFilename)){
					if(isset($proofFilename['verification'])){
						$data = $proofFilename['verification'];
					}
				}
			}
		}

		return $data;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Set default value of proof filename
	 *
	 * details : Set default value of proof filename if it's null or empty
	 *
	 * @param int $player_id	player_id
	 */
	public function set_default_value_proof_filename($playerId) {
		$playerInfo = $this->getPlayerInfo($playerId);
		if(!isset($playerInfo['proof_filename'])){
			$proofFilename = json_decode($playerInfo['proof_filename'],true);
			if(empty($proofFilename)){
				$data = array(
					'proof_filename' => json_encode(array(
						'verification' => array(),
						'profile_image' => array(),
					)),
				);

				$this->player->editPlayerDetails($data, $playerId);
			}
		}
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Update Verification data
	 *
	 * details : Update Verification data , saving by data tags
	 *
	 * @param int $player_id	player_id
	 */
	public function update_verification_data($playerId,$verificationData){
		$response = null;
		if(!empty($verificationData)){
			$playerInfo = $this->getPlayerInfo($playerId);
			if(isset($playerInfo['proof_filename'])){
				$proofFilename = json_decode($playerInfo['proof_filename'],true);
				if(!empty($proofFilename)){
					if(isset($proofFilename['verification'])){
						if(!empty($proofFilename['verification'])) {
							foreach ($verificationData as $key => $value) {
								$proofFilename['verification'][$key] = $value;
								$verificationData = $proofFilename['verification'];
							}
						}
						$data = array(
							'proof_filename' => json_encode(array(
								'verification' => $proofFilename['verification'] = $verificationData,
								'profile_image' => $proofFilename['profile_image']
							)),
						);

						$this->player->editPlayerDetails($data, $playerId);
					}
				}
			}
		}
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Update Verification data
	 *
	 * details : Update Verification data , saving by data tags
	 *
	 * @param int $player_id	player_id
	 */
	public function getVerificationData($playerId,$tag = null, $use_cache = true) {
		$response = null;
		if(!empty($playerId)) {
			$playerInfo = $this->getPlayerInfo($playerId, $use_cache);
			if(isset($playerInfo['proof_filename'])){
				$proofFilename = json_decode($playerInfo['proof_filename'],true);
				if(!empty($proofFilename)){
					if(isset($proofFilename['verification'])){
						if(!empty($proofFilename['verification'])){
							$response = $proofFilename['verification'];
						}
					}
				}
			}
		}

		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Check if playe no attach documents uploaded
	 *
	 * details : Check if playe no attach documents uploaded
	 *
	 * @param int $playerId	player_id
	 */
	public function checkNoAttachement($playerId) {

		$verified = $this->get_verification_info($playerId);
		$this->load->model('player_attached_proof_file_model');
		
		if(empty($verified)){
			$this->player_attached_proof_file_model->getPlayerAttachmentInfoList($playerId);
			$verified = $this->get_verification_info($playerId);
		}

		$response = TRUE;
		$photoAttached = $this->player_attached_proof_file_model->getAttachementRecordInfo($playerId,null,self::Verification_Photo_ID);

		if(!empty($photoAttached)){
			if(!empty($verified)){
				if(isset($verified['photo_id']['no_attach'])){
					$photo_id = $verified['photo_id']['no_attach'];
					if(isset($photo_id['status']) && isset($photo_id['auto_status'])){
						$response = ($photo_id['status'] || $photo_id['auto_status']) ? TRUE : FALSE;
					}
				}
			}
		}

		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Check if player is depositor or non depositor
	 *
	 * details : Check if player is depositor or non depositor . Only approved deposit transaction only
	 *
	 * @param int $playerId	player_id
	 */
	public function player_depositor($playerId) {
		$depositor = $this->transactions->getTransactionCount(array(
				'to_id' => $playerId,
				'to_type' => Transactions::PLAYER,
				'transaction_type' => Transactions::DEPOSIT,
				'status' => Transactions::APPROVED,
			));
		return ($depositor) ? TRUE : FALSE ;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Account name verification
	 *
	 * details : Check if player account name use in deposit is simmilar to registered real name in the system
	 *
	 * @param int $playerId	player_id
	 */
	public function player_identity_verification($playerId) {
		// check identity verification to latest deposit
		$depositsTrasactionList = $this->transactions->getPlayerDepositsTrasactionList($playerId);
		$depositsTrasaction = array();
		if(!empty($depositsTrasactionList)){
			if(isset($depositsTrasactionList['account_name'])){
				$depositsTrasaction = $depositsTrasactionList['account_name'];
			}
		}
		$playerInfo = $this->getPlayerInfo($playerId);
		$proof_filename = json_decode($playerInfo['proof_filename'],true);

		if(!empty($depositsTrasaction)) {
			if($playerInfo['firstName'] == $depositsTrasaction[(count($depositsTrasaction)-1)] && !empty($playerInfo['firstName']) && !empty($depositsTrasaction[(count($depositsTrasaction)-1)])) {
				$data = [
						"photo_id" => [
							"system_verified" => [
								"status" => self::TRUE,
								"auto_status" => self::TRUE,
								"comments" => (!empty($comments)) ? $comments : null
							]
						],
					];

				$this->update_verification_data($playerId,$data);
			}
		}

		$verified = $this->get_verification_info($playerId);
		$response = FALSE;
		if(!empty($verified)){
			if(isset($verified['photo_id']['verified'])){
				$photo_id = $verified['photo_id']['verified'];
				if(isset($photo_id['status']) && isset($photo_id['auto_status'])){
					$response = ($photo_id['status'] || $photo_id['auto_status']) ? TRUE : FALSE ;
				}
			}
		}

		return $response;

	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Check if uploaded photo id is valid
	 *
	 * details : Check if uploaded photo id documents
	 *
	 * @param int $playerId	player_id
	 */
	public function player_valid_documents($playerId) {
		$verified = $this->get_verification_info($playerId);
		$response = FALSE;
		if(!empty($verified)){
			if(isset($verified[self::Verification_Photo_ID]['verified'])){
				$photo_id = $verified[self::Verification_Photo_ID]['verified'];
				if(isset($photo_id['status']) && isset($photo_id['auto_status'])){
					$response = ($photo_id['status'] || $photo_id['auto_status']) ? TRUE : FALSE ;
				}
			}
		}

		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Check if uploaded proof of income is valid
	 *
	 * details : Check if uploaded proof of income is valid documents
	 *
	 * @param int $playerId	player_id
	 */
	public function player_valid_proof_of_income($playerId) {
		$verified = $this->get_verification_info($playerId);
		$response = FALSE;
		if(!empty($verified)){
			if(isset($verified[self::Verification_Income]['verified'])){
				$income = $verified[self::Verification_Income]['verified'];
				if(isset($income['status']) && isset($income['auto_status'])){
					$response = ($income['status'] || $income['auto_status']) ? TRUE : FALSE ;
				}
			}
		}

		return $response;
	}

		/**
	 * @author Jhunel L. Ebero
	 * overview : Check if uploaded proof of deposit/withdrawal is valid
	 *
	 * details : Check if uploaded proof of deposit/withdrawal is valid documents
	 *
	 * @param int $playerId	player_id
	 */
	public function player_valid_proof_of_deposit_withdrawal($playerId) {
		$verified = $this->get_verification_info($playerId);
		$response = FALSE;
		if(!empty($verified)){
			if(isset($verified[self::Verification_Deposit_Withrawal]['verified'])){
				$income = $verified[self::Verification_Deposit_Withrawal]['verified'];
				if(isset($income['status']) && isset($income['auto_status'])){
					$response = ($income['status'] || $income['auto_status']) ? TRUE : FALSE ;
				}
			}
		}

		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Check if verification info of image by tagging
	 *
	 * details : Check if verification info of image by tagging
	 *
	 * @param int $playerId	player_id
	 * @param string $verificationType
	 */
	public function get_verification_info_by_tag($playerId,$verificationType){
		$verified = $this->get_verification_info($playerId);
		$response = null;
		if(!empty($verified)){
			if(isset($verified[$verificationType])){
				$info = $verified[$verificationType];
				$response = $info ;
			}
		}
		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Generate and update latest Kyc status by player
	 *
	 * details : Generate and update latest kyc status by checking the criteria of Kyc.
	 *
	 * @param int $player_id	player_id
	 * @param int $auto_generated true=1 / false=0
	 * @param int $set_current_status //0 current status if autogenerated generated
	 */
	public function generate_player_kyc_status($playerId,$auto_generated = TRUE,$set_current_status = 0,$criteria_refresh = FALSE){
		$player_kyc = array();
		$kyc_status = $this->getAllKycStatus();
		$new_status = null;
		/*
		===========target_function
		* player_depositor = check if player is depositor
		* player_identity_verification = check if the player name provide in the system is match to document provided
		* player_valid_documents = Provide valid documents
		* player_valid_identity_and_proof_of_address = Provided document is match the address to the system
		* player_valid_proof_of_income = Provided document is valid proof of income or source of wealth
		*/
		if(!empty($kyc_status)){
			foreach ($kyc_status as $key => $value) {
				$status = false;
				if(isset($value['target_function'])){
					switch ($value['target_function']) {
						case self::player_no_attached_document:
							$status = $this->checkNoAttachement($playerId);
							break;
						case self::player_depositor:
							$status = $this->player_depositor($playerId);
							break;
						case self::player_identity_verification:
							$status = $this->player_identity_verification($playerId);
							break;
						case self::player_valid_documents:
							$status = $this->player_valid_documents($playerId);
							break;
						case self::player_valid_identity_and_proof_of_address:
							$status = $this->player_valid_identity_and_proof_of_address($playerId);
							break;
						case self::player_valid_proof_of_income:
							$status = $this->player_valid_proof_of_income($playerId);
							break;
						default:
							break;
					}
				}

				$player_kyc[$value['id']] = array(
					'kyc_id' => $value['id'],
					'status' => $status,
				);

			}
		}

//echo "<pre>";print_r($player_kyc);die();
		if(!empty($player_kyc)){
			if($auto_generated) {
				foreach ($player_kyc as $key => $value) {
					if ( $value['status'] || $key == self::R1) {
						$player_kyc[$key]['current_kyc_status'] = TRUE;
						for ($x = 1 ; $x < $key ; $x++) {
							$player_kyc[$x]['current_kyc_status'] = FALSE;
						}
						$new_status = $key;
					} else {
						$player_kyc[$key]['current_kyc_status'] = FALSE;
					}
				}

			} else {
				foreach ($player_kyc as $key => $value) {
					if($key == $set_current_status) {
						$player_kyc[$key]['current_kyc_status'] = TRUE;
						$new_status = $key;
					} else {
						$player_kyc[$key]['current_kyc_status'] = FALSE;
					}
				}
			}
		}


		$update = $this->player_kyc->savePlayerKycStatus($playerId,json_encode($player_kyc),$auto_generated,$new_status,$criteria_refresh);

		return $update;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Update kyc status by player
	 *
	 * details : Update kyc status by player via auto system generating
	 *
	 * @param int $player_id	player_id
	 */
	public function automatic_player_kyc_status($playerId){
		if($this->system_feature->isEnabledFeature('show_kyc_status')) {
			$this->load->model('player_kyc');

			$kycStatus=$this->player_kyc->getPlayerCurrentKycStatus($playerId);

			if(!empty($kycStatus)){
				if (isset($kycStatus['rate_code'])) {
					$action_response = $this->generate_player_kyc_status($playerId);
					$response = ($action_response) ? array( 'status' => "success",'message' => lang("Update Successful")) : array( 'status' => "fail",'message' => lang("Update Failed"));
						return $response;
				} else {
					$this->generate_player_kyc_status($playerId);
				}
			} else {
				$this->generate_player_kyc_status($playerId);
			}
		}
	}


	public function getPlayerCurrentStatus($playerId) {
		$response = null;

		if($this->system_feature->isEnabledFeature('show_kyc_status')) {
			$player_kyc_status = $this->player_kyc->getPlayerKycStatus($playerId);

			if ( isset($player_kyc_status[0]['auto_generated']) ) {
				if ( $player_kyc_status[0]['auto_generated'] || empty($player_kyc_status) ) {
					$this->automatic_player_kyc_status($playerId);
				}
			} else {
				$this->automatic_player_kyc_status($playerId);
			}
			$currentKycStatus = $this->player_kyc->getPlayerCurrentKycStatus($playerId);
			if(isset($currentKycStatus['rate_code'])){
				$response = $currentKycStatus['rate_code'];
			}
		}

		return $response;
	}

	public function displayVerification($proof_file_name, $verification_type){
		$verification =$this->getVerificationInfoByTag($proof_file_name, $verification_type); 

		$verification_description = $this->config->item('verification');
		if(!$verification) return lang('lang.norecyet');

		$remarks = [
			self::Remark_No_Attach      => ['class' => 'text-secondary'],
			self::Remark_Wrong_attach   => ['class' => 'text-danger'],
			self::Remark_Verified 		=> ['class' => 'text-success'],
			self::Remark_Not_Verified   => ['class' => 'text-warning'],
		];

		foreach ($remarks as $key => $remark) {
			if(isset($verification[$key])){
				return "<span class=".$remark['class'].">" .$verification_description[$key]['description']. "</span>";
				break;
			}
		}

		return lang('lang.norecyet');
	}

	public function getVerificationInfoByTag($proof_file_name, $verification_type){
		$verification_list = json_decode($proof_file_name,true);
		if(!empty($verification_list['verification'][$verification_type])){
			return $verification_list['verification'][$verification_type];
		}
		return false;
	}

}
