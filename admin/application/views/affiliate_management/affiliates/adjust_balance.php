<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="icon-equalizer2"></i> <?=lang('transaction.adjustCredit')?> </h4>
		<a href="<?=BASEURL . 'affiliate_management/userInformation/' . $affiliate_id?>" class="btn btn-default btn-sm pull-right" id="reset_password"><span class="glyphicon glyphicon-remove"></span></a>
		<div class="clearfix"></div>
	</div>

	<div class="panel panel-body" id="player_panel_body">
		<form method="POST" action="<?=BASEURL . 'affiliate_management/processAdjustBalance/' . $affiliate_id?>" autocomplete="off">

			<div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th class="active col-md-2"><?=lang('aff.creditAmount');?>:</th>
								<td class="col-md-4">
									<input type="text" name="current_balance" id="current_balance" class="form-control" value="<?=$affiliate['balance']?>" readonly>
								</td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('pay.transactionType');?>:</th>
								<td class="col-md-4">
									<select name="transaction_type" class="form-control">
										<option value="add"><?=lang('transaction.transaction.type.17')?></option>
										<option value="subtract"><?=lang('transaction.transaction.type.18')?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('aff.apay08');?>:</th>
								<td class="col-md-4">
									<input type="hidden" name="before_balance" id="before_balance" class="form-control" value="<?=$affiliate['balance']?>">
									<input type="number" name="adjust_amount" id="adjust_amount" class="form-control" min="1" value="" required>
									<span style="color:red;"><?php echo form_error('new_balance'); ?></span>
								</td>
							</tr>
							<input type="hidden" name="affiliate_id" id="affiliate_id" class="form-control" value="<?=$affiliate_id?>">
						</table>
						<center>
							<input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>" value="<?=lang('lang.submit');?>">
							<a href="<?=BASEURL . 'affiliate_management/userInformation/' . $affiliate_id?>" class="btn btn-md <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" id="reset_password"><?=lang('lang.cancel');?></a>
						</center>
					</div>
				</div>
			</div>
		</form>
	</div>

</div>