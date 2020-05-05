<div class="container dashboar-container">
    <?php include __DIR__ . '/../../includes/overview.php';?>

    <div class="member-center row">
      <div class="col-md-3 mc-ul navigation-menu">
          <?php include VIEWPATH . '/resources/common/components/player_center_navigation.php';?>
      </div>
        <div class="col-md-9 tab-content mc-content">
            <div id="memberCenter" class="tab-pane main-content fade in active">
                <h1><?php
                        $memberCenterName = $this->utils->getConfig('playercenter.memberCenterName');
                        echo lang($memberCenterName ?: "Cashier Center");
                    ?>
                </h1>
                <div class="row cashier-center-header">
                    <div class="col-sm-4 col-md-4">
                        <div class="inner-content">
                            <p><?=lang("Main Wallet Total")?></p>
                            <h2 class="d-yen main-total"><?=$this->utils->displayCurrency(isset($total_main_wallet_balance) ? $total_main_wallet_balance : 0)?></h2>
                            <div class="text-right">
                            <?php if($this->utils->isEnabledFeature('cashier_multiple_refresh_btn')) : ?>
                                <a href="javascript:void(0)" onclick="return PlayerCashier.manuallyRefreshBalance();" class="btn quick-refresh"><i class="glyphicon glyphicon-refresh"></i></a>
                            <?php else : ?>
                                <a href="javascript:void(0)" onclick="return PlayerCashier.manuallyRefreshBalance();" class="refreshBalanceButton btn" style="display: inline-block;"><i class="glyphicon glyphicon-refresh"></i><?=lang('lang.refreshbalance')?></a>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <div class="inner-content">
                            <p><?=lang("Game Wallet Total")?></p>
                            <h2 class="game-total"><?=$this->utils->displayCurrency(isset($total_subwallet_balance) ? $total_subwallet_balance : 0) ?></h2>
                            <div class="clearfix text-right">
                                <a href="javascript:void(0)" class="btn d-btn <?= empty($total_subwallet_balance) ? 'disabled' : '' ?> " id="transferAllToMainBtn"><?=lang("Transfer Back All")?></a>
                            <?php if($this->utils->isEnabledFeature('cashier_multiple_refresh_btn')) : ?>
                                <a href="javascript:void(0)" onclick="return PlayerCashier.manuallyRefreshBalance();" class="btn quick-refresh"><i class="glyphicon glyphicon-refresh"></i></a>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <div class="inner-content">
                            <p><?=lang("Total Balance")?></p>
                            <h2 class="total-balance"><?=$this->utils->displayCurrency(isset($total_no_frozen) ? $total_no_frozen : 0)?></h2>
                            <div class="text-right">
                            <?php if($this->utils->isEnabledFeature('cashier_multiple_refresh_btn')) : ?>
                                <a href="javascript:void(0)" onclick="return PlayerCashier.manuallyRefreshBalance();" class="btn quick-refresh"><i class="glyphicon glyphicon-refresh"></i></a>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row cashier-center-withdrawal mt20">
                    <?php if(!$this->utils->isEnabledFeature('hidden_player_center_pending_withdraw_balance_tab')) :  ?>
                    <div class="col-sm-4 cashier_center_pending_withdraw_balance">
                        <div class="clearfix inner-content">
                            <div class="col-xs-7 text-label"><?=lang("cashier.pendingBalance");?></div>
                            <div class="col-xs-5 text-right pending-total"><?=$this->utils->displayCurrency(isset($total_frozen) ? $total_frozen : 0)?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(!$this->utils->isEnabledFeature('hidden_player_center_total_deposit_amount_tab')) :  ?>
                    <div class="col-sm-4 cashier_center_total_deposit_amount">
                        <div class="clearfix inner-content">
                            <div class="col-xs-7 text-label"><?=lang("player.ui15");?></div>
                            <div class="col-xs-5 text-right"><?=$this->utils->displayCurrency($playerBalance[0])?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(!$this->utils->isEnabledFeature('hidden_player_center_total_withdraw_amount_tab')) :  ?>
                    <div class="col-sm-4 cashier_center_total_withdraw_amount">
                        <div class="clearfix inner-content">
                            <div class="col-xs-7 text-label"><?=lang("player.ui18");?></div>
                            <div class="col-xs-5 text-right"><?=$this->utils->displayCurrency($playerBalance[1])?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <ul class="fm-ul row cashier-center-tabs">
                    <?php if(!$this->utils->isEnabledFeature('always_auto_transfer_if_only_one_game')) {?>
                        <li id="transfer-panel-page" class="col-xs-3<?=($sub_nav_active == 'transfer') ? ' active' : '' ?>">
                            <a href="<?=$system_hosts['player']?>/player_center/dashboard/cashier#memberCenter"><i class="fa fa-credit-card" aria-hidden="true"></i><?= lang('Transfer') ?></a>
                        </li>
                    <?php } ?>
                    <li id="deposit-panel-page" class="col-xs-3<?=($sub_nav_active == 'deposit') ? ' active' : '' ?>">
                        <a href="<?=$system_hosts['player']?>/player_center2/deposit"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?= lang('Deposit') ?>

                        </a>
                    </li>
                    <li id="withdrawal-panel-page" class="col-xs-3<?=($sub_nav_active == 'withdraw') ? ' active' : '' ?>">
                        <a href="<?=$system_hosts['player']?>/player_center2/withdraw"><i class="fa fa-credit-card" aria-hidden="true"></i><?= lang('Withdrawal') ?></a>
                    </li>
                    <li id="bank-info-panel-page" class="col-xs-3<?=($sub_nav_active == 'bank_account') ? ' active' : '' ?>">
                        <a href="<?=$system_hosts['player']?>/player_center2/bank_account"><i class="fa fa-university" aria-hidden="true"></i><?= lang('cashier.16') ?></a>
                    </li>
                </ul>
                <div class="tab-content fm-content">
                    <?=$main_content?>
                </div>
            </div>
        </div>
    </div>
</div>