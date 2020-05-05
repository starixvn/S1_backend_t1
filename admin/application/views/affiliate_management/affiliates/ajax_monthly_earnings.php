<table class="table table-striped">
	<thead>
		<th><?= lang('aff.ai43'); ?></th>
		<th><?= lang('aff.ai44'); ?></th>
		<th><?= lang('aff.ai45'); ?></th>
		<th><?= lang('aff.ai46'); ?></th>
		<th><?= lang('aff.ai47'); ?></th>
		<th><?= lang('aff.ai48'); ?></th>
		<th><?= lang('aff.ai49'); ?></th>
	</thead>

	<tbody>
		<?php 
			if(!empty($earnings)) {
				foreach ($earnings as $key => $earnings_value) { 
		?>
					<tr>
						<td><?= date('Y-m-d', strtotime( $earnings_value['createdOn'])) ?></td>
						<td><?= $earnings_value['active_players'] ?></td>
						<td><?= $earnings_value['opening_balance'] ?></td>
						<td><?= $earnings_value['earnings'] ?></td>
						<td><?= $earnings_value['approved'] ?></td>
						<td><?= $earnings_value['closing_balance'] ?></td>
						<td><?= ($earnings_value['notes'] == null) ? '<i>n/a</i>':$earnings_value['notes'] ?></td>
					</tr>
		<?php 
				}
			} else {
		?>
					<tr>
                        <td colspan="7" style="text-align:center"><span class="help-block"><?= lang('aff.ai50'); ?></span></td>
                    </tr>
		<?php
			}
		?>
	</tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>