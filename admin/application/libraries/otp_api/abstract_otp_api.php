<?php

/**
 *
 * Defines general behavior of external login API classes.
 *
 * General behaviors include:
 * * Generate Content
 * * Send Content
 *
 *
 * @category API
 * @version 6.16
 * @copyright 2013-2019 TripleOneTech
 *
 */
abstract class Abstract_otp_api {

    function __construct() {
        $this->CI = &get_instance();
        $this->utils = $this->CI->utils;
    }

    private $objectType=null;
    private $objectId=null;
    private $objectName=null;
    private $object=null;

    public function getObjectType(){
        return $this->objectType;
    }

    public function getObjectId(){
        return $this->objectId;
    }

    public function getObjectName(){
        return $this->objectName;
    }

    public function getObject(){
        return $this->object;
    }

    const OBJECT_TYPE_ADMINUSER=1;
    const OBJECT_TYPE_PLAYER=2;
    const OBJECT_TYPE_AGENCY=3;
    const OBJECT_TYPE_AFFILIATE=4;

    public function initAdminUser($userId){
        return $this->initObject(self::OBJECT_TYPE_ADMINUSER, $userId);
    }

    public function initPlayer($playerId){
        return $this->initObject(self::OBJECT_TYPE_PLAYER, $playerId);
    }

    public function initAgency($agencyId){
        return $this->initObject(self::OBJECT_TYPE_AGENCY, $agencyId);
    }

    public function initAffiliate($affiliateId){
        return $this->initObject(self::OBJECT_TYPE_AFFILIATE, $affiliateId);
    }

    public function initObject($objectType, $objectId){
        $success=false;
        $this->objectType=$objectType;
        $this->objectId=$objectId;
        $this->object=null;
        switch ($objectType) {
            case self::OBJECT_TYPE_ADMINUSER:
                //load user
                $this->CI->load->model(['users']);
                $this->object=$this->CI->users->getUserById($objectId);
                if(!empty($this->object)){
                    $this->objectName=$this->object['username'];
                    $success=true;
                }
                break;
            case self::OBJECT_TYPE_PLAYER:
                //load player
                $this->CI->load->model(['player_model']);
                $this->object=$this->CI->player_model->getPlayerArrayById($objectId);
                if(!empty($this->object)){
                    $this->objectName=$this->object['username'];
                    $success=true;
                }
                break;
            case self::OBJECT_TYPE_AGENCY:
                //load player
                $this->CI->load->model(['agency_model']);
                $this->object=$this->CI->agency_model->get_agent_by_id($objectId);
                if(!empty($this->object)){
                    $this->objectName=$this->object['agent_name'];
                    $success=true;
                }
                break;
            case self::OBJECT_TYPE_AFFILIATE:
                //load player
                $this->CI->load->model(['affiliatemodel']);
                $this->object=$this->CI->affiliatemodel->getAffiliateById($objectId);
                if(!empty($this->object)){
                    $this->objectName=$this->object['username'];
                    $success=true;
                }
                break;
        }

        return $success;
    }

    /**
     * get qr code , url or image
     * @param  string $title
     * @param  array $extra
     * @return array ['url'=>, 'text'=>, 'secret'=>]
     */
    public abstract function initCodeInfo(array $extra=null);

    /**
     * validateCode
     * @param  string     $secret
     * @param  string     $code
     * @param  array|null $extra
     * @return array ['success'=>, 'message'=>]
     */
    public abstract function validateCode($secret, $code, array $extra=null);

    public function getPlatformCode(){
        return OTP_AUTH_API;
    }

    public function getSettings(){
        return $this->CI->utils->getConfig('otp_settings');
    }

    public function returnCodeInfo($url, $text, $secret){
        return ['url'=>$url, 'text'=>$text, 'secret'=>$secret];
    }

    public function getOTPFormat($name, $secret, $title = null){
        $content = 'otpauth://totp/'.$name.'?secret='.$secret.'';
        if (isset($title)) {
            $content .= '&issuer='.urlencode($title);
        }

        return $content;
    }

}
