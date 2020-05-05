<?php
/**
 * player_main_js_addons_player_message.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Player_main_js_addons_player_message extends Player_main_js_addons_abstract {
    public function isEnabled(){
        return TRUE;
    }

    public function variables(){
        $variables['message'] = [
            'enabled_refresh_message_on_player' => $this->CI->utils->isEnabledFeature('enabled_refresh_message_on_player'),
            'refreshInternalMessageTimeInterval' => ($this->CI->config->item('get_new_msg_interval')) ? $this->CI->config->item('get_new_msg_interval') : 30000,
            'refreshInternalMessageUrl' => $this->CI->utils->getSystemUrl("player", '/async/get_unread_messages'),
            'loadMessageUrl' => $this->CI->utils->getSystemUrl("player", '/async/get_message'),
            'replyMessageUrl' => $this->CI->utils->getSystemUrl("player", '/async/reply_message'),
        ];

        if($this->CI->utils->isEnabledFeature('enable_player_message_request_form')){
            $this->CI->load->library(['player_message_library']);
            $variables['message']['request_form_settings'] = $this->CI->player_message_library->getRequestFormSettings();
        }

        return $variables;
    }
}