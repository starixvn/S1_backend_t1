
<?php

$loggedPlayerId=$this->utils->getLoggedPlayerId();

$bigWallet=$this->utils->getBigWalletByPlayerId($loggedPlayerId);

$subwalletsBalance = array();

foreach ($bigWallet['sub'] as $apiId=>$subWallet) {
    $subwalletsBalance[$apiId]=$subWallet['total_nofrozen'];
}

$total_balance = $bigWallet['main']['total_nofrozen'] + array_sum($subwalletsBalance) + $bigWallet['main']['frozen'];

?>

<div class="menu">
    <div class="member_main">
        <div class="member_main_top">
           <span class="font1"><?=lang('cp.emailMsgPassChg1')?>，<a href="<?=site_url('player_center/profile#usersetting')?>"><?=$player['username']?></a></span><span class="font2"><?=lang('Good luck in your games')?></span>
        </div>
        <div class="member_main_center">
            <div class="member_main_left">
                <div class="member_main_left_icon_list">
                    <span class="member_main_left_icon"><i class="top_icon_1"></i><i class="top_icon_menutext"><a href="<?=site_url('player_center/withdraw')?>"><?=lang('Bank Account')?></a></i></span>
                    <span class="member_main_left_icon"><i class="top_icon_2"></i><i class="top_icon_menutext"><a href="<?=site_url('player_center/profile')?>#usersetting"><?=lang('Real Name')?></a></i></span>
                    <span class="member_main_left_icon"><i class="top_icon_3"></i><i class="top_icon_menutext">

                        <?php
                            $email = ($player['verified_email']) ? true : false;
                        ?>

                        <?php
                            if( $email ){
                        ?>
                                <a href="javascript:void(0)" title="<?=lang('Email')?>">
                                    <?=lang('cms.verificationStatus.ok')?> 
                                </a>
                        <?php
                            }else{
                        ?>
                                <a href="<?=site_url('player_center/profile')?>#usersetting_email_verification">
                                    <?=lang('Not verified')?> 
                                </a>

                        <?php
                            }
                        ?>

                    </i></span>
                    <span class="member_main_left_icon"><i class="top_icon_4"></i><i class="top_icon_menutext">

                        <?php
                            $mobile = ($player['verified_phone'] != '' && $player['verified_phone'] != 0) ? true : false;
                        ?>

                        <?php
                            if( $mobile ){
                        ?>
                                <a href="javascript:void(0)" title="<?=lang('Phone Number')?>">
                                    <?=lang('cms.verificationStatus.ok')?> 
                                </a>
                        <?php
                            }else{
                        ?>
                                <a href="<?=site_url('player_center/profile')?>#usersetting_mobile_verification">
                                    <?=lang('Not verified')?> 
                                </a>

                        <?php
                            }
                        ?>

                        
                    </i></span>
                </div>
                <p class="user_top_center_p3"><?=lang('player.42')?>：<?=$this->utils->getPlayerLastLogInTime()?></p>
            </div>

            <div class="member_main_middle">
                <?=lang('Wallet Balance')?>:<br><font data-bind="text: amount">
                    <label id="playerTotalBalance"><?=$this->utils->displayCurrency($player['totalBalanceAmount'])?></label>
                    </font>
                    <span class="refreshBalanceButton" onclick="refreshBalance()"></span>
            </div>

            <div id="vip_level" class="member_main_right vip_level" style="position: relative;">
                <label class="viploading"><?=lang('text.loading')?></label>
            </div>
            
            <div class="clear"></div>
        </div>
    </div>
    <div class="main_bottom">
        <?php
            $uri_2segments = $this->uri->segment(1) . '/' . $this->uri->segment(2);
            $uri_3segments = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3);
        ?> 
        <div class="menu_div <?=($uri_2segments == "player_center/auto_payment" || $uri_2segments == "player_center/manual_payment") ? 'selected' : ''?>" ><a href="<?= site_url('player_center/iframe_makeDeposit'); ?>" class="menua"><i class="ck"></i><i class="menutext"><?=lang('Deposit')?></i></a></div>
        <div class="menu_div <?=( $uri_2segments == "player_center/dashboard" ) ? 'selected' : ''?>"><a href="<?=site_url('player_center/dashboard'); ?>" class="menua"><i class="zz"></i><i class="menutext"><?=lang('cashier.10')?></i></a></div>
        <div class="menu_div <?=(  $uri_2segments == "player_center/withdraw" ) ? 'selected' : ''?>"><a href="<?= site_url('player_center/withdraw'); ?>" class="menua"><i class="tk"></i><i class="menutext"><?=lang('Withdrawal')?></i></a></div>
        <div class="menu_div <?=(  $uri_2segments == "player_center/viewTransactions" ) ? 'selected' : ''?>"><a href="<?= site_url('player_center/viewTransactions'); ?>" class="menua"><i class="jy"></i><i class="menutext"><?=lang('Transaction Record')?></i></a></div>
        <div class="menu_div <?=(  $uri_2segments == "player_center/my_promo" ) ? 'selected' : ''?>"><a href="<?= site_url('player_center/my_promo'); ?>" class="menua"><i class="yh"></i><i class="menutext"><?=lang('cashier.myPromo')?></i></a></div>
    </div>
    <div class="clear"></div>
</div>