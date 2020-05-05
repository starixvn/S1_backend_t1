<table class="table table-striped table-hover" style="margin: 30px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th>#</th>
			<th>Bet</th>
			<th>Time</th>
			<th>Result</th>
			<th>Profit/Loss</th>
			<th>Status</th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($game)) { ?>
			<?php 
				$cnt = 0;

				foreach($game as $game) { 
					$cnt++;
			?>
					<tr>
						<td><?= $cnt?></td>
						<td><?= $game['bet']?></td>
						<td><?= $game['time']?></td>
						<td><?= $game['result']?></td>
						<td><?= $game['profitLoss']?></td>
						<td><?= $game['status']?></td>
					</tr>
			<?php } ?>
		<?php } else { ?>
				<tr>
					<td colspan="5" style="text-align:center"><span class="help-block">No Records Found</span></td>
				</tr>
		<?php } ?>
	</tbody>
</table>

<br/><br/>

<div class="col-md-12 col-offset-0">
	<input type="hidden" value="<?= $gameHistoryId ?>" id="gameHistoryId">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>