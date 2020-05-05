<form action="saveAffiliateCommissionTier" method="POST">
<div class="row">
	<div class="col-md-8 col-md-offset-2">

			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Affiliate Commission Tier</h3> 
				</div>
				<div class="panel-body">
					<div class="well">
						<label>Gross Formula:</label><br/>
						<label class="radio-inline">
						<input type="radio" name="baseIncomeConfig" value="1" checked="checked">
							(Total Loss - Total Win) &times; Game Platform Rate												
						</label>
				    </div>
					<button type="button" class="btn btn-default btn-xs pull-right" onclick="addTier()"><i class="fa fa-plus fa-fw"></i> Add Affiliate Commission Tier</button>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th>Tier</th>
							<th>Minimum Active Players</th>
							<th>Minimum Net Revenue</th>
							<th>Commission Percentage</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data['tiers'] as $i => $tier): ?>
							<tr class="tier-group" id="tier-group-<?=$i+1?>">
								<td><?=$i+1?></td>
								<td><input type="number" name="active_players[]" class="form-control input-sm" value="<?=number_format($tier['active_players'], 2, '.', ',')?>" min="0" step="1"/></td>
								<td><input type="number" name="net_revenue[]" class="form-control input-sm" value="<?=number_format($tier['net_revenue'], 2, '.', ',')?>" min="0" step="any"/></td>
								<td>
									<div class="input-group">
										<input type="number" name="commission_percentage[]" class="form-control input-sm" value="<?=number_format($tier['commission_percentage'], 2, '.', ',')?>" min="0" step="any"/>
										<div class="input-group-addon">%</div>
									</div>
								</td>
								<td>
									<ul class="list-unstyled">
										<li><a href="javascript:void(0)" class="edit-tier" onclick="editTier(this)" tabindex="-1"><i class="fa fa-pencil fa-fw"></i> Edit</a></li>
										<li><a href="javascript:void(0)" class="remove-tier" onclick="removeTier(this)" tabindex="-1" style="display: none;"><i class="fa fa-trash fa-fw"></i> Remove</a></li>
									</ul>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
				<div class="panel-body">

					<div class="form-group">
						<label class="control-label" for="minimumDeposit">Minimum Deposit Per Player ( Main wallet )</label>
						<input type="text" class="form-control amount_only user-success" name="minimumDeposit" id="minimumDeposit" value="<?=number_format($data['minimumDeposit'], 2, '.', ',')?>" min="0.00" step="0.01">
					</div>

				    <p class="help-block well"><b>Active Player</b> means total number of players that has above minimum Deposit or Betting amount based on the Current Month. If a game provider is selected, then active players should be above or equal to total active players on selected game provider.</p>
				    
			    </div>
				<div class="panel-footer"></div>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Affiliate Commission Fees</h3> 
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="form-group input-sm col-lg-6">
							<div class="input-group">
								<div class="input-group-addon">
									<input type="checkbox" id="admin_fee" onclick="toggleInput(this, 'input[name=\'admin_fee\']')" <?php if ($data['admin_fee']) echo 'checked="checked"'?>> 
								</div>
								<label for="admin_fee" class="input-group-addon" style="width: 140px; text-align: left;">
									Admin Fee
								</label>
								<input type="number" name="admin_fee" class="form-control text-right" value="<?=$data['admin_fee'] ? number_format($data['admin_fee'], 2, '.', ',') : '0.00'?>" min="0.00" step="0.01" <?php if ( ! $data['admin_fee']) echo 'disabled="disabled"'?>>
								<div class="input-group-addon">%</div>
							</div>
						</div>
						<div class="form-group input-sm col-lg-6">
							<div class="input-group">
								<div class="input-group-addon">
									<input type="checkbox" id="transaction_fee" onclick="toggleInput(this, 'input[name=\'transaction_fee\']')" <?php if ($data['transaction_fee']) echo 'checked="checked"'?>>
								</div>
								<label for="transaction_fee" class="input-group-addon" style="width: 140px; text-align: left;">
									Transaction Fee
								</label>
								<input type="number" name="transaction_fee" class="form-control text-right" value="<?=$data['transaction_fee'] ? number_format($data['transaction_fee'], 2, '.', ',') : '0.00'?>" min="0.00" step="0.01" <?php if ( ! $data['transaction_fee']) echo 'disabled="disabled"'?>>
								<div class="input-group-addon">%</div>
							</div>
						</div>
						<div class="form-group input-sm col-lg-6">
							<div class="input-group">
								<div class="input-group-addon">
									<input type="checkbox" id="bonus_fee" onclick="toggleInput(this, 'input[name=\'bonus_fee\']')" <?php if ($data['bonus_fee']) echo 'checked="checked"'?>> 
								</div>
								<label for="bonus_fee" class="input-group-addon" style="width: 140px; text-align: left;">
									Bonus Fee
								</label>
								<input type="number" name="bonus_fee" class="form-control text-right" value="<?=$data['bonus_fee'] ? number_format($data['bonus_fee'], 2, '.', ',') : '0.00'?>" min="0.00" step="0.01" <?php if ( ! $data['bonus_fee']) echo 'disabled="disabled"'?>>
								<div class="input-group-addon">%</div>
							</div>
						</div>
						<div class="form-group input-sm col-lg-6">
							<div class="input-group">
								<div class="input-group-addon">
									<input type="checkbox" id="cashback_fee" onclick="toggleInput(this, 'input[name=\'cashback_fee\']')" <?php if ($data['cashback_fee']) echo 'checked="checked"'?>> 
								</div>
								<label for="cashback_fee" class="input-group-addon" style="width: 140px; text-align: left;">
									Cashback Fee
								</label>
								<input type="number" name="cashback_fee" class="form-control text-right" value="<?=$data['cashback_fee'] ? number_format($data['cashback_fee'], 2, '.', ',') : '0.00'?>" min="0.00" step="0.01">
								<div class="input-group-addon">%</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Affiliate Commission Payment Settings</h3> 
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label" for="minimumPayAmount">Minimum Affiliate Pay</label>
								<input type="number" name="minimumPayAmount" class="form-control" id="minimumPayAmount" value="<?=number_format($data['minimumPayAmount'], 2, '.', ',')?>" min="0.00" step="0.01">
							</div>

							<div class="form-group">
								<label>Transfer automatically to:</label><br/>
								<label class="radio-inline"><input type="radio" name="autoTransferToWallet" value="none" checked="checked"> None</label>
								<label class="radio-inline"><input type="radio" name="autoTransferToWallet" value="main"> Main Wallet</label>
								<label class="radio-inline"><input type="radio" name="autoTransferToWallet" value="locked"> Locked Wallet</label>
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
									<label class="control-label" for="minimumPayAmount">Affiliate Payment Schedule</label>
									<div class="input-group">
										<div class="input-group-addon">
											<input type="radio" name="paymentSchedule" value="" id="monthly" onchange="toggleInput(this, '#paymentDay')" <?php if (is_int($data['paymentSchedule'])) echo 'checked="checked"'?>> 
										</div>
								      	<label class="input-group-addon" for="paymentSchedule">
								      		Monthly Payment Day
							      		</label>
										<select name="paymentSchedule" class="form-control" id="paymentDay">
											<option value="">Select Day of the Month</option>
											<?php for ($i = 1; $i <= 31; $i++): ?>
												<option value="<?=$i?>" <?php if ($data['paymentSchedule'] == $i) echo 'selected="selected"'?>><?=$i?></option>
											<?php endfor ?>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label>Weekly:</label><br/>
					                <label class="radio-inline"><input type="radio" name="paymentSchedule" value="monday" onchange="disableInput(this, '#paymentDay')" <?php if ($data['paymentSchedule'] === 'monday') { echo 'checked="checked"'; } ?>>Monday</label>
					                <label class="radio-inline"><input type="radio" name="paymentSchedule" value="tuesday" onchange="disableInput(this, '#paymentDay')" <?php if ($data['paymentSchedule'] === 'tuesday') { echo 'checked="checked"'; } ?>>Tuesday</label>
					                <label class="radio-inline"><input type="radio" name="paymentSchedule" value="wednesday" onchange="disableInput(this, '#paymentDay')" <?php if ($data['paymentSchedule'] === 'wednesday') { echo 'checked="checked"'; } ?>>Wednesday</label>
					                <label class="radio-inline"><input type="radio" name="paymentSchedule" value="thursday" onchange="disableInput(this, '#paymentDay')" <?php if ($data['paymentSchedule'] === 'thursday') { echo 'checked="checked"'; } ?>>Thursday</label>
					                <label class="radio-inline"><input type="radio" name="paymentSchedule" value="friday" onchange="disableInput(this, '#paymentDay')" <?php if ($data['paymentSchedule'] === 'friday') { echo 'checked="checked"'; } ?>>Friday</label>
					                <label class="radio-inline"><input type="radio" name="paymentSchedule" value="saturday" onchange="disableInput(this, '#paymentDay')" <?php if ($data['paymentSchedule'] === 'saturday') { echo 'checked="checked"'; } ?>>Saturday</label>
					                <label class="radio-inline"><input type="radio" name="paymentSchedule" value="sunday" onchange="disableInput(this, '#paymentDay')" <?php if ($data['paymentSchedule'] === 'sunday') { echo 'checked="checked"'; } ?>>Sunday</label>
								</div>
						</div>
					</div>
				</div>
				<div class="panel-footer"></div>
			</div>
			<div class="panel panel-primary">
	            <div class="panel-heading">
	                <h3 class="panel-title">Sub Affiliate Commission Setting</h3>
	            </div>
				<div class="panel-body">
					<div class="panel panel-default">
								<div class="panel-body">
									<div class="row">
						                <div class="col-xs-6">
											<div class="form-group">
												<label>
													<input type="checkbox" name="manual_open" value="true" <?php if ($data['manual_open']) echo 'checked="checked"'?>>
													Manual Open Sub Affiliate
												</label>
											</div>
										</div>
						                <div class="col-xs-6">
											<div class="form-group">
												<label>
													<input type="checkbox" name="sub_link" value="true" <?php if ($data['sub_link']) echo 'checked="checked"'?>>
													Sub Affiliate Link
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="panel-footer"></div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">Sub Affiliates Percentage</h3>
								</div>
								<div class="panel-body">
									<div class="row">
										<?php for ($i = 1; $i <= 10; $i++): ?>
											<div class="form-group input-sm col-md-6">
												<div class="input-group">
											      	<div class="input-group-addon" style="width: 100px; text-align: left;">Level <?=$i?>:</div>
													<input type="number" name="sub_levels[]" class="form-control text-right" value="<?=number_format($data['sub_levels'][$i-1], 2, '.', ',')?>" min="0.00" step="0.01">
											      	<div class="input-group-addon">%</div>
											    </div>
											</div>
										<?php endfor ?>
									</div>
								</div>
								<div class="panel-footer"></div>
							</div>
							<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save fa-fw"></i> Save Changes</button>
				    		
			            </div><!-- end panel-body -->
			        </div>
			    </div>
			</div>
		</form>
	</div>
</div>
</form>
<script type="text/javascript">

	$(function() {

	});

	function addTier() {
		var tier = $('.tier-group').length + 1;
		var $tier = $('.tier-group:first').clone();
		$tier.attr('id', 'tier-group-' + tier)
		$tier.find('td:first').text(tier);
		$tier.find('input').val('');
		$('tbody').append($tier);
		$('.tier-group .remove-tier').hide();
		$('.tier-group .remove-tier:not(:first):last').show();
	}

	function removeTier(e) {
		var $tier = $(e).parents('tr');
		$tier.remove();
		$('.tier-group .remove-tier').hide();
		$('.tier-group .remove-tier:not(:first):last').show();
	}

	function toggleInput(e, selector) {
		$(selector).prop('disabled', ! e.checked);
	}

	function disableInput(e, selector) {
		$(selector).prop('disabled', e.checked);
	}

</script>