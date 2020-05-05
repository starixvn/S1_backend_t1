<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get attached proof file document
 * * Get/insert/update/ player attached proof file document
 *
 * @category Player KYC attached proof file document
 * @version 1.8.10
 * @author Jhunel L. Ebero
 * @copyright 2013-2017 TripleOneTech
 */
class player_attached_proof_file_model extends BaseModel {

	public function __construct() {
		parent::__construct();
		$this->load->model(array('player_kyc','player_model','kyc_status_model','riskscore_kyc_chart_management_model','users'));
		$this->load->library(array('player_manager','multiple_image_uploader'));
	}

	protected $tableName = 'player_attached_proof_file';


	/**
	 * @author Jhunel L. Ebero
	 * overview : Get Player Attachement Info List
	 *
	 * details : Get Player Attachement Info List by group of available type of attachment,
	 * 				default attachment type (photo_id , proof_of_address , proof_of_income)
	 *
	 * @param int $playerId	player_id
	 */
	public function getPlayerAttachmentInfoList($playerId, $activeStatusInfoOnly = false, $use_cache = true){
		$this->migrateImage($playerId);
		$response = $this->config->item('proof_attachment_type');

		if(!empty($response)){
			$getVerificationData = $this->kyc_status_model->getVerificationData($playerId, null, $use_cache);

			foreach ($response as $key => $value) {

				$img_file = $this->getAttachementRecordInfo($playerId,null,$value['tag']);
				if(empty($img_file)){
					$tags_info = $this->kyc_status_model->get_verification_info_by_tag($playerId,$value['tag']);
					if(empty($tags_info)){
						$data = [
							$value['tag'] => [
								self::Remark_No_Attach => [
									"status" => self::TRUE,
									"auto_status" => self::FALSE,
									"comments" => null
								]
							],
						];
					} else {
						$comments = null;
						foreach ($tags_info as $tags_info_key => $tags_info_value) {
							if(isset($tags_info_value['comments'])){
								$comments = $tags_info_value['comments'];
							}

						}

						$data = [
							$value['tag'] => [
								self::Remark_No_Attach => [
									"status" => self::FALSE,
									"auto_status" => self::FALSE,
									"comments" => $comments
								]
							],
						];
					}

					if(!isset($tags_info[self::Remark_No_Attach])){
						$this->kyc_status_model->update_verification_data($playerId,$data);
					}

				}

				$activeStatusInfo = array();
				$verificationList = $this->config->item('verification');
				if(!empty($verificationList)){

					if(isset($getVerificationData[$value['tag']])){
						$activeRemarks = $getVerificationData[$value['tag']];
						//echo "<pre>";print_r($activeRemarks);
						foreach ($activeRemarks as $activeRemarks_key => $activeRemarks_value) {
							if(isset($verificationList[$activeRemarks_key])){
								$activeRemarksStatus = $activeRemarks[$activeRemarks_key];
								$status = (isset($activeRemarksStatus['status'])) ? $activeRemarksStatus['status'] : null;
								$auto_status = (isset($activeRemarksStatus['auto_status'])) ? $activeRemarksStatus['auto_status'] : null;
								$comments = (isset($activeRemarksStatus['comments'])) ? $activeRemarksStatus['comments'] : null;

								$activeStatusInfo = [
									'verification' => $activeRemarks_key,
									'status' => $status,
									'auto_status' => $auto_status,
									'comments' => $comments,
								];

								$verificationList[$activeRemarks_key]['active'] = isset($activeStatusInfo['status']) ? $activeStatusInfo['status'] : self::FALSE;
							}
						}
					}
				}

				if(!empty($img_file)){
					foreach ($img_file as $img_file_key => $img_file_value) {
					    $img_file[$img_file_key]['file_name'] = $this->utils->getPlayerInternalUrl('admin',PLAYER_INTERNAL_KYC_ATTACHMENT_PATH).$img_file_value['player_id'].'/'.$img_file_value['id'];

						if($img_file_value['admin_user_id']){
							$adminInfo = $this->users->selectUsersById($img_file_value['admin_user_id']);
							if(!empty($adminInfo)){
								if(isset($adminInfo['username'])){
									$img_file[$img_file_key]['uploaded_by'] = $adminInfo['username'];
								}
							}
						} else {
							$playerInfo = $this->player_model->getPlayerInfoDetailById($img_file_value['player_id']);
							if(!empty($playerInfo)){
								if(isset($playerInfo['username'])){
									$img_file[$img_file_key]['uploaded_by'] = $playerInfo['username'];
								}
							}
						}
					}
				}

				if(!$activeStatusInfoOnly){
					$response[$key]['img_file'] = $img_file;
					$response[$key]['verificationList'] = $verificationList;
				}

				$response[$key]['activeStatusInfo'] = $activeStatusInfo;
			}
		}
		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : add image attachment file by player
	 *
	 * details : add image attachment file by player
	 *
	 * @param int $playerId	player_id
	 * @param array $data	data to be safe including img file info and other details
	 */
	public function addAttachementRecord($data){
		$response = false;
		if(!empty($data)){
			$response = $this->insertData($this->tableName, $data);
		}
		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : remove image attachment file by player
	 *
	 * details : remove image attachment file by player
	 *
	 * @param int $playerId	player_id
	 * @param array $data	data to be safe including img file info and other details
	 */
	public function removeAttachementRecord($data){
		$response = false;
		if(!empty($data)){
			if(isset($data['picId'])){
				$this->db->where('id', $data['picId']);
			}
			if(isset($data['imgFile'])){
				$imgFile = $data["imgFile"];
				$this->db->where("file_name LIKE '%$imgFile%'");
			}
			$this->db->where('player_id', $data['playerId']);
			$response = $this->db->delete($this->tableName);
		}
		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : add image attachment file by player
	 *
	 * details : add image attachment file by player
	 *
	 * @param int $playerId	player_id
	 * @param array $data	data to be safe including img file info and other details
	 */
	public function getAttachementRecordInfo($playerId, $file_name = null, $tag = null,$img_id = null,$single_info = false,$sales_order_id = null,$notProfile = true,$notDeposit = true, $order_by_id = true){
		$this->db->select('*');
		$this->db->from($this->tableName);
		$this->db->where('player_id', $playerId);
		if(!empty($file_name)){
			$this->db->where("file_name LIKE '%$file_name%'");
		}
		if(!empty($tag)){
			$this->db->where('tag', $tag);
		}
		if(!empty($img_id)){
			$this->db->where('id', $img_id);
		}
		if(!empty($sales_order_id)){
			$this->db->where('sales_order_id', $sales_order_id);
		}
		if($notProfile){
			$this->db->where('tag !=', self::PROFILE_PICTURE);
		}
		if($notDeposit){
			$this->db->where('tag !=', self::Deposit_Attached_Document);
		}
		if($order_by_id){
            $this->db->order_by('id','desc');
        }

        if($single_info){
			$res = $this->runOneRowArray();
		} else {
			$res = $this->runMultipleRowArray();
		}

		$this->utils->printLastSQL();

		return $res;

		// if($single_info){
		// 	return $this->runOneRowArray();
		// } else {
		// 	return $this->runMultipleRowArray();
		// }

	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : migrate image
	 *
	 * details : Check if player has image record in table playerdetails column proof_filename->img_file, if theres a record migrate
	 *			the data to player_attached_proof_file table and clear and remove entity img_file to proof_filename.
	 *
	 * @param int $playerId	player_id
	 */
	public function migrateImage($playerId) {
		if(!empty($playerId)){
			$playerInfo = $this->player_model->getPlayerInfoById($playerId);
			$data = array();
			if(!empty($playerInfo)){
				if(isset($playerInfo['proof_filename'])){
					if(empty($playerInfo['proof_filename'])){
						$this->kyc_status_model->set_default_value_proof_filename($playerId);
					} else {
						$proof_filename = json_decode($playerInfo['proof_filename'],true);
						if(isset($proof_filename['img_file'])){
							$img_file = $proof_filename['img_file'];
							if(!empty($img_file)){
								$pass = true;
								foreach ($img_file as $key => $value) {
									if(empty($this->getAttachementRecordInfo($playerId,$value))){
										$data = [
											'player_id' => $playerId,
											'admin_user_id' => ($this->authentication->getUserId()) ? $this->authentication->getUserId() : null,
											'file_name' => $value,
											'tag' => self::Verification_Photo_ID,
											'created_at' => $this->utils->getCurrentDatetime()
										];
										$addResponse = $this->addAttachementRecord($data);
										if(!$addResponse){
											$pass = false;
										}
									}
								}
								if($pass){
									$data = array(
										'proof_filename' => json_encode(array(
											'verification' => $proof_filename['verification'],
											'profile_image' => $proof_filename['profile_image']
										)),
									);
									$this->player_manager->editPlayerDetails($data, $playerId);
								}
							}
						}
					}
				}
			}
		}
	}

    /**
     * @author curtis.php.tw
     * overview : Upload Deposit Receipt
     *
     * details : Player upload deposit receipt;
     *
     * @param array ['player_id','remarks','tag','comments','image']
     */
    public function upload_deposit_receipt($data){
        $response = array();
        if(!empty($data)){
            $playerId = (isset($data['input']['player_id'])) ? $data['input']['player_id'] : null;
            $remarks = (isset($data['input']['remarks'])) ? $data['input']['remarks'] : null;
            $tag = (isset($data['input']['tag'])) ? $data['input']['tag'] : null;
            $sales_order_id = (isset($data['input']['sales_order_id'])) ? $data['input']['sales_order_id'] : null;
            $comments = (isset($data['input']['comments'])) ? $data['input']['comments'] : null;
            $image = (isset($data['image'])) ? $data['image']: null;
            $uploadResponseStatus = true;

            if(!empty($image['name'][0]) && !empty($image['tmp_name'][0])){
                $this->load->model(['sale_order']);
                $path = $this->utils->getPlayerInternalPath(PLAYER_INTERNAL_DEPOSIT_RECEIPT_PATH);
                $config = $this->utils->getUploadConfig($path);
                $order = $this->sale_order->getSaleOrderById($sales_order_id);
                $custom_name = $order->secure_id.str_random(5);

                $uploadResponse = $this->multiple_image_uploader->do_multiple_uploads($image,$path,$config,$custom_name);

                $this->utils->debug_log('uploadDepositReceiptResponse after', $uploadResponse);
                if($uploadResponse['status'] == "success") {
                    foreach ($uploadResponse['filename'] as $key => $value) {
                        $data = [
                            'player_id' => $playerId,
                            'admin_user_id' => ($this->authentication->getUserId()) ? $this->authentication->getUserId() : 0,
                            'file_name' => $value,
                            'tag' => $tag,
                            'sales_order_id' => $sales_order_id,
                            'created_at' => $this->utils->getCurrentDatetime()
                        ];

                        $addResponse = $this->addAttachementRecord($data);
                    }
                    $response = array(
                        'status' => 'success',
                        'msg' => lang('Successfully uploaded.'),
                        'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
                    );
                } else {
                    $uploadResponseStatus = false;
                    $response = array(
                        'status' => 'error',
                        'msg' => $uploadResponse['message'],
                        'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                    );
                }
            }

            if(!empty($remarks) || !empty($comments)) {
                if($uploadResponseStatus) {
                    $data = [
                        $tag => [
                            $remarks => [
                                "status" => self::TRUE,
                                "auto_status" => self::FALSE,
                                "comments" => (!empty($comments)) ? $comments : null
                            ]
                        ],
                    ];
                    //echo "<pre>";print_r($data);die();
                    $this->kyc_status_model->update_verification_data($playerId,$data);
                    $response = array(
                        'status' => 'success',
                        'msg' => lang('save.success'),
                        'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
                    );
                }
            }
        }
        $this->utils->debug_log('uploadDepositReceiptResponse done', $response);
        return $response;
    }

	/**
	 * @author Jhunel L. Ebero
	 * overview : Upload Documents
	 *
	 * details : Player upload documents;
	 *
	 * @param array ['player_id','remarks','tag','comments','image']
	 */
	public function upload_proof_document($data){
		$response = array();
		if(!empty($data)){
			$playerId = (isset($data['input']['player_id'])) ? $data['input']['player_id'] : null;
			$remarks = (isset($data['input']['remarks'])) ? $data['input']['remarks'] : null;
			$tag = (isset($data['input']['tag'])) ? $data['input']['tag'] : null;
			$sales_order_id = (isset($data['input']['sales_order_id'])) ? $data['input']['sales_order_id'] : null;
			$comments = (isset($data['input']['comments'])) ? $data['input']['comments'] : null;
			$image = (isset($data['image'])) ? $data['image']: null;
			$uploadResponseStatus = true;

			if(!empty($image['name'][0]) && !empty($image['tmp_name'][0])){

				$path=realpath($this->utils->getUploadPath()).'/'.$this->config->item("player_upload_folder");
				$path=rtrim($path, '/');
				$this->utils->addSuffixOnMDB($path);
                $config = $this->utils->getUploadConfig($path);

                $uploadResponse = $this->multiple_image_uploader->do_multiple_uploads($image,$path,$config);

				$this->utils->debug_log('uploadResponse after', $uploadResponse);
				if($uploadResponse['status'] == "success") {

					if ($tag == self::PROFILE_PICTURE) {
						$imgInfo = $this->getAttachementRecordInfo($playerId,null,self::PROFILE_PICTURE, null, false, null, false);
						if(!empty($imgInfo)){
							foreach ($imgInfo as $key => $value) {
								$data = array(
									'playerId' => $playerId,
									'picId' => $value['id'],
								);
								$this->remove_proof_document($data);
							}
						}

					}

					foreach ($uploadResponse['filename'] as $key => $value) {
	            		$data = [
							'player_id' => $playerId,
							'admin_user_id' => ($this->authentication->getUserId()) ? $this->authentication->getUserId() : 0,
							'file_name' => $value,
							'tag' => $tag,
							'sales_order_id' => $sales_order_id,
							'created_at' => $this->utils->getCurrentDatetime()
						];

						$addResponse = $this->addAttachementRecord($data);
	            	}
	            	$response = array(
							'status' => 'success',
							'msg' => lang('Successfully uploaded.'),
							'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
						);
				} else {
					$uploadResponseStatus = false;
					$response = array(
							'status' => 'error',
							'msg' => $uploadResponse['message'],
							'msg_type' => BaseController::MESSAGE_TYPE_ERROR
						);
				}
			}

			if(!empty($remarks) || !empty($comments)) {
				if($uploadResponseStatus) {
					$data = [
						$tag => [
							$remarks => [
								"status" => self::TRUE,
								"auto_status" => self::FALSE,
								"comments" => (!empty($comments)) ? $comments : null
							]
						],
					];
					//echo "<pre>";print_r($data);die();
					$this->kyc_status_model->update_verification_data($playerId,$data);
					$response = array(
							'status' => 'success',
							'msg' => lang('save.success'),
							'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
						);
				}
			}
		}
		$this->utils->debug_log('uploadResponse done', $response);
		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Remove Uploaded Documents
	 *
	 * details : Player remove uploaded documents by image id;
	 *
	 * @param array ['playerId','picId']
	 */
	public function remove_proof_document($data){
		$response = array();
		if(!empty($data)){

			$imgInfo = $this->getAttachementRecordInfo($data['playerId'], $file_name = null, $tag = null, $data['picId'],true, null, null, null);
			if(empty($imgInfo)){
                $response = [
                    'status' => 'error',
                    'msg' => lang('imageInfo is not exist.'),
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                ];
                $this->utils->debug_log('remove proof document file not exist record',$response);
                return $response;
            }

            $receiptRecord = $imgInfo['file_name'];
			$tag = $imgInfo['tag'];
            $file_path = $this->utils->getPlayerInternalPath(PLAYER_INTERNAL_DEPOSIT_RECEIPT_PATH.$receiptRecord);
            if($tag == self::PROFILE_PICTURE){
                $file_path=realpath($this->utils->getUploadPath()).'/'.$this->config->item("player_upload_folder").'/'.$receiptRecord;
            }


            if(!file_exists($file_path)){
                $response = [
                    'status' => 'error',
                    'msg' => lang('image is not exist.'),
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                ];
                $this->utils->debug_log('remove proof document file not exist',$response);
                return $response;
            }

            $deleted_file = unlink($file_path);
            if(!$deleted_file){
                $response = [
                    'status' => 'error',
                    'msg' => lang('Failed to remove image. Please try again.'),
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                ];
                $this->utils->debug_log('remove proof document delete file failed',$response);
                return $response;
            }

            $this->removeAttachementRecord($data);
            $this->utils->debug_log('remove proof document file successfully',$data);
            $response = array(
                'status' => 'success',
                'msg' => lang('Image successfully deleted!'),
                'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
            );
		}

		return $response;
	}

	/**
	 * @author Jhunel L. Ebero
	 * overview : Update data of Uploaded Documents
	 *
	 * details : update data Uploaded Documents;
	 *
	 * @param array []
	 */
	public function update_proof_document($picId,$playerId,$data){
		$response = false;
		if(!empty($data)){
			$response = $this->db->update($this->tableName,$data,array('id' => $picId,'player_id' => $playerId));
		}
		return $response;
	}


	/**
	 * @author Jhunel L. Ebero
	 * overview : Change Visibility of Uploaded Documents
	 *
	 * details : Change Visibility of Uploaded Documents to player center;
	 *
	 * @param array ['playerId','action']
	 */
	public function change_visibility_proof_document($data){
		$response = array();
		if(!empty($data)){
			if(isset($data['action']) && isset($data['playerId']) && isset($data['picId'])){
				$imgInfo = $this->getAttachementRecordInfo($data['playerId'], $file_name = null, $tag = null,$data['picId'],true);
				if(!empty($imgInfo)){
					$action = self::DB_FALSE;

					if($data['action'] == "visible"){
						$action = self::DB_TRUE;
					}

					$dataUpdate = [
						"visible_to_player" => $action
					];

					$removeResponse = $this->update_proof_document($data['picId'],$data['playerId'], $dataUpdate);

					if($removeResponse){
						$response = array(
							'status' => 'success',
							'msg' => lang('Image successfully update!'),
							'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
						);
					} else {
						$response = array(
										'status' => 'error',
										'msg' => lang('Failed to update image. Please try again.'),
										'msg_type' => BaseController::MESSAGE_TYPE_ERROR
									);
					}
				}
			}
		}

		return $response;
	}

	public function getDepositReceiptFileList($player_id, $sale_order_id){
        $filepath = [];
        $this->load->model(['sale_order']);
        $player_internal_url = $this->utils->getPlayerInternalUrl('player',PLAYER_INTERNAL_DEPOSIT_RECEIPT_PATH);

        $response = $this->getAttachementRecordInfo($player_id, null, null, null, false, $sale_order_id, null, null);
        if(!empty($response)){
            foreach ($response as $val){
                array_push($filepath , $player_internal_url . $val['sales_order_id'] . '/' . $val['id']);
            }
            return json_encode($filepath,JSON_UNESCAPED_SLASHES);
        }

        return FALSE;
    }

    /**
     * Updates attached_file_status table to save a new history
     *
     * @param  int $player_id
     * @param  string $attachment_tag
     * @param  string $status
     * @return boolean
     * @author Cholo Miguel Antonio <cholo.php.ph@tripleonetech.net>
     */
    public function saveAttachedFileStatusHistory($player_id)
    {
    	if(!isset($player_id)) return FALSE;

    	$verification_list = $this->utils->getConfig('verification');
		$proof_attachment_type_list = $this->utils->getConfig('proof_attachment_type');

		if(empty($verification_list)){
			$this->utils->error_log('saveAttachedFileStatusHistory > ERROR: EMPTY VERIFICATION CONFIG');
			return false;
		}

		if(empty($proof_attachment_type_list)){
			$this->utils->error_log('saveAttachedFileStatusHistory > ERROR: EMPTY PROOF_ATTACHMENT_TYPE CONFIG');
			return false;
		}

		// -- get player attachment info
		$attachment_info = (array) $this->getPlayerAttachmentInfoList($player_id, true, false);

		// -- default values
		$data = array(
			'player_id' => $player_id,
			'attachment_tag' => '',
			'status' => '',
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
		);

		$attachement_tag_json = array();
		$status_json = array();

		// -- if player has no attachment proof available, insert default one
		if(empty($attachment_info) || !is_array($attachment_info)){

			foreach ($proof_attachment_type_list as $key => $proof_attachment_type_list_value) {

				array_push($attachement_tag_json, $proof_attachment_type_list_value['tag']);
				$status_json[$proof_attachment_type_list_value['tag']] = 'no_attach';

			}

			$data['attachment_tag'] = json_encode($attachement_tag_json);
			$data['status'] = json_encode($status_json);

			$this->db->insert('attached_file_status',$data);

			// -- report if error occurs
			if($this->db->_error_message()){
				$this->utils->error_log('saveAttachedFileStatusHistory > ERROR:'. $this->db->_error_message());
				return FALSE;
			}

			return TRUE;
		}

		$attachement_tag_json = array();
		$status_json = array();

		foreach ($attachment_info as $attachment_info_key => $attachment_info_value) {

			array_push($attachement_tag_json, $attachment_info_value['tag']);
			$status_json[$attachment_info_value['tag']] = isset($attachment_info_value['activeStatusInfo']['verification']) ? $attachment_info_value['activeStatusInfo']['verification'] : 'no_attach';

		}

		$data['attachment_tag'] = json_encode($attachement_tag_json);
		$data['status'] = json_encode($status_json);

		$this->db->insert('attached_file_status',$data);

		// -- report if error occurs
		if($this->db->_error_message()){
			$this->utils->error_log('saveAttachedFileStatusHistory > ERROR:'. $this->db->_error_message());
			return FALSE;
		}

		return TRUE;
    }

    /**
     * Retrieve count of updated player attachments within the last 24 hours
     * @return int
     */
    public function getTodayPlayerAttachmentCount()
    {
    	$start_today = date('Y-m-d H:i:s', strtotime($this->utils->getNowForMysql() . "-1 days"));
		$end_today   = $this->utils->getNowForMysql();

    	$query = $this->db->from('attached_file_status')
    	->where('updated_at >= ',$start_today)
    	->where('updated_at <= ',$end_today)
        ->where('attached_file_status.player_id IN ( SELECT player_id FROM player_attached_proof_file WHERE tag IN ( "income", "address", "photo_id", "dep_wd" ) )')
        ->where('attached_file_status.player_id IN ( SELECT playerId FROM playerdetails WHERE proof_filename IS NOT NULL )')
    	->group_by('player_id')
    	->get();

    	if(empty($query->result_array())) return 0;

    	return $query->num_rows();
    }

    public function getTimeDuration(){
        $range = [];

        $defaultInterval = $this->utils->getConfig('player_kyc_valid_time_interval') ?: 60;
        $totalInterval = 60 / $defaultInterval;

        $minuteNow = date('i');
        $interval = (int)floor($minuteNow / $defaultInterval); //now interval

        for($i=0;$i<$totalInterval;$i++){
            $range[] = empty($i) ? '00' : $defaultInterval * $i ;
        }

        $hashTime = date('YmdH');
        $minute = '00';

        if(isset($range[$interval])){
            $minute = $range[$interval];
        }

        $hashTime.=$minute;

        return $hashTime;
    }

    public function getApiToken($player_id, $image_id){
        if(empty($player_id) || empty($image_id)){
            return '';
        }

        $time = $this->getTimeDuration();
        $magic = $this->utils->getConfig('player_kyc_access_key') ?: '**ole777**';  // (或其他預設magic字串)
        $access_hash = sha1( $player_id . '%' . $image_id . '%' . $magic . '%' . $time );

        return $access_hash;
    }

    public function verifyApiToken($player_id, $image_id, $api_token){
        if(empty($player_id) || empty($image_id) || empty($api_token)){
            return FALSE;
        }

        $token = $this->getApiToken($player_id, $image_id);
        if($api_token != $token){
            return FALSE;
        }

        return TRUE;
    }
}