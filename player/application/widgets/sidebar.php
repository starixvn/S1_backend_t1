<?php

/**
 * Class Widget_Sidebar
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Widget_Sidebar extends MY_Widget {
    public function initialize($options = []){
        if(!$this->load->get_var('isLogged')){
            return false;
        }

        $playerId = $this->load->get_var('playerId');

        $subwallet=null;
        $success=$this->wallet_model->lockAndTransForPlayerBalance($playerId, function () use (
            $playerId, &$subwallet) {

            $subwallet = $this->wallet_model->getAllPlayerAccountByPlayerId($playerId);
            return !empty($subwallet);
        });
        $data['game'] = $this->external_system->getAllActiveSytemGameApi();
        $data['subwallet'] = $subwallet;

        $data['big_wallet'] = $this->wallet_model->getOrderBigWallet($playerId);
        $data['pendingBalance'] = (object) ['frozen' => $data['big_wallet']['main']['frozen']];
        $data['totalBalance'] = $data['big_wallet']['total'];
        $subwallets = $data['big_wallet']['sub'];
        $data['subwallets'] = $subwallets;

        $data['walletinfo'] = array(
            'mainWallet' => $data['big_wallet']['main']['total_nofrozen'],
            'frozen' => $data['big_wallet']['main']['frozen'],
            'subwallets' => $subwallets
        );

        $this->_data = $data;
    }
}