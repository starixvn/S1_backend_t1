<?php

$sub_affiliate_terms_type = "";
$sub_allowed = "";
$sub_level = "";
$sub_levels = [];
$sub_shares_percent = "";

$operator_settings = json_decode($operator_settings);
$sub_affiliate_default = json_decode($sub_affiliate_terms);

$no_setting_found = false;

if (!empty($sub_affiliate_default)) {
	if (!empty($sub_affiliate_term) || $sub_affiliate_term != 0) {
		$sub_affiliate_terms = $sub_affiliate_term;
	}

	$sub_affiliate_terms = json_decode($sub_affiliate_terms)->terms;

	// var_dump($sub_affiliate_terms);

	if (!empty($sub_affiliate_terms) || $sub_affiliate_terms != 0) {
		// $sub_default = json_decode($sub_affiliate_terms); // comment on debugger mode
		$sub_default = $sub_affiliate_terms; // comment on debugger mode
		$sub_affiliate = $sub_default;

		$sub_affiliate_terms_type = $sub_affiliate->terms_type;
		switch ($sub_affiliate_terms_type) {
		case 'allow':
			$sub_allowed = @$sub_affiliate->sub_allowed;
			// switch ($sub_allowed) {
			// 	case 'manual':
			$sub_level = @$sub_affiliate_default->terms->sub_level;
			$sub_levels = @$sub_affiliate_default->terms->sub_levels; // explode(',', $sub_affiliate_default->terms->sub_levels);
			if (isSet($sub_affiliate->level_master)) {
				$level_master = $sub_affiliate->level_master;
			} else {
				$level_master = $operator_settings->level_master;
			}

			if (isSet($sub_affiliate->manual_open)) {
				if ($sub_affiliate->manual_open != false) {
					$manual_open = true;
				}

			}

			if (isSet($sub_affiliate->sub_link)) {
				if ($sub_affiliate->sub_link != false) {
					$sub_link = true;
				}

			}

			break;
			// }
			// break;
		}

	}
} else {
	$no_setting_found = true;
}
?>

