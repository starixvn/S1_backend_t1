<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th><?= lang('lang.date'); ?></th>
			<th><?= lang('earn.actplayers'); ?></th>
			<th><?= lang('earn.openbal'); ?></th>
			<th><?= lang('earn.current'); ?></th>
			<th><?= lang('earn.approved'); ?></th>
			<th><?= lang('earn.closebal'); ?></th>
			<th><?= lang('lang.notes'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($earnings)) { ?>
			<?php foreach ($earnings as $value) { ?>
				<tr>
					<td><?= date('Y-m-d', strtotime($value['createdOn'])) ?></td>
					<td><?= $value['active_players'] ?></td>
					<td><?= $value['opening_balance'] ?></td>
					<td><?= $value['earnings'] ?></td>
					<td><?= $value['approved'] ?></td>
					<td><?= $value['closing_balance'] ?></td>
					<td><?= ($value['notes'] == null) ? '<i>n/a</i>':$value['notes'] ?></td>
				</tr>
			<?php } ?>
		<?php } else { ?>
				<tr>
                    <td colspan="7" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
				</tr>
		<?php } ?>
	</tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul> 
</div>