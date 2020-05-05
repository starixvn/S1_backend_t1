<style>
    .dataTables_wrapper{
        overflow-y: hidden;
        width: 100%;
    }
</style>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePromotionReport" class="btn btn-info btn-xs"></a>
            </span>
            <i class="fa fa-search"></i> Search 
        </h4>
    </div>
    <div class="panel-collapse">
        <div class="panel-body">
        	<form class="form-inline">
	        	<div class="col-md-2 form-group">
	        		<label class="control-label">Date</label>
                    <input class="form-control dateInput" style="width: 85%" data-start="#date_from" data-end="#date_to" data-time="false"/>
                    <input type="hidden" id="date_from" name="date_from" value="<?=$date_from?>"/>
                    <input type="hidden" id="date_to" name="date_to" value="<?=$date_to?>"/>
        		</div>
        		<button type="submit" class="btn btn-primary">Search</button>
        		<?php
        		$login = $this->authentication->getUsername();
        		if ($login == 'superadmin' || substr($login, 0, 3) =='t1_') { ?>
        		<a class="btn btn-danger btn-generate">Generate</a>
        		<?php } ?>
        	</form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title">
			<?=lang('Daily Player Balance Report')?>
		</h4>
	</div>
	<div class="panel-body">
		<table class="table table-condensed table-bordered table-hover">
			<thead>
				<tr style="background-color: #5FB3E6;">
					<th rowspan="2"><?=lang('Date')?></th>
					<th rowspan="2"><?=lang('Opening Real Player Balance')?></th>
					<th colspan="<?=count($datas['deposit']);?>"><?=lang('Deposits')?></th>
					<th colspan="<?=count($datas['withdraw']);?>"><?=lang('Withdrawals')?></th>
					<?php foreach ($datas['game_platform'] as $game_platform): ?>
					<th colspan="5"><?=lang($game_platform['gamePlatformName'])?></th>
					<?php endforeach ?>
					<th rowspan="2"><?=lang('Closing Real Player Balance')?></th>
					<th rowspan="2"><?=lang('Total of unsettled bets')?></th>
					<th rowspan="2" style="background-color:#D0B7FF"><?=lang('Total Player Balance coverage Requirement')?></th>
				</tr>
				<tr style="background-color: #5FB3E6;">
					<?php foreach($datas['deposit'] as $deposit) { ?>
					<th>
					<?=lang($deposit['bankName'])?>
					</th>
					<?php } ?>
					<?php foreach($datas['withdraw'] as $withdraw) { ?>
					<th>
					<?=lang($withdraw['bankName']);?>
					</th>
					<?php } ?>
					<?php foreach ($datas['game_platform'] as $gamePlatform) {
						for ($i = 1; $i <= 5; $i++) {?>
					<th><?php
						switch($i) {
							case 1 : echo lang('Player Bets'); break;
							case 2 : echo lang('Player Win'); break;
							case 3 : echo lang('Cancelled Bets'); break;
							case 4 : echo lang('Manual Adjustments'); break;
							case 5 : echo lang('Real Bonus'); break;
						}
					?></th>
					<?php } 
					} ?>
				</tr>
			</thead>
			<tbody>
				<?php 
				$DepositTotal = 0;
				$WithdrawTotal = 0;
				if (!empty($datas['data'])) {
				foreach ($datas['data'] as $date => $row) {
					$totalDeposit = [];
					$totalWithdraw = [];
					$totalGame = [];
				?>
				<tr style="background-color: lightblue;">
					<td style="background-color: #5FB3E6;" nowrap><?=$date;?></td>
					<td style="text-align: right;"><?= isset($row['open']) ? $row['open'] : 0; ?></td>
					<?php foreach ($datas['deposit'] as $deposit) { 
						if (!isset($totalDeposit[$deposit['bankTypeId']])) {
							$totalDeposit[$deposit['bankTypeId']] = 0;
						}

						if (isset($row['deposit'][$deposit['bankTypeId']])) {
							$totalDeposit[$deposit['bankTypeId']] += $row['deposit'][$deposit['bankTypeId']];
							$DepositTotal += $row['deposit'][$deposit['bankTypeId']];
						}
						
					?>
					<td style="text-align: right;"><?= isset($row['deposit'][$deposit['bankTypeId']]) ? $row['deposit'][$deposit['bankTypeId']] : 0.00  ?></td>
					<?php } ?>
					<?php foreach($datas['withdraw'] as $withdraw) {
						if (!isset($totalWithdraw[$withdraw['bankTypeId']])) {
							$totalWithdraw[$withdraw['bankTypeId']] = 0;
						}

						if (isset($row['withdraw'][$withdraw['bankTypeId']])) {
							$totalWithdraw[$withdraw['bankTypeId']] -= $row['withdraw'][$withdraw['bankTypeId']];

							$style = "color: black;";
							if ($row['withdraw'][$withdraw['bankTypeId']] > 0) {
								$row['withdraw'][$withdraw['bankTypeId']] = 0 - $row['withdraw'][$withdraw['bankTypeId']];
								$style = "color: red;";
							}
							$WithdrawTotal -= $row['withdraw'][$withdraw['bankTypeId']];	
						}
						
					?>
					<td style="text-align: right; <?=$style?>"><?= isset($row['withdraw'][$withdraw['bankTypeId']]) ? $row['withdraw'][$withdraw['bankTypeId']]: 0; ?></td>
					<?php } ?>
					<?php foreach ($datas['game_platform'] as $game_platform) { 
						if (!isset($totalGame[$game_platform['id']]))
							$totalGame[$game_platform['id']] = [];
					?>

					<?php if (isset($row['gamePlatform'][$game_platform['id']])) : ?>
						<?php foreach ($row['gamePlatform'][$game_platform['id']] as $value): ?>
							<?php $totalGame[$game_platform['id']][] = $value; ?>
							<td style="text-align: right;"><?=$value;?></td>
						<?php endforeach; ?>
					<?php else: ?>
						<?php for ($i = 1; $i <= 5; $i++): ?>
							<td style="text-align: right;">0.00</td>
						<?php endfor; ?>
					<?php endif; ?>

					<?php } ?>
					<td style="text-align: right;"><?= isset($row['closing']) ? $row['closing'] : 0 ;?></td>
					<td style="text-align: right;"><?= isset($row['unsettle']) ? $row['unsettle'] : 0;?></td>
					<td style="text-align: right; background-color:#D0B7FF;"><?= isset($row['totalBalance']) ? $row['totalBalance'] : 0 ;?></td>
				</tr>
				<?php
				}
				}
				?>
			</tbody>
			<tfoot>
				<tr style="background-color: #68eda1">
					<td rowspan="2" colspan="2" style="text-align:center; background-color: #34db7c"><?=lang('Totals')?></td>
					<?php foreach ($datas['deposit'] as $deposit) { ?>
					<td style="text-align: right;"><?=isset($totalDeposit[$deposit['bankTypeId']])?$totalDeposit[$deposit['bankTypeId']]: 0;?></td>
					<?php } ?>
					<?php foreach($datas['withdraw'] as $withdraw) { ?>
					<td style="text-align: right;"><?=isset($totalWithdraw[$withdraw['bankTypeId']])?$totalWithdraw[$withdraw['bankTypeId']]: 0;?></td>
					<?php } ?>
					<?php 
					foreach ($datas['game_platform'] as $game_platform) {
						if (isset($totalGame[$game_platform['id']]) && !empty($totalGame[$game_platform['id']])) {
							foreach ($totalGame[$game_platform['id']] as $key => $value) { 
								$style = 'color: black;';
								if ($key == 0 || $key == 3) {
									if ($value > 0) {
										$style = 'color: red;';
										$value = 0 - $value;
									}
								}
					?>
								<td rowspan="2" style="text-align: right; <?=$style?>"><?=$value;?></td>
					<?php
							}
						} else {
							for ($i = 1; $i <= 5; $i++) {
					?>
								<td rowspan="2" style="text-align: right;">0</td>
					<?php 
							}
						}
					} ?>
					<td rowspan="2" style="text-align: center;"></td>
					<td rowspan="2" style="text-align: center;"></td>
					<td rowspan="2" style="text-align: center; background-color:#D0B7FF;"></td>
				</tr>
				<tr style="background-color: #68eda1">
					<td colspan="<?=count($datas['deposit'])?>" style="text-align: center;"><?=$DepositTotal;?></td>
					<td colspan="<?=count($datas['withdraw'])?>" style="text-align: center;"><?=$WithdrawTotal;?></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>

