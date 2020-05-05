<?php
/**
 * player_security_library.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 *
 * @property BaseController $CI
 * @property Registration_setting $registration_setting
 * @property kyc_status_model $kyc_status_model
 * @property player_attached_proof_file_model $player_attached_proof_file_model
 */
class Player_security_library {
    protected $_player = null;

    public function __construct(){
        $this->CI =& get_instance();

        $this->CI->load->model(['registration_setting']);
        $this->CI->load->model(['player_model','player_attached_proof_file_model','kyc_status_model']);

        $this->CI->load->library(['multiple_image_uploader','authentication']);

        $this->registration_setting = $this->CI->registration_setting;
        $this->kyc_status_model = $this->CI->kyc_status_model;
        $this->player_attached_proof_file_model = $this->CI->player_attached_proof_file_model;

        $this->multiple_image_uploader = $this->CI->multiple_image_uploader;
        $this->authentication = $this->CI->authentication;

        $this->_init();
    }

    protected function _init(){
        $this->_load_player();
    }

    protected function _load_player(){
        $player = $this->CI->load->get_var('player');

        if(!empty($player)){
            return FALSE;
        }
        return $this->setPlayer($player);
    }

    public function setPlayer($player){
        $this->_player = $player;

        return $this;
    }

    public function assign_common_vars(){
        $this->CI->load->vars([
            'player_verification' => $this->player_verification_info($this->_player['playerId']),
            'limit_of_upload_attachment' => $this->CI->utils->getConfig('kyc_limit_of_upload_attachment'),
            'showSMSField' => $this->CI->utils->isSMSEnabled(),
            'showEmailVerifyField' => $this->CI->utils->isEmailVerifyEnabled($this->_player['playerId']),
            'isPhoneVerified' => $this->_player['verified_phone'],
            'isEmailVerified' => $this->_player['verified_email'],
            'isEmailFilledIn' => $this->_player['email'],
            'secretQuestion' => $this->_player['secretQuestion'],
            'secretAnswer' => urldecode($this->_player['secretAnswer']),
            'withdraw_password' => $this->_player['withdraw_password'],
            'directChangePassword' => $this->CI->utils->checkPlayerCanDirectlyChangePassword()
        ]);
    }

    public function player_verification_info($player_id, $return_with_token = false) {
        $this->kyc_status_model->set_default_value_proof_filename($player_id);

        $count_proof_attachment = [
            'photo_id' => ['total' => 0, 'allowUpload' => BaseModel::TRUE],
            'address' => ['total' => 0, 'allowUpload' => BaseModel::TRUE],
            'income' => ['total' => 0, 'allowUpload' => BaseModel::TRUE],
            'dep_wd' => ['total' => 0, 'allowUpload' => BaseModel::TRUE],
        ];

        $imgInfo = $this->player_attached_proof_file_model->getAttachementRecordInfo($player_id);
        $img_file = array();
        $token = '';
        if(!empty($imgInfo)){
            foreach ($imgInfo as $key => $value) {
                $file_name = $this->CI->utils->getPlayerInternalUrl('player',PLAYER_INTERNAL_KYC_ATTACHMENT_PATH).$value['player_id'].'/'.$value['id'];

                if($return_with_token){
                    $token = $this->player_attached_proof_file_model->getApiToken($value['player_id'], $value['id']);
                    $file_name .= '/'.$token;
                }

                $img_file[] = array(
                    "file_name" => $file_name,
                    "tag" => $value['tag'],
                    "visible" => $value['visible_to_player']
                );

                $count_proof_attachment[$value['tag']]['total']++;
                if($count_proof_attachment[$value['tag']]['total'] >= $this->CI->utils->getConfig('kyc_limit_of_upload_attachment')){
                    $count_proof_attachment[$value['tag']]['allowUpload'] = BaseModel::FALSE;
                }
            }
        }

        $verified = $this->kyc_status_model->player_valid_documents($player_id);
        $verified_address = $this->kyc_status_model->player_valid_identity_and_proof_of_address($player_id);
        $verified_income = $this->kyc_status_model->player_valid_proof_of_income($player_id);
        $verified_deposit_withdrawal = $this->kyc_status_model->player_valid_proof_of_deposit_withdrawal($player_id);

        $data = array(
            "verified" => $verified,
            "verified_address" => $verified_address,
            "verified_income" => $verified_income,
            "verified_dep_wd" => $verified_deposit_withdrawal,
            "count_proof_attachment" => $count_proof_attachment,
            "img_file" => $img_file,
        );

        return $data;
    }

    function allowUploadAttachment($player_id, $tag){
        $result = FALSE;
        $player_attachment_record = $this->player_attached_proof_file_model->getAttachementRecordInfo($player_id, null, $tag);
        $limit_of_upload_attachment = $this->CI->utils->getConfig('kyc_limit_of_upload_attachment');
        if(count($player_attachment_record) >= $limit_of_upload_attachment){
            $result = array(
                'status' => 'error',
                'msg' => sprintf(lang('kyc_attachment.upload_file_max_up_to'),$limit_of_upload_attachment),
                'msg_type' => BaseController::MESSAGE_TYPE_ERROR
            );
            $this->CI->utils->debug_log($tag.' attached proof file is full, can\'t upload !', $result);
        }
        return $result;
    }

    function uploadResponseStatus($player_id, $tag, $remarks, $comments){
        $data = [
            $tag => [
                $remarks => [
                    "status" => BaseController::TRUE,
                    "auto_status" => BaseController::FALSE,
                    "comments" => (!empty($comments)) ? $comments : null
                ]
            ],
        ];

        $this->kyc_status_model->update_verification_data($player_id,$data);
        $response = array(
            'status' => 'success',
            'msg' => lang('save.success'),
            'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
        );

        return $response;
    }
    public function request_upload_realname_verification($player_id, $tag = BaseModel::Verification_Photo_ID, $image, $remarks = null, $comments = null) {
        $response = array();
        if(!empty($remarks) || !empty($comments)) {
            $response = $this->uploadResponseStatus($player_id, $tag, $remarks, $comments);
        }

        if(!empty($image['name'][0]) && !empty($image['tmp_name'][0])){
            if(FALSE !== $response = $this->allowUploadAttachment($player_id, $tag)){
                return $response;
            }

            $path = $this->CI->utils->getPlayerInternalPath(PLAYER_INTERNAL_KYC_ATTACHMENT_PATH);
            $config = $this->CI->utils->getUploadConfig($path);

            $uploadResponse = $this->multiple_image_uploader->do_multiple_uploads($image,$path,$config);

            $this->CI->utils->debug_log('Response after upload realname verification', $uploadResponse);
            if($uploadResponse['status'] == "success") {
                foreach ($uploadResponse['filename'] as $key => $value) {
                    $data = [
                        'player_id' => $player_id,
                        'admin_user_id' => ($this->authentication->getUserId()) ? $this->authentication->getUserId() : 0,
                        'file_name' => $value,
                        'tag' => $tag,
                        'created_at' => $this->CI->utils->getCurrentDatetime()
                    ];

                    $insert_id = $this->player_attached_proof_file_model->addAttachementRecord($data);
                }

                if($insert_id){
                    $response = array(
                        'status' => 'success',
                        'msg' => lang('Successfully uploaded.'),
                        'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
                    );
                }else{
                    $response = array(
                        'status' => 'error',
                        'msg' => lang('error.default.db.message'),
                        'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                    );
                }

            } else {
                $response = array(
                    'status' => 'error',
                    'msg' => $uploadResponse['message'],
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                );
            }
        }

        $this->CI->utils->debug_log('upload realname verification done', $response);
        return $response;
    }

    public function request_upload_address($player_id, $tag = BaseModel::Verification_Adress, $image, $remarks = null, $comments = null) {
        $response = array();
        if(!empty($remarks) || !empty($comments)) {
            $response = $this->uploadResponseStatus($player_id, $tag, $remarks, $comments);
        }

        if(!empty($image['name'][0]) && !empty($image['tmp_name'][0])){
            if(FALSE !== $response = $this->allowUploadAttachment($player_id, $tag)){
                return $response;
            }

            $path = $this->CI->utils->getPlayerInternalPath(PLAYER_INTERNAL_KYC_ATTACHMENT_PATH);
            $config = $this->CI->utils->getUploadConfig($path);

            $uploadResponse = $this->multiple_image_uploader->do_multiple_uploads($image,$path,$config);

            $this->CI->utils->debug_log('Response after upload address', $uploadResponse);
            if($uploadResponse['status'] == "success") {
                foreach ($uploadResponse['filename'] as $key => $value) {
                    $data = [
                        'player_id' => $player_id,
                        'admin_user_id' => ($this->authentication->getUserId()) ? $this->authentication->getUserId() : 0,
                        'file_name' => $value,
                        'tag' => $tag,
                        'created_at' => $this->CI->utils->getCurrentDatetime()
                    ];

                    $insert_id = $this->player_attached_proof_file_model->addAttachementRecord($data);
                }

                if($insert_id){
                    $response = array(
                        'status' => 'success',
                        'msg' => lang('Successfully uploaded.'),
                        'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
                    );
                }else{
                    $response = array(
                        'status' => 'error',
                        'msg' => lang('error.default.db.message'),
                        'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                    );
                }

            } else {
                $response = array(
                    'status' => 'error',
                    'msg' => $uploadResponse['message'],
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                );
            }
        }

        $this->CI->utils->debug_log('upload address done', $response);
        return $response;
    }

    public function request_upload_deposit_withdrawal($player_id, $tag = BaseModel::Verification_Deposit_Withrawal, $image, $remarks = null, $comments = null) {
        $response = array();
        if(!empty($remarks) || !empty($comments)) {
            $response = $this->uploadResponseStatus($player_id, $tag, $remarks, $comments);
        }

        if(!empty($image['name'][0]) && !empty($image['tmp_name'][0])){
            if(FALSE !== $response = $this->allowUploadAttachment($player_id, $tag)){
                return $response;
            }

            $path = $this->CI->utils->getPlayerInternalPath(PLAYER_INTERNAL_KYC_ATTACHMENT_PATH);
            $config = $this->CI->utils->getUploadConfig($path);

            $uploadResponse = $this->multiple_image_uploader->do_multiple_uploads($image,$path,$config);

            $this->CI->utils->debug_log('Response after upload deposit withdrawal', $uploadResponse);
            if($uploadResponse['status'] == "success") {
                foreach ($uploadResponse['filename'] as $key => $value) {
                    $data = [
                        'player_id' => $player_id,
                        'admin_user_id' => ($this->authentication->getUserId()) ? $this->authentication->getUserId() : 0,
                        'file_name' => $value,
                        'tag' => $tag,
                        'created_at' => $this->CI->utils->getCurrentDatetime()
                    ];

                    $insert_id = $this->player_attached_proof_file_model->addAttachementRecord($data);
                }

                if($insert_id){
                    $response = array(
                        'status' => 'success',
                        'msg' => lang('Successfully uploaded.'),
                        'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
                    );
                }else{
                    $response = array(
                        'status' => 'error',
                        'msg' => lang('error.default.db.message'),
                        'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                    );
                }

            } else {
                $response = array(
                    'status' => 'error',
                    'msg' => $uploadResponse['message'],
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                );
            }
        }

        $this->CI->utils->debug_log('upload deposit withdrawal done', $response);
        return $response;
    }

    public function request_upload_income($player_id, $tag = BaseModel::Verification_Income, $image, $remarks = null, $comments = null) {
        $response = array();
        if(!empty($remarks) || !empty($comments)) {
            $response = $this->uploadResponseStatus($player_id, $tag, $remarks, $comments);
        }

        if(!empty($image['name'][0]) && !empty($image['tmp_name'][0])){
            if(FALSE !== $response = $this->allowUploadAttachment($player_id, $tag)){
                return $response;
            }

            $path = $this->CI->utils->getPlayerInternalPath(PLAYER_INTERNAL_KYC_ATTACHMENT_PATH);
            $config = $this->CI->utils->getUploadConfig($path);

            $uploadResponse = $this->multiple_image_uploader->do_multiple_uploads($image,$path,$config);

            $this->CI->utils->debug_log('Response after upload income', $uploadResponse);
            if($uploadResponse['status'] == "success") {
                foreach ($uploadResponse['filename'] as $key => $value) {
                    $data = [
                        'player_id' => $player_id,
                        'admin_user_id' => ($this->authentication->getUserId()) ? $this->authentication->getUserId() : 0,
                        'file_name' => $value,
                        'tag' => $tag,
                        'created_at' => $this->CI->utils->getCurrentDatetime()
                    ];

                    $insert_id = $this->player_attached_proof_file_model->addAttachementRecord($data);
                }

                if($insert_id){
                    $response = array(
                        'status' => 'success',
                        'msg' => lang('Successfully uploaded.'),
                        'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
                    );
                }else{
                    $response = array(
                        'status' => 'error',
                        'msg' => lang('error.default.db.message'),
                        'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                    );
                }

            } else {
                $response = array(
                    'status' => 'error',
                    'msg' => $uploadResponse['message'],
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                );
            }
        }

        $this->CI->utils->debug_log('upload income done', $response);
        return $response;
    }

    public function remove_kyc_player_proof_document($data){
        $response = array();
        if(!empty($data)){
            $imgInfo = $this->player_attached_proof_file_model->getAttachementRecordInfo($data['playerId'], $file_name = null, $tag = null, $data['picId'],true);
            if(empty($imgInfo)){
                $response = [
                    'status' => 'error',
                    'msg' => lang('imageInfo is not exist.'),
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                ];
                $this->CI->utils->debug_log('remove kyc player proof document file not exist record',$response);
                return $response;
            }

            $receiptRecord = $imgInfo['file_name'];
            $file_path = $this->CI->utils->getPlayerInternalPath(PLAYER_INTERNAL_KYC_ATTACHMENT_PATH.$receiptRecord);

            if(!file_exists($file_path)){
                $response = [
                    'status' => 'error',
                    'msg' => lang('image is not exist.'),
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                ];
                $this->CI->utils->debug_log('remove kyc player proof document file not exist',$response);
                return $response;
            }

            $deleted_file = unlink($file_path);
            if(!$deleted_file){
                $response = [
                    'status' => 'error',
                    'msg' => lang('Failed to remove image. Please try again.'),
                    'msg_type' => BaseController::MESSAGE_TYPE_ERROR
                ];
                $this->CI->utils->debug_log('remove kyc player proof document delete file failed',$response);
                return $response;
            }

            $this->player_attached_proof_file_model->removeAttachementRecord($data);
            $response = array(
                'status' => 'success',
                'msg' => lang('Image successfully deleted!'),
                'msg_type' => BaseController::MESSAGE_TYPE_SUCCESS
            );
        }
        return $response;
    }

    public function get_player_kyc_real_name_image_list($playerId){
        $player_verification = $this->player_verification_info($playerId, true);

        $list = [];
        if (!isset($player_verification['img_file'])) {
            return $list;
        }

        foreach ($player_verification['img_file'] as $key => $val) {
            if (($val['tag'] == BaseModel::Verification_Photo_ID) && $val['visible']) {
                $list[] = $val['file_name'];
            }
        }

        return $list;
    }
}