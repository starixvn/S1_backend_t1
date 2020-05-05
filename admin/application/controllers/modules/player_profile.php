<?php

const PROFILE_PICTURE_PATH = '/player/profile_picture';

trait player_profile {
	/**
	 * Upload Profile Picture
	 * @author Hayme.php 2017-05-06
	 *
	 * @param 	none
	 * @return	void
	 */

	public function uploadProfilePicture() {
		$profilePicture = $this->input->post('profileToUpload');
		$image = isset($_FILES['profileToUpload']) ? $_FILES['profileToUpload'] : null;

		//new method, jhunel 1-9-2017
		$this->load->model(array('player_attached_proof_file_model'));
		$player_id = $this->authentication->getPlayerId();
		$input = array(
			'player_id' => $player_id,
			'tag' => player_attached_proof_file_model::PROFILE_PICTURE,
		);

		$data = [
			'input' => $input,
			'image' => isset($_FILES['profileToUpload']) ? $_FILES['profileToUpload'] : null
		];

		$response = $this->player_attached_proof_file_model->upload_proof_document($data);

		if(!empty($response)){
			if($response['status'] == "success"){
				return array('status' => 'success');
			} else {
				return array('status' => 'error', 'msg' => $response['msg']);
			}
		}

		return false;
		//end new function

		//old process
		/*$fullpath = $this->getProfilePictureFullPath();


		$config = array(
            'allowed_types' => "jpg|jpeg|png|gif",
            'max_size'      => $this->utils->getMaxUploadSizeByte(),
            'overwrite'     => true,
            'remove_spaces' => true,
            'upload_path'   => $fullpath,
        );

		if(!$profilePicture){
			if (!empty($image)) {
		        $this->load->library('multiple_image_uploader');
				$response = $this->multiple_image_uploader->do_multiple_uploads($image, $fullpath, $config);


				if (strtolower($response['status']) == "success") {
					$this->updateProfilePictureToDB($response['filename'][0], $fullpath, true);
					return array('status' => 'success');
				}

				if(strtolower($response['status']) == "fail"){
					return array('status' => 'error', 'msg' => $response['message']);
				}
			}
		}

		return false;*/
	}

	public function getProfilePicture() {
		$player_id = $this->authentication->getPlayerId();
		$this->load->model(['player']);
		return $this->player->getPlayerProfilePictureFileName($player_id);
	}

	public function hasUploadedProfilePicture() {
		$profile_filename = $this->getActiveProfilePicture();
		$file_loc = $this->getProfilePictureFullPath() . '/'. $profile_filename;
		if($this->CI->agent->is_mobile()){
			$file_loc = str_replace('/mobile', '', $file_loc);
		}

		if (!isset($profile_filename) || empty($profile_filename) || !file_exists($file_loc)) { return false; }

		return true;
	}

	public function setProfilePicture() {
		if ($this->hasUploadedProfilePicture()) {
			return base_url().str_replace('/mobile', '',$this->getProfilePictureUploadPath()) . '/' . $this->getActiveProfilePicture();
		} else {
			if($this->CI->agent->is_mobile()){
				return site_url($this->utils->getPlayerCenterTemplate().'/images/user_icon.svg');
			} else {
				return base_url().$this->utils->getPlayerCenterTemplate() . '/img/default-profile.png';

			}
		}
	}

	/**
	 * @author Hayme.php 2017-05-09
	 * Overview : Update profile picture json data
	 * @param 	String      filename
	 * @param 	String      file_path
	 * @param 	Bool        active
	 * @return	void
	 */
	public function updateProfilePictureToDB($fileName, $filePath, $active = false) {
		$player_id = $this->authentication->getPlayerId();

		// Create initial setup if proof_filename is empty
		$this->set_default_value_proof_filename($player_id);
		$this->load->model(array('player_model'));

		// Set all profile to inactive
		$this->setAllProfileToInactive();
		//
		$result = $this->player_model->getPlayerInfoDetailById($player_id)['proof_filename'];

		$player_data = json_decode($result, true);
		$arrProfilePicture = array($fileName => array("active"=> $active, "path" => $filePath));

		if (!empty($player_data['profile_image'])) {
			$player_data['profile_image'] = $player_data['profile_image'] + $arrProfilePicture;
		} else {
			$player_data['profile_image'] = $arrProfilePicture;
		}

		$proof_filename = array('proof_filename' => json_encode($player_data));
		$this->player_functions->editPlayerDetails($proof_filename, $player_id);
	}

