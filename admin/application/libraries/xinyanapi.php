<?php

class Xinyanapi{
    const ACTION_REGISTERSUBSCRIBER_TITLE = 'Register Subscriber';

    public function __construct(){
        $this->CI = &get_instance();
        // $this->utils->info_log('load subscriber class', get_class());
    }

    public function submitToXinyanApi($playerId, $player, $playerDetial) {
        $this->CI->load->library('utils');
        $register_options = $this->CI->utils->getConfig('register_event_xinyan_api');

        $this->CI->utils->debug_log('==============submitToXinyanApi register_options', $register_options);

        $username  = $player['username'];
        $secure_id = $player['secure_id'];
        $id_card   = $playerDetial[0]['id_card_number'];
        $name      = $playerDetial[0]['firstName'];
        $mobile    = $playerDetial[0]['contactNumber'];
        $url       = $register_options['register_event_url'];
        $register_priv_key          = $register_options['register_priv_key'];
        # GET DISPATCH ACCOUNT LEVEL FROM CONFIG
        $new_dispatch_account_level = $register_options['assign_members_in_specific_dispatc_level'];

        $params['member_id']      = $register_options['member_id'];
        $params['terminal_id']    = $register_options['terminal_id'];
        $params['data_type']      = $register_options['data_type'];
        $submit['member_id']      = $params['member_id'];
        $submit['terminal_id']    = $params['terminal_id'];
        $submit['trans_id']       = $secure_id.'_'.$this->getMillisecond();
        $submit['trade_date']     = date('Ymdhis');
        $submit['id_card']        = $id_card;
        $submit['name']           = $name;
        $submit['mobile']         = $mobile;
        $submit['verify_element'] = $register_options['verify_element'];

        $submit   = json_encode($submit);

        $params['data_content']   = $this->encryptedByXinyanPrivateKey($submit, $register_priv_key);
        $this->CI->utils->debug_log('==============submitToXinyanApi params', $params, $url, $submit);
        $result   = $this->processXinyanCurl($url, $params, true, $secure_id, $playerId);
        $response = json_decode($result,true);

        return $response;
    }

    protected function processXinyanCurl($url, $params, $post = true, $secure_id, $playerId) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $response    = curl_exec($ch);
        $this->CI->utils->debug_log('==============processXinyanCurl curl content ', $response);
        $errCode     = curl_errno($ch);
        $error       = curl_error($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header      = substr($response, 0, $header_size);
        $content     = substr($response, $header_size);
        $statusCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $last_url    = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $statusText  = $errCode . ':' . $error;
        curl_close($ch);

        $this->CI->utils->debug_log('url', $url, 'params', $params , 'response', $response, 'errCode', $errCode, 'error', $error, 'statusCode', $statusCode);

        $response_result_id = $this->submitPreprocess($params, $content, $url, $response, array('errCode' => $errCode, 'error' => $error, 'statusCode' => $statusCode), $secure_id, $playerId);

        return $content;
    }

    public function submitPreprocess($params, $content, $url, $response, $fields=NULL, $playerSecureId=NULL, $playerId) {
        $player_id = NULL;
        $secure_id = NULL;
        $order_id  = NULL;
        // $method = 'register_response';
        if(!empty($playerSecureId)){
            #save to response result
            $player_id = $playerId;
            $secure_id = $playerSecureId;
            $method = 'register_response';
            $this->CI->utils->debug_log('==============player_id', $player_id , 'secure_id', $secure_id);
        }
        $this->CI->load->model(array('response_result'));
        #save more
        $resultAll['type']       = 'submit';
        $resultAll['url']        = $url;
        $resultAll['params']     = $params;
        $resultAll['content']    = $content;
        $statusCode = (array_key_exists("statusCode", $fields)) ? $fields['statusCode'] : NULL;
        $errCode    = (array_key_exists("errCode", $fields)) ? $fields['errCode'] : NULL;
        $error      = (array_key_exists("error", $fields)) ? $fields['error'] : NULL;
        $statusText = $errCode.":".$error;

        return $this->CI->response_result->saveResponseResult(IDENTITY_API, Response_result::FLAG_NORMAL,
            $method, 'submit', json_encode($resultAll), $statusCode, $statusText, NULL,
            array('player_id' => $player_id, 'related_id1' => $player_id, 'related_id2' => $secure_id));
    }

    public function encryptedByXinyanPrivateKey($data_content, $registe_priv_key)
    {
        $data_content = base64_encode($data_content);
        $encrypted = "";
        $totalLen = strlen($data_content);
        $encryptPos = 0;
        while ($encryptPos < $totalLen) {
            openssl_private_encrypt(substr($data_content, $encryptPos, 117), $encryptData, $this->getXinyanPrivKey($registe_priv_key));
            $encrypted .= bin2hex($encryptData);
            $encryptPos += 117;
        }
        return $encrypted;
    }

    private function getXinyanPrivKey($registe_priv_key) {
        $registe_priv_key = $registe_priv_key;
        $priv_key = '-----BEGIN PRIVATE KEY-----' . PHP_EOL . chunk_split($registe_priv_key, 64, PHP_EOL) . '-----END PRIVATE KEY-----' . PHP_EOL;
        return openssl_get_privatekey($priv_key);
    }

    public function getMillisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    public function assignToDispatchAccount($playerId, $username, $new_dispatch_account_level){
        $this->CI->utils->debug_log('==============assignToDispatchAccount username ', $username);

        $this->CI->load->model(array('player_model'));

        $oldlevel = $this->CI->player_model->getPlayerDispatchAccountLevel($playerId);
        $this->CI->utils->debug_log('==============assignToDispatchAccount oldlevel', $oldlevel);
        if(!isset($oldlevel)){
            $msg = 'get dispatch account oldlevel id is failed '.$oldlevel;
            return array('success' => false, 'message' => $msg);
        }

        $this->CI->player_model->adjustDispatchAccountLevel($playerId, $new_dispatch_account_level);

        $newlevel = $this->CI->player_model->getPlayerDispatchAccountLevel($playerId);
        $this->CI->utils->debug_log('==============assignToDispatchAccount newlevel', $newlevel);

        if($newlevel['dispatch_account_level_id'] != $new_dispatch_account_level){
            $msg = 'get dispatch account newlevel id is failed ,do not match '.$new_dispatch_account_level;
            return array('success' => false, 'message' => $msg);
        }

        $this->CI->utils->recordAction(self::ACTION_REGISTERSUBSCRIBER_TITLE, lang('player.100'), "User " . $username . "register xinyan has adjusted dispatch account of player '" . $playerId . "'");

        $this->CI->player_model->savePlayerUpdateLog($playerId, lang('player.100') . ' - ' .
            lang('adjustmenthistory.title.beforeadjustment') . ' (' . lang($oldlevel['group_name']) . ' - ' . $oldlevel['level_name'] . ') ' .
            lang('adjustmenthistory.title.afteradjustment') . ' (' . lang($newlevel['group_name']) . ' - ' . $newlevel['level_name'] . ') ',$username); // Add log in playerupdatehistory

        $current_disparch_account_level = $this->CI->player_model->getCurrentDispatchAccountLevel($playerId);
        $this->CI->utils->debug_log('==============current_disparch_account_level', $current_disparch_account_level);

        array_walk($current_disparch_account_level, function(&$row){
            $row['group_name'] = lang($row['group_name']);
            $row['level_name'] = lang($row['level_name']);
        });

        return array('success' => true, 'message' => $current_disparch_account_level);
    }
}
