<?php

$enable_tier = false;
if(isset($settings['enable_commission_by_tier']) && $settings['enable_commission_by_tier'] == true){
    $enable_tier = true;
}

if($this->utils->isEnabledFeature('individual_affiliate_term') || empty($affiliateId)){ ?>
	<label><strong><?=lang('earnings.formula');?></strong></label>
	<fieldset>
		<br>
			<?php if ( ! $this->utils->isEnabledFeature('switch_to_ibetg_commission')) { ?>
				<div class="col-xs-6">
					<div class="form-group">
						<label for="">
							<input type="radio" name="baseIncomeConfig" value="1" <?php echo $settings['baseIncomeConfig'] == Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN ? "checked='checked'" : ""; ?> >
							<?=lang('earnings.betwin');?>
							<span class="badge" data-toggle="tooltip" data-placement="top" title="<?=lang('earnings.betwin.formula');?>">?</span>
						</label>
					</div>
				</div>
				<?php if (!$this->utils->isEnabledFeature('disable_aff_gross_rev_formula_dep_minus_withdraw')) { ?>
				<div class="col-xs-6">
					<div class="form-group">
						<label for="">
							<input type="radio" name="baseIncomeConfig" value="2" <?php echo $settings['baseIncomeConfig'] == Affiliatemodel::INCOME_CONFIG_TYPE_DEPOSIT_WITHDRAWAL ? "checked='checked'" : ""; ?> >
							<?=lang('earnings.depositwithdraw');?>
							<span class="badge" data-toggle="tooltip" data-placement="top" title="<?=lang('earnings.depositwithdraw.formula');?>">?</span>
						</label>
					</div>
				</div>
				<?php } ?>
			<?php } else { ?>
				<div class="col-xs-3">
					<div class="form-group">
						<label for="">
							<input type="radio" name="baseIncomeConfig" value="1" <?php echo $settings['baseIncomeConfig'] == Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN ? "checked='checked'" : ""; ?> >
							<?=lang('earnings.betwin');?>
							<span class="badge" data-toggle="tooltip" data-placement="top" title="<?=lang('earnings.betwin.formula');?>">?</span>
						</label>
					</div>
				</div>
				<div class="col-xs-3">
					<div class="form-group">
						<label for="">
							<input type="radio" name="baseIncomeConfig" value="3" <?php echo $settings['baseIncomeConfig'] == Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN_TOTALCOMMISSION ? "checked='checked'" : ""; ?> >
							<?=lang('earnings.betwin.totalcommission');?>
							<span class="badge" data-toggle="tooltip" data-placement="top" title="<?=lang('earnings.betwin.totalcommission.formula');?>">?</span>
						</label>
					</div>
				</div>
				<div class="col-xs-3">
					<div class="form-group">
						<label for="">
							<input type="radio" name="baseIncomeConfig" value="4" <?php echo $settings['baseIncomeConfig'] == Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN_ACTIVEPLAYERCOMMISSION ? "checked='checked'" : ""; ?> >
							<?=lang('earnings.betwin.activeplayercommission');?>
							<span class="badge" data-toggle="tooltip" data-placement="top" title="<?=lang('earnings.betwin.activeplayercommission.formula');?>">?</span>
						</label>
					</div>
				</div>
				<div class="col-xs-3">
					<div class="form-group">
						<label for="">
							<input type="radio" name="baseIncomeConfig" value="5" <?php echo $settings['baseIncomeConfig'] == Affiliatemodel::INCOME_CONFIG_TYPE_BET_WIN_ACTIVEPLAYERCOMMISSIONBYGAMEPLATFORM ? "checked='checked'" : ""; ?> >
							<?=lang('earnings.betwin.activeplayercommissionbygameplatform');?>
							<span class="badge" data-toggle="tooltip" data-placement="top" title="<?=lang('earnings.betwin.activeplayercommissionbygameplatform.formula');?>">?</span>
						</label>
					</div>
				</div>
			<?php } ?>
		<br>
	</fieldset>

	<br>
	<label>
		<strong><?=lang('earnings.cost');?></strong>
	</label>
	<fieldset>
		<br>

		<?php if (empty($affiliateId)): ?>
			
			<div class="row">
				<div class="col-md-12">
					<label>
						<strong><?php echo lang('Game Platform Fee'); ?></strong>
						<small><a target="_blank" href="/game_api/viewGameApi">Edit</a></small>
					</label>
				</div>
				<?php foreach ($game as $g) { ?>
					<div class="col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon" style=""><?=$g['system_code'];?></div>
								<input class="form-control" type="text" value="<?=100-$g['game_platform_rate']?>" disabled="disabled"/>
								<div class="input-group-addon">%</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>

			<hr/>

		<?php endif ?>

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="input-group">
						<label class="input-group-addon" style="text-align: left;">
							<input type="checkbox" name="allowed_fee[]" value="admin_fee" <?= $settings['admin_fee'] > 0 ? "checked='checked'":""; ?> > <?=lang('fee.admin');?>
						</label>
						<input type="text" class="form-control amount_only" name="admin_fee" maxlength="5" value='<?php echo $settings['admin_fee']; ?>' />
						<div class="input-group-addon">%</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="input-group">
						<label class="input-group-addon" style="text-align: left;">
							<input type="checkbox" name="allowed_fee[]" value="bonus_fee" <?= $settings['bonus_fee'] > 0 ? "checked='checked'" : "" ;?> > <?=lang('Bonus Fee');?>
						</label>
						<input type="text" class="form-control amount_only" name="bonus_fee" maxlength="5" value='<?php echo $settings['bonus_fee']; ?>' />
						<div class="input-group-addon">%</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="input-group">
						<label class="input-group-addon" style="text-align: left;">
							<input type="checkbox" name="allowed_fee[]" value="cashback_fee" <?= $settings['cashback_fee'] > 0 ? "checked='checked'" : ""; ?> > <?=lang('fee.cashback');?>
						</label>
						<input type="text" class="form-control amount_only" name="cashback_fee" maxlength="5" value='<?php echo $settings['cashback_fee']; ?>' />
						<div class="input-group-addon">%</div>
					</div>
				</div>
			</div>
		</div>

        <fieldset class="form-group">
            <legend>
                <label><strong><?=lang('fee.transaction');?></strong></label>
                <input id="transaction_fee" name="allowed_fee[]" value="transaction_fee" type="checkbox" data-toggle="toggle" data-size="mini" <?=isset($settings['transaction_fee']) ? "checked='checked'" : '' ?>>
            </legend>
            <br />
            <div class="col-lg-8">
                <div class="form-group">
                    <div class="input-group">
                        <input id="inp-transaction-fee" type="text" class="form-control amount_only" name="transaction_fee" maxlength="3" value='<?=$settings['transaction_fee']; ?>'; />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="checkbox-inline">
                        <input name="split_transaction_fee" id="split_transaction_fee" type="checkbox" data-toggle="toggle" <?=isset($settings['split_transaction_fee']) && $settings['split_transaction_fee'] == true ? "checked='checked'" : '' ?>><?=lang('Split Transaction Fee');?>
                    </label>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <div class="input-group">
                        <label class="input-group-addon" style="text-align: left;"><?=lang('Deposit Fee')?></label>
                        <input id="inp-deposit-fee" disabled type="text" class="form-control amount_only" name="transaction_deposit_fee" maxlength="3" value='<?=isset($settings['transaction_deposit_fee']) ? $settings['transaction_deposit_fee'] : ''; ?>'; />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <div class="input-group">
                        <label class="input-group-addon" style="text-align: left;"><?=lang('Withdrawal Fee')?></label>
                        <input id="inp-withdrawal-fee" disabled type="text" class="form-control amount_only" name="transaction_withdrawal_fee" maxlength="3" value='<?=isset($settings['transaction_withdrawal_fee']) ? $settings['transaction_withdrawal_fee'] : ''; ?>'; />
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
        </fieldset>

        <p class="help-block well"><b><?=lang('Game Platform Fee');?></b> = <b><?=lang('Gross Revenue');?></b> &times; <b><?=lang('Game Platform Fee Rate');?></b></p>
	</fieldset>

<?php }?>
<br>
<div>
    <label><strong><?=lang('aff.ts01');?> <?=lang('aff.ts02');?></strong></label>
    <input id="default_shares_checkbox" type="checkbox" data-toggle="toggle" data-size="mini" value="false" name="enable_commission_by_tier" <?=$enable_tier == false ? 'checked' : '' ?>/>
</div>
<fieldset id="default_shares_sets">
<br>
<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-addon"><?=lang('lang.level');?> 0: <?=lang('lang.master');?></div>
				<input type="text" maxlength="10" class="form-control amount_only" name="level_master" id="total_shares"
					value="<?php echo $settings['level_master']; ?>" required />
				<div class="input-group-addon">%</div>
			</div>
		</div>
		<label><strong><?php echo lang('Sharing Rate by Platform'); ?></strong></label>
	</div><!-- end col-md-12 -->

		<?php foreach ($game as $g) { ?>
			<div class="col-md-6">
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-addon" style=""><?=$g['system_code'];?></div>
						<input class="form-control" type="text" name="platform_shares[<?=$g['id'];?>]" value="<?=isset($settings['platform_shares'][$g['id']]) ? $settings['platform_shares'][$g['id']] : '100'?>" min="0"/>
						<div class="input-group-addon">%</div>
					</div>
				</div>
			</div>
		<?php }?>
</div>
<p class="help-block well"><b><?=lang('Gross Revenue');?></b> = (<b><?=lang('Total Loss');?></b> - <b><?=lang('Total Win');?></b>) &times; <b><?php echo lang('Sharing Rate by Platform'); ?></b></p>
</fieldset>

<?php if( $this->uri->segment(2) == 'viewTermsSetup'){ // for global first?>
<br>
<div>
    <label><strong><?=lang('Commission Computation By Tier');?></strong></label>
    <input id="comm_by_tier_checkbox" type="checkbox" data-toggle="toggle" data-size="mini" name="enable_commission_by_tier" value="true" <?=$enable_tier == true ? 'checked' : '' ?> />
</div>
<fieldset>
    <br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center"><?=lang('Level');?></th>
                <th class="text-center"><?=lang('Active Member');?></th>
                <th colspan="2" class="text-center"><?=lang('Net Revenue');?></th>
                <th class="text-center"><?=lang('Commission Rates');?></th>
                <th colspan="2" class="text-center"><?=lang('Actions');?></th>
            </tr>
        </thead>
        <tbody  class="form-group">
        <!-- ko foreach: dataLists() -->
            <tr>
                <td class="text-center">
                    <label data-bind="text: $index() + 1"></label>
                    <input data-bind="value: level, visible: false"/>
                </td>
                <td><input class="form-control input-sm" data-bind="numeric, value: active_mem, enable: isEditing" /></td>
                <td><input class="form-control input-sm" data-bind="numeric, value: min_net_rev, enable: isEditing" placeholder="Min"/></td>
                <td><input class="form-control input-sm" data-bind="numeric, value: max_net_rev, enable: isEditing" placeholder="Max"/></td>
                <td>
                    <div class="input-group">
                        <input class="form-control input-sm" data-bind="numeric, value: commission_rate, enable: isEditing" />
                        <div class="input-group-addon">%</div>
                    </div>
                </td>
                <td>
                    <a href="#" title="<?=lang('tool.cms03');?>" data-bind="click: $root.tdEdit, visible: showEditBtn" data-toggle="tooltip" class="blue">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                    <a href="#" title="<?=lang('Save');?>" data-bind="click: $root.tdSave, disable: $root.onProcess, visible: showSaveBtn" data-toggle="tooltip" class="blue">
                        <span class="glyphicon glyphicon-floppy-save"></span>
                    </a>
                </td>
                <td>
                    <a href="#" data-bind="click: $root.tdDelete, disable: $root.onProcess, visible: showDeleteBtn" data-toggle="tooltip" title="<?=lang('tool.cms04');?>" class="blue">
                        <span class="glyphicon glyphicon-trash"></span>
                    </a>
                    <a href="#" data-bind="click: $root.tdCancel, visible: showCancelBtn" data-toggle="tooltip" title="<?=lang('Cancel');?>" class="blue">
                        <span class="glyphicon glyphicon-remove"></span>
                    </a>
                </td>
            </tr>
        <!-- /ko -->
            <tr>
                <td colspan="5"></td>
                <td colspan="2">
                    <button data-bind='click: addSetting' class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Add</button>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="panel panel-default">
        <div class="panel-heading"><?=lang('Select Game Platform');?></div>
        <div class="panel-body">
            <?php foreach ($game as $g) { ?>
                <div class="form-group col-xs-3">
                    <label class="control-label">
                        <input type="checkbox" name="tier_provider[]" value="<?=$g['id'];?>" <?= isset($settings['tier_provider']) ? in_array($g['id'], $settings['tier_provider']) ? "checked='checked'" : "" : "";?>>
                        <?=$g['system_code'];?>
                    </label>
                </div>
            <?php }?>
        </div>
    </div>

    <p class="help-block well"><b><?=lang('role.280');?> : If computation by tier is On, individual commission settings for affiliate won't make any effect.</b></p>
</fieldset>
<?php } ?>

<?php if (empty($affiliateId)) { ?>
	<br>
	<label><strong><?=lang('earnings.payout');?></strong></label>
	<fieldset>
		<br>

		<div class="form-group">
			<div class="input-group">
				<div class="input-group-addon"><?=lang('Minimum Affiliate Pay');?></div>
				<input type="text" class="form-control amount_only" name="minimumPayAmount" maxlength="10" value='<?php echo $settings['minimumPayAmount']; ?>'/>
			</div>
		</div>

		<div class="form-group">
			<div class="input-group">
				<div class="input-group-addon">
					<?=lang('earnings.payDay');?>
				</div>
				<select class="form-control input-sm" name="paymentDay" id="paymentDay" <?php // echo $settings['paymentSchedule'] != 'monthly' ? "disabled='disabled'" : ""; ?>>
					<option value=""><?=lang('earnings.selectDay');?></option>
					<?php for ($i = 1; $i <= 31; $i++) {?>
						<option <?php echo $settings['paymentDay'] == $i ? "selected='selected'" : ""; ?>><?php echo $i; ?></option>
					<?php }?>
				</select>
			</div>
		</div>
		<br>
		<div class="form-group">
			<?php echo lang('Transfer Automatically To'); ?>:
			<input type="radio" name="autoTransferToWallet" value="none" <?php echo !$settings['autoTransferToWallet'] && !$settings['autoTransferToLockedWallet'] ? "checked='checked'" : ""; ?> > <?php echo lang('None');?>
			<input type="radio" name="autoTransferToWallet" value="main" <?php echo $settings['autoTransferToWallet'] ? "checked='checked'" : ""; ?> > <?php echo lang('Main Wallet');?>
			<input type="radio" name="autoTransferToWallet" value="locked" <?php echo $settings['autoTransferToLockedWallet'] ? "checked='checked'" : ""; ?> > <?php echo lang('Locked Wallet');?>
		</div>
	</fieldset>
<?php } ?>

<br>
<button type="submit" id="btn_form_operator" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>

