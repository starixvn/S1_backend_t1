<?php
/**
 * player_main_js_addons_player_notification.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Player_main_js_addons_player_notification extends Player_main_js_addons_abstract {
    public function isEnabled(){
        return $this->CI->operatorglobalsettings->getSettingBooleanValue('player_center_notification');
    }

    public function variables(){
        $display_time = $this->CI->utils->getConfig('player_notification_display_time');
        $variables = [
            'player_notification' => [
                'enabled' => $this->CI->operatorglobalsettings->getSettingBooleanValue('player_center_notification'),
                'check_interval' => $this->CI->operatorglobalsettings->getSettingIntValue('player_center_notification_check_interval', 60),
                'display_time' => (!empty($display_time)) ? $display_time : 5
            ]
        ];
        return $variables;
    }
}