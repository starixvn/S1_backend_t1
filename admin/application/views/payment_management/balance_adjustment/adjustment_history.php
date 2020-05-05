<div class="panel panel-primary
              " id="adjustment-history">
	<div class="panel-heading">
		<h4 class="panel-title"><?=lang('pay.adjustHistory')?></h4>
	</div>
	<nav class="navbar navbar-default navbar-static-top">
		<div class="container-fluid">
			<div class="navbar-left">
				<?php if ($player_id): ?>
					<a href="/payment_management/adjust_balance/<?=$player_id?>" class="btn btn-default navbar-btn" title="<?=lang('pay.05')?>"><?=lang('pay.05')?></a>
				<?php endif?>
			</div>
			<div class="navbar-right">
<!-- 				<a href="/payment_management/member_balance" class="btn btn-default navbar-btn" title="<?=lang('pay.adjustHistory')?>"><?=lang('con.pb')?></a>
 -->			</div>
		</div>
	</nav>
	<div class="panel-body">

		<table class="table table-bordered table-hover" id="myTable">
			<thead>
				<tr>
					<th nowrap="nowrap"><?=lang('player.uab01')?></th>
					<th nowrap="nowrap"><?=lang('pay.username')?></th>
					<th nowrap="nowrap"><?=lang('player.uab02')?></th>
					<th nowrap="nowrap"><?=lang('adjustmenthistory.title.adjustmenttype')?></th>
					<th nowrap="nowrap" style="text-align: right;"><?=lang('adjustmenthistory.title.adjustmentamount')?></th>
					<th nowrap="nowrap" style="text-align: right;"><?=lang('adjustmenthistory.title.beforeadjustment')?></th>
					<th nowrap="nowrap" style="text-align: right;"><?=lang('adjustmenthistory.title.afteradjustment')?></th>
					<th nowrap="nowrap"><?=lang('player.uab06')?></th>
					<th nowrap="nowrap"><?=lang('lang.notes')?></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($adjustment_history as $history_item): ?>
					<tr>
						<td><?=date('Y-m-d H:i:s', strtotime($history_item['created_at']))?></td>
						<td><?=$history_item['to_username'] == '' ? '<i class="text-muted"><?= lang("lang.norecyet"); ?><i/>' : $history_item['to_username']?></td>
						<td><?=$history_item['walletType'] ?: lang('player.uab07')?></td>
						<td><?=lang('transaction.transaction.type.' . $history_item['transaction_type'])?></td>
						<td align="right"><?=number_format($history_item['amount'], 2)?></td>
						<td align="right"><?=number_format($history_item['before_balance'], 2)?></td>
						<td align="right"><?=number_format($history_item['after_balance'], 2)?></td>
						<td><?=$history_item['process_user_id'] == '' ? $history_item['from_username'] == '' ? '<i class="text-muted"><?= lang("lang.norecyet"); ?><i/>' : $history_item['from_username'] : $history_item['adminname']?></td>
						<td><?=$history_item['note'] == '' ? '<i class="text-muted"><?= lang("lang.norecyet"); ?><i/>' : $history_item['note']?></td>
					</tr>
				<?php endforeach?>
				<!---<?php if (empty($adjustment_history)): ?>-->
				<!-- 	<tr>
						<td colspan="9" align="center"><i class="text-muted"><?=lang('lang.norecyet')?></i></td>
					</tr> -->
				<!---<?php endif?>-->
			</tbody>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>
<script type="text/javascript">
	// $(document).ready(function(){
	// 	<?php if ($adjustment_history): ?>
	// 	$('#myTable').DataTable({
	// 		order: [ 0, 'desc' ],
	// 	});
	// 	<?php endif?>
	// });
</script>