<table class="table table-striped table-hover" style="margin: 0px 0 0 0;" id="myTable">
	<thead>
		<tr>
			<th><?= lang('aff.at01'); ?></th>
			<th><?= lang('aff.at02'); ?></th>
			<th><?= lang('aff.at03'); ?></th>
			<th><?= lang('aff.at04'); ?></th>
			<th><?= lang('aff.at05'); ?></th>
			<th><?= lang('aff.at06'); ?></th>
			<th><?= lang('aff.at07'); ?></th>
			<th><?= lang('aff.at08'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
			if(!empty($traffic)) {
				foreach($traffic as $traffic) {
					$date = date('Y-m-d', strtotime($traffic['start_date']));
		?>	
					<tr>
						<td><?= $date ?></td>
						<?php if($traffic['players'] > 0) { ?>
							<td><a href="#players" onclick="players('<?= $traffic['trafficId'] ?>')"><?= $traffic['players'] ?></a></td>
						<?php } else { ?>
							<td><?= $traffic['players'] ?></td>
						<?php } ?>
						<td><?= $traffic['register_players'] ?></td>
						<td><?= $traffic['deposit_players'] ?></td>
						<td><?= $traffic['deposit_amount'] ?></td>
						<td><?= $traffic['withdraw_amount'] ?></td>
						<td><?= $traffic['bets'] ?></td>
						<td><?= $traffic['wins'] ?></td>
					</tr>
        <?php 
    			}
			} else {
		?>
					<tr>
                        <td colspan="8" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
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