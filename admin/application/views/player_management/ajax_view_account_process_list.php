<input type="hidden" value="<?= $batch_id ?>" id="batch_id" />
<table class="table table-striped table-hover" style="margin: 30px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th><?= lang('player.01'); ?></th>
			<th><?= lang('player.56'); ?></th>
			<?php if($this->permissions->checkPermissions('edit_player') || $this->permissions->checkPermissions('delete_player') ) { ?>
				<th><?= lang('lang.action'); ?></th>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($batch_account)) { ?>
			<?php foreach($batch_account as $row) { ?>
				<tr>
					<td><?= $row['username']?></td>
					<td><?= $row['batchPassword']?></td>
					<td>
						<!-- <a href="#" data-toggle="tooltip" id="view" onclick=""><span class="glyphicon glyphicon-zoom-in"></span></a> -->

						<?php if($this->permissions->checkPermissions('edit_player')) { ?>
							<a href="#" data-toggle="tooltip" class="edit_account" onclick="editAccountProcessDetails(<?= $row['playerId']?>);"><span class="glyphicon glyphicon-pencil"></span></a>
						<?php } ?>

						<?php if($this->permissions->checkPermissions('delete_player')) { ?>
							<!-- <a href="<?= BASEURL . 'player_management/deleteAccountProcessDetails/' . $row['playerId'] . '/' . $row['batchId'] . '/' . $row['type'] ?>" data-toggle="tooltip" class="delete_account"><span class="glyphicon glyphicon-trash"></span></a> -->
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
                <td colspan="3" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
            </tr>
		<?php } ?>
	</tbody>
</table>

<br/><br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>