<script type="text/javascript">
	$(function(){
		var dataTable = $('.table').DataTable({
			paging:   false,
			ordering: false,
			searching: false,
			fixedColumns:   {
            	leftColumns: 1,
        	},
        	<?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
        	dom: "<'panel-body' <'pull-left'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        	buttons: [
        		{
                    extend: 'colvis',
                    columns: ':not(.noVis)',
                    postfixButtons: [
                        { extend: 'colvisRestore', text: '<?=lang('showDef')?>' },
                        { extend: 'colvisGroup', text: '<?=lang('showAll')?>', show: ':hidden'} ]
                }
        	]
		});
		$('.btn-generate').on('click', function(){
			$.ajax({
				url: '<?=site_url('report_management/generateDailyBalanceReport')?>',
				type: 'POST',
				data: {
					date_from: $('input[type="hidden"][name="date_from"]').val(),
					date_to: $('input[type="hidden"][name="date_to"]').val(),
				},
				timeout: (1000 * 60 * 5)
			});
			// $.post(
			// 	'<?=site_url('report_management/generateDailyBalanceReport')?>',
			// 	{
			// 		date_from: $('input[type="hidden"][name="date_from"]').val(),
			// 		date_to: $('input[type="hidden"][name="date_to"]').val(),
			// 	}, function(response) {

			// 	},
			// 	'json'
			// );
		});
	});
</script>

