<table class="table table-striped table-hover" style="margin: 0px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th><?= lang('report.sum05'); ?></th>
			<th><?= lang('report.sum14'); ?></th>									
			<th><?= lang('report.sum06'); ?></th>			
			<th><?= lang('report.sum07'); ?></th>			
			<th><?= lang('report.sum08'); ?></th>			
			<th><?= lang('report.sum09'); ?></th>
			<th><?= lang('report.sum10'); ?></th>
			<th><?= lang('report.sum11'); ?></th>
			<th><?= lang('report.sum12'); ?></th>
			<th><?= lang('report.sum13'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($summary_report)) { ?>
			<?php 
				foreach ($summary_report as $key => $value) { 
					$date = date('Y-m-d', strtotime($value['start_date']));
			?>
				<tr>
					<td><?= $date ?></td>
					<td><?= $value['registeredPlayer'] ?></td>
					<td><?= $value['onlinePlayer'] ?></td>
					<td><?= $value['depositPlayer'] ?></td>
					<td><?= $value['thirdPartyDepositPlayer'] ?></td>
					<td><?= $value['withdrawalPlayer'] ?></td>
					<td><?= $value['firstDepositPlayer'] ?></td>
					<td><?= $value['firstDepositAmount'] ?></td>
					<td><?= $value['secondDepositPlayer'] ?></td>
					<td><?= $value['secondDepositAmount'] ?></td>
				</tr>
		<?php
				}
			} else {
		?>
				<tr>
                    <td colspan="9" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
                </tr>
        <?php } ?>
	</tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>