<div class="container">
	<br/>

	<!-- Personal Information -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title"><i class="glyphicon glyphicon-cog"></i> <?=lang('mod.accinfo');?> </h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="info_panel_body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<span for="username" class="col-md-4"><?=lang('reg.03');?></span>
							<label class="col-md-8"><?=($affiliate['username'] == null) ? '<span class="napp">--<span>' : $affiliate['username'];?></label>
						</div>
						<div class="form-group">
							<span for="firstname" class="col-md-4"><?=lang('reg.a09');?></span>
							<label class="col-md-8"><?=($affiliate['firstname'] == null) ? '<span class="napp">--<span>' : $affiliate['firstname'];?></label>
						</div>
						<div class="form-group">
							<span for="lastname" class="col-md-4"><?=lang('reg.a10');?></span>
							<label class="col-md-8"><?=($affiliate['lastname'] == null) ? '<span class="napp">--<span>' : $affiliate['lastname'];?></label>
						</div>
						<div class="form-group">
							<span for="gender" class="col-md-4"><?=lang('reg.a12');?></span>
							<label class="col-md-8"><?=($affiliate['gender'] == null) ? '<span class="napp">--<span>' : $affiliate['gender'];?></label>
						</div>
						<div class="form-group">
							<span for="birthday" class="col-md-4"><?=lang('reg.a11');?></span>
							<label class="col-md-8"><?=explode(" ", $affiliate['birthday'])[0];?></label>
						</div>
						<div class="form-group">
							<span for="phone" class="col-md-4"><?=lang('reg.a25');?></span>
							<label class="col-md-8"><?=($affiliate['phone'] == null) ? '<span class="napp">--<span>' : $affiliate['phone'];?></label>
						</div>
						<div class="form-group">
							<span for="mobile" class="col-md-4"><?=lang('reg.a24');?></span>
							<label class="col-md-8"><?=($affiliate['mobile'] == null) ? '<span class="napp">--<span>' : $affiliate['mobile'];?></label>
						</div>
						<div class="form-group">
							<span for="address" class="col-md-4"><?=lang('reg.a20');?></span>
							<label class="col-md-8"><?=($affiliate['address'] == null) ? '<span class="napp">--<span>' : $affiliate['address'];?></label>
						</div>
						<div class="form-group">
							<span for="city" class="col-md-4"><?=lang('reg.a19');?></span>
							<label class="col-md-8"><?=($affiliate['city'] == null) ? '<span class="napp">--<span>' : $affiliate['city'];?></label>
						</div>
						<div class="form-group">
							<span for="country" class="col-md-4"><?=lang('reg.a23');?></span>
							<label class="col-md-8"><?=($affiliate['country'] == null) ? '<span class="napp">--<span>' : $affiliate['country'];?></label>
						</div>
						<div class="form-group">
							<span for="zip" class="col-md-4"><?=lang('reg.a21');?></span>
							<label class="col-md-8"><?=($affiliate['zip'] == null) ? '<span class="napp">--<span>' : $affiliate['zip'];?></label>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<span for="state" class="col-md-5"><?=lang('reg.a22');?></span>
							<label class="col-md-7"><?=($affiliate['state'] == null) ? '<span class="napp">--<span>' : $affiliate['state'];?></label>
						</div>
						<div class="form-group">
							<span for="occupation" class="col-md-5"><?=lang('reg.a16');?></span>
							<label class="col-md-7"><?=($affiliate['occupation'] == null) ? '<span class="napp">--<span>' : $affiliate['occupation'];?></label>
						</div>
						<div class="form-group">
							<span for="company" class="col-md-5"><?=lang('reg.a15');?></span>
							<label class="col-md-7"><?=($affiliate['company'] == null) ? '<span class="napp">--<span>' : $affiliate['company'];?></label>
						</div>
						<div class="form-group">
							<span for="imtype1" class="col-md-5"><?=lang('reg.a26');?></span>
							<label class="col-md-7"><?=($affiliate['imType1'] == null) ? '<span class="napp">--<span>' : $affiliate['imType1'];?></label>
						</div>
						<div class="form-group">
							<span for="im1" class="col-md-5"><?=lang('reg.a30');?></span>
							<label class="col-md-7"><?=($affiliate['im1'] == null) ? '<span class="napp">--<span>' : $affiliate['im1'];?></label>
						</div>
						<div class="form-group">
							<span for="imtype2" class="col-md-5"><?=lang('reg.a31');?></span>
							<label class="col-md-7"><?=($affiliate['imType2'] == null) ? '<span class="napp">--<span>' : $affiliate['imType2'];?></label>
						</div>
						<div class="form-group">
							<span for="im2" class="col-md-5"><?=lang('reg.a35');?></span>
							<label class="col-md-7"><?=($affiliate['im2'] == null) ? '<span class="napp">--<span>' : $affiliate['im2'];?></label>
						</div>
						<div class="form-group">
							<span for="website" class="col-md-5"><?=lang('reg.a41');?></span>
							<label class="col-md-7"><?=($affiliate['website'] == null) ? '<span class="napp">--<span>' : $affiliate['website'];?></label>
						</div>
						<div class="form-group">
							<span for="mode_of_contact" class="col-md-5"><?=lang('reg.a36');?></span>
							<label class="col-md-7"><?=($affiliate['modeOfContact'] == null) ? '<span class="napp">--<span>' : ucfirst($affiliate['modeOfContact']);?></label>
						</div>
						<div class="form-group">
							<span for="email" class="col-md-5"><?=lang('reg.a17');?></span>
							<label class="col-md-7"><?=($affiliate['email'] == null) ? '<span class="napp">--<span>' : $affiliate['email'];?></label>
						</div>
						<div class="form-group">
							<span for="email" class="col-md-5"><?=lang('reg.05');?></span>
							<a href="<?=BASEURL . 'affiliate/modifyPassword'?>" class="col-md-7 "><?=lang('lang.reset');?></a>
						</div>
					</div>

					<input type="hidden" name="currency" id="currency" value="<?=$affiliate['currency']?>">
					<span class="clearfix"></span>
					<hr class="style-one"/>
				</div>
				<div class="row">
					<center>
						<a href="<?=BASEURL . 'affiliate/editInfo/' . $affiliate['affiliateId']?>" class="btn btn-primary"> <?=lang('mod.editProfile');?> </a>
					</center>
				</div>
			</div>
		</div>
	</div>
	<!-- End of Personal Information -->

	<!-- Tracking Code and Links -->
	<div class="row">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-cog"></i> <?=lang('mod.tracking');?> </h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="code_panel_body">
				<div class="row">
					<!-- <div class="col-md-6 col-md-offset-0">
						<label for="tracking_code"><?=lang('mod.code');?>: </label>
						<label class="input-sm"><i><?=$affiliate['trackingCode'];?></i></label>
					</div> -->
					<div class="col-md-12 col-md-offset-0">
						<form action="<?=site_url('affiliate/createCode/' . $affiliate['affiliateId'])?>" method="POST" class="form-horizontal">
							<div class="col-md-6 col-md-offset-3">
								<div class="form-group col-xs-5">
									<label for="tracking_code" class="control-label" style="text-align:right;"><?=lang('aff.ai40'); //lang('aff.ai37');?> </label>
									<div>
										<input type="text" name="tracking_code" id="tracking_code" class="form-control" minlength="8" maxlength="8" value="<?=(empty($affiliate['trackingCode'])) ? 'n/a' : $affiliate['trackingCode']?>"/>
										<?php echo form_error('tracking_code', '<span style="color:#CB3838;">'); ?>
									</div>
								</div>
								<div class="btn-group col-xs-7" role="group" aria-label="..." style="margin-top: 27px;">
								  <a href="#randomCode" class="btn btn-info hidden-xs" id="random_code" onclick="randomCode('8');"/> <?=lang('aff.ai38');?> </a>
								  <input type="submit" class="btn btn-primary" value="<?=lang('aff.ai39');?>"/>
								</div>
							</div>
							<div class="clearfix"></div>
							<script>
							function randomCode(len)
							{
							    var text = '';

							    var charset = "abcdefghijklmnopqrstuvwxyz0123456789";

							    for( var i=0; i < len; i++ ) {
							        text += charset.charAt(Math.floor(Math.random() * charset.length));
							    }

							    $('#tracking_code').val(text);
							}
							</script>
						</form>
					</div>
				</div>

				<div class="col-md-12 col-md-offset-0 table-responsive">
					<div class="row">
	                    <?php if (!empty($sublink) && !empty($affiliate['trackingCode']) && isSet($sub_link)) {?>
	                    <div class="col-md-12 well" style="overflow: auto;">
	                        <table class="table table-striped">
	                            <thead>
	                                <th><?=lang('aff.asb11');?></th>
	                            </thead>

	                            <tbody>
	                                <tr>
	                                    <td><?=$sublink . $affiliate['trackingCode'];?></td>
	                                </tr>
	                            </tbody>
	                        </table>
	                    </div>
	                    <?php }
