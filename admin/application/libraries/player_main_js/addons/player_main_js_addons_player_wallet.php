<?php
/**
 * player_main_js_addons_player_wallet.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Player_main_js_addons_player_wallet extends Player_main_js_addons_abstract {
    public function isEnabled(){
        return TRUE;
    }

    public function variables(){
        $game_with_fixed_currency = $this->CI->config->item('game_with_fixed_currency');
        $game_with_fixed_currency = (!empty($game_with_fixed_currency)) ? $game_with_fixed_currency : [];

        $variables['player_wallet'] = [
            'game_transfer_limit' => $this->CI->utils->getGameTransferLimit(),
            'game_with_fixed_currency' => $game_with_fixed_currency,
        ];
        return $variables;
    }
}