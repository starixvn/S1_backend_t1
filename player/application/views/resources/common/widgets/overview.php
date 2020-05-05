<div class="t1t-widget widget-overview overview">
    <!-- Greeting section -->
    <div class="col-sm-12 col-md-3 profile-section nopadding">
        <div class="row" id="overview-profile">
            <p class="player-name"><?php echo lang("Hi") ?>, <?php echo $this->authentication->getUsername(); ?></p>
            <p class="welcome-title"><?php echo lang("Welcome Back") ?>!</p>
            <div class="login-content">
                <p><span><?php echo lang("Last Login Time") ?> : </span><span id="lastLogintimezoneTxt" class="<?=($this->utils->isEnabledFeature('display_last_login_timezone_in_overview')) ? '' : ' hidden'?>"></span><span id="lastLogintimeTxt"></span></p>
            </div>
        </div>
    </div>
    <!-- VIP section -->
    <?php if (!$this->utils->isEnabledFeature('hidden_vip_icon_LevelName') || !$this->utils->isEnabledFeature('hidden_vip_status_ExpBar') || $this->utils->isEnabledFeature('display_total_bet_amount_in_overview')): ?>
        <div class="col-sm-6 col-md-5 points nopadding">
            <div class="row" id="overview-points">
                <?php if (!$this->utils->isEnabledFeature('hidden_vip_icon_LevelName')): ?>
                    <div class="vip-status">
                        <img class="vip-icon" id="vip-icon" src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/icons/star.png">
                        <span id="vipGroupLvl"></span>
                        <?php if ($this->utils->isEnabledFeature('display_vip_upgrade_schedule_in_player')): ?>
                            <span style="margin-left: 10px !important;" class="upgrade_notice"></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!$this->utils->isEnabledFeature('hidden_vip_status_ExpBar')): ?>
                    <div class="clearfix vip-status-bar">
                        <div class="col-md-4 text-right" id="vipLvlName"></div>
                        <div class="col-md-6">
                            <div class="vip-progress-bar-content">
                                <div class="progress-bar-color" id="vipGroupNextLvlPercentage" style="width:0%">
                                </div>
                            </div>
                            <p class="vip_detail">
                                <?php echo lang("Deposit") ?> (<?php echo isset($currency) ? $currency['symbol'] : null ?>):
                                <span id="currentVipGroupDepAmtTxt"></span>/<span id="vipUpradeDepAmtReqTxt"></span>
                                <?php if (!$this->utils->isEnabledFeature('hidden_vip_betting_Amount_part')): ?>
                                    <br/>
                                    <?php echo lang("Betting") ?> (<?php echo isset($currency) ? $currency['symbol'] : null ?>):
                                    <span id="currentVipGroupBettingAmtTxt"></span>/<span id="vipUpradeBetAmtReqTxt"></span>
                                <?php endif; ?>
                            </p>
                            <p class="vip_max_detail" style="display:none;">
                                <span><?= lang('vip.is.max.member') ?></span>
                            </p>
                        </div>
                        <div id="vipGroupNextLvlPercentageTxt" class="col-md-2"></div>
                    </div>
                <?php endif; ?>
                <?php if ($this->utils->isEnabledFeature('display_total_bet_amount_in_overview')): ?>
                    <div class="total_bet_amount_container">
                        <span class="col-md-3 text-right total_bet_amount_label"><?=lang('Betting today');?></span>
                        <span class="col-md-6 total_bet_amount_result"></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <!-- Validation status section -->
    <?php # Verification status translation
        $isBankInfoAdded = isset($isBankInfoAdded) ? $isBankInfoAdded : false;
        $isPlayerVerified = isset($player_verification) ? $player_verification['verified'] : false;
        $isEmailVerificationNeeded = $this->utils->isEmailVerifyEnabled($player['playerId']);
        $isEmailVerified = isset($isEmailVerified) ? $isEmailVerified : false;
        $isPhoneVerificationNeeded = isset($showSMSField) ? $showSMSField : false;
        $isPhoneVerified = isset($isPhoneVerified) ? $isPhoneVerified : false;
    ?>
    <div class="col-sm-6 <?= (!$this->utils->isEnabledFeature('hidden_vip_icon_LevelName') || !$this->utils->isEnabledFeature('hidden_vip_status_ExpBar')) ? 'col-md-4' : 'col-md-9' ?> vip-rewards nopadding">
        <div class="row" id="overview-rewards">
            <div class="clearfix verifiled">
                <div class="col-md-3">
                    <a id="bank_account_info" href="<?php echo site_url('player_center2/bank_account')?>">
                        <i class="top_icon top_icon_bank <?= $isBankInfoAdded ? 'verified' : '' ?>"></i>
                        <p><?= $isBankInfoAdded ? lang('overview.bankcard.added') : lang('overview.bankcard.not.added') ?></p>
                    </a>
                </div>
                <?php if($this->utils->isEnabledFeature('show_player_upload_realname_verification')): ?>
                    <div class="col-md-3">
                        <a id="security_player_verified" href="<?php echo site_url('player_center2/security')?>">
                            <i class="top_icon top_icon_player <?= $isPlayerVerified ? 'verified' : '' ?>"></i>
                            <p><?= $isPlayerVerified ? lang('cms.verificationStatus.ok') : lang('cms.verificationStatus.none') ?></p>
                        </a>
                    </div>
                <?php endif;?>
                <?php if($isEmailVerificationNeeded): ?>
                    <div class="col-md-3">
                        <a id="security_email_verified" href="<?php echo site_url('player_center2/security')?>">
                            <i class="top_icon top_icon_email <?= $isEmailVerified ? 'verified' : '' ?>"></i>
                            <p><?= $isEmailVerified ? lang('cms.verificationStatus.ok') : lang('cms.verificationStatus.none') ?></p>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ($isPhoneVerificationNeeded): ?>
                    <div class="col-md-3">
                        <a id="security_phone_verified" href="<?php echo site_url('player_center2/security')?>">
                            <i class="top_icon top_icon_phone <?= $isPhoneVerified ? 'verified' : '' ?>"></i>
                            <p><?= $isPhoneVerified ? lang('cms.verificationStatus.ok') : lang('cms.verificationStatus.none') ?></p>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="clearfix user-msg">
                <div class="col-md-6 t-amount">
                    <a id="playercenter_total_balance" href="<?php echo site_url('player_center/dashboard')?>#memberCenter">
                        <p><?=lang("Total Balance")?>
                            <br><span class="total-balance"><?=$this->utils->displayCurrency(isset($total_no_frozen) ? $total_no_frozen : 0)?></span>
                        </p>
                    </a>
                </div>
                <div class="col-md-6 new-msg">
                    <?php # TODO: use onhashchagne event here to avoid page reload ?>
                    <a id="playercenter_message" href="<?php echo site_url('player_center2/messages')?>">
                        <i class="fa fa-envelope-o" aria-hidden="true"></i>
                        <span><?php echo lang("playercenter.messages") ?></span>
                        <span class="new-msg-tip"><?=$this->utils->unreadMessages($player['playerId'])?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>