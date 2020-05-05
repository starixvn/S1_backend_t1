<style>
	.push-down { margin-top: 15px; }
</style>
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseMonthlyEarnings" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseMonthlyEarnings" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
			<form method="post" action="<?=site_url('affiliate_management/viewAffiliateMonthlyEarnings/');?>">
				<div class="form-group">
					<div class="col-md-2">
						<label class="control-label"><?=lang('earnings.ymfilter');?></label>
						<select class="form-control input-sm" name="year_month" id="yearmonth">
							<?php
if (!empty($year_months)) {
	echo '<option value=""></option>';
	$yearmonth = array(date('Ym'));
	foreach ($year_months as $key => $value) {
		// if (!in_array($ym['year_month'], $yearmonth) > 0) {
		?>
										<option value="<?=$value;?>" <?php if ($value == $year_month) {
			echo 'selected';
		}
		?>><?=substr($value, 0, 4) . '-' . substr($value, 4, 2);?></option>';

										<?php $yearmonth[] = $value;?>
									<?php }
	// }
}
?>
						</select>
					</div>
					<div class="col-md-2">
						<label for="username" class="control-label"><?=lang('aff.al10');?></label>
						<input type="text" name="username" id="username" class="form-control input-sm" value="<?=$username;?>">
						<?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					</div>
					<div class="col-md-2">
						<label for="parent" class="control-label"><?=lang('lang.parentAffiliate');?></label>
						<select class="form-control input-sm" name="parentId" id="parent">
							<option value=""></option>
							<?php foreach ($affiliates_list as $a) {
	?>
								<option value="<?=$a['affiliateId'];?>" <?php if ($parentId == $a['affiliateId']) {
		echo 'selected';
	}
	?>><?=$a['username'];?></option>
							<?php }
?>
						</select>
					</div>
					<div class="col-md-2">
						<label class="control-label"><?=lang('lang.status');?></label>
						<select class="form-control input-sm" name="paid_flag">
							<option value=""></option>
							<option value="0" <?php if ($paid_flag == "0") {
	echo 'selected';
}
?>><?=lang('lang.unpaid');?></option>
							<option value="1" <?php if ($paid_flag == "1") {
	echo 'selected';
}
?>><?=lang('lang.paid');?></option>
						</select>
					</div>
					<div class="col-md-3" style="padding-top:23px;text-align:left">
						<div class="form-group">
							<input type="reset" value="<?=lang('aff.al22');?>" class="btn btn-default btn-sm">
							<input type="submit" value="<?=lang('aff.al21');?>" id="search_main"class="btn btn-primary btn-sm">

						</div>
					</div>

					<?php if ($this->config->item('show_calculate_button')) {?>
					<div class="col-md-2" style="text-align:left">
						<div class="form-group">
							<button type="button" class="btn btn-lg btn-warning calculate">
								<i class="fa fa-exclamation-circle"></i> <?=lang('lang.calculatenow');?>
							</button>
						</div>
					</div>
					<?php }
