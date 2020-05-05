<h3 style="margin-top:1px;"><i class="fas fa-tachometer-alt"></i> <?=lang('lang.dashboard')?></h3>
<!-- <hr style="margin-top:1px; margin-bottom:10px; border-top: 1px solid #777;"/> -->

<div class="row top-dashboards-gt-1400">
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'scooter' : 'new-red' ?>">
			<div class="visual">
				<i class="fas fa-users"></i>
			</div>
			<div class="details">
				<div class="number">
					<span><?=number_format($total_active_players)?></span>
				</div>
				<div class="desc"><?php echo lang('Today active players');?></div>
			</div>
			<span class="more"> <?=lang('dashboard.yesterday')?>: <strong><?= number_format($total_active_players_yesterday) ?></strong> </span>
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'danube' : 'new-blue' ?>">
			<div class="visual">
				<i class="fas fa-stopwatch"></i>
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
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'sanmarino' : 'new-green' ?>">
			<div class="visual">
				<i class="fas fa-user-plus"></i>
			</div>
			<div class="details">
				<div class="number">
					<span><?=number_format($today_member_count)?></span>
				</div>
				<div class="desc"> <?=lang('Today New Reg. Players')?> </div>
			</div>
			<span class="more"> <?=lang('Yesterday: New Reg. Players')?>: <strong><?=number_format($yesterday_member_count)?></strong></span>
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'apricot' : 'new-orange' ?>">
			<div class="visual">
				<i class="fas fa-chart-bar"></i>
			</div>
			<div class="details">
				<div class="number">
					<span><?=number_format($all_member_count)?></span>
				</div>
				<div class="desc"> <?=lang('Total Reg. Players')?> </div>
			</div>
			<span class="more"> <?=lang('Total Deposit Players')?>: <strong><?=number_format($all_member_deposited)?></strong> </span>
		</div>
	</div>
</div>
<div class="row top-dashboards-le-1400">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'scooter' : 'new-red' ?>">
			<div class="visual">
				<i class="fas fa-users"></i>
			</div>
			<div class="details">
				<div class="number">
					<span><?=number_format($total_active_players)?></span>
				</div>
				<div class="desc"><?php echo lang('Today active players');?></div>
			</div>
			<span class="more"> <?=lang('dashboard.yesterday')?>: <strong><?= number_format($total_active_players_yesterday) ?></strong> </span>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'danube' : 'new-blue' ?>">
			<div class="visual">
				<i class="fas fa-stopwatch"></i>
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
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'sanmarino' : 'new-green' ?>">
			<div class="visual">
				<i class="fas fa-user-plus"></i>
			</div>
			<div class="details">
				<div class="number">
					<span><?=number_format($today_member_count)?></span>
				</div>
				<div class="desc"> <?=lang('Today New Reg. Players')?> </div>
			</div>
			<span class="more"> <?=lang('Yesterday: New Reg. Players')?>: <strong><?=number_format($yesterday_member_count)?></strong></span>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'apricot' : 'new-orange' ?>">
			<div class="visual">
				<i class="fas fa-chart-bar"></i>
			</div>
			<div class="details">
				<div class="number">
					<span><?=number_format($all_member_count)?></span>
				</div>
				<div class="desc"> <?=lang('Total Reg. Players')?> </div>
			</div>
			<span class="more"> <?=lang('Total Deposit Players')?>: <strong><?=number_format($all_member_deposited)?></strong> </span>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'white-tacao' : 'old-purple' ?>">
			<div class="visual side first">
				<i class="fas fa-file-invoice-dollar"></i>
			</div>
			<div class="details">
				<div class="number larger">
					<span><?=$this->utils->displayCurrency($total_all_balance_include_subwallet);?></span>
				</div>
				<div class="desc"><?php echo lang('Total Balance');?></div>
			</div>
			<span class="more"> &nbsp; </span>
		</div>
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'white-sanmarino' : 'old-blue' ?>">
			<div class="visual side">
				<i class="fas fa-donate"></i>
			</div>
			<div class="details">
				<div class="number larger">
					<span><?=$this->utils->displayCurrency($today_deposit_sum)?></span>
				</div>
				<div class="desc"> <?=lang('Today Total Deposit Amount')?> </div>
			</div>
			<span class="more"> <?=lang("Count of Today Player Deposit")?>: <strong><?=number_format($today_deposited_player)?></strong>
				<span class="pull-right"><?=lang("Count of Today Deposit Success")?>: <strong><?=number_format(isset($today_deposit_count) ? $today_deposit_count : 0 )?></strong></span>  </span>
		</div>
		<div class="dashboard-stat <?=$this->utils->getConfig('use_new_sbe_color') ? 'white-shakespeare' : 'old-green' ?>">
			<div class="visual side">
				<i class="fas fa-hand-holding-usd"></i>
			</div>
			<div class="details">
				<div class="number larger">
					<span><?=$this->utils->displayCurrency($today_withdrawal_sum)?></span>
				</div>
				<div class="desc"> <?=lang('Today Total Withdraw Amount')?> </div>
			</div>
			<span class="more"> <?=lang('Count of Today Player Withdraw')?>: <strong><?=number_format($today_withdrawed_player)?></strong>
				<span class="pull-right"><?=lang("Count of Today Withdraw Success")?>: <strong><?=number_format(isset($today_withdraw_count) ? $today_withdraw_count : 0 )?></strong></span></span>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="panel shaded active-players">

			<div class="panel-heading"><?= ucwords(lang('Today active players')) ?></div>
			<div class="panel-body">
				<div class="ColorTicket">
					<div class="Ticket"><i class="fas fa-square"></i> <span><?= lang('Live Dealers') ?></span></div>
					<div class="Ticket"><i class="fas fa-square"></i> <span><?= lang('Sports') ?></span></div>
					<div class="Ticket"><i class="fas fa-square"></i> <span><?= lang('Slots') ?></span></div>
					<div class="Ticket"><i class="fas fa-square"></i> <span><?= lang('Poker') ?></span></div>
					<div class="Ticket"><i class="fas fa-square"></i> <span><?= lang('Fishing') ?></span></div>
					<div class="Ticket"><i class="fas fa-square"></i> <span><?= lang('Lottery') ?></span></div>
					<div class="Ticket"><i class="fas fa-square"></i> <span><?= lang('Chess') ?></span></div>
					<div class="Ticket"><i class="fas fa-square"></i> <span><?= lang('Financial Bets') ?></span></div>
					<div class="Ticket"><i class="fas fa-square"></i> <span><?= lang('Others') ?></span></div>
				</div>
				<div id="DonutChart"></div>
			</div>

		</div>
	</div>
