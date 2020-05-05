<?php
/**
 * player_main_js_addons_player.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Player_main_js_addons_player extends Player_main_js_addons_abstract {
    public function isEnabled(){
        return TRUE;
    }

    public function variables(){
        $logged = $this->CI->authentication->isLoggedIn();

        $playerId = 0;
        $playerUsername = null;
        $playerInfo = [
            'firstName' => ''
        ];
        $VIP_group = null;
        $messageCount = 0;

        $walletInfo = [];

        //

        $token = null;
        if ($logged) {
            $this->CI->session->unset_userdata('httpHeaderInfo');

            $playerId = $this->CI->authentication->getPlayerId();
            $playerUsername = $this->CI->authentication->getUsername();
            $playerInfo = $this->CI->utils->get_player_info($playerId);
            $VIP_group = $this->CI->authentication->getPlayerMembership();

            $walletInfo = $this->CI->utils->getSimpleBigWallet($playerId);
            $messageCount = $this->CI->internal_message->countPlayerUnreadMessages($playerId);

            if(!empty($playerId)){
                $token = $this->CI->common_token->getPlayerToken($playerId);
            }
        }

        $total_balance = isset($walletInfo['total_balance']['balance']) ? $walletInfo['total_balance']['balance'] : 0;
        $total_withfrozen = isset($walletInfo['total_withfrozen']) ? $walletInfo['total_withfrozen'] : 0;

        $variables = [
            'currentLang' => $this->CI->language_function->getCurrentLanguage(),
            'currentLangName' => $this->CI->language_function->getCurrentLanguageName(),
            'is_mobile' => $this->CI->utils->is_mobile(),
            'logged' => $logged,
            'playerId' => $playerId,
            'playerUsername' => $playerUsername,
            'walletInfo' => $walletInfo,
            'main_wallet_id' => Wallet_model::MAIN_WALLET_ID,
            'token' => $token,
            'VIP_group' => $VIP_group,
            'firstName' => $playerInfo['firstName'],
            'ui' => [
                'withdraw_password' => empty($playerInfo['withdraw_password']) ? '' : '1',
                'total_balance' => $total_balance,
                'total_hasfrozen' => $total_withfrozen,
                'messageCount' => $messageCount,
            ],
            'templates' => [
                'player_active_profile_picture' => $this->CI->utils->getPlayerActiveProfilePicture($playerId),
            ],
            'player_center' => [
                'player_auto_lock' => $this->CI->operatorglobalsettings->getSettingIntValue('player_auto_lock', 0),
                'player_auto_lock_time_limit' => $this->CI->operatorglobalsettings->getSettingIntValue('player_auto_lock_time_limit', 600),
                'player_auto_lock_password_failed_attempt' => $this->CI->operatorglobalsettings->getSettingIntValue('player_auto_lock_password_failed_attempt', $this->CI->utils->getUploadConfig('player_auto_lock_password_failed_attempt')),
                'player_auto_lock_window_auto_logout' => 60,
                'locale' => [
                    'player_auto_lock_window_header' => lang('player_auto_lock_window_header'),
                    'player_auto_lock_window_auto_logout' => lang('player_auto_lock_window_auto_logout'),
                    'player_auto_lock_window_submit' => lang('player_auto_lock_window_submit')
                ]
            ]
        ];

        return $variables;
    }
}