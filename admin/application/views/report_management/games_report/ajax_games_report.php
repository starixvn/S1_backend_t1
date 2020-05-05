<table class="table table-striped table-hover" style="margin: 0px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th><?= lang('report.g02'); ?></th>			
			<th>PT <?= lang('report.g04'); ?></th>		
			<th>AG <?= lang('report.g04'); ?></th>			
			<th>PT <?= lang('report.g05'); ?></th>		
			<th>AG <?= lang('report.g05'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($games_report)) { ?>
			<?php 
				foreach ($games_report as $key => $value) { 
					$date = date('Y-m-d', strtotime($value['start_date']));
			?>
				<tr>
					<td><?= $date ?></td>
					<td><?= $value['pt_total_bets'] ?></td>
					<td><?= $value['ag_total_bets'] ?></td>
					<td><?= $value['pt_total_earn'] ?></td>
					<td><?= $value['ag_total_earn'] ?></td>
				</tr>
		<?php
				}
			} else {
		?>
				<tr>
                    <td colspan="5" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
                </tr>
        <?php } ?>
	</tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>