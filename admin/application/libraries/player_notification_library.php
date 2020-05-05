<?php
/**
 * player_notification_library.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 *
 * @property BaseController $CI
 * @property Player_notification $player_notification
 */
class Player_notification_library {
    /* @var BaseController */
    public $CI;

    public function __construct(){
        $this->CI =& get_instance();

        $this->CI->load->model(['player_notification']);
        $this->CI->load->library(['form_validation']);

        $this->player_notification = $this->CI->player_notification;
    }

    public function getNotify($player_id){
        $self = $this;

        $player_notify_list = $this->player_notification->getNotify($player_id);
        if(empty($player_notify_list)){
            return [];
        }

        foreach($player_notify_list as $notify_id => $notify_content){
            $title = @json_decode($notify_content['title'], TRUE);
            $message = @json_decode($notify_content['message'], TRUE);

            if(is_array($title)){
                $title[0] = lang($title[0]);
                switch($notify_content['source_type']){
                    case Player_notification::SOURCE_TYPE_VIP_UPGRADE:
                        if(isset($title[2])){
                            $title[2] = lang($title[2]);
                        }
                        if(isset($title[3])){
                            $title[3] = lang($title[3]);
                        }
                        if(isset($title[4])){
                            $title[4] = lang($title[4]);
                        }
                        if(isset($title[5])){
                            $title[5] = lang($title[5]);
                        }
                        break;
                    default:
                        break;
                }
                $notify_content['title'] = @call_user_func_array('sprintf', $title);
            }else{
                $notify_content['title'] = lang($notify_content['title']);
            }

            if(is_array($message)){
                $message[0] = lang($message[0]);
                switch($notify_content['source_type']){
                    case Player_notification::SOURCE_TYPE_VIP_UPGRADE:
                        if(isset($message[2])){
                            $message[2] = lang($message[2]);
                        }
                        if(isset($message[3])){
                            $message[3] = lang($message[3]);
                        }
                        if(isset($message[4])){
                            $message[4] = lang($message[4]);
                        }
                        if(isset($message[5])){
                            $message[5] = lang($message[5]);
                        }
                        break;
                    default:
                        break;
                }
                $notify_content['message'] = @call_user_func_array('sprintf', $message);
            }else{
                $notify_content['message'] = lang($notify_content['message']);
            }

            $player_notify_list[$notify_id] = $notify_content;
        }

        return $player_notify_list;
    }

    public function setIsNotify($player_id, $notify_id){
        return $this->player_notification->setIsNotify($player_id, $notify_id);
    }

    public function createNotify($player_id, $source_type, $notify_type, $title = NULL, $message = NULL, $url = NULL, $url_target = NULL){
        $self = $this;

        if(!$this->CI->operatorglobalsettings->getSettingBooleanValue('player_center_notification')){
            return FALSE;
        }

        $allowed_source_type = $this->CI->operatorglobalsettings->getSettingJson('player_center_notification_source_type', 'value', []);
        if(empty($allowed_source_type)){
            return FALSE;
        }

        if(!in_array((string)$source_type, (array)$allowed_source_type)){
            return FALSE;
        }

        $title = (is_array($title)) ? json_encode($title) : $title;
        $message = (is_array($message)) ? json_encode($message) : $message;

        return $self->player_notification->createNotify($player_id, $source_type, $notify_type, $title, $message, $url, $url_target);
    }

    public function info($player_id, $source_type, $title = NULL, $message = NULL, $url = NULL, $url_target = NULL){
        return $this->createNotify($player_id, $source_type, Player_notification::NOTIFICATION_TYPE_INFO, $title, $message, $url, $url_target);
    }

    public function success($player_id, $source_type, $title = NULL, $message = NULL, $url = NULL, $url_target = NULL){
        return $this->createNotify($player_id, $source_type, Player_notification::NOTIFICATION_TYPE_SUCCESS, $title, $message, $url, $url_target);
    }

    public function warning($player_id, $source_type, $title = NULL, $message = NULL, $url = NULL, $url_target = NULL){
        return $this->createNotify($player_id, $source_type, Player_notification::NOTIFICATION_TYPE_WARNING, $title, $message, $url, $url_target);
    }

    public function danger($player_id, $source_type, $title = NULL, $message = NULL, $url = NULL, $url_target = NULL){
        return $this->createNotify($player_id, $source_type, Player_notification::NOTIFICATION_TYPE_DANGER, $title, $message, $url, $url_target);
    }
}