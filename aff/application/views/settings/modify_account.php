<?php

$affId=$affiliate['affiliateId'];

?>
<!--
<?php echo json_encode($commonSettings, JSON_PRETTY_PRINT);?>
-->

<style type="text/css">
	.acct_info .col-md-5 {
		white-space: nowrap;
		overflow: hidden;
	}
</style>

<div class="container">
	<br/>

	<!-- Personal Information -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="nav-head panel-heading">
				<h4 class="panel-title"><i class="glyphicon glyphicon-cog"></i> <?=lang('mod.accinfo');?> </h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="info_panel_body">
				<div class="row acct_info">
					<div class="col-md-6">
						<div class="form-group">
							<span for="username" class="col-md-4"><?=lang('reg.03');?></span>
							<label class="col-md-8"><?=empty($affiliate['username']) ? '<i class="text-muted">N/A</i>' : $affiliate['username'];?></label>
						</div>
						<div class="form-group">
							<span for="firstname" class="col-md-4"><?=lang('reg.a09');?></span>
							<label class="col-md-8"><?=empty($affiliate['firstname']) ? '<i class="text-muted">N/A</i>' : $affiliate['firstname'];?></label>
						</div>
						<div class="form-group">
							<span for="lastname" class="col-md-4"><?=lang('reg.a10');?></span>
							<label class="col-md-8"><?=empty($affiliate['lastname']) ? '<i class="text-muted">N/A</i>' : $affiliate['lastname'];?></label>
						</div>
						<div class="form-group">
							<span for="gender" class="col-md-4"><?=lang('reg.a12');?></span>
							<label class="col-md-8"><?=empty($affiliate['gender']) ? '<i class="text-muted">N/A</i>' : $affiliate['gender'];?></label>
						</div>
						<div class="form-group">
							<span for="birthday" class="col-md-4"><?=lang('reg.a11');?></span>
							<label class="col-md-8"><?=explode(" ", $affiliate['birthday'])[0] != '0000-00-00' ? explode(" ", $affiliate['birthday'])[0] : '<i class="text-muted">N/A</i>';?></label>
						</div>
						<div class="form-group">
							<span for="phone" class="col-md-4"><?=lang('reg.a25');?></span>
							<label class="col-md-8"><?=empty($affiliate['phone']) ? '<i class="text-muted">N/A</i>' : $affiliate['phone'];?></label>
						</div>
						<div class="form-group">
							<span for="mobile" class="col-md-4"><?=lang('reg.a24');?></span>
							<label class="col-md-8"><?=empty($affiliate['mobile']) ? '<i class="text-muted">N/A</i>' : $affiliate['mobile'];?></label>
						</div>
						<div class="form-group">
							<span for="address" class="col-md-4"><?=lang('reg.a20');?></span>
							<label class="col-md-8"><?=empty($affiliate['address']) ? '<i class="text-muted">N/A</i>' : $affiliate['address'];?></label>
						</div>
						<div class="form-group">
							<span for="city" class="col-md-4"><?=lang('reg.a19');?></span>
							<label class="col-md-8"><?=empty($affiliate['city']) ? '<i class="text-muted">N/A</i>' : $affiliate['city'];?></label>
						</div>
						<div class="form-group">
							<span for="country" class="col-md-4"><?=lang('reg.a23');?></span>
							<label class="col-md-8"><?=empty($affiliate['country']) ? '<i class="text-muted">N/A</i>' : $affiliate['country'];?></label>
						</div>
						<div class="form-group">
							<span for="zip" class="col-md-4"><?=lang('reg.a21');?></span>
							<label class="col-md-8"><?=empty($affiliate['zip']) ? '<i class="text-muted">N/A</i>' : $affiliate['zip'];?></label>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<span for="state" class="col-md-5"><?=lang('reg.a22');?></span>
							<label class="col-md-7"><?=empty($affiliate['state']) ? '<i class="text-muted">N/A</i>' : $affiliate['state'];?></label>
						</div>
						<div class="form-group">
							<span for="occupation" class="col-md-5"><?=lang('reg.a16');?></span>
							<label class="col-md-7"><?=empty($affiliate['occupation']) ? '<i class="text-muted">N/A</i>' : $affiliate['occupation'];?></label>
						</div>
						<div class="form-group">
							<span for="company" class="col-md-5"><?=lang('reg.a15');?></span>
							<label class="col-md-7"><?=empty($affiliate['company']) ? '<i class="text-muted">N/A</i>' : $affiliate['company'];?></label>
						</div>
						<div class="form-group">
							<span for="imtype1" class="col-md-5"><?=lang('reg.a26');?></span>
							<label class="col-md-7"><?=empty($affiliate['imType1']) ? '<i class="text-muted">N/A</i>' : lang($affiliate['imType1']);?></label>
						</div>
						<div class="form-group">
							<span for="im1" class="col-md-5"><?=lang('reg.a30');?></span>
							<label class="col-md-7"><?=empty($affiliate['im1']) ? '<i class="text-muted">N/A</i>' : $affiliate['im1'];?></label>
						</div>
						<div class="form-group">
							<span for="imtype2" class="col-md-5"><?=lang('reg.a31');?></span>
							<label class="col-md-7"><?=empty($affiliate['imType2']) ? '<i class="text-muted">N/A</i>' : lang($affiliate['imType2']);?></label>
						</div>
						<div class="form-group">
							<span for="im2" class="col-md-5"><?=lang('reg.a35');?></span>
							<label class="col-md-7"><?=empty($affiliate['im2']) ? '<i class="text-muted">N/A</i>' : $affiliate['im2'];?></label>
						</div>
						<div class="form-group">
							<span for="website" class="col-md-5"><?=lang('reg.a41');?></span>
							<label class="col-md-7"><?=empty($affiliate['website']) ? '<i class="text-muted">N/A</i>' : $affiliate['website'];?></label>
						</div>
						<div class="form-group">
							<span for="mode_of_contact" class="col-md-5"><?=lang('reg.a36');?></span>
							<label class="col-md-7"><?=empty($affiliate['modeOfContact']) ? '<i class="text-muted">N/A</i>' : ucfirst($affiliate['modeOfContact']);?></label>
						</div>
						<div class="form-group">
							<span for="email" class="col-md-5"><?=lang('reg.a17');?></span>
							<label class="col-md-7"><?=empty($affiliate['email']) ? '<i class="text-muted">N/A</i>' : $affiliate['email'];?></label>
						</div>
						<?php if ( ! $this->utils->is_readonly()): ?>
							<div class="form-group">
								<span for="email" class="col-md-5"><?=lang('reg.05');?></span>
								<div class="col-md-7">
									<a href="<?=BASEURL . 'affiliate/modifyPassword'?>"><?=lang('Reset Password');?></a>
									<?php if ($this->utils->isEnabledFeature('affiliate_second_password')) {?>
									<br>
									<a href="<?=BASEURL . 'affiliate/modifySecondPassword'?>"><?=lang('Change Secondary Password');?></a>
									<?php }?>
								</div>
							</div>
						<?php endif ?>
					</div>

					<input type="hidden" name="currency" id="currency" value="<?=$affiliate['currency']?>">
					<span class="clearfix"></span>
					<hr class="style-one"/>
				</div>
				<?php if ( ! $this->utils->is_readonly()): ?>
					<div class="row">
						<center>
							<a href="<?=BASEURL . 'affiliate/editInfo/' . $affiliate['affiliateId']?>" class="btn-hov btn btn-info"> <?=lang('mod.editProfile');?> </a>
						</center>
					</div>
				<?php endif ?>
			</div>
		</div>
	</div>
	<!-- End of Personal Information -->

	<!-- Bank Information -->
	<div class="row">
		<div id="back-info" class="panel panel-warning">
			<div class="panel-heading">
				<h4 class="panel-title">
					<i class="glyphicon glyphicon-cog"></i> <?=lang('mod.bankinfo');?>

					<?php if ( ! $this->utils->is_readonly()): ?>
						<a href="<?=BASEURL . 'affiliate/addNewAccount'?>" role="button" class="btn btn-sm btn-primary pull-right" title="<?=lang('mod.addacc');?>">
	                        <span class="glyphicon glyphicon-plus-sign"></span> <?=lang('lang.addbank');?>
	                    </a>
					<?php endif ?>
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
								<!-- OGP-1087 <th class="input-sm"><?=lang('lang.action');?></th> -->
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
												<td class="input-sm"><?=lang($payment_value['bankName'])?></td>
												<td class="input-sm"><?=$payment_value['accountName']?></td>
												<td class="input-sm"><?=$payment_value['accountNumber']?></td>
												<td class="input-sm"><?=$payment_value['accountInfo']?></td>
												<td class="input-sm"><?=($payment_value['status'] == 0) ? lang('lang.active') : lang('Blocked')?></td>
											<!-- OGP-1087 <td class="input-sm"> -->
												<!-- <?php
                                                        // 0 - True | 1 - False
                                                        $check_edit = $payment_value['editCount'];

                                                        if ($check_edit > 0) {
                                                            $payment_edit_status = 1;
                                                        } else {
                                                            $payment_edit_status = 0;
                                                        }

                                                        if ($payment_edit_status == 0) {
                                                    ?>
                                                            <a href="<?=BASEURL . 'affiliate/editPayment/' . $payment_value['affiliatePaymentId']?>" data-toggle="tooltip" title="<?=lang('lang.edit');?>"><i class="glyphicon glyphicon-edit"></i></a>
                                                    <?php
                                                        }
                                                    ?> -->
                                            <!-- OGP-1087
													<?php if ($payment_value['status'] == 0) {?>
														<a href="#" data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="inactive" onclick="deactivatePayment('<?=$payment_value['affiliatePaymentId']?>','<?=$payment_value['bankName']?>'); "><i class="glyphicon glyphicon-remove-circle"></i></a>
													<?php } else {?>
														<a href="#" data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="active" onclick="activatePayment('<?=$payment_value['affiliatePaymentId']?>','<?=$payment_value['bankName']?>'); "><i class="glyphicon glyphicon-ok-sign"></i></a>
														<a href="#" data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="active" onclick="deletePayment('<?=$payment_value['affiliatePaymentId']?>', '<?=$payment_value['bankName']?>'); "><i class="glyphicon glyphicon-remove"></i></a>
													<?php } ?>
												</td>
											-->
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

