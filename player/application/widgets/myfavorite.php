<?php

/**
 * Class Widget_Myfavorite
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Widget_Myfavorite extends MY_Widget {
    public function initialize($options = []){
        if(!$this->load->get_var('isLogged')){
            return false;
        }

        $player_id = $this->CI->authentication->getPlayerId();

        $this->_data['favorites'] = $this->utils->getPlayerMyFavoriteGames($player_id);

        $this->_data['player_myfavorite_limit_count'] = $this->utils->getConfig('player_myfavorite_limit_count');
    }
}