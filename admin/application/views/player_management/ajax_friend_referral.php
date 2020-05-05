<table class="table table-striped table-hover" id="myTable">
	<thead>
		<tr>
			<th><?= lang('player.fr03'); ?></th>
			<th><?= lang('player.fr05'); ?></th>
			<th><?= lang('player.fr04'); ?></th>
			<th><?= lang('player.fr02'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($players)) { ?>
			<?php foreach($players as $player) { ?>
				<tr>
					<td><?= $player['inviter']?></td>
					<td><?= $player['inviterCode']?></td>
					<td><?= $player['username']?></td>
					<td><?= $player['createdOn']?></td>
					<!-- <td>
						<a href="#" data-toggle="tooltip" class="details" onclick="viewPlayer(<?= $player['playerId']?>, 'referred');"><span class="glyphicon glyphicon-zoom-in"></span></a>
					</td> -->
				</tr>
			<?php } ?>
		<?php } else { ?>
                <tr>
                    <td colspan="4" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
                </tr>
		<?php } ?>
	</tbody>
</table>

<br/><br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>