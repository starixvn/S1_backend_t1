<table class="table table-striped table-hover" style="margin: 30px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th>Player Name</th>
			<th>Game Type</th>
			<th>Game Begin</th>
			<th>Game End</th>
			<th>Total Win</th>
			<th>Total Loss</th>
			<th>Action</th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($players)) { ?>
			<?php foreach($players as $players) { ?>
				<tr>
					<td><?= $players['username']?></td>
					<td><?= $players['game']?></td>
					<td><?= $players['gameBegin']?></td>
					<td><?= $players['gameEnd']?></td>
					<td><?= $players['totalWin']?></td>
					<td><?= $players['totalLoss']?></td>
					<td><a href="#" data-toggle="tooltip" title="<?= lang('tool.cms05'); ?>" id="details" onclick="viewGameDetails(<?= $players['gameHistoryId']?>);"><span class="glyphicon glyphicon-zoom-in"></span></a></td>
				</tr>
			<?php } ?>
		<?php } else { ?>
				<tr>
					<td colspan="7" style="text-align:center"><span class="help-block">No Records Found</span></td>
				</tr>
		<?php } ?>
	</tbody>
</table>

<br/><br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>