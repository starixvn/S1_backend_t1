
<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><?=lang('con.pb')?></h4>
	</div>
	<nav class="navbar navbar-default navbar-static-top">
	    <div class="container-fluid">
	    	<div class="navbar-right">
				<a href="/payment_management/adjustment_history" class="btn btn-default navbar-btn" title="<?=lang('pay.adjustHistory')?>"><?=lang('pay.adjustHistory')?></a>
			</div>
	    </div>
	</nav>
	<div class="panel-body">
		<table class="table table-bordered table-hover" id="myTable">
			<thead>
				<tr>
					<th><?=lang('pay.username')?></th>
					<th style="text-align: right;"><?=lang('pay.mainwalltbal')?></th>
					<?php foreach ($game_platforms as $game_platform): ?>
						<th style="text-align: right;"><?=$game_platform['system_code']?> <?=lang('pay.walltbal')?></th>
					<?php endforeach?>
					<th style="text-align: right;"><?=lang('pay.totalbal')?></th>
					<th style="text-align: right;"><?=lang('lang.action')?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($playerDetails as $playerDetails): ?>
					<tr class="<?=$playerDetails['total'] ? 'info' : ''?>">
						<td><?=$playerDetails['username']?></td>
						<td align="right" class="<?=$playerDetails['main'] ? '' : 'text-muted'?>"><?=number_format($playerDetails['main'], 2)?></td>
						<?php foreach ($game_platforms as $game_platform): ?>
							<td align="right" class="<?=$playerDetails[strtolower($game_platform['system_code'])] ? '' : 'text-muted'?>"><?=number_format($playerDetails[strtolower($game_platform['system_code'])], 2)?></td>
						<?php endforeach?>
						<td align="right" class="<?=$playerDetails['total'] ? '' : 'text-muted'?>"><strong><?=number_format($playerDetails['total'], 2)?></strong></td>
						<td align="right">
							<ul class="list-inline">
								<a href="/payment_management/adjustment_history/<?=$playerDetails['playerId']?>" title="<?=lang('player.ui69')?>"><i class="fa fa-list"></i></a>
								<a href="/payment_management/adjust_balance/<?=$playerDetails['playerId']?>" title="<?=lang("pay.adjust")?>"><i class="icon-equalizer2"></i></a>
							</ul>
						</td>
					</tr>
				<?php endforeach?>
			</tbody>

		</table>

	</div>
	<div class="panel-footer"></div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#myTable').DataTable({
			columnDefs: [ {
				targets: <?=count($game_platforms) + 3?>,
				orderable: false
			} ],
            order: [ 0, 'asc' ],
        });
    });
</script>