<div class="col-md-12">
    <h3 style="margin-top:1px;"><i class="fa fa-dashboard"></i> <?=lang('lang.dashboard')?></h3>
    <hr style="margin-top:1px; margin-bottom:10px; border-top: 1px solid #777;"/>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <div class="dashboard-stat red checked">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span><?=number_format($total_active_players)?></span>
                    </div>
                    <div class="desc"><?php echo lang('Today active players');?></div>
                </div>
                <span class="more"> &nbsp; </span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-xs-12">
            <div class="dashboard-stat red checked">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span><?=number_format($count_player_session);?></span>
                    </div>
                    <div class="desc"><?php echo lang('Last hour active players');?></div>
                </div>
                <span class="more"> &nbsp; </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <div class="dashboard-stat blue checked">
                <div class="visual">
                    <i class="fa fa-comments"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span><?=number_format($today_member_count)?></span>
                    </div>
                    <div class="desc"> <?=lang('Today: new players')?> </div>
                </div>
                <span class="more"> <?=lang('dashboard.yesterday')?>: <strong><?=number_format($yesterday_member_count)?></strong></span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-xs-12">
            <div class="dashboard-stat purple checked">
                <div class="visual">
                    <i class="fa fa-globe"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span><?=number_format($all_member_count)?></span>
                    </div>
                    <div class="desc"> <?=lang('Player count')?> </div>
                </div>
                <span class="more"> <?=lang('total player deposits')?>: <strong><?=number_format($all_member_deposited)?></strong> </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <div class="dashboard-stat blue-steel checked">
                <div class="visual">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span><?php echo $this->utils->formatCurrency($total_all_balance_include_subwallet);?></span>
                    </div>
                    <div class="desc"><?php echo lang('Total Balance');?></div>
                </div>
                <span class="more"> &nbsp; </span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-xs-12">
            <div class="dashboard-stat grey-cascade checked">
                <div class="visual">
                    <i class="fa fa-sitemap"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span><?=number_format($total_sub_accounts)?></span>
                    </div>
                    <div class="desc"> <?=lang('Sub-Agent Count')?> </div>
                </div>
                <span class="more"> &nbsp; </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <div class="dashboard-stat yellow-gold checked">
                <div class="visual">
                    <i class="fa fa-dollar"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span><?=$this->utils->formatCurrency($today_deposit_sum)?></span>
                    </div>
                    <div class="desc"> <?=lang('Today: sum of deposits')?> </div>
                </div>
                <span class="more">
                    <?=lang('player deposits today')?>: <strong><?=number_format($today_deposited_player)?></strong>
                    <span class="pull-right">
                        <?=lang("Today Deposit Count")?>: <strong><?=number_format(isset($today_deposit_count) ? $today_deposit_count : 0 )?></strong></span>  </span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-xs-12">
            <div class="dashboard-stat green checked">
                <div class="visual">
                    <i class="fa fa-money"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span><?=$this->utils->formatCurrency($today_withdrawal_sum)?></span>
                    </div>
                    <div class="desc"> <?=lang('Today: sum of withdrawals')?> </div>
                </div>
                <span class="more"> <?=lang('player withdrawals today')?>: <strong><?=number_format($today_withdrawed_player)?></strong>
                    <span class="pull-right"><?=lang("Today Withdraw Count")?>: <strong><?=number_format(isset($today_withdraw_count) ? $today_withdraw_count : 0 )?></strong></span></span>
            </div>
        </div>
    </div>
</div>
