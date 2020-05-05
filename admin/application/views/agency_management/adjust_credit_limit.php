<div class="panel panel-primary">
	<div class="panel-heading">
        <h4 class="panel-title pull-left">
            <i class="icon-equalizer2"></i> 
            <?=lang('transaction.adjustCredit')?> 
        </h4>
        <a href="<?=BASEURL . 'agency_management/agent_information/' . $agent_id?>" 
            class="btn btn-default btn-sm pull-right" id="reset_password">
            <span class="glyphicon glyphicon-remove"></span>
        </a>
        <div class="clearfix"></div>
	</div>

	<div class="panel panel-body" id="player_panel_body">
        <form method="POST" action="<?=BASEURL . 'agency_management/process_adjust_credit_limit/' . $agent_id?>" 
            autocomplete="off">

			<div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th class="active col-md-2"><?=lang('Current Credit Limit');?>:</th>
								<td class="col-md-4">
                                    <input type="text" name="credit_limit" id="credit_limit" class="form-control" 
                                    value="<?=$agent['credit_limit']?>" readonly>
								</td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('Available Credit');?>:</th>
								<td class="col-md-4">
                                    <input type="text" name="current_credit" id="current_credit" class="form-control" 
                                    value="<?=$agent['available_credit']?>" readonly>
								</td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('New Credit Limit');?>:</th>
								<td class="col-md-4">
                                    <input type="text" name="new_credit_limit" id="new_credit_limit" class="form-control" 
                                    value="" title="New credit limit must greater than current available credit">
                                    <span class="errors"><?php echo form_error('new_credit_limit'); ?></span>
                                    <span id="error-new_credit_limit" class="errors"></span>
                                </td>
							</tr>
							<input type="hidden" name="agent_id" id="agent_id" class="form-control" value="<?=$agent_id?>">
                            <input type="hidden" name="parent_id" id="parent_id" class="form-control" 
                            value="<?=$agent['parent_id']?>">
						</table>
						<center>
							<input type="submit" class="btn btn-info" value="<?=lang('lang.submit');?>">
                            <a href="<?=BASEURL . 'agency_management/agent_information/' . $agent_id?>" 
                            class="btn btn-default btn-md" id="reset_credit_limit"><?=lang('lang.cancel');?></a>
						</center>
					</div>
				</div>
			</div>
		</form>
	</div>

</div>
