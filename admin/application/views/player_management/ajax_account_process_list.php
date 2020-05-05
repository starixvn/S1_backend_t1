<table class="table table-striped table-hover" id="myTable">
	<thead>
		<tr>
			<th><?= lang('player.mp02'); ?></th>
			<th><?= lang('player.mp03'); ?></th>
			<th><?= lang('player.mp04'); ?></th>
			<th><?= lang('lang.action'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($batch)) { ?>
			<?php foreach($batch as $batch) { ?>
				<tr>
					<td><?= $batch['name']?></td>
					<td><?= $batch['count']?></td>
					<td><?= ($batch['description'] == null) ? '<i>'. lang('player.mp05') .'</i>':$batch['description'] ?></td>
					<td>
						<a href="#" data-toggle="tooltip" title="<?= lang('tool.cms.05'); ?>" class="details" onclick="viewAccountProcess(<?= $batch['batchId']?>);"><span class="glyphicon glyphicon-zoom-in"></span></a>

						<!--?php if($this->permissions->checkPermissions('edit_account_batch_process')) { ?-->
							<a href="#" data-toggle="tooltip" title="<?= lang('lang.edit'); ?>" class="edit" onclick="editAccountProcess(<?= $batch['batchId']?>);"><span class="glyphicon glyphicon-pencil"></span></a>
						<!--?php } ?-->

						<?php if($this->permissions->checkPermissions('delete_account_batch_process')) { ?>
							<!-- <a href="<?= BASEURL . 'player_management/deleteAccountProcess/' . $batch['batchId']?>" data-toggle="tooltip" class="delete"><span class="glyphicon glyphicon-trash"></span></a> -->
						<?php } ?>
					</td>
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