<?php
$player_validator = $this->load->config->item('player_validator');
?>
<!-- Deposit submit modal -->
<?php if(($deposit_process_mode != DEPOSIT_PROCESS_MODE1) || (!$this->config->item('enable_deposit_mode_1_two_steps_flow'))) { ?>
<div class="modal fade submit-deposit-modal" id="submit-done-deposit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?=lang('collection.heading.1')?></h4>
            </div>
            <div class="modal-body">
                <div class="row deposit-payment-account-detail">
                    <div class="col col-md-8">
                        <h4><?=lang('Check your deposit')?>:</h4>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right text-danger"><strong><?=lang('collection.label.1')?>:</strong></span>
                            <span class="col col-xs-5 col-md-5 text-left text-danger" id="modal_order_id"></span>
                            <span class="col col-xs-3 col-md-3">
                                <button type="button" class="btn btn-copy"
                                    data-clipboard-action="copy"
                                    data-clipboard-target="#modal_order_id"
                                    title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                            </span>
                        </div>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Account Name')?>:</strong></span>
                            <span class="col col-xs-5 col-md-5 text-left" id="modal_account_name"></span>
                            <span class="col col-xs-3 col-md-3">
                                <button type="button" class="btn btn-copy"
                                    data-clipboard-action="copy"
                                    data-clipboard-target="#modal_account_name"
                                    title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                            </span>
                        </div>
                        <div class="row account-number">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Account Number')?>:</strong></span>
                            <span class="col col-xs-5 col-md-5 text-left" id="modal_account_number"></span>
                            <span class="col col-xs-3 col-md-3">
                                <button type="button" class="btn btn-copy"
                                    data-clipboard-action="copy"
                                    data-clipboard-target="#modal_account_number"
                                    title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                            </span>
                        </div>
                        <div class="row bank-branch-name">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Bank Branch Name')?>:</strong></span>
                            <span class="col col-xs-5 col-md-5 text-left" id="modal_bank_branch_name"></span>
                            <span class="col col-xs-3 col-md-3">
                                <button type="button" class="btn btn-copy"
                                    data-clipboard-action="copy"
                                    data-clipboard-target="#modal_bank_branch_name"
                                    title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                            </span>
                        </div>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Deposit Amount')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_deposit_amount"></span>
                        </div>
                        <div class="row text-danger">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Min deposit per transaction')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_min_deposit_trans"></span>
                        </div>
                        <div class="row text-danger">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Max deposit per transaction')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_max_deposit_trans"></span>
                        </div>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('collection.label.6')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_requested_on"></span>
                        </div>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('collection.label.7')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_expires_on"></span>
                        </div>
                        <?php if ($this->utils->isEnabledFeature('enable_deposit_datetime')) :?>
                            <div class="row">
                                <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Deposit Date time')?>:</strong></span>
                                <span class="col col-xs-8 col-md-8 text-left" id="modal_deposit_date_time"></span>
                            </div>
                            <div class="row">
                                <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Mode of Deposit')?>:</strong></span>
                                <span class="col col-xs-8 col-md-8 text-left" id="modal_mode_of_deposit"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col col-md-4 account-image">
                        <span id="modalAccountImage"><img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" /></span>
                    </div>
                </div>

                <div class="helper-content text-danger font-weight-bold">
                    <p><?=lang('collection.text.1')?></p>
                </div>
            </div>
            <div class="modal-footer text-left">
                <?php if(!$this->utils->isEnabledFeature('hidden_print_deposit_order_button')): ?>
                <button type="button" class="btn btn-default hidden-sm hidden-xs" id="printThisPageBtn" onclick="printDepositOrder();"><?=lang('action.print_current_page')?></button>
                <?php endif ?>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="modalDepositBtnClose"><?=lang('Close')?></button>
            </div>
        </div>
    </div>
</div>

<!-- Deposit Print layout -->
<div id="printDoneDeposit" class="hidden">
    <h2 class="modal-title" id="printLabel"><?=lang('collection.heading.1')?></h2>
    <hr />

    <div class="modal-body">
        <h4 ><?=lang('Check your deposit')?>:</h4>
        <table>
            <tbody>
            <tr>
                <td align="right"><strong><?=lang('collection.label.1')?>:</strong></td>
                <td><span id="print_modal_order_id"></span></td>
            </tr>
            <tr>
                <td align="right"><strong><?=lang('Account Name')?>:</strong></td>
                <td><span id="print_modal_account_name"></span></td>
            </tr>
            <tr>
                <td align="right"><strong><?=lang('Account Number')?>:</strong></td>
                <td><span id="print_modal_account_number"></td>
            </tr>
            <tr>
                <td align="right"><strong><?=lang('Deposit Amount')?>:</strong></td>
                <td><span id="print_modal_deposit_amount"></span></td>
            </tr>
            <tr>
                <td align="right"><strong><?=lang('collection.label.6')?>:</strong></td>
                <td><span id="print_modal_requested_on"></span></td>
            </tr>
            <tr>
                <td align="right"><strong><?=lang('collection.label.7')?>:</strong></td>
                <td><span id="print_modal_expires_on"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>

<!-- Deposit Confirm Modal -->
<div class="modal fade submit-deposit-modal" id="deposit-confirmation" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?=lang('Deposit')?></h4>
            </div>
            <div class="modal-body">
                <p class="text-center">
                    <span id="modal-deposit-confirmation-span"></span>
                </p>
            </div>
            <div class="modal-footer text-left">
                <button type="button" class="btn btn-default submit-btn" id="modalConfirmOk" data-dismiss="modal"><?=lang('lang.close')?></button>
            </div>
        </div>
    </div>
</div>

<!-- Deposit Modal-> auto payment -->
<div class="modal fade submit-deposit-modal" id="auto-payment-confirmation" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?=lang('Deposit Confirmation')?></h4>
            </div>
            <div class="modal-body">
                <p class="text-center">
                    <span id="modal-auto-deposit-confirmation-span"><?=lang("cashier.139")?></span>
                </p>
            </div>
            <div class="modal-footer text-left">
                <button type="button" class="btn btn-default submit-btn" onclick="return PlayerCashier.goSubmit()" data-dismiss="modal"><?=lang('cashier.140')?></button>
                <button type="button" class="btn btn-default submit-btn" data-dismiss="modal"><?=lang('cashier.141')?></button>
                <a href="<?=$this->utils->getLiveChatLink()?>" target="_blank" class="btn btn-default submit-btn"><?=lang('role.97')?></a>
            </div>
        </div>
    </div>
</div>

<!-- Deposit Player Bank List Modal -->
<div class="modal fade" id="player-deposit-banks" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title f24" id="myModalLabel"><?=lang('Select other deposit bank account')?></h4>
			</div>

			<div class="modal-body deposit-modal-body">
				<div class="clearfix">
					<div class="col-md-12 fmd-step1">
						<?php if(!empty($player_bank_accounts)) :?>
							<ul class="bank-list bank-list-main" id="currentBankList">
                            <?php foreach($player_bank_accounts as $player_bank_account) :?>
                                <?php if($player_bank_account['payment_type_flag'] == $payment_type_flag): ?>
                                    <li data-id="<?=$player_bank_account['playerBankDetailsId']?>"
                                        data-bank-type-id="<?=$player_bank_account['bankTypeId']?>"
                                        data-bank-name="<?=lang($player_bank_account['bankName'])?>"
                                        data-branch="<?=lang($player_bank_account['branch'])?>"
                                        data-province="<?=lang($player_bank_account['province'])?>"
                                        data-city="<?=lang($player_bank_account['city'])?>"
                                        data-acc-num="<?=Playerbankdetails::getDisplayAccNum($player_bank_account['bankAccountNumber'])?>"
                                        data-acc-name="<?=lang($player_bank_account['bankAccountFullName'])?>"
                                        data-mobile-num="<?=lang($player_bank_account['phone'])?>"
                                    class="<?=($player_bank_account['isDefault']) ? 'active' : ''?>">
                                        <a href="#<?=lang($player_bank_account['bankTypeId'])?>" data-toggle="tab" title="<?=$player_bank_account['displayName']?>">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                            <?=Banktype::renderBankEntry($player_bank_account['bankTypeId'], $player_bank_account['displayName'], $player_bank_account['bankIcon']);?>
                                        </a>
                                    </li>
                                <?php endif;?>
							<?php endforeach;?>
							</ul>
						<?php else :?>
							<?=lang('No deposit bank account available')?>.
						<?php endif?>
					</div>
				</div>
			</div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-primary" id="setup-selected-playet-deposit-bank" onclick="setupSelectPlayerDepositBank();"><?=lang("lang.save")?></button>
            </div>
		</div>
	</div>
</div>

<!-- Show Deposit Verify Window Modal -->
<div class="modal fade security-modal" id="deposit-verify-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?=lang('Security Verify')?><small> - <?=lang('Withdrawal Password')?></small></h4>
            </div>
            <div class="modal-body">
                <form autocomplete="off" onsubmit="return false;">
                    <div id="messages" class="hidden">
                        <span message="please_input_withdrawal_password"><?=lang('Please Input Withdrawal Password')?></span>
                    </div>
                    <div class="form-group has-feedback">
                        <label><?=lang('Please Input Withdrawal Password')?>:</label>
                        <div class="row">
                            <input type="password" class="form-control" id="deposit_verify_withdrawal_password"
                                   required="required"
                                   minlength="<?=$player_validator['password']['min']?>"
                                   maxlength="<?=$player_validator['password']['max']?>"
                                   data-required-error="<?=lang('Please Input Withdrawal Password')?>"
                                   data-min-error="<?=sprintf(lang('gen.error.between'), lang('Please Input Withdrawal Password'), $player_validator['password']['min'], $player_validator['password']['max'])?>">
                        </div>
                        <div class="help-block with-errors"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn form-control mc-btn btn-primary submit-btn"><?=lang('lang.submit')?></button>
            </div>
        </div>
    </div>
</div>

<!-- Deposit promotion detail -->
<div class="modal fade promo-modal in" id="deposit-promo-detail-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <img id="promoItemPreviewImg" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D">
                <h4 class="modal-title"><span></span></h4>
            </div>
            <div class="modal-body">
                <p><h4>Promo Type:</h4></p>
                <p id="promoCmsPromoType"></p>

                <p><h4>Description:</h4></p>
                <p id="promoCmsPromoDetails"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default mc-btn submit-btn" data-dismiss="modal" style=""><?=lang('lang.close')?></button>
            </div>
        </div>
    </div>
</div>