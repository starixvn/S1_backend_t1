<?php $col = $this->utils->isEnabledFeature('hide_sub_affiliates_on_affiliate') ? '4' : '3' ?>

<div class="col-lg-<?=$col?> col-md-<?=$col?> col-sm-6 col-xs-12">
    <div class="dashboard-stat blue">
        <div class="visual">
            <i class="fa fa-users"></i>
        </div>
        <div class="details">
            <div class="number">
                <span><?=$total_players;?></span>
            </div>
            <div class="desc"> <?=lang('lang.countplayers');?> </div>
        </div>
        <span class="more"> <?=lang('traffic.today');?>: <strong><?=$today_players_today;?></strong></span>
    </div>
</div>
<div class="col-lg-<?=$col?> col-md-<?=$col?> col-sm-6 col-xs-12">
    <div class="dashboard-stat red">
        <div class="visual">
            <i class="fa fa-credit-card"></i>
        </div>
        <div class="details">
            <div class="number">
                <span><?php echo $total_deposit; ?></span>
            </div>
            <div class="desc"> <?=lang('lang.totaldeposit');?> </div>
        </div>
        <span class="more"> <?=lang('traffic.today');?>: <strong><?=$today_deposit; ?></strong> </span>
    </div>
</div>
<div class="col-lg-<?=$col?> col-md-<?=$col?> col-sm-6 col-xs-12">
    <div class="dashboard-stat green">
        <div class="visual">
            <i class="fa fa-money"></i>
        </div>
        <div class="details">
            <div class="number">
                <span><?php echo $total_withdraw; ?></span>
            </div>
            <div class="desc"> <?=lang('lang.totalwithdraw');?> </div>
        </div>
        <span class="more"> <?=lang('traffic.today');?>: <strong><?=$today_withdraw; ?></strong> </span>
    </div>
</div>
<?php if ( ! $this->utils->isEnabledFeature('hide_sub_affiliates_on_affiliate')): ?>
    <div class="col-lg-<?=$col?> col-md-<?=$col?> col-sm-6 col-xs-12">
        <div class="dashboard-stat purple">
            <div class="visual">
                <i class="fa fa-user-secret"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span><?=$total_subaffiliates;?></span>
                </div>
                <div class="desc"> <?=lang('lang.countsubaffiliates');?> </div>
            </div>
            <span class="more"> <?=lang('traffic.today');?>: <strong><?=$today_subaffiliates_today;?></strong> </span>
        </div>
    </div>
<?php endif ?>