	/**
	 * @author Hayme.php 2017-05-09
	 * Overview : Set all active profile to inactive
	 * @param 	none
	 * @return	void
	 */
	public function setAllProfileToInactive() {
		$player_id = $this->authentication->getPlayerId();
		// Check player proof_filename
		$this->load->model(array('player_model'));
		$player_data = json_decode($this->player_model->getPlayerInfoDetailById($player_id)['proof_filename'], true);

		// Get active profile picture file name
		foreach ($player_data['profile_image'] as $key => $values) {
			if ($player_data['profile_image'][$key]['active']) {
				$player_data['profile_image'][$key]['active'] = false;
			}
		}

		$proof_filename = array('proof_filename' => json_encode($player_data));
		$this->player_functions->editPlayerDetails($proof_filename, $player_id);
	}

	/**
	 * @author Hayme.php 2017-05-09
	 * Overview : Get player current profile picture setup on DB
	 * @param 	String      playerId
	 * @return	void
	 */
	public function getActiveProfilePicture() {
		$player_id = $this->authentication->getPlayerId();
		// Check player proof_filename
		$this->load->model(array('player_model','player_attached_proof_file_model'));
		//NEW Function jhunel.php.ph 1-9-2018
		$response = $this->player_attached_proof_file_model->getAttachementRecordInfo($player_id,null,player_attached_proof_file_model::PROFILE_PICTURE,null,false,null,false);
		if(!empty($response)){
			foreach ($response as $key => $value) {
				if(isset($value['visible_to_player'])){
					if($value['visible_to_player']){
						if(isset($value['file_name'])) {
							return $value['file_name'];
						}
					}
				}
			}
		}

		return false;

		/*OLD Function
		$result = json_decode($this->player_model->getPlayerInfoDetailById($player_id)['proof_filename'], true);

		if (!$result) {
			return false;
		}

		if (empty($result['profile_image'])) {
			$this->set_default_value_proof_filename($player_id);
			return false;
		}

		// Get active profile picture file name
		foreach ($result['profile_image'] as $key => $values) {
			if ($result['profile_image'][$key]['active']) {
				return $key;
			}
		}*/
	}

	/**
	 * @author Hayme.php 2017-05-10
	 * Overview : Get player account information progress
	 * @param 	none
	 * @return	int
	 */
	public function getProfileProgress() {
		$totalFields = count($this->getRequiredAndVisibleFieldsSettings());

		// If all player registaration settings are hidden and not required
		if (!$totalFields) {
			return 100;
		}

		$totalFieldsWithValue = $this->getTotalFieldWithValue();

		return $progressPercentage = round(($totalFieldsWithValue / $totalFields) * 100);
	}

	public function getRequiredAndVisibleFieldsSettings() {
		$this->load->model(array('registration_setting'));
		$regSettings = $this->registration_setting->getRegistrationFields();
		$excludedInAccountSettings = $this->utils->getConfig('excluded_in_account_info_settings');

		$fields = array();

		foreach ($regSettings as $key => $value) {
			if ($regSettings[$key]['type'] == 1 && $regSettings[$key]['account_visible'] == '0' && $regSettings[$key]['account_required'] == '0' &&
				$regSettings[$key]['alias'] && !in_array($regSettings[$key]['alias'], $excludedInAccountSettings))
			{

				if ($regSettings[$key]['alias'] == 'bankAccountName') {
					$regSettings[$key]['alias'] = 'bankAccountFullName';
				}

				if ($regSettings[$key]['alias'] == 'city') {
					$regSettings[$key]['alias'] = 'a.city';
				}

				if ($regSettings[$key]['alias'] == 'withdrawPassword') {
					$regSettings[$key]['alias'] = 'withdraw_password';
				}

				if ($regSettings[$key]['alias'] == 'affiliateCode') {
					$regSettings[$key]['alias'] = 'affiliateId';
				}

				array_push($fields, $regSettings[$key]['alias']);
			}
		}

		return $fields;
	}

