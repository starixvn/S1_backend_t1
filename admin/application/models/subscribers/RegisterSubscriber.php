<?php

require_once dirname(__FILE__) . "/../events/RegisterEvent.php";
require_once dirname(__FILE__) . "/AbstractSubscriber.php";

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegisterSubscriber extends AbstractSubscriber implements EventSubscriberInterface{
    const ACTION_REGISTERSUBSCRIBER_TITLE = 'Register Subscriber';
    const SUCCESS_CODE = '0';

    public function __construct(){
        parent::__construct();
        // $this->utils->info_log('load subscriber class', get_class());
    }

    public static function getSubscribedEvents(){
        return array(
            Queue_result::EVENT_REGISTER_AFTER_DB_TRANS => 'afterRegisterDBTrans',
        );
    }

    public function afterRegisterDBTrans(RegisterEvent $event){

        $playerId=$event->getPlayerId();
        $player=null;
        $playerDetial=null;

        $this->load->model(array('player_model','player_api_verify_status'));
        $this->load->library('xinyanapi');

        if(!empty($playerId)){
            $player = $this->player_model->getPlayerArrayById($playerId);
            $playerDetial = $this->player_model->getPlayerDetails($playerId);
        }

        $this->utils->debug_log('start process Register event', $event, $playerId, $player, $playerDetial);

        if($this->utils->isEnabledFeature('enable_registered_triggerRegisterEvent_for_xinyan_api')) {
            #check Register payment xinyan api
            $response = $this->xinyanapi->submitToXinyanApi($playerId,$player,$playerDetial);
            $register_options = $this->getConfig('register_event_xinyan_api');
            $new_dispatch_account_level = $register_options['assign_members_in_specific_dispatc_level'];
            $username = $player['username'];

            if(!isset($response['success'])){
                $this->player_api_verify_status->add($playerId, player_api_verify_status::API_UNKNOWN);
                $this->CI->utils->debug_log('==============submitToXinyanApi no response');
            }else if($response['success'] && $response['data']['code'] == self::SUCCESS_CODE){
                $this->player_api_verify_status->add($playerId, player_api_verify_status::API_RESPOSE_SUCCESS);
                $this->CI->utils->debug_log('==============submitToXinyanApi response verify success', $response);
            }else{
                $this->player_api_verify_status->add($playerId, player_api_verify_status::API_RESPOSE_FAIL);
                $assignToDispatchAccount = $this->xinyanapi->assignToDispatchAccount($playerId, $username, $new_dispatch_account_level);
                if(!$assignToDispatchAccount['success']){
                    $get_notify_params = $this->getXinyanNotifyParams($response, $username, $playerId);
                    $url = $this->getConfig('register_xinyan_notify_url');
                    $user = $this->getConfig('register_xinyan_notify_user');
                    $channel = $this->getConfig('register_xinyan_notify_channel');
                    $this->utils->sendMessageService($get_notify_params['msg'], $url, $user, $channel);
                    $this->CI->utils->debug_log('==============submitToXinyanApi response verify failed and assing to new Dispatch Account failed ,Will be notified by Mattermost', $response, $assignToDispatchAccount);
                }else{
                    $this->CI->utils->debug_log('==============submitToXinyanApi response verify failed and assing to new Dispatch Account group', $response, $assignToDispatchAccount);
                }
            }
        }
        $this->utils->debug_log('end process Register event', $event);
    }

    public function getXinyanNotifyParams($response, $username, $playerId){
        if (!empty($playerId)) {
            $this->load->model(array('player_model'));

            if($response['data'] == null || empty($response['data'])){
                $desc = $response['errorMsg'].', errorCode =>'.$response['errorCode'];
                $trans_id = 'null';
            }else{
                $desc = $response['data']['desc'];
                $trans_id = $response['data']['trans_id'];
            }

            $dispatchaccountlevel = $this->CI->player_model->getPlayerDispatchAccountLevel($playerId);
            $result_msg =
                "register Xinyan playerId: " .$playerId. " | \n".
                "register Xinyan username: " .$username. " | \n".
                "register Xinyan desc: "     .$desc. " | \n".
                "register Xinyan trans_id: " .$trans_id. " | \n".
                "current Player Dispatch Account Group: " .$dispatchaccountlevel['dispatch_account_level_id'];

                $this->utils->debug_log('============getXinyanNotifyParams true, '.$result_msg);

                $result_msg = "=============== Mismatch member info responded from Xinyan API ===============\nMismatch member not added to Xinyan Mismatch group \n".$result_msg;

            return array('success' => true, 'msg' => $result_msg);
        }
        $this->utils->debug_log('============getXinyanNotifyParams false playerId, '.$playerId);
        return array('success' => false, 'msg' => 'Mattermost notify failed.');
    }
}