?>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-wallet"></i> <?=lang('aff.sb7');?>
					<span class="clearfix"></span>
				</h4>
			</div>

			<div class="panel-body" id="affiliate_panel_body">
				<div class="col-md-12 hide" id="payment">
					<h2><i class="fa fa-paper-plane-o"></i> <?=lang('earnings.pay');?></h2>
					<form id="frm-pay" class="well" method="post" action="<?=site_url('affiliate_management/postAffiliateMonthEarnings')?>">
						<input type="hidden" name="type" value="pay">
						<input type="hidden" name="affiliateId" value="">
						<input type="hidden" name="balance" value="">
						<input type="hidden" name="year_month" value="">

						<div class="form-group col-md-3">
							<lable><?=lang('lang.yearmonth');?>:</lable>
							<input name="year_month" type="text" class="form-control" readonly>
						</div>
						<div class="form-group col-md-3">
							<lable><?=lang('aff.as03');?>:</lable>
							<input name="username" type="text" class="form-control" readonly>
						</div>
						<div class="form-group col-md-3">
							<lable><?=lang('lang.balance');?>:</lable>
							<input name="viewbalance" type="text" class="form-control">
						</div>
						<div class="form-group col-md-3">
							<lable><?=lang('player.upay05');?>:</lable>
							<input name="notes" type="text" class="form-control">
						</div>
						<div class="form-group col-md-3">
							<a class="btn btn-default push-down cancel"><i class="fa fa-times"></i> <?=lang('lang.cancel');?></a>
							<input type="submit" class="btn btn-primary push-down" value="<?=lang('earnings.pay');?>">
						</div>
						<div class="clearfix"></div>
					</form>
				</div>
				<div class="col-md-12 hide" id="adjustment">
					<h2><i class="fa fa-pencil"></i> <?=lang('pay.adjust');?></h2>
					<form id="frm-adjust" class="well" method="post" action="<?=site_url('affiliate_management/postAffiliateMonthEarnings')?>">
						<input type="hidden" name="type" value="adjust">
						<input type="hidden" name="affiliateId" value="">
						<input type="hidden" name="year_month" value="">

						<div class="form-group col-md-3">
							<lable><?=lang('lang.yearmonth');?>:</lable>
							<input name="year_month" type="text" class="form-control" readonly>
						</div>
						<div class="form-group col-md-3">
							<lable><?=lang('aff.as03');?>:</lable>
							<input name="username" type="text" class="form-control" readonly>
						</div>
						<div class="form-group col-md-3">
							<lable><?=lang('lang.balance');?>:</lable>
							<input name="balance" type="text" class="form-control" value="0">
						</div>
						<div class="form-group col-md-3">
							<lable><?=lang('player.upay05');?>:</lable>
							<input name="notes" type="text" class="form-control" required>
						</div>
						<div class="form-group col-md-3">
							<a class="btn btn-default push-down cancel"><i class="fa fa-times"></i> <?=lang('lang.cancel');?></a>
							<input type="submit" class="btn btn-success push-down" value="<?=lang('pay.adjust');?>">
						</div>
						<div class="clearfix"></div>
					</form>
				</div>
				<div class="row" id="monthlyEarnings">
					<?php $cur_year_month = date('Ym');?>
					<div class="panel-body">
                    <div class="table-responsive"  >
					<table class="table table-striped table-bordered" id="earningsTable" style="width:100%;">
						<thead>
							<!-- <th></th> -->
							<th><?=lang('lang.action');?></th>
							<th><?=lang('aff.ai12');?></th>
							<th><?=lang('system.word38');?></th>
							<th><?=lang('system.word39');?></th>
							<th><?=lang('lang.yearmonth');?></th>
							<th><?=lang('aff.sb8');?></th>
							<th><?=lang('aff.ts08');?></th>
							<th><?=lang('aff.as24');?></th>
							<th><?=lang('earnings.gross');?></th>
							<th><?=lang('earnings.fee');?></th>
							<th><?=lang('earnings.net');?></th>
							<th><?=lang('aff.ts02');?></th>
							<th><?=lang('system.word32');?></th>
							<th><?=lang('lang.status');?></th>
							<th><?=lang('aff.ai49');?></th>
						</thead>
						<tbody>
							<?php
if (!empty($earnings)) {
	foreach ($earnings as $e) {
		?>
									<tr <?php if ($e->amount < 0) {
			echo 'class="danger"';
		} elseif ($e->amount > 0) {
			echo 'class="info"';
		}
		?>>
										<!-- <td></td> -->
										<td>
											<?php if ($e->paid_flag == 0 && $e->amount >= $min_amount) {?>
												<button class="btn btn-xs btn-primary pay" data-earningid="<?=$e->id;?>">
													<i class="fa fa-paper-plane-o"></i> <?=lang('Transfer to wallet');?>
												</button>
											<?php }
		?>
										</td>
										<td>
											<?php if ($e->status == 0) {?>
												<?=lang('player.14');?>
											<?php } elseif ($e->status == 1) {?>
												<?=lang('player.tl09');?>
											<?php } elseif ($e->status == 2) {?>
												<?=lang('player.15');?>
											<?php }
		?>
										</td>
										<td class="id username" val="<?=$e->affiliate_id;?>" username="<?=$e->username;?>"><a href="<?=site_url('affiliate_management/userInformation/' . $e->affiliate_id);?>"><?=$e->username;?></a></td>
										<td><?=$e->firstname . " " . $e->lastname;?></td>
										<td class="yearmonth" val="<?=$e->year_month;?>">
											<form method="post" action="<?=site_url('affiliate_management/viewAffiliateMonthlyEarnings/all');?>">
												<input type="hidden" name="year_month" value="<?=$e->year_month;?>">
												<input type="hidden" name="username" value="<?=$e->username;?>">
												<input type="hidden" name="parentId" value="">
												<input type="hidden" name="paid_flag" value="">
												<a href="javascript:;" onclick="parentNode.submit();"><?=$e->year_month;?></a>
											</form>
										</td>
										<td>
											<?php if ($e->sub_affiliates > 0) {?>
											<form method="post" action="<?=site_url('affiliate_management/postSearchPage');?>">
												<input type="hidden" name="parentId" value="<?=$e->affiliate_id;?>">
												<input type="hidden" name="alltime" value="true">
												<a href="javascript:;" onclick="parentNode.submit();"><?=$e->sub_affiliates;?></a>
											</form>
											<?php } else {?>
												<?=$e->sub_affiliates;?>
											<?php }
		?>
										</td>
										<td>
											<?php if (count($e->active_players) > 0) {?>
												<a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo implode(', ', $e->active_players); ?>"><?=count($e->active_players);?></a>
											<?php } else {?>
												<?=count($e->active_players);?>
											<?php }
		?>
										</td>
										<td>
											<?php if ($e->count_players > 0) {?>
												<a href="<?=site_url('player_management/searchAllPlayer?master_affiliate=' . $e->username);?>"><?=$e->count_players;?></a>
											<?php } else {?>
												<?=$e->count_players;?>
											<?php }
		?>
										</td>
										<td><?=$e->gross_net;?></td>
										<td><?=$e->bonus_fee + $e->transaction_fee + $e->cashback + $e->admin_fee;?></td>
										<td><?=$e->net;?></td>
										<td><?=$e->rate_for_affiliate;?></td>
										<td class="amount"><?=$e->amount;?></td>
										<td><?php if ($e->paid_flag == 0) {
			echo lang('lang.unpaid');
		} else {
			echo lang('lang.paid');
		}
		?></td>
										<td class="note"><?=$e->note;?></td>
									</tr>
								<?php }
	?>
							<?php }