	public function getVisibleFielsForPlayer() {
		$this->load->model(array('registration_setting'));
		$regSettings = $this->registration_setting->getRegistrationFields();

		if (empty($regSettings)) {
			return false;
		}

		$fields = array();

		foreach ($regSettings as $key => $value) {
			if ($value['type'] == 1 && $value['account_visible'] == '0' && !empty($value['alias'])) {
				array_push($fields, strtoupper($regSettings[$key]['alias']));
			}
		}

		return $fields;
	}

	public function getTotalFieldWithValue() {
		$player_id = $this->authentication->getPlayerId();
		$fields = implode(", ", $this->getRequiredAndVisibleFieldsSettings());

		if (empty($fields)) {
			return 0;
		}

		$this->load->model(array('player'));
		$result =  $this->player->getPlayerProfileProgres($fields, $player_id);

		// Get total number of fields with value (not 0, null, "")
		$counter = 0;

		if(empty($result)){
		    return 0;
        }

		foreach ($result as $key => $value) {
			if ($result[$key]) {
				$counter++;
			}
		}

		return $counter;
	}

	/**
	 * overview : User Join Vip
	 *
	 * detail :  @return json data for player level details
	 *
	 */
	public function joinVip() {
		$this->load->model(array('group_level','player'));

		# Only allow editing of current player id, also checks for login
		$playerId = $this->authentication->getPlayerId();
		if(empty($playerId)){
			# Prevent access without login info
			echo json_encode(array('status'=>'error', 'msg' => lang('notify.69')));
			return;
		}

		$newPlayerLevel = $this->input->post('newPlayerLevel');
		$groupName = $this->input->post('groupName');

		if(empty($newPlayerLevel)){
			# Prevent returning success when there is an error (newPlayerLevel missing)
			echo json_encode(array('status'=>'error', 'msg' => 'Please specify new level.'));
			return;
		}

		$this->group_level->adjustPlayerLevel($playerId, $newPlayerLevel);
		$result = array('status' => 'success', 'msg' => sprintf(lang('vip.join.msg.success'), $groupName));

		echo json_encode($result);
	}

	/**
	 * Update user login info, such as IP-address or login time, and
	 * clear previously generated (but not activated) passwords.
	 *
	 * @param	int
	 * @param	bool
	 * @param	bool
	 * @return	void
	 */
	public function updateLoginInfo() {
		$this->load->model('player');
		$playerId = $this->input->post('playerId');
		$ip = NULL;
		$record_time = $this->utils->getNowForMysql();
		$this->player->updateLoginInfo($playerId, $ip, $record_time);
	}

	public function getProfilePictureFullPath() {
		//NEW Function jhunel.php 1-9-2018
		$path=$this->utils->getUploadPath() . '/'. $this->config->item("player_upload_folder");
		$this->utils->addSuffixOnMDB($path);
		return $path;
		/*old
		return $this->utils->getUploadPath() . '/player/profile_picture/'. $this->utils->getPlayerCenterTemplate();*/
	}

	public function getProfilePictureUploadPath() {
		//new function, jhunel.php 1-9-2018
		$path='upload/' . $this->config->item("player_upload_folder");
		$this->utils->addSuffixOnMDB($path);
		return $path;
		/*OLD function
		return 'upload/player/profile_picture/' . $this->utils->getPlayerCenterTemplate();
		*/
	}
}

/* End of file ip.php */
/* Location: ./application/controller/player_profile.php */