<?php
if($commonSettings['manual_open'] || $commonSettings['sub_link']){
?>
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
					<form id="frm_sub_option" method="post">

					<div class="sub-affiliate-options">
		            	<!-- sub option -->
						<div class="col-xs-12" id="btn_group_sub_allowed">
							<div class="form-group">
								<fieldset>
									<br>
					                <div class="col-xs-6">
										<div class="form-group">
											<label for="pt">
												<input type="checkbox" disabled name="manual_open" value="true" <?php echo $commonSettings['manual_open'] ? 'checked' : '';?> readonly>
												<?=lang('aff.ai94');?>
											</label>
										</div>
									</div>
					                <div class="col-xs-6">
										<div class="form-group">
											<label for="pt">
												<input type="checkbox" disabled name="sub_link" value="true" <?php echo $commonSettings['sub_link'] ? 'checked' : '';?> readyonly>
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
														value="<?php echo $commonSettings['level_master']; ?>" readonly />
											      	<div class="input-group-addon">%</div>
											    </div>
											</div>
										</div><!-- end col-md-12 -->
									</div>
								</div><!-- end col-md-12 -->
								<div class="col-md-12">
									<label id="sub_level_label"><?=lang('aff.ai99');?></label>
									<input type="hidden" id="sub_level" name="sub_level" value="<?php echo $commonSettings['sub_level']; ?>">
									<div class="row" id="sub_level_container">
										<?php
											$sub_levels=$commonSettings['sub_levels'];
											if (count($sub_levels) > 0) {
		?>
												<?php foreach ($sub_levels as $key => $value) {
				?>
												<?php
													$value= empty($value) ? 0 : $value;
												?>
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
									</div><!-- sub_level_container -->
								</div><!-- end col-md-12 -->
				            </fieldset>
						</div>
		            </div>
		            <div class="col-md-12">
					</div>
					</form>

            </div><!-- end panel-body -->
        </div>
    </div>
	<!-- End Sub Affiliate Terms -->
<?php
}?>

<?php if ($this->utils->isEnabledFeature('aff_enable_read_only_account')): ?>
	<div class="panel panel-warning">
		<div class="panel-heading">
			<h4 class="panel-title">
				<i class="glyphicon glyphicon-cog"></i> <?=lang('Read-only Accounts');?>
				<?php if ( ! $this->utils->is_readonly()): ?>
					<a href="/affiliate/addReadOnlyAccount" class="btn btn-sm btn-primary pull-right"><?=lang('Add Read-only Account')?></a>
				<?php endif ?>
				<span class="clearfix"></span>
			</h4>

		</div>

		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped" id="readOnlyAccounts" style="width:100%;">
					<thead>
						<th><?=lang('Username');?></th>
						<th class="text-right"><?=lang('Created At');?></th>
						<?php if ( ! $this->utils->is_readonly()): ?>
							<th class="text-right"></th>
						<?php endif ?>
					</thead>
					<tbody>
						<?php if ( ! empty($readonly_accounts)): ?>
							<?php foreach ($readonly_accounts as $account): ?>
							<tr>
								<td><?=$account['username']?></td>
								<td align="right"><?=$account['created_at']?></td>
								<?php if ( ! $this->utils->is_readonly()): ?>
									<td align="right">
										<a href="/affiliate/changeReadOnlyAccountPassword/<?=$account['id']?>" class="btn btn-sm btn-warning"><?=lang('Change Password')?></a>
										<a href="/affiliate/deleteReadOnlyAccount/<?=$account['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('<?=lang('sys.gd4')?>');"><?=lang('Delete')?></a>
									</td>
								<?php endif ?>
							</tr>
							<?php endforeach ?>
						<?php endif ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php endif ?>

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
