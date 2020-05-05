<table class="table table-striped table-hover" style="margin: 0px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th><?= lang('report.in02'); ?></th>									
			<th><?= lang('report.in03'); ?></th>			
			<th><?= lang('report.in04'); ?></th>			
			<th><?= lang('report.in05'); ?></th>			
			<th><?= lang('report.in06'); ?></th>
			<th><?= lang('report.in07'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($income_report)) { ?>
			<?php 
				foreach ($income_report as $key => $value) { 
					$date = date('Y-m-d', strtotime($value['start_date']));
			?>
				<tr>
					<td><?= $date ?></td>
					<td><?= $value['depositAmount'] ?></td>
					<td><?= $value['thirdPartyAmount'] ?></td>
					<td><?= $value['withdrawalAmount'] ?></td>
					<td><?= $value['bonus'] ?></td>
					<td><?= $value['amountEarned'] ?></td>
				</tr>
		<?php
				}
			} else {
		?>
				<tr>
                    <td colspan="6" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
                </tr>
        <?php } ?>
	</tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>