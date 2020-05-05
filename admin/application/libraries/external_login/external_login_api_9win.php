<?php

require_once dirname(__FILE__) . '/abstract_external_login_api.php';

/**
 * external_login_settings:
 * 9win_pass_key
 */
class External_login_api_9win extends abstract_external_login_api{

    public function validateUsernamePassword($playerId, $username, $password, &$message=''){

        $success=false;

        $this->CI->utils->debug_log('username: '.$username.', password:'.$password);

        $settings=$this->getSettings();
        $key=$settings['9win_pass_key'];

        $this->CI->load->model(['player_model']);
        $temppass=$this->CI->player_model->getTemppassById($playerId);
        if(!empty($temppass)){
            if(strtolower($temppass)==strtolower(md5($password.$key))){
                $success=true;
            }else{
                $message='cannot match old password';
            }
        }else{
            $message='cannot find old system password';
        }

        return $success;
    }

}
