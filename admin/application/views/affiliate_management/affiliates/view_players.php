<br/>
<div class="col-md-12">
	<div class="row">
		<label class="pull-left"><?= lang('aff.ap01'); ?></label>
		<a href="<?= BASEURL . 'affiliate_management/trafficStats/' . $affiliate_id ?>" class="btn-sm btn-primary pull-right"><?= lang('aff.ap02'); ?></a>
	</div>

	<table class="table table-striped" style="margin: 20px 0 0 0;">
		<thead>
			<th><?= lang('aff.ap03'); ?></th>
			<th><?= lang('aff.ap04'); ?></th>
			<th><?= lang('aff.ap05'); ?></th>
			<th><?= lang('aff.ap06'); ?></th>
			<th><?= lang('aff.at05'); ?></th>
			<th><?= lang('aff.at06'); ?></th>
			<th><?= lang('aff.at07'); ?></th>
			<th><?= lang('aff.at08'); ?></th>
		</thead>

		<tbody>
			<?php if(!empty($players)) { ?>
				<?php 
					foreach ($players as $value) { 
						$registration_date = date('Y-m-d', strtotime($value['createdOn']));
						$last_login_date = date('Y-m-d', strtotime($value['lastLoginTime']));

						if($value['first_deposit_date'] == null) {
							$first_deposit_date = 'n/a';
						} else {
							$first_deposit_date = date('Y-m-d', strtotime($value['first_deposit_date']));
						}
				?>
					<tr>
						<td><?= $value['username'] ?></td>
						<td><?= $registration_date ?></td>
						<td><?= $last_login_date ?></td>
						<td><?= $first_deposit_date ?></td>
						<td><?= ($value['deposit_amount'] == null) ? '0':$value['deposit_amount'] ?></td>
						<td><?= ($value['withdrawal_amount'] == null) ? '0':$value['withdrawal_amount'] ?></td>
						<td><?= ($value['bets'] == null) ? '0':$value['bets'] ?></td>
						<td><?= ($value['wins'] == null) ? '0':$value['wins'] ?></td>
					</tr>
				<?php } ?>
			<?php } else { ?>
					<tr>
	                    <td colspan="8" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
					</tr>
			<?php } ?>
		</tbody>
	</table>
</div>