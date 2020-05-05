<?php include APPPATH . "/views/includes/big_wallet_details.php";?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <?=lang('player.ui66')?>
        </h4>
    </div>
    <div class="panel-body">
        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-md-3">
                    <label><?=lang('pay.username')?></label>
                    <div class="input-group">
                        <input type="text" class="form-control" readonly="readonly" value="<?=$playerDetails[0]['username']?>"/>
                        <span class="input-group-btn">
                            <a target="_blank" href="<?php echo site_url('/player_management/userInformation/'.$playerDetails[0]['playerId']); ?>" class="btn btn-primary" title="<?=lang('player.ui67')?>"><i class="fa fa-search"></i></a>
                        </span>
                    </div>
                </div>

                <div class="col-md-3">
                    <label><?=lang("pay.realname")?></label>
                    <input type="text" class="form-control" readonly="readonly" value="<?=$playerDetails[0]['firstName'] . ' ' . $playerDetails[0]['lastName']?>"/>
                </div>

                <div class="col-md-3">
                    <label><?=lang('pay.playerlev')?></label>
                    <input type="text" class="form-control" readonly="readonly" value="<?=lang($playerDetails[0]['groupName']) . ' ' . $playerDetails[0]['vipLevel']?>"/>
                </div>

                <div class="col-md-3">
                    <label><?=lang('pay.memsince')?></label>
                    <input type="text" class="form-control" readonly="readonly" value="<?=$playerDetails[0]['createdOn']?>"/>
                </div>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?=lang('pay.walltbal')?>
                  <a href="javascript:void(0)" class="btn btn-success btn-xs show_bigwallet_details" data-playerid="<?php echo $playerDetails[0]['playerId'];?>">
                      <i class="fa fa-money"></i>
                      <?php echo lang('Wallet Details');?>
                  </a>
                        </h4>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><?=lang('cashier.36')?></th>
                                <th style="text-align: right;"><?=lang('con.pb')?></th>
                                <th style="text-align: right;"><?=lang('lang.action')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=lang('pay.mainwalltbal')?></td>
                                <td align="right" class="<?=$walletAccounts['main'] ? '' : 'text-muted'?>"><?=number_format($walletAccounts['main'], 2)?></td>
                                <td align="right">
                                    <ul style="list-style: none">
                                        <?php if ($this->permissions->checkPermissions('manually_add_bonus')) {?>
                                        <li><a href="/marketing_management/manually_add_bonus/<?=$playerDetails[0]['playerId']?>" title="<?=lang('transaction.transaction.type.' . Transactions::ADD_BONUS)?>"><i class="fa fa-star"></i> <?=lang('transaction.transaction.type.' . Transactions::ADD_BONUS)?></a></li>
                                        <?php }?>
                                        <?php if ($this->permissions->checkPermissions('manually_subtract_bonus')) {?>
                                        <li><a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/<?=implode('/', array(0, Transactions::SUBTRACT_BONUS, $playerDetails[0]['playerId']))?>" title="<?=lang('transaction.transaction.type.' . Transactions::SUBTRACT_BONUS)?>"><i class="fa fa-minus"></i> <?=lang('transaction.transaction.type.' . Transactions::SUBTRACT_BONUS)?></a></li>
                                         <?php }?>
                                        <li><a data-target="#balance-adjustment-form" href="<?php echo site_url('/payment_management/add_withdraw_condition/'.$playerDetails[0]['playerId']); ?>" title="<?=lang('Add Withdraw Condition')?>"><i class="fa fa-trophy"></i> <?=lang('Add Withdraw Condition')?></a></li>
                                        <?php if($this->permissions->checkPermissions('adjust_manually_wallet')){ ?>
                                        <li><a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/<?=implode('/', array(0, Transactions::MANUAL_SUBTRACT_BALANCE, $playerDetails[0]['playerId']))?>" title="<?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_BALANCE)?>"><i class="fa fa-minus"></i> <?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_BALANCE)?></a></li>
                                        <li><a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/<?=implode('/', array(0, Transactions::MANUAL_ADD_BALANCE, $playerDetails[0]['playerId']))?>" title="<?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_BALANCE)?>"><i class="fa fa-plus"></i> <?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_BALANCE)?></a></li>
                                        <?php }?>
                                        <li><a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/<?=implode('/', array(0, Transactions::AUTO_ADD_CASHBACK_TO_BALANCE, $playerDetails[0]['playerId']))?>" title="<?=lang('transaction.transaction.type.' . Transactions::AUTO_ADD_CASHBACK_TO_BALANCE)?>"><i class="fa fa-star"></i> <?=lang('transaction.transaction.type.' . Transactions::AUTO_ADD_CASHBACK_TO_BALANCE)?></a></li>
                                        <li><a href="<?php echo site_url('/payment_management/newDeposit');?>"> <i class="fa fa-plus"></i> <?php echo lang('New Deposit'); ?></a></li>
                                        <li><a href="<?php echo site_url('/payment_management/newWithdrawal');?>"> <i class="fa fa-plus"></i> <?php echo lang('New Withdraw'); ?></a></li>
                                    </ul>
                                </td>
                            </tr>
                            <?php