?>
	                </div>
					<br/>
					<div class="row">
	                    <div class="col-md-12 well" style="overflow: auto;">
	                        <table class="table table-striped">
	                            <thead>
	                                <th><?=lang('lang.affdomain');?></th>
	                            </thead>
	                            <tbody>
	                                <tr>
	                                    <td><?=empty($affiliate['affdomain']) ? lang('lang.norecyet') : $affiliate['affdomain'];?></td>
	                                </tr>
	                            </tbody>
	                        </table>
	                    </div>
	                </div>

					<br/>

					<table class="table table-striped table-hover" id="trackingTable" style="width:100%;">
						<thead>
							<th></th>
							<th class="input-sm">#</th>
							<th class="input-sm"><?=lang('mod.url');?></th>
							<th class="input-sm"></th>
							<th class="input-sm"><?=lang('mod.updateDate');?></th>
							<th class="input-sm"><?=lang('lang.status');?></th>
							<th class="input-sm"><?=lang('lang.notes');?></th>
						</thead>

						<tbody>
							<?php
$cnt = 0;

foreach ($domain as $key => $domain_value) {
	$cnt++;
	?>
									<tr>
										<td></td>
										<td class="input-sm"><?=$cnt?></td>
										<td class="input-sm"><?=$domain_value['domainName'] . '/aff/' . $affiliate['trackingCode']?></td>
										<td class="input-sm"><?=$domain_value['domainName'] . '/aff.html?code=' . $affiliate['trackingCode']?></td>
										<!--td class="input-sm"><?=$domain_value['domainName'] . $player_register_uri . '?aff=' . $affiliate['trackingCode']?></td-->
										<td class="input-sm"><?=$domain_value['updatedOn']?></td>
										<td class="input-sm"><?=($domain_value['status'] == 0) ? lang('lang.active') : lang('Blocked')?></td>
										<td class="input-sm"><?=$domain_value['notes']?></td>
									</tr>
							<?php
}
?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- End of Tracking Code and Links -->

	<!-- Bank Information -->
	<div class="row">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h4 class="panel-title">
					<i class="glyphicon glyphicon-cog"></i> <?=lang('mod.bankinfo');?>

					<a href="<?=BASEURL . 'affiliate/addNewAccount'?>" role="button" class="btn btn-sm btn-primary pull-right" title="<?=lang('mod.addacc');?>">
						<span class="glyphicon glyphicon-plus-sign"></span> <?=lang('lang.addbank');?>
					</a>
					<span class="clearfix"></span>
				</h4>
			</div>

			<div class="panel panel-body" id="code_panel_body">
				<div class="row table-responsive">
					<div class="col-md-12 col-md-offset-0">
						<table class="table table-striped" id="bankTable" style="width:100%;">
							<thead>
								<th></th>
								<th class="input-sm">#</th>
								<th class="input-sm"><?=lang('pay.bankname');?></th>
								<th class="input-sm"><?=lang('pay.accname');?></th>
								<th class="input-sm"><?=lang('pay.accnum');?></th>
								<th class="input-sm"><?=lang('pay.accinfo');?></th>
								<th class="input-sm"><?=lang('lang.status');?></th>
								<th class="input-sm"><?=lang('lang.action');?></th>
							</thead>

							<tbody>
								<?php