?>
						</tbody>
							<tfoot>
								<tr>
									<td colspan="1"></td>
									<td>
										<a href="<?=site_url('affiliate_management/transfer_all/' . $year_month);?>" class="btn btn-danger">
											<i class="fa fa-paper-plane-o"></i> <?=lang('Transfer all to wallet');?>
										</a>
									</td>
									<td colspan="14"><h5><b><?=lang('lang.totalunpaid');?>: <?=$unpaid_amount;?><b></h5></td>
								</tr>
							</tfoot>

					</table>
				     </div>
			       </div>
				</div>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
		$('#earningsTable').DataTable( {
			stateSave: true,
			dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
	        "buttons": [
	            {
	                extend: 'colvis',
	                postfixButtons: [ 'colvisRestore' ]
	            },
	        	'excel'
				// {
		  //           extend: 'excel',
		  //           text: 'Excel',
		  //       }
	        ],
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 3, 'desc' ]
        } );

        // Filters
        // $('#yearmonth').on('change', function(){
        //     var filter = $(this).val();
        //     $('#earningsTable').DataTable().search(filter).draw();
        // });
        $('.pay').on('click', function(e){
        	var earningid=$(this).data('earningid');

        	window.location.href='<?php echo site_url('/affiliate_management/transfer_one'); ?>'+"/"+earningid;

        	e.preventDefault();

        	// var tr 			= $(this).closest('tr');
        	// var id 			= tr.find('.id').attr('val');
        	// var yearmonth	= tr.find('.yearmonth').attr('val');
        	// var username 	= tr.find('.username').attr('username');
        	// var balance		= tr.find('.balance').html();
        	// var note 		= tr.find('.note').html();

        	// $('#payment').removeClass('hide');
        	// $('#adjustment').addClass('hide');

        	// $('#payment input[name="affiliateId"').val(id);
        	// $('#payment input[name="year_month"').val(yearmonth);
        	// $('#payment input[name="username"').val(username);
        	// $('#payment input[name="balance"').val(balance);
        	// $('#payment input[name="viewbalance"').val(balance);
        	// $('#payment input[name="note"').val(note);
        });
        // $('.adjust').on('click', function(){
        // 	var tr 			= $(this).closest('tr');
        // 	var id 			= tr.find('.id').attr('val');
        // 	var yearmonth	= tr.find('.yearmonth').attr('val');
        // 	var username 	= tr.find('.username').attr('username');
        // 	var balance 	= tr.find('.balance').html();
        // 	var note 		= tr.find('.note').html();

        // 	$('#payment').addClass('hide');
        // 	$('#adjustment').removeClass('hide');

        // 	$('#adjustment input[name="affiliateId"').val(id);
        // 	$('#adjustment input[name="year_month"').val(yearmonth);
        // 	$('#adjustment input[name="username"').val(username);
        // 	$('#adjustment input[name="balance"').val(balance);
        // 	$('#adjustment input[name="note"').val(note);
        // });
        $('.cancel').on('click', function(){
        	$('#payment').addClass('hide');
        	$('#adjustment').addClass('hide');
        });

        $('.calculate').on('click', function(){
        	var url = "<?php echo BASEURL; ?>affiliate_management/calculateMonthlyEarnings/" + $('#yearmonth').val();
        	window.location.href = url;
        });

        $('.btn_pay_all').click(function(){
        	//pay all now
        	window.location.href='<?php echo site_url('/affiliate_management/transfer_all'); ?>'+"/"+$('#yearmonth').val();
        });

    } );
</script>
