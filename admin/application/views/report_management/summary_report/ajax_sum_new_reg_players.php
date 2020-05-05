<div class="table-responsive">
	<?php if($export_report_permission){ ?>
        <a href="<?= BASEURL . 'report_management/exportSummaryReportToExcel' ?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?= lang('lang.export'); ?>" data-placement="top">
            <i class="glyphicon glyphicon-share"></i>
        </a>
    <?php } ?>
    <hr class="hr_between_table"/>
	<table class="table table-striped table-hover" style="margin: 0px 0 0 0; width: 100%;" id="playerTable">
		<thead>
			<tr>
				<th></th>
				<th class="input-sm"><?= lang('report.pr01'); ?></th>
				<th class="input-sm"><?= lang('report.pr02'); ?></th>
				<th class="input-sm"><?= lang('report.pr15'); ?></th>
				<th class="input-sm"><?= lang('report.pr16'); ?></th>
				<th class="input-sm"><?= lang('report.pr17'); ?></th>
				<th class="input-sm"><?= lang('report.pr18'); ?></th>
				<th class="input-sm"><?= lang('report.pr19'); ?></th>
				<th class="input-sm"><?= lang('report.pr20'); ?></th>
				<th class="input-sm"><?= lang('report.pr21'); ?></th>
				<th class="input-sm"><?= lang('report.pr22'); ?></th>
				<th class="input-sm"><?= lang('report.pr03'); ?></th>
				<th class="input-sm"><?= lang('report.pr04'); ?></th>
				<th class="input-sm"><?= lang('report.pr05'); ?></th>
				<th class="input-sm"><?= lang('report.pr06'); ?></th>
				<th class="input-sm"><?= lang('report.pr07'); ?></th>
				<th class="input-sm"><?= lang('report.pr08'); ?></th>
				<th class="input-sm"><?= lang('report.pr09'); ?></th>
				<th class="input-sm"><?= lang('report.pr10'); ?></th>
				<th class="input-sm"><?= lang('report.pr11'); ?></th>
				<th class="input-sm"><?= lang('report.pr12'); ?></th>
				<th class="input-sm"><?= lang('report.pr13'); ?></th>
				<th class="input-sm"><?= lang('report.pr14'); ?></th>			
			</tr>
		</thead>

		<tbody>
			<?php if(!empty($player)) { ?>
				<?php 
					foreach ($player as $key => $value) { 
						$date = date('Y-m-d', strtotime($value['date']));
				?>
					<tr>
						<td></td>
						<td class="input-sm"><?= $value['username'] ?></td>
						<td class="input-sm"><?= ($value['realname'] == null) ? lang('lang.norecord'):$value['realname'] ?></td>
						<td class="input-sm"><?= $value['total_deposit_bonus'] ?></td>
						<td class="input-sm"><?= $value['total_cashback_bonus'] ?></td>
						<td class="input-sm"><?= $value['total_referral_bonus'] ?></td>
						<td class="input-sm"><?= $value['total_bonus'] ?></td>
						<td class="input-sm"><?= $value['total_first_deposit'] ?></td>
						<td class="input-sm"><?= $value['total_second_deposit'] ?></td>
						<td class="input-sm"><?= $value['total_deposit'] ?></td>
						<td class="input-sm"><?= $value['total_withdrawal'] ?></td>
						<td class="input-sm"><?= $value['playerlevel'] ?></td>
						<td class="input-sm"><?= ($value['email'] == null) ? lang('lang.norecord'):$value['email'] ?></td>
						<td class="input-sm"><?= ($value['registered_by'] == 'website') ? lang('player.68'):lang('player.69') ?></td>
						<td class="input-sm"><?= ($value['registrationIp'] == null) ? lang('lang.norecord'):$value['registrationIp']  ?></td>
						<td class="input-sm"><?= ($value['lastLoginIp'] == null) ? lang('lang.norecord'):$value['lastLoginIp']  ?></td>
						<td class="input-sm"><?= ($value['lastLoginTime'] == null) ? lang('lang.norecord'):$value['lastLoginTime']  ?></td>
						<td class="input-sm"><?= ($value['lastLogoutTime'] == null) ? lang('lang.norecord'):$value['lastLogoutTime']  ?></td>
						<td class="input-sm"><?= ($value['createdOn'] == null) ? lang('lang.norecord'):$value['createdOn']  ?></td>
						<td class="input-sm"><?= ($value['gender'] == null) ? lang('lang.norecord'):$value['gender']  ?></td>
						<td class="input-sm"><?= $value['mainwallet'] ?></td>
						<td class="input-sm"><?= $value['ptwallet'] ?></td>
						<td class="input-sm"><?= $value['agwallet'] ?></td>
					</tr>
			<?php
					}
				}
			?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#playerTable').DataTable( {
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        } );
    });
</script>