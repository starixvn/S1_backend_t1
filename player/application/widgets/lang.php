<?php

/**
 * Class Widget_Lang
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Widget_Lang extends MY_Widget {
    public function initialize($options = []){

        $lang = [];
        $lang['Upgrade Condition'] = lang('Upgrade Condition');
        $lang['text.loading'] = lang('text.loading');
        $lang['lang.close'] = lang('lang.close');
        $lang['lang.confirm'] = lang('Confirm');
        $lang['lang.settings'] = lang('lang.settings');
        $lang['alert-success'] = lang('alert-success');
        $lang['alert-info'] = lang('alert-info');
        $lang['alert-warning'] = lang('alert-warning');
        $lang['alert-danger'] = lang('alert-danger');
        $lang['Please update account name to add bank account'] = lang('Please update account name to add bank account');
        $lang['pay.reg'] = lang('pay.reg');
        $lang['cashier.deposit.force_setup_player_deposit_bank_hint'] = lang('cashier.deposit.force_setup_player_deposit_bank_hint');
        $lang['cashier.withdrawal.player_center_withdrawal_bank_messagebox_title_info'] = lang('cashier.withdrawal.player_center_withdrawal_bank_messagebox_title_info');
        $lang['cashier.withdrawal.force_setup_player_withdrawal_bank_hint'] = lang('cashier.withdrawal.force_setup_player_withdrawal_bank_hint');
        $lang['internet_banking'] = lang('internet_banking');
        $lang['over_the_counter_deposit'] = lang('over_the_counter_deposit');
        $lang['atm_transfer'] = lang('atm_transfer');
        $lang['mobile_banking'] = lang('mobile_banking');
        $lang['cash_deposit_machine'] = lang('cash_deposit_machine');
        $lang['This is a required field'] = lang('This is a required field');
        $lang['Before adding a bank account, please set your'] = lang('Before adding a bank account, please set your');
        $lang['Real Name'] = lang('Real Name');
        $lang['Birthday'] = lang('Birthday');
        $lang['Changing Currency'] = lang('Changing Currency');
        $lang['Change Currency Failed'] = lang('Change Currency Failed');

        $this->_data['lang'] = $lang;
    }
}