foreach ($game_platforms as $game_platform) {
	$sys_code = $game_platform['system_code'];
	$wallet_bal = isset($walletAccounts[strtolower($sys_code)]) ? $walletAccounts[strtolower($sys_code)] : 0;
	?>
                                <tr>
                                    <td><?=$game_platform['system_code'] . ' ' . lang('pay.subwalltbal')?></td>
                                        <td align="right" class="<?php echo $wallet_bal ? '' : 'text-muted'; ?>"><?php echo number_format($wallet_bal, 2); ?></td>
                                        <td align="right">
                                            <ul style="list-style: none">
                                                <li><a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/<?=implode('/', array($game_platform['id'], Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET, $playerDetails[0]['playerId']))?>" title="<?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET)?>"><i class="fa fa-minus"></i> <?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET)?></a></li>
                                                <li><a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/<?=implode('/', array($game_platform['id'], Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET, $playerDetails[0]['playerId']))?>" title="<?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET)?>"><i class="fa fa-plus"></i> <?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET)?></a></li>
                                                <?php if ($game_platform['is_seamless'] && $this->permissions->checkPermissions('adjust_manually_seamless_wallet')) {?>
                                                     <li><a data-target="#balance-adjustment-form" href="/payment_management/adjust_seamless_balance_form/<?=implode('/', array($game_platform['id'], Transactions::MANUAL_ADD_SEAMLESS_BALANCE, $playerDetails[0]['playerId']))?>" title="<?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_SEAMLESS_BALANCE)?>"><i class="fa fa-plus"></i> <?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_SEAMLESS_BALANCE)?></a></li>
                                                     <li><a data-target="#balance-adjustment-form" href="/payment_management/adjust_seamless_balance_form/<?=implode('/', array($game_platform['id'], Transactions::MANUAL_SUBTRACT_SEAMLESS_BALANCE, $playerDetails[0]['playerId']))?>" title="<?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_SEAMLESS_BALANCE)?>"><i class="fa fa-minus"></i> <?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_SEAMLESS_BALANCE)?></a></li>
                                                <?php } ?>
                                            </ul>
                                        </td>
                                </tr>
                            <?php

}
?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th><?=lang('system.word66')?></th>
                                <th style="text-align: right;"><?=number_format($walletAccounts['total'], 2)?></th>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="panel-footer"></div>
                </div>
            </div>

            <div class="col-md-6" id="balance-adjustment-form"></div>

        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"><?=lang('player.ui68')?></h4>
            </div>
            <div class="table-responsive" id="adjustment-history"></div>
            <div class="panel-body text-center" style="border-top: 1px solid #ccc;">
                <a href="/payment_management/adjustment_history/<?=$playerDetails[0]['playerId']?>"><i class="fa fa-list"></i> <?=lang('player.ui69')?></a>
            </div>
            <div class="panel-footer"></div>
        </div>

    </div>
    <div class="panel-footer"></div>
</div>

<script type="text/javascript">
    $(function() {

        $('#adjustment-history').load("/payment_management/adjustment_history/<?=$playerDetails[0]['playerId']?>?items_per_page=5 #adjustment-history .table");
        // $('#balance-adjustment-form').load("/payment_management/adjust_balance_form/<?=implode('/', array(0, Transactions::MANUAL_ADD_BALANCE, $playerDetails[0]['playerId']))?>");

        $('a[data-target]').click( function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            var url = $(this).attr('href');
            $(target).load(url);
        });

    });
</script>