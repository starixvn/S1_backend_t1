<div class="container">
	<br/>

	<div class="row">
		<div class="col-md-12" id="toggleView">

		    <div class="panel panel-primary">
		        <div class="panel-heading">
		            <h4 class="panel-title">
		                <i class="fa fa-search"></i> <?=lang("lang.search")?>
		                <span class="pull-right">
		                    <a data-toggle="collapse" href="#collapsePlayerReport" class="btn btn-default btn-xs"></a>
		                </span>
		            </h4>
		        </div>
		        <div id="collapsePlayerReport" class="panel-collapse">
		            <div class="panel-body">
		                <form id="form-filter" class="form-inline" method="get">
		                    <label class="control-label"><?=lang('traffic.regdate')?></label><br>

		                    <div class="input-group">
		                        <input class="form-control dateInput" id="date-range" data-start="#date_from" data-end="#date_to" data-time="true" style="width: 500px;" />
		                        <input type="hidden" id="date_from" name="date_from" value="<?= $date_from ?>"/>
		                        <input type="hidden" id="date_to" name="date_to" value="<?= $date_to ?>"/>
		                        <span class="input-group-addon input-sm">
		                            <input type="checkbox" name="by_date" id="by_date" value="1"
		                            <?= empty($by_date) ? '' : ' checked="1" ' ?>
		                        />
		                        </span>
		                    </div>
		                    <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-primary">

		                </form>
		            </div>
		        </div>
		    </div>

			<div class="panel panel-primary">
				<div class="nav-head panel-heading">
					<h4 class="panel-title pull-left">
						<?=lang('traffic.playerlist');?>
					</h4>
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body table-responsive" id="affiliate_panel_body">

						<div class="col-md-12" id="view_payments" style="margin: 30px 0 0 0;">
							<table class="table table-striped table-hover" id="paymentTable" style="width: 100%">
								<thead>
									<tr>
										<th></th>
										<th class="input-sm"><?=lang('traffic.playerusername');?></th>
										<?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
											<th class="input-sm"><?=lang('Real Name');?></th>
										<?php endif ?>
										<th class="input-sm"><?=lang('aff.al11');?></th>
										<th class="input-sm"><?=lang('aff.al16');?></th>
										<th class="input-sm"><?=lang('traffic.regdate');?></th>
										<th class="input-sm"><?=lang('player.if_online');?></th>
										<th class="input-sm"><?=lang('player.42');?></th>
										<th class="input-sm"><?=lang('Last Deposit Time');?></th>
										<th class="input-sm"><?=lang('yuanbao.deposit.times');?></th>
									</tr>
								</thead>

								<tbody>
									<?php if (!empty($players)) {
	?>
										<?php foreach ($players as $value) {
		?>
											<tr>
												<td></td>
												<td class="input-sm">
												<?php $mask = $this->utils->keepOnlyString($value['username'], 4); ?>
												<?php if($this->utils->isHidePlayerInfoOnAff()){ ?>
													<?=  $this->utils->isEnabledFeature('masked_player_username_on_affiliate')  ? $mask : $value['username']?>
												<?php }else{?>
													<a href="<?php echo site_url('affiliate/viewPlayerById/' . $value['playerId']); ?>">
														<?=  $this->utils->isEnabledFeature('masked_player_username_on_affiliate')  ? $mask : $value['username']?>
													</a>
												<?php }?>
												</td>
												<?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
													<td class="input-sm"><?=$value['realName'] ? : lang('lang.norecyet')?></td>
												<?php endif ?>
												<td class="input-sm"><?php echo $this->utils->isHidePlayerContactOnAff() ? '******' : $value['email']; ?></td>
												<td class="input-sm"><?php echo $value['status']=='0' ? lang('Active') : lang('Inactive') ; ?></td>
												<td class="input-sm"><?php echo $value['createdOn']; ?></td>
												<td class="input-sm"><?php echo $value['online'] ? '<i class="text-muted">' . lang('icon.online') . '</i>' : '<i class="text-muted">' . lang('icon.offline') . '</i>' ?></td>
												<td class="input-sm"><?php echo $value['last_login'] ? : lang('lang.norecyet'); ?></td>
												<td class="input-sm"><?php echo $value['last_deposit'] ? : lang('lang.norecyet'); ?></td>
												<td class="input-sm"><?php echo $value['deposit_count']; ?></td>
											</tr>
										<?php
}
}
?>
								</tbody>
								<tfoot>
									<tr>
										<th></th>
										<th></th>
										<?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
											<th></th>
										<?php endif ?>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th><?=lang('Total Players Online')?>: <?=$online_count?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#paymentTable').DataTable( {
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
            "columnDefs": [
				{
                className: 'control',
                orderable: false,
                targets:   0
           		}
			],
            "order": [ 4, 'desc' ]
        } );
    } );
</script>