</div>

	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="panel shaded" style="height:450px;">
			<div class="panel-heading">
				<?= lang('Betting Amount & Gross Profit') ?>
				<div class="pull-right">
					<!-- select name="yymmdd" id="yymmdd" onchange="chart_mode_change(this)">
						<option value="week"><?= ucwords(lang('week')) ?></option>
						<option value="month"><?= ucwords(lang('month')) ?></option>
						<option value="year"><?= ucwords(lang('year')) ?></option>
					</select -->
				</div>
			</div>
			<!-- <div id="chart-container" style="width: 80%;"> -->
				<div id="revenue-chart"></div>
			<!-- </div> -->
			<div class="revenue-chart">
					<p><?= lang('Total Betting Amount') ?></p>
					<h2 class="figure amount_bet">999,999</h2>
					<p><?= lang('Total Gross Profit') ?></p>
					<h2 class="figure gross_profit">999,999</h2>
				<div class="ticket-group" style="visibility: hidden;">
					<div class="revenue-ticket"><i class="fas fa-square"></i> <span><?= lang('Betting Amount') ?></span></div>
					<div class="revenue-ticket"><i class="fas fa-square"></i> <span><?= lang('Gross Profit') ?></span></div>
					<div class="revenue-ticket"><i class="fas fa-square"></i> <span><?= lang('Ratio of Betting') ?></span></div>
					<div class="revenue-ticket"><i class="fas fa-square"></i> <span><?= lang('Ratio of Gross Profit') ?></span></div>
				</div>
			</div>
			</div>

		</div>
	</div>
