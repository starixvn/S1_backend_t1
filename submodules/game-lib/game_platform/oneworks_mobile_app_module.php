<?php

trait oneworks_mobile_app_module {

    public function mobileAppAuthentication($request){
        return $request;
    }

    public function getMobileAppToken($playerName){
        $gameUsername = $this->getGameUsernameByPlayerUsername($playerName);
        $context = array(
            'callback_obj' => $this,
            'callback_method' => 'processResultForGetToken',
            'playerName' => $playerName,
        );

        $params = array(
            "vendor_id" => $this->oneworks_vendor_id,
            "vendor_member_id" => $gameUsername,
        );

        return $this->callApi(self::API_checkLoginToken, $params, $context);
        
    }

    public function processResultForGetToken($params){
        $responseResultId = $this->getResponseResultIdFromParams($params);
        $resultJsonArr = $this->getResultJsonFromParams($params);
        $playerName = $this->getVariableFromContext($params, 'playerName');

        $success = $this->processResultBoolean($responseResultId, $resultJsonArr, $playerName);
        $result = array();
        if($success){
            $result['sb_token'] = isset($resultJsonArr['Data']) ? $resultJsonArr['Data']: null;//sportsbook token
            $player_token = $this->getPlayerTokenByUsername($playerName);//player token
            $result['fund_transfer_link'] = $this->utils->getSystemUrl('player',"/callback/game/58/transfer/?token={$player_token}");
        }
        return array($success, $result);
    }
    
}
