<?php
	$isBank = ('bank' == $type);
	$isAlipay = ('alipay' == $type);
	$isWechat = ('wechat' == $type);
?>
<?php if (!empty($result)) : ?>
<div id="messageDialog" class="alert alert-<?= $result['success']? 'success':'danger'?> alert-dismissable">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	<?= $result['message'] ?>
</div>
<script>
$(function(){
	$('html, body').animate({
		scrollTop: $("#messageDialog").offset().top
	}, 1000);
});
</script>
<?php endif; ?>

<script type="text/javascript">
var EMPTY_ACCOUNT_NAME_REDIRECT_URL = '<?=(!$this->utils->is_mobile()) ? '/player_center/dashboard/index#accountInformation' : '/player_center/profile'?>';
$(function(){
    var allowed_withdrawal_status_kyc_risk_score = "<?= $allowed_withdrawal_status_kyc_risk_score ?>";

    if(allowed_withdrawal_status_kyc_risk_score != 1) {
        MessageBox.info("<?= lang('not_allowed_kyc_risk_score_message') ?>", null, function(){
            $('#submitBtn').attr({'disabled':true});
        });
    }

    _withdraw.isAutoTransferAll = <?= ($this->utils->isAllowedAutoTransferOnFeature()) ? 1 : 0 ?>;

    <?php if($this->utils->isEnabledFeature('enabled_withdrawal_password') && !$has_withdraw_password): ?>
    MessageBox.info("<?=lang('withdrawal.msg3')?>", '<?=lang('lang.info')?>', function(){
        show_loading();
        window.location.href = '/player_center2/security#withdrawal';
    },
    [
        {
            'text': '<?=lang('lang.close')?>',
            'attr':{
                'class':'btn btn-info',
                'data-dismiss':"modal"
            }
        }
    ]);
    <?php endif ?>
});
</script>