if (!empty($payment)) {
	$cnt = 0;

	foreach ($payment as $key => $payment_value) {
		$cnt++;
		?>
											<tr>
												<td class="input-sm"></td>
												<td class="input-sm"><?=$cnt?></td>
												<td class="input-sm"><?=$payment_value['bankName']?></td>
												<td class="input-sm"><?=$payment_value['accountName']?></td>
												<td class="input-sm"><?=$payment_value['accountNumber']?></td>
												<td class="input-sm"><?=$payment_value['accountInfo']?></td>
												<td class="input-sm"><?=($payment_value['status'] == 0) ? lang('lang.active') : lang('Blocked')?></td>
												<td class="input-sm">
													<a href="<?=BASEURL . 'affiliate/editPayment/' . $payment_value['affiliatePaymentId']?>" data-toggle="tooltip" title="<?=lang('lang.edit');?>"><i class="glyphicon glyphicon-edit"></i></a>

													<?php if ($payment_value['status'] == 0) {?>
														<a href="#" data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="inactive" onclick="deactivatePayment('<?=$payment_value['affiliatePaymentId']?>','<?=$payment_value['bankName']?>'); "><i class="glyphicon glyphicon-remove-circle"></i></a>
													<?php } else {?>
														<a href="#" data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="active" onclick="activatePayment('<?=$payment_value['affiliatePaymentId']?>','<?=$payment_value['bankName']?>'); "><i class="glyphicon glyphicon-ok-sign"></i></a>
														<a href="#" data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="active" onclick="deletePayment('<?=$payment_value['affiliatePaymentId']?>', '<?=$payment_value['bankName']?>'); "><i class="glyphicon glyphicon-remove"></i></a>
													<?php }
		?>
												</td>
											</tr>
								<?php
}
}
?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End of Bank Information -->

	<!-- Start Sub Affiliate Terms -->
	<div class="row">
		<div class="panel panel-danger">
			<div class="panel-heading">
				<h4 class="panel-title">
                    <i class="fa fa-cogs"></i> <?=lang('aff.asb10');?>
                </h4>

				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="sub_affiliate_main_panel_body">
            	<?php if ($no_setting_found) {?>
            		<b class="text-danger text-center"><?=lang('aff_setting_error');?></b>
            	<?php } else {
	?>
					<form id="frm_sub_option" method="post">

		            <div class="row">
		            	<input type="hidden" name="terms_type" value="allow">
					</div>
					<div class="sub-affiliate-options">
		            	<!-- sub option -->
						<div class="col-xs-12" id="btn_group_sub_allowed">
							<div class="form-group">
								<input type="hidden" name="sub_allowed" value="manual">
								<!--lablel><b></b></lablel-->
								<fieldset>
									<br>
					                <div class="col-xs-6">
										<div class="form-group">
											<label for="pt">
												<input type="checkbox" name="manualOpen" value="manual" <?php if (isSet($manual_open)) {
		echo 'checked';
	}
	?> readonly>
												<?=lang('aff.ai94');?>
											</label>
										</div>
									</div>
					                <div class="col-xs-6">
										<div class="form-group">
											<label for="pt">
												<input type="checkbox" name="subLink" value="link" <?php if (isSet($sub_link)) {
		echo 'checked';
	}
	?> readyonly>
												<?=lang('aff.ai95');?>
											</label>
										</div>
									</div>
					            </fieldset>
				            </div>
						</div>
					</div>
					<div class="sub-affiliate-options">
			            <!-- SUB-OPTION 1 -->
			            <div class="col-md-12">
			            	<lablel><b><?=lang('aff.ts01');?> <?=lang('aff.ts02');?></b></lablel>
							<fieldset>
			            	<br>
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<div class="input-group">
											      	<div class="input-group-addon"><?=lang('lang.level');?> 0: <?=lang('lang.master');?></div>
													<input type="number" class="form-control" name="level_master" id="total_shares"
														value="<?php if (isset($level_master) && $level_master != '') {
		echo $level_master;
	}
	?>" readonly />
											      	<div class="input-group-addon">%</div>
											    </div>
											</div>
										</div><!-- end col-md-12 -->
									</div>
								</div><!-- end col-md-12 -->
								<div class="col-md-12">
									<label id="sub_level_label"><?=lang('aff.ai99');?></label>
									<input type="hidden" id="sub_level" name="sub_level" value="<?php echo $this->config->item('subAffiliateLevels'); ?>">
									<div class="row" id="sub_level_container">
										<?php if (count($sub_levels) > 0) {
		?>
											<?php if (count($sub_levels) == 1) {?>
												<div class="col-md-12">
													<div class="form-group">
														<div class="input-group">
													      	<div class="input-group-addon"><?=lang('lang.level');?> 1:</div>
															<input type="number" class="form-control" name="sub_levels[]" id="total_shares"
																value="<?php echo $sub_levels[0]; ?>" readonly />
													      	<div class="input-group-addon">%</div>
													    </div>
													</div>
												</div><!-- end col-md-12 -->
											<?php } else {
			?>
												<?php foreach ($sub_levels as $key => $value) {
				?>
												<?php if (!empty($value)) {?>
													<div class="col-md-3">
														<div class="form-group">
															<div class="input-group">
														      	<div class="input-group-addon"><?=lang('lang.level');?> <?php echo $key + 1; ?>:</div>
																<input type="number" class="form-control" name="sub_levels[]" id="total_shares"
																	value="<?php echo $value; ?>" readonly />
														      	<div class="input-group-addon">%</div>
														    </div>
														</div>
													</div><!-- end col-md-6 -->
												<?php }
				?>
												<?php }
			?>
											<?php }
		?>
										<?php }
	?>
									</div><!-- sub_level_container -->
								</div><!-- end col-md-12 -->
				            </fieldset>
						</div>
		            </div>
		            <div class="col-md-12">
					</div>
					</form>

            	<?php }
?>
            </div><!-- end panel-body -->
        </div>
    </div>
	<!-- End Sub Affiliate Terms -->
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#trackingTable').DataTable({
        	// "responsive": {
         //        details: {
         //            type: 'column'
         //        }
         //    },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        } );

        $('#bankTable').DataTable( {
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        } );
    } );
</script>