<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_walletaccount_bank_details_20180126 extends CI_Migration {
    public function up() {
        $this->load->model('wallet_model');
        $result = $this->wallet_model->updateWalletaccountBankdetails();
    }

    public function down() {
    }

}