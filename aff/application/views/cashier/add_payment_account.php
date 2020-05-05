<div class="container">
	<br/><br/>

	<!-- Payment Information -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-cog"></i> <?=lang('pay.newacc');?> </h4>
				<a href="<?=BASEURL . 'affiliate/cashier'?>" class="btn btn-info btn-xs pull-right"><span class="glyphicon glyphicon-remove "></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="info_panel_body">
				<form action="<?=BASEURL . 'affiliate/verifyAddNewPayment'?>" method="POST">
					<div class="row">
						<div class="col-md-6 col-md-offset-0">
							<label for="bank_name"><?=lang('pay.bankname');?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="account_name"><?=lang('pay.accname');?>: </label>
						</div>

						<div class="col-md-5 col-md-offset-0">
							<select name="bank_name" id="bank_name" class="form-control">
								<option value="">-- <?=lang('cashier.73');?> --</option>
								<?php foreach ($banks as $row): ?>
									<?php if ($row['enabled_withdrawal']): ?>
										<option value="<?=$row['bankTypeId']?>" <?php echo set_select('bank_name',  $row['bankTypeId']); ?>><?=lang($row['bankName'])?></option>
									<?php endif?>
								<?php endforeach?>
							</select>
							<label style="color: red; font-size: 12px;"><?php echo form_error('bank_name');?></label>
						</div>

						<div class="col-md-5 col-md-offset-1">
							<input type="text" name="account_name" id="account_name" class="form-control" value="<?=implode(' ', [$affiliate['firstname'], $affiliate['lastname']])?>" readonly="readonly">
							<label style="color: red; font-size: 12px;"><?php echo form_error('account_name');?></label>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-md-offset-0">
							<label for="account_info"><?=lang('pay.accinfo');?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="account_number"><?=lang('pay.accnum');?>: </label>
						</div>

						<div class="col-md-5 col-md-offset-0">
							<input type="text" name="account_info" id="account_info" class="form-control" value="<?=set_value('account_info');?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('account_info');?></label>
						</div>

						<div class="col-md-5 col-md-offset-1">
							<input type="text" name="account_number" id="account_number" class="form-control" value="<?=set_value('account_number');?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('account_number');?></label>
						</div>
					</div>

					<div class="row">
						<div class="col-md-2 col-md-offset-5">
							<input type="submit" name="submit" id="submit" class="btn btn-primary" value="<?=lang('lang.save');?>">
							<a href="<?=BASEURL . 'affiliate/cashier'?>" class="btn btn-primary"><?=lang('lang.cancel');?></a>
						</div>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
	<!-- End of Payment Information -->
</div>