<div class="panel panel-primary">
	<div class="panel-heading">
        <h4 class="panel-title pull-left">
            <i class="icon-equalizer2"></i> 
            <?=lang('transaction.adjustCredit')?> 
        </h4>
        <a href="<?=BASEURL . 'agency/agent_information/' . $agent_id?>" 
            class="btn btn-default btn-sm pull-right" id="reset_password">
            <span class="glyphicon glyphicon-remove"></span>
        </a>
        <div class="clearfix"></div>
	</div>

	<div class="panel panel-body" id="player_panel_body">
        <form method="POST" action="<?=BASEURL . 'agency/process_adjust_credit/' . $agent_id?>" 
            autocomplete="off">

			<div class="row">
                    <div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th class="active col-md-2"><?=lang('Credit Limit');?>:</th>
								<td class="col-md-4">
                                    <input type="text" name="credit_limit" id="credit_limit" class="form-control" 
                                    value="<?=$this->utils->formatCurrencyNoSym($agent['credit_limit'])?>" readonly>
								</td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('Available Credit');?>:</th>
								<td class="col-md-4">
                                    <input type="text" name="current_credit" id="current_credit" class="form-control" 
                                    value="<?=$this->utils->formatCurrencyNoSym($agent['available_credit'])?>" readonly>
								</td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('pay.transactionType');?>:</th>
								<td class="col-md-4">
                                    <!--
									<select name="transaction_type" class="form-control">
										<option value="add"><?=lang('transaction.transaction.type.17')?></option>
										<option value="subtract"><?=lang('transaction.transaction.type.18')?></option>
                                    </select> -->
                                    <div class="col-md-6">
                                        <input type="radio" name="transaction_type" value="add" checked>
                                        <?=lang('transaction.transaction.type.17');?>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="radio" name="transaction_type" value="sub">
                                        <?=lang('transaction.transaction.type.18');?>
                                    </div>
								</td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('Amount');?>:</th>
								<td class="col-md-4">
                                    <input type="number" name="adjust_amount" id="adjust_amount" class="form-control" 
                                    value="" required>
                                    <span class="errors"><?php echo form_error('adjust_amount'); ?></span>
                                    <span id="error-adjust_amount" class="errors"></span>
								</td>
							</tr>
							<input type="hidden" name="agent_id" id="agent_id" class="form-control" value="<?=$agent_id?>">
						</table>
						<center>
							<input type="submit" class="btn btn-info" value="<?=lang('lang.submit');?>">
                            <a href="<?=BASEURL . 'agency/agent_information/' . $agent_id?>" 
                            class="btn btn-default btn-md" id="reset_credit"><?=lang('lang.cancel');?></a>
						</center>
					</div>
			</div>
		</form>
	</div>

</div>
