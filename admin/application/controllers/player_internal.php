<?php
require_once dirname(__FILE__) . '/BaseController.php';

/**
 * player_internal.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class player_internal extends BaseController {
    public function __construct(){
        parent::__construct();

        $this->load->library(['authentication']);
    }

    protected function _sendFileHeader($related_file_path){
        $content_type = $this->utils->getFileMimeType(pathinfo($related_file_path, PATHINFO_EXTENSION));
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Type: " . $content_type);
        $this->output->set_header('X-Accel-Redirect: ' . $this->utils->getPlayerInternalXAccelRedirectUrl($related_file_path));
    }

    protected function _check_player_permission($target_player_id, $allow_logged_player = FALSE){
        if($this->utils->isFromAdminHost()){
            return $this->authentication->isLoggedIn();
        }else{
            if(!$this->authentication->isLoggedIn()){
                return FALSE;
            }

            if($allow_logged_player){
                return TRUE;
            }

            $playerId = $this->authentication->getPlayerId();
            // $username = $this->authentication->getUsername();

            return ($playerId == $target_player_id);
        }
    }

    public function player(){
        $args = func_get_args();

        $interna_upload_method = null;
        
        if($this->utils->isEnabledMDB()){ 
            $interna_upload_method = $args[1];
            unset($args[0]);
            unset($args[1]);
        }else{
            $interna_upload_method = array_shift($args);
        }

        if(!method_exists($this, '_' . $interna_upload_method)){
            show_404();
            return;
        }

        return call_user_func_array([$this, '_' . $interna_upload_method], $args);
    }

    public function remote_logs($logfile){

        $this->load->library(['permissions']);
        $this->load->model(['users']);

        if($this->users->isT1User($this->authentication->getUsername())){

            $data = array('title' => lang('Download Remote Log'), 'sidebar' => 'system_management/sidebar',
                'activenav' => 'dev_functions');

            
            if (!$this->permissions->checkPermissions('dev_functions') || !$this->users->isT1User($this->authentication->getUsername()) || empty($logfile)) {
                return $this->showErrorAccess($data['title'], $data['sidebar'], $data['activenav']);
            }

            return $this->_sendFileHeader(PLAYER_INTERNAL_REMOTE_LOGS_PATH . $logfile);

        }else{
          //if($this->permissions->checkPermissions('download_remote_log')) {
               $this->_sendFileHeader(PLAYER_INTERNAL_REMOTE_LOGS_PATH . $logfile);             
           // }else{
           //     show_error('No permissions', 403);
           // }
     }


 }

    protected function _deposit_receipt($sales_order_id, $img_id){
        $this->load->model(array('sale_order','player_attached_proof_file_model'));

        $order = $this->sale_order->getSaleOrderById($sales_order_id);
        if(empty($order)){
            show_404();
            return;
        }

        $receipt_player = $order->player_id;
        $attachementRecordInfo = $this->player_attached_proof_file_model->getAttachementRecordInfo($receipt_player, null, player_attached_proof_file_model::Deposit_Attached_Document, $img_id, true, $sales_order_id, null, null);
        if(empty($attachementRecordInfo)){
            show_404();
            return;
        }

        $file_name = $attachementRecordInfo['file_name'];
        if(!$this->_check_player_permission($receipt_player)){
            show_404();
            return;
        }

        $this->_sendFileHeader(PLAYER_INTERNAL_DEPOSIT_RECEIPT_PATH . $file_name);
    }

    protected function _kyc_attachment($player_id, $img_id, $token = null){
        $this->load->model(array('player_attached_proof_file_model'));

        $attachementRecordInfo = $this->player_attached_proof_file_model->getAttachementRecordInfo($player_id, null, null, $img_id, true);
        if(empty($attachementRecordInfo)){
            show_404();
            return;
        }

        $file_name = $attachementRecordInfo['file_name'];
        $file_player = $attachementRecordInfo['player_id'];

        $verified = FALSE;
        if($token){
            $verified = $this->player_attached_proof_file_model->verifyApiToken($player_id, $img_id, $token);
        }

        if($verified){
            $this->_sendFileHeader(PLAYER_INTERNAL_KYC_ATTACHMENT_PATH . $file_name);
            return;
        }

        if(!$this->_check_player_permission($file_player)){
            show_404();
            return;
        }

        $this->_sendFileHeader(PLAYER_INTERNAL_KYC_ATTACHMENT_PATH . $file_name);
    }
}