<!-- <div id="charta"></div> -->
<div class="row report-tables">
	<!-- TABLES -->
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- DEPOSIT COUNT TOP 10 TODAY -->
		<div class="panel">
			<div class="panel-heading bermuda-gray"><?=lang('dashboard.depositCountToday')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th class="report-tables-header-sequence"><span class="report-tables-header-text">#</span></th>
                            <th><span class="report-tables-header-text"><?=lang('a_header.player')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('player.mp03')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('report.sum09')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach (array_slice($today_last_deposit_list, 0, 10) as $i => $item): ?>
                        <?php
                        ?>
							<tr>
								<td><?=$item ? ($i + 1) : '&nbsp;'?></td>
								<td>
									<?php if ($item): ?><a href="/player_management/userInformation/<?=$item['playerid']?>" class="report-tables-player-link" target="_blank"><?=$item['username']?></a><?php endif ?>
								</td>
								<td align="right"><span class="report-table-value"><?=$item ? $item['count'] : ''?></span></td>
								<td align="right"><?=$item ? $this->utils->displayCurrency($item['total']) : ''?></td>
							</tr>
						<?php endforeach?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
    <?php if($this->utils->isEnabledFeature('show_total_deposit_amount_today')) {?>
	    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- TOTAL DEPOSIT AMOUNT TOP 10 TODAY -->
		<div class="panel">
			<div class="panel-heading bermuda-gray"><?=lang('Total deposit amount top 10 today')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th class="report-tables-header-sequence"><span class="report-tables-header-text">#</span></th>
                            <th><span class="report-tables-header-text"><?=lang('a_header.player')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('Total Deposit')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php
                        if(isset($today_total_deposit_list)){
                            foreach (array_slice($today_total_deposit_list, 0, 10) as $i => $item) {
                                ?>
                                <tr>
                                    <td><?= $item ? ($i + 1) : '&nbsp;' ?></td>
                                    <td>
                                        <?php if ($item): ?><a href="/player_management/userInformation/<?= $item['playerid'] ?>" class="report-tables-player-link" target="_blank"><?= $item['username'] ?></a><?php endif ?>
                                    </td>
                                    <td align="right"><?= $item ? $this->utils->displayCurrency($item['amount']) : '' ?></td>
                                </tr>
                            <?php
                            }
                        }?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
    <?php } ?>

    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
    <!-- DEPOSIT MAX TOP 10 TODAY -->
    <div class="panel">
        <div class="panel-heading bermuda-gray"><?=lang('dashboard.depositMaxToday')?></div>
        <div class="panel-body">
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th class="report-tables-header-sequence"><span class="report-tables-header-text">#</span></th>
                        <th><span class="report-tables-header-text"><?=lang('a_header.player')?></span></th>
                        <th class="text-right"><span class="report-tables-header-text"><?=lang('system.word32')?></span></th>
                        <th><span class="report-tables-header-text"><?=lang('pay.time')?></span></th>
                        <th><span class="report-tables-header-text"><?=lang('pay.depmethod')?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($today_max_deposit_list, 0, 10) as $i => $item): ?>
                        <tr>
                            <td><?=$item ? ($i + 1) : '&nbsp;'?></td>
                            <td>
                                <?php if ($item): ?><a href="/player_management/userInformation/<?=$item['playerid']?>" class="report-tables-player-link" target="_blank"><?=$item['username']?></a><?php endif ?>
                            </td>
                            <td align="right"><?=$item ? $this->utils->displayCurrency($item['amount']) : ''?></td>
                            <td><?=$item ? date('H:i:s', strtotime($item['created_at'])) : ''?> </td>
                            <td><span class="report-table-value">
                                <?php
                                if($item['deposit_method']){

                                    switch ($item['deposit_method']) {

                                        case MANUAL_ONLINE_PAYMENT:

                                        echo  lang('pay.manual_online_payment');

                                        break;

                                        case AUTO_ONLINE_PAYMENT:

                                        echo  lang('pay.auto_online_payment');

                                        break;

                                        case LOCAL_BANK_OFFLINE:

                                        echo lang('pay.local_bank_offline');

                                        break;

                                        default:
                                        echo lang('lang.norecyet') ;
                                        break;
                                    }
                                }


                                ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <?php if($this->utils->isEnabledFeature('show_total_withdrawal_amount_today')) {?>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
            <!-- TOTAL DEPOSIT AMOUNT TOP 10 TODAY -->
            <div class="panel">
                <div class="panel-heading bermuda-gray"><?=lang('Total withdrawal amount top 10 today')?></div>
                <div class="panel-body">
                    <table class="table" style="margin-bottom: 0;">
                        <thead>
                        <tr>
                            <th class="report-tables-header-sequence"><span class="report-tables-header-text">#</span></th>
                            <th><span class="report-tables-header-text"><?=lang('a_header.player')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('report.sum10')?></span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(isset($today_total_withdrawal_list)){
                            foreach (array_slice($today_total_withdrawal_list, 0, 10 ) as $i => $item) {
                                ?>
                                <tr>
                                    <td><?= $item ? ($i + 1) : '&nbsp;' ?></td>
                                    <td>
                                        <?php if ($item): ?><a href="/player_management/userInformation/<?= $item['playerid'] ?>" class="report-tables-player-link" target="_blank"><?= $item['username'] ?></a><?php endif ?>
                                    </td>
                                    <td align="right"><?= $item ? $this->utils->displayCurrency($item['total_withdraw_amount']) : '' ?></td>
                                </tr>
                            <?php
                            }
                        }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- Withdraw Count Top 10 Today -->
		<div class="panel">
			<div class="panel-heading bermuda-gray"><?=lang('Withdraw Count Top 10 Today')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th class="report-tables-header-sequence"><span class="report-tables-header-text">#</span></th>
                            <th><span class="report-tables-header-text"><?=lang('a_header.player')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('player.mp03')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('report.sum10')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach (array_slice($top_withdrawal_count, 0, 10) as $i => $item): ?>
							<tr>
								<td><?=$item ? ($i + 1) : '&nbsp;'?></td>
								<td>
									<?php if ($item): ?><a href="/player_management/userInformation/<?=$item['to_id']?>" class="report-tables-player-link" target="_blank"><?=$item['username']?></a><?php endif ?>
								</td>
                                <td align="right"><span class="report-table-value"><?=$item ? $item['count'] : ''?></span></td>
								<td align="right"><?=$item ? $this->utils->displayCurrency($item['total_withdraw_amount']) : ''?></td>
							</tr>
						<?php endforeach?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- DEPOSIT COUNT TOP 10 ALL -->
		<div class="panel">
			<div class="panel-heading bermuda-gray"><?=lang('Deposit count top 10 all time')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th class="report-tables-header-sequence"><span class="report-tables-header-text">#</span></th>
                            <th><span class="report-tables-header-text"><?=lang('a_header.player')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('player.mp03')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('report.sum09')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach (array_slice($all_max_deposit_list, 0, 10) as $i => $item): ?>
							<tr>
								<td><?=$item ? ($i + 1) : '&nbsp;'?></td>
								<td>
									<?php if ($item): ?><a href="/player_management/userInformation/<?=$item['playerid']?>" class="report-tables-player-link" target="_blank"><?=$item['username']?></a><?php endif ?>
								</td>
                                <td align="right"><span class="report-table-value"><?=$item ? $item['count'] : ''?></span></td>
								<td align="right"><?=$item ? $this->utils->displayCurrency($item['total']) : ''?></td>
							</tr>
						<?php endforeach?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- LAST 7 DAYS DEPOSIT -->
		<div class="panel seven-cols">
			<div class="panel-heading bermuda-gray"><?=lang('Deposits last 7 days')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th class="date"><span class="report-tables-header-text"><?=lang('sys.ip09')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('system.word32')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('pay.approved_deposit_count')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('lang.average')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($weekly_deposit_list as $date => $item): ?>
							<tr>
								<td class="date"><?=$date?></td>
								<td align="right"><?=$this->utils->displayCurrency($item ? $item['total'] : 0)?></td>
								<td align="right"><?=$item ? $item['count'] : 0?></td>
								<td align="right"><?=$this->utils->formatCurrencyNoSym($item ? $item['total'] / $item['count'] : 0)?></td>
							</tr>
						<?php endforeach?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- LAST 7 DAYS WITHDRAWAL -->
		<div class="panel seven-cols">
			<div class="panel-heading bermuda-gray"><?=lang('Withdrawals last 7 days')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th class="date"><span class="report-tables-header-text"><?=lang('sys.ip09')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('system.word32')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('dashboard.memberCount')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('lang.average')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($weekly_withdraw_list as $date => $item): ?>
							<tr>
								<td class="date"><?=$date?></td>
								<td align="right"><?=$this->utils->displayCurrency($item ? $item['total'] : 0)?></td>
                                <td align="right"><span class="report-table-value"><?=$item ? $item['count'] : 0?></span></td>
								<td align="right"><?=$this->utils->formatCurrencyNoSym($item ? $item['total'] / $item['count'] : 0)?></td>
							</tr>
						<?php endforeach?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- LAST 7 DAYS REGISTERED MEMBER -->
		<div class="panel seven-cols">
			<div class="panel-heading bermuda-gray"><?=lang('Registered members last 7 days')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th class="date"><span class="report-tables-header-text"><?=lang('sys.ip09')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('player.mp03')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('player.97')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('dashboard.conversionRate')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($weekly_member_list as $date => $item) {?>
						<tr>
							<td class="date"><?=$date?></td>
                            <td align="right"><span class="report-table-value"><?=$item ? $item['count'] : 0?></span></td>
                            <td align="right"><span class="report-table-value"><?=$item ? $item['deposit_count'] : 0?></span></td>
                            <td align="right"><span class="report-table-value"><?=$item ? number_format($item['deposit_count'] / $item['count'], 2) : 'N/A'?></span></td>
						</tr>
						<?php }
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- LAST 7 DAYS REGISTERED MEMBER -->
		<div class="panel seven-cols">
			<div class="panel-heading bermuda-gray"><?=lang('dashboard.todaysTransactions')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th width="70%"><span class="report-tables-header-text"><?=lang('pay.transact')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('system.word32')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($transactions as $item): ?>
							<tr>
								<td>
									<?php if ($item): ?>
										<?=lang('transaction.transaction.type.'.$item['transaction_type']) ?> <?= ($item['system_code']) ? '-'.$item['system_code'] : '&nbsp;' ?>
									<?php endif; ?>
								</td>
								<td align="right">
									<?php if ($item): ?>
										<?= $this->utils->displayCurrency($item['total_amount'])?>
									<?php else: ?>
										&nbsp;
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- TOP 10 BETTING PLAYERS TODAY -->
		<div class="panel seven-cols">
			<div class="panel-heading bermuda-gray"><?=lang('dashboard.top10PlayersTodayByBetAmount')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th width="70%"><span class="report-tables-header-text"><?=lang('Player')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('Betting today')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php for ($i = 0; $i < 10; ++$i) : ?>
							<?php if ($i < count($top_bet_amount_players_today)) : ?>
								<?php $row = $top_bet_amount_players_today[$i]; ?>
								<tr>
									<td>
										<?= $row['username'] ?>
									</td>
									<td align="right">
										<?= $this->utils->displayCurrency($row['total_bets']) ?>
									</td>
								</tr>
							<?php else : ?>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
							<?php endif; ?>
						<?php endfor; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
		<!-- TOP 10 PERFORMING GAMES TODAY -->
		<div class="panel seven-cols">
			<div class="panel-heading bermuda-gray"><?=lang('dashboard.top10GamesTodayByBetAmount')?></div>
			<div class="panel-body">
				<table class="table" style="margin-bottom: 0;">
					<thead>
						<tr>
                            <th width="70%"><span class="report-tables-header-text"><?=lang('Game')?></span></th>
                            <th class="text-right"><span class="report-tables-header-text"><?=lang('Betting today')?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php for ($i = 0; $i < 10; ++$i) : ?>
							<?php if ($i < count($top_bet_amount_games_today)) : ?>
								<?php $row = $top_bet_amount_games_today[$i]; ?>
								<tr>
									<td>
										<?= !empty($row['game_name']) ? lang($row['game_name']) : lang($row['game_lang']) ?>
									</td>
									<td align="right">
										<?= $this->utils->displayCurrency($row['total_bets']) ?>
									</td>
								</tr>
							<?php else : ?>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
							<?php endif; ?>
						<?php endfor; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="/resources/third_party/raphael/2.1/raphael-min.js"></script>
<script type="text/javascript" src="/resources/third_party/morris/0.5/morris.min.js"></script>
<script type="text/javascript" src="/resources/third_party/d3.js/5.7/d3.min.js"></script>
<script type="text/javascript" src="/resources/third_party/c3.js/0.6/c3.min.js"></script>
<script type="text/javascript">
(function($) {
    'use strict';
    $.fn.tooltipOnOverflow = function() {
        $(this).on("mouseenter", function() {
            if (this.offsetWidth < this.scrollWidth) {
                $(this).attr('title', $(this).text());
            } else {
                $(this).removeAttr("title");
            }
        });
    };
})(jQuery);

$(function(){
    $("td, th").tooltipOnOverflow();
});
</script>
<script type="text/javascript">
	var cat_colors = {
		'live_dealer'	: '<?=$this->utils->getConfig('use_new_sbe_color') ? "#DD6D8F" : "#BD34D6" ?>',
		'sports'		: '<?=$this->utils->getConfig('use_new_sbe_color') ? "#6474BC" : "#3480D6" ?>',
		'slots'			: '<?=$this->utils->getConfig('use_new_sbe_color') ? "#E6B970" : "#F5AD1D" ?>',
		'poker'			: '<?=$this->utils->getConfig('use_new_sbe_color') ? "#7EDDB6" : "#27C485" ?>',
		'fishing_game'	: '<?=$this->utils->getConfig('use_new_sbe_color') ? "#E39A9A" : "#2DD4E1" ?>',
		'lottery'		: '<?=$this->utils->getConfig('use_new_sbe_color') ? "#A07FE3" : "#F24C52" ?>',
		'chess'			: '<?=$this->utils->getConfig('use_new_sbe_color') ? "#E594C4" : "#582CB2" ?>',
		'financial'		: '<?=$this->utils->getConfig('use_new_sbe_color') ? "#53BDD1" : "#EC2C9F" ?>',
		'others'		: '<?=$this->utils->getConfig('use_new_sbe_color') ? "#C3B1B1" : "#C59E73" ?>'
	};
	var act_players_by_type = <?= json_encode($active_players_by_gametype['morris']) ?>;
	var cat_cues = <?= json_encode($active_players_by_gametype['cat_cues']) ?>;
	var actpl_colors = [];
	for (var i in cat_cues) {
		actpl_colors.push(cat_colors[cat_cues[i]]);
	}
	// DONUT CHART
	var donut = new Morris.Donut({
      element: 'DonutChart',
      resize: true,
      colors: actpl_colors ,
      data: act_players_by_type ,
      hideHover: 'auto'
    });

	setPieChartElementColor();

	function setPieChartElementColor() {
		$('.Ticket:nth-child(1) i').css('color', '#DD6D8F');
		$('.Ticket:nth-child(2) i').css('color', '#6474BC');
		$('.Ticket:nth-child(3) i').css('color', '#E6B970');
		$('.Ticket:nth-child(4) i').css('color', '#7EDDB6');
		$('.Ticket:nth-child(5) i').css('color', '#E39A9A');
		$('.Ticket:nth-child(6) i').css('color', '#A07FE3');
		$('.Ticket:nth-child(7) i').css('color', '#E594C4');
		$('.Ticket:nth-child(8) i').css('color', '#53BDD1');
		$('.Ticket:nth-child(9) i').css('color', '#C3B1B1');
	}

</script>
<script>

   // AREA CHART
	var figs ={
		week: 	<?= json_encode($summary_week['chart_figs']) ?>
	};
	var sums = {
		week: 	<?= json_encode($summary_week['sums']) ?>
	}
	// var summary_week = <?= json_encode($summary_week) ?>;

// var selectyymmdd = figs.week;

function render_c3(mode) {

	// Update grandsums for total betting/gross profit
	// -- Determine scale of grandsum value
	var sum_max = -1;
	for (var i in sums[mode]) {
		if (sums[mode][i] > sum_max) { sum_max = sums[mode][i]; }
	}
	console.log('render_c3', sum_max, Math.log10(sum_max));

	// -- Render figures
	for (var i in sums[mode]) {
		// Default format: currency with 2 dec places
		var format_currency = d3.format('$,.2f');
		if (Math.log10(sum_max) >= 6) {
			// remove dec places if value range > 1e+06
			format_currency = d3.format('$,.0f');
		}
		$('.figure.' + i).text(format_currency(sums[mode][i]).replace(/\$/, '<?=$current_currency['symbol']?>'));
	}

	// Generate c3 chart
	var area_chart = c3.generate({
		bindto: '#revenue-chart' ,
		data: {
			// bindto: d3.select('#chart0') ,
			x: 'x' ,
			columns: figs[mode] ,
			axes: {
				'ratio_bet'		: 'y2' ,
				'ratio_profit'	: 'y2'
			} ,
			types: {
				'amount_bet'	: 'area-spline' ,
				'ratio_bet'		: 'area-spline' ,
				'gross_profit'	: 'area-spline' ,
				'ratio_profit'	: 'area-spline'
			} ,
			names : {
				'amount_bet'	: '<?= lang('Betting Amount') ?>' ,
				'ratio_bet'		: '<?= lang('Ratio of Betting') ?>' ,
				'gross_profit'	: '<?= lang('Gross Profit') ?>' ,
				'ratio_profit'	: '<?= lang('Ratio of Gross Profit') ?>'
			}
		},
		color: {
			pattern: <?=$this->utils->getConfig('use_new_sbe_color') ? "['#4FADE3', '#DBA882','#6878B7','#E3AABA']" : "['#96C4FF', '#FF5C5C','#6D902D','#FFB927']" ?>
		} ,
		axis: {
			x: {
				type: 'timeseries',
				// format: '%Y-%m-%d' ,
				padding: 0 ,
				tick : {
					format: function(x) {
						switch (mode) {
							// case 'year' :
							// 	return moment(x).format('MMM YYYY');
							// 	break;
							// case 'month' :
							// 	return moment(x).format('MMM DD YYYY');
							// 	break;
							case 'week' :
								return moment(x).format('YYYY-M-D');
								break;
						}
					}
				}
			} ,
            y: {
            	padding: {
            		top: 10 ,
					bottom: 10
				},
				tick: {
					format: function(v){
						var tlabel = d3.format('$,.0s')(v);
						// fit system currency symbol
					    tlabel = tlabel.replace(/\$/, '<?=$current_currency['symbol']?>');
					    return tlabel;
                    }
				}
			} ,
			y2: {
				show: true ,
				// max: 100 ,
				padding: {
				} ,
				tick: {
				} ,
				label: {
					text: '(%)' ,
					position: 'outer-middle'
				}
			}
		} ,
		padding: {
			top: 10 ,
			// left: 95 ,
			left: 70 ,
			right: 60
		} ,
		grid: {
			y2: {
				show: true
			}
		} ,
		legend: {
			position: 'bottom' ,
			// inset: { anchor: 'top-left', x: 50, y: 0, step: 1 }
		} ,
		tooltip: {
			format: {
				// title: function (x) {
				// } ,
				value: function (value, ratio, id) {
					var format = d3.format('$,.2f');
					if (id == 'ratio_bet' || id == 'ratio_profit') {
						format = function(v) { return v.toFixed(2) + '%'; }
					}
					return format(value).replace(/\$/, '<?=$current_currency['symbol']?>');
				}
			} ,
			contents : function (d, dtf, dvf, color) {
				var $$=this, cf = $$.config;
				var vformat = cf.tooltip_format_value || dvf;
				// title
				var title = dtf(d[0].x);
				// address tooltip order random issue
				d.sort(function(a, b) { return a.id > b.id; });
				var val = [];

				for (var i in d) {
					val.push({
						v: vformat(d[i].value, d[i].ratio, d[i].id, d[i].index),
						n: d[i].name,
						c: $$.levelColor ? levelColor(d[i].value) : color(d[i].id)
					});
				}

				// Build html
				var ht = "<table class='c3-tooltip'>"
					+ "<thead><tr><th colspan='2'>" + title + "</th></tr></thead>"
					+ "<tbody>";
				for (var j in val) {
					ht += "<tr><td class='name'>"
						+ "<span style='color:" + val[j].c + ";'>"
						+ "&#x25A9;</span>"
						+ val[j].n + "</td>"
						+ "<td class='value'>" + val[j].v + "</td><tr>";
				}
				ht += "</tbody></table>";

				return ht;
			}
		}
	});
} // End function render_c3()

function chart_mode_change(self) {
	var mode = $(self).val();
	render_c3(mode);
}

render_c3('week');

// var config = new Morris.Area({
// 	element: 'revenue-chart',
// 	resize: true,
// 	data:selectyymmdd,
// 	xkey: 'y',
// 	ykeys: ['amount_bet', 'ratio_bet', 'gross_profit', 'ratio_profit'],
// 	labels: ['Betting Amount', 'Ratio of Betting', 'Gross Profit', 'Ratio of Gross Profit'],
// 	lineColors: ['#96C4FF', '#FF5C5C','#6D902D','#FFB927'],
// 	yLabelFormat:function (y) { return '$' + y.toString(); },
// 	xLabelFormat:function (x) { return x.toDateString()},
// 	pointFillColors: ['#96C4FF', '#FF5C5C','#6D902D','#FFB927'],
// 	hideHover: 'auto'
// });
// Morris.Area(config);
// figs.year.ax

// function changeval(){
// 	var optVal= $("#yymmdd option:selected").val();
// 	// selectyymmdd = eval("line_data."+optVal);
// 	// config.setData(selectyymmdd);
// 	var figs = line_data[optVal];
// 	config.setData(figs);
// }

</script>