<div id="withdrawal" class="panel">
    <div class="panel-heading">
        <h1 class="hidden-xs hidden-sm"><?= lang('Security') ?></h1>
    </div>
    <div class="withdrawal-customize-content">
        <p><?=lang('deposit_category_customize_content')?></p>
    </div>
    <div id="fm-withdrawal" class="panel-body withdrawal-form fmd-step1">
        <form name="withdrawForm" role="form" action="<?=site_url('player_center2/withdraw/verify')?>" method="POST">
            <?=$double_submit_hidden_field?>
            <input type="hidden" name="type" value="<?=$type?>">
            <?php if($isAlipayEnabled || $isWeChatEnabled) : ?>
                <ul class="nav inner-tab">
                    <li<?= $isBank ? ' class="active"' : ''?>><a href='<?=site_url('player_center2/withdraw/bank')?>'><?= lang('Bank') ?></a></li>
                    <?php if($isAlipayEnabled) : ?>
                        <li<?= $isAlipay ? ' class="active"' : ''?>><a href='<?=site_url('player_center2/withdraw/alipay')?>'><?= lang('Alipay') ?></a></li>
                    <?php endif; ?>
                    <?php if($isWeChatEnabled) : ?>
                        <li<?= $isWechat ? ' class="active"' : ''?>><a href='<?=site_url('player_center2/withdraw/wechat')?>'><?= lang('WeChat') ?></a></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
            <div class="select-player-withdrawal-bank-account">
                <p class="step"><span>1</span> <?= lang('Please select receiving account') ?></p>

                <div class="form-group has-feedback player-withdrawal-bank-account-content">
                    <div class="col-md-6 current-withdrawal-bank">
                        <p><strong><?= lang('Your current receiving account') ?></strong></p>
                        <ul class="bank-list">
                            <li class="active">
                                <a data-toggle="modal" href="#player-banks">
                                    <?php if(empty($playerBankDetails)) :
                                        $bankTypeId = $bankDetailsId = $bankName = $bankAccNum = $bankAccName = $bankAddress = $bankBranch = $mobileNum = $bank_icon_url = '';
                                        ?>
                                    <?php else :
                                        $bankTypeId = $playerDefaultBankDetail['bankTypeId'];
                                        $bankDetailsId = $playerDefaultBankDetail['playerBankDetailsId'];
                                        $bankName = $playerDefaultBankDetail['bankName'];
                                        $bankAccNum = $playerDefaultBankDetail['bankAccountNumber'];
                                        $bankAccName = $playerDefaultBankDetail['bankAccountFullName'];
                                        $bankAddress = $playerDefaultBankDetail['bankAddress'];
                                        $bankBranch = $playerDefaultBankDetail['branch'];
                                        $bankBranch = $bankBranch ?: '';
                                        $mobileNum = $playerDefaultBankDetail['phone'];
                                        $mobileNum = $mobileNum ?: '';
                                        $bank_icon_url = isset($playerDefaultBankDetail['bank_icon_url']) ? $playerDefaultBankDetail['bank_icon_url'] : '';
                                    endif; ?>
                                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                                    <?php if (empty($bank_icon_url)) : ?>
                                        <span id="activeBankName" class="b-icon <?= !empty($bankTypeId) ? 'bank_'.$bankTypeId : '' ?>">
                                            &nbsp;<?= !empty($bankName) ? lang($bankName) : lang('No withdrawal account selected'); ?>
                                        </span>
                                    <?php else : ?>
                                        <span id="activeBankName" class="b-icon-custom">
                                            <img src="<?= $bank_icon_url ?>" />
                                            &nbsp;<?= !empty($bankName) ? lang($bankName) : lang('No withdrawal account selected'); ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        </ul>
                        <div>
                            <div class="dispBankInfo <?= !empty($playerDefaultBankDetail) ? '' : 'hide'; ?>" style="font-size:13px;">
                                <p class='<?=(empty($bankAccNum)) ? 'hide' : ''?> <?=($this->utils->isEnabledFeature("hidden_player_bank_account_number_in_the_withdraw")) ? 'hidden' : ''?>'>
                                    <strong><?= lang('pay.acctnumber') ?>:</strong>
                                    <span id="activeAccNum">
                            <?= $bankAccNum ?>
                        </span>
                                </p>
                                <p class='<?= (empty($bankAccName)) ? 'hide' : ''?>'>
                                    <strong><?= lang('pay.acctname') ?>:</strong>
                                    <span id="activeAccName">
                            <?= $bankAccName ?>
                        </span>
                                </p>
                                <p class='<?= empty($bankBranch) ? 'hide' : ''?>'>
                                    <strong><?= lang('pay.branchname') ?>:</strong>
                                    <span id="activeBranch">
                            <?= $bankBranch ?>
                        </span>
                                </p>
                                <p class='<?= empty($mobileNum) ? 'hide' : ''?>'>
                                    <strong><?= lang('Mobile Number') ?>:</strong>
                                    <span id="activeMobileNum">
                            <?= $mobileNum ?>
                        </span>
                                </p>
                                <input type="hidden" id="activeBankTypeIdField" name="bankTypeId" value='' />
                                <input type="hidden" id="activeBankDetailsIdField" name="bankDetailsId" value='<?= $bankDetailsId ?>' />
                                <input type="hidden" id="activeAccNameField" name="bankAccName" value='' />
                                <input type="hidden" id="activeAccNumField" name="bankAccNum" value='' />
                                <input type="hidden" id="activeBankAddressField" name="bankAddress" value='' />
                                <input type="hidden" id="activeCityField" name="city" value='' />
                                <input type="hidden" id="activeProvinceField" name="province" value='' />
                                <input type="hidden" id="activeBranchField" name="branch" value='' />
                                <input type="hidden" id="activeMobileNumField" name="phone" value='' />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 other-withdrawal-bank">
                        <p><?= lang('Change your current receiving account') ?></p>
                        <p>
                            <a class="change-withdrawal-bank" href="#player-withdrawal-banks" data-toggle="modal">
                                <?= lang('Select other bank account') ?>
                            </a>
                        </p>
                        <!--
                        <?php if (Playerbankdetails::AllowAddBankDetail(Playerbankdetails::WITHDRAWAL_BANK, $playerBankDetails)): ?>
                            <?php if($isBank) : ?>
                                <p>
                                    <a class="mc-btn add-bank-account" href="javascript: void(0);" data-bank-type="withdrawal" data-callback="setupSelectPlayerWithdrawalBank" data-payment-type-flag="<?=Financial_account_setting::PAYMENT_TYPE_FLAG_BANK?>">
                                        <?=lang('Add receiving bank account')?>
                                    </a>
                                </p>
                            <?php elseif($isAlipay || $isWechat) : ?>
                                <p>
                                    <a class="mc-btn add-bank-account" href="javascript: void(0);" data-bank-type="withdrawal" data-callback="setupSelectPlayerWithdrawalBank" data-payment-type-flag="<?=Financial_account_setting::PAYMENT_TYPE_FLAG_EWALLET?>">
                                        <?=lang('Add receiving bank account')?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
                        -->
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <hr />

            <div class="form-group has-feedback">
                <p class="step">
                    <span>2</span>
                    <label class="control-label"><?= lang('Please Input Withdrawal Amount') ?></label>
                </p>
                <?php if (!$this->utils->isEnabledFeature('always_auto_transfer_if_only_one_game')) : ?>
                    <p>【<?= lang('Please transfer your balance back to main wallet') ?>】</p>
                <?php endif; ?>
                <div class="input-group">
                    <input type="number" step="<?=$withdraw_amount_step_limit?>" class="form-control" id="amount" name="amount"
                        <?php if (lang('Please Input Withdrawal Amount For Placeholder')) : ?>
                            placeholder="<?= lang('Please Input Withdrawal Amount For Placeholder') ?>"
                        <?php endif; ?>
                           min="<?=$withdrawSetting['min_withdraw_rule']?>"
                           max="<?=$withdrawSetting['max_withdraw_per_transaction']?>"
                           data-min-error="<?=sprintf(lang('formvalidation.greater_than'), lang('Withdrawal Amount'), $this->utils->formatCurrency($withdrawSetting['min_withdraw_rule'], $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw')))?>"
                           data-max-error="<?=sprintf(lang('formvalidation.less_than'), lang('Withdrawal Amount'), $this->utils->formatCurrency($withdrawSetting['max_withdraw_per_transaction'], $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw')))?>"
                           data-required-error="<?=lang('Fields with (*) are required.')?>"
                           data-error="<?=lang('text.error')?>"
                           style="width:260px" required/>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div>
                <div class="help-block with-errors"></div>
                <div class="helper-content">
                    <p class="deposit-limit"><?=lang('Withdraw.Amount.Note')?></p>

                    <?php if(stristr(lang('Min withdraw per transaction'),'%s') && stristr(lang('Max withdraw per transaction'),'%s') && stristr(lang('Daily Limit'),'%s')) :?>
                        <p class="deposit-limit min">
                            <?=sprintf(lang('Min withdraw per transaction'), '<span>'.$this->utils->formatCurrency($withdrawSetting['min_withdraw_rule'], $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw')).'</span>'); ?>
                        </p>
                        <?php if($showMaxWithdrawalPerTransaction):?>
                            <p class="deposit-limit max">
                                <?=sprintf(lang('Max withdraw per transaction'), '<span>'.$this->utils->formatCurrency($withdrawSetting['max_withdraw_per_transaction'], $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw')).'</span>'); ?>
                            </p>
                        <?php endif;?>
                        <?php if($showDailyMaxWithdrawalAmount):?>
                            <?php if(lang('Daily Limit')) : ?>
                                <p class="deposit-limit">
                                    <?=sprintf(lang('Daily Limit'), '<span>'.$this->utils->formatCurrency($withdrawSetting['dailyMaxWithdrawalAmount'], $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw')).'</span>'); ?>
                                </p>
                            <?php endif; ?>
                        <?php endif;?>
                    <?php else: ?>
                        <p class="deposit-limit min">
                            <?=lang('Min withdraw per transaction')?> : <span><?=$this->utils->formatCurrency($withdrawSetting['min_withdraw_rule'], $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw'))?></span>
                        </p>
                        <?php if($showMaxWithdrawalPerTransaction):?>
                            <p class="deposit-limit max">
                                <?=lang('Max withdraw per transaction')?> : <span><?=$this->utils->formatCurrency($withdrawSetting['max_withdraw_per_transaction'], $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw'))?></span>
                            </p>
                        <?php endif;?>
                        <?php if($showDailyMaxWithdrawalAmount):?>
                            <?php if(lang('Daily Limit')) : ?>
                                <p class="deposit-limit"><?= lang('Daily Limit') ?>: <span><?=$this->utils->formatCurrency($withdrawSetting['dailyMaxWithdrawalAmount'], $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw'))?> </span></p>
                            <?php endif; ?>
                        <?php endif;?>
                    <?php endif ?>
                    <?php if(isset($cryptocurrencies)): ?>
                        <p class="deposit-limit"><b><?=lang('Cryptocurrencies Exchange Rate')?></b></p>
                        <?php foreach ($cryptocurrencies as $cryptocurrency => $value) : ?>
                            <p class="deposit-limit"><?=lang($cryptocurrency)?>: <?=$value?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if ($this->utils->isEnabledFeature('enable_withdrawal_amount_note')) : ?>
                    <div class="helper-content text-danger font-weight-bold">
                        <p><?=lang('collection_withdrawal_amount')?></p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($this->utils->getConfig('withdraw_verification') == 'withdrawal_password' && $this->utils->isEnabledFeature('enabled_withdrawal_password')) : ?>
                <div class="form-group">
                    <p class="step"><span>3</span><?= lang('Please Input Withdrawal Password') ?></p>
                    <input type="password" class="form-control" id="password" name="withdrawal_password" placeholder="<?= lang('Type your password') ?>">
                    <input type="hidden" id="hasWithdrawPass" value="1">
                </div>
            <?php endif ?>

            <div class="form-group">
                <input class="btn form-control" id="submitBtn" value="<?= lang('Withdraw') ?>" type="submit" />
                <p class="step" ><?= lang('Withdraw pending ask customer service') ?></p>
            </div>
        </form>
    </div>
</div>

<!-- Withdrawal Player Bank List Modal -->
<div class="modal fade" id="player-withdrawal-banks" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title f24" id="myModalLabel"><?= lang('Select Withdrawal Bank Account') ?></h4>
			</div>

			<div class="modal-body withdrawal-modal-body">
				<div class="clearfix">
					<div class="col-md-12 fmd-step1">
						<?php if(!empty($playerBankDetails)) : ?>
							<ul class="bank-list bank-list-main" id="currentBankList">
							<?php foreach($playerBankDetails as $bank) : ?>
								<li data-id="<?= $bank['playerBankDetailsId'] ?>"
									data-bank-type-id="<?= $bank['bankTypeId'] ?>"
									data-bank-name="<?= lang($bank['bankName']) ?>"
                                    data-bank-code="<?= $bank['bank_code'] ?>"
                                    data-is-crypto="<?= $bank['is_crypto'] ?>"
									data-branch="<?= lang($bank['branch']) ?>"
									data-province="<?= lang($bank['province']) ?>"
									data-city="<?= lang($bank['city']) ?>"
									data-acc-num="<?= $bank['bankAccountNumber'] ?>"
									data-acc-name="<?= lang($bank['bankAccountFullName']) ?>"
									data-mobile-num="<?= lang($bank['phone']) ?>"
                                    data-default="<?=$bank['isDefault']?>"
								>
									<a href="#<?= lang($bank['bankTypeId']) ?>" data-toggle="tab" title="<?= $bank['displayName'] ?>">
										<i class="fa fa-check-circle" aria-hidden="true"></i>
										<?php if (empty($bank['bank_icon_url'])) : ?>
											<span class="b-icon bank_<?= lang($bank['bankTypeId']) ?>">
												<?= $bank['displayName'] ?>
											</span>
										<?php else : ?>
											<span class="b-icon-custom">
												<img src="<?= $bank['bank_icon_url'] ?>" />
												<?= $bank['displayName'] ?>
											</span>
										<?php endif; ?>
									</a>
								</li>
							<?php endforeach; ?>
							</ul>
							<div class="row">
								<div class="text-center col-md-6 top-buffer">
									<button type="button" class="btn btn-primary" id="saveChosenBank"><?= lang("lang.save") ?></button>
								</div>
							</div>
						<?php else : ?>
							<?= lang('No withdrawal bank account available') ?>.
						<?php endif ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include __DIR__ . '/../../bank_account/content/modal.php';?>