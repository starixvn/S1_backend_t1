<?php include VIEWPATH . '/player_management/user_information/modals.php'; ?>
<?php
    $promoRulesConfig = $this->utils->getConfig('promotion_rules');
    $enabled_show_withdraw_condition_detail_betting = $promoRulesConfig['enabled_show_withdraw_condition_detail_betting'];
?>
<?=$this->load->view("resources/third_party/bootstrap-tagsinput")?>

<script type="text/javascript">
    var gameSystemMap = <?=$this->utils->encodeJson($this->utils->getGameSystemMap()); ?>;

    var playerId = "<?=$player['playerId']?>";
    var baseUrl = "<?=base_url();?>";
    var message = {
        ublTitle    :  "<?=lang('role.25');?>",
        ublConfirm  :  "<?=lang('player.confirm.unblock');?>",
        ublContent  :  "<?=lang('tool.pm09');?>",
        blContent   :  "<?=lang('tool.pm08');?>",
        no          :  "<?=lang('No');?>",
        yes         :  "<?=lang('Yes');?>"
    };

    function changeUserInfoTab(tab) {
        window.location.reload();
    }

    function refresh_fin_info() {
        changeUserInfoTab(5);
        $('#simpleModal').modal('hide');
    }
</script>

<style type="text/css">
    .nav-tabs li a {font-size:13px;}
    .select-xs {
        height: 16px;
        line-height: 16px;
    }

    a[disabled="disabled"] {
        pointer-events: none;
    }
</style>
<style type="text/css">
    .grid-wrapper {
        --field-title-bg-color: #f5f5f5;
        --wrapper-border-style:1px solid #ddd;
    }
    .grid-wrapper {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        border: var(--wrapper-border-style);
        grid-gap: 0px;
    }
    .grid-field-wrapper {
        display: grid;
        grid-template-columns: 1fr 2fr;
        border-bottom: var(--wrapper-border-style);
    }
    .grid-field-wrapper:nth-last-child(-n+2) {
        border-bottom: none;
    }
    .grid-field-wrapper:nth-child(odd) {
        border-right: var(--wrapper-border-style);
    }
    .sub-field {
        padding: 8px;
        font-size: 12px;
    }
    .grid-field-title {
        font-weight: bold;
        background-color: var(--field-title-bg-color);
        border-right: var(--wrapper-border-style);
    }
</style>

<div class="col-md-12">
    <!-- Quick Link -->
    <div class="row" style="margin:10px;">
        <div class="col-md-12" style="padding:10px;">
            <div class="row">
                <fieldset style="padding:10px;">
                    <legend><h4><b><?=lang('player.ui01')?></b></h4></legend>
                    <div class="col-md-1 col-md-offset-0">
                        <div class="checkbox checkbox-info checkbox-circle">
                            <input id="checkall" value="checkall" class="checkall" type="checkbox" onclick="checkAllPlayerInfo(this.value)" checked>
                            <label for="checkbox8">
                                <?=lang('player.ui02')?>
                            </label>
                        </div>
                    </div>
                    <div class="checkbox checkbox-info checkbox-circle">

                    </div>
                    <div class="col-md-3 col-md-offset-0">
                        <div class="checkbox checkbox-info checkbox-circle">
                            <input id="signup" value="signup" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                            <label for="checkbox8">
                                <a href="#signup_form"><?=lang('player.ui03')?></a>
                            </label>
                            <br/>
                            <input id="personal" value="personal" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                            <label for="checkbox8">
                                <a href="#personal_form"><?=lang('player.ui04')?></a>
                            </label>
                            <br/>
                            <?php if ($this->permissions->checkPermissions('player_basic_info') && (
                                    $this->permissions->checkPermissions('player_contact_information_email') ||
                                    $this->permissions->checkPermissions('player_contact_information_contact_number') ||
                                    $this->permissions->checkPermissions('player_contact_information_im_accounts')
                                )) {?>
                                <input id="contact" value="contact" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                <label for="checkbox8">
                                    <a href="#contact_form"><?=lang('reg.74')?></a>
                                </label>
                            <?php }
                            ?>
                            <br />
                            <?php if ($this->permissions->checkPermissions('responsible_gaming_info') && $this->utils->isEnabledFeature('responsible_gaming')) : ?>
                                <input id="resp_gaming_info" value="resp_gaming_info" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                <label for="checkbox8">
                                    <a href="#resp_gaming_info_form"><?=lang('Responsible Gaming')?></a>
                                </label>
                                <br/>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-3 col-md-offset-0">
                        <div class="checkbox checkbox-info checkbox-circle">
                            <input id="account" value="account" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                            <label for="checkbox8">
                                <a href="#account_form"><?=lang('player.ui05')?></a>
                            </label>
                            <br/>
                            <input id="game" value="game" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                            <label for="checkbox8">
                                <a href="#game_form"><?=lang('player.ui06')?></a>
                            </label>
                            <br/>
                            <input id="withdraw_condition" value="withdraw_condition" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                            <label for="checkbox8">
                                <a href="#withdraw_condition_form"><?=lang('pay.withdrawalCondition')?></a>
                            </label>
                            <br/>
                            <?php if($this->utils->isEnabledFeature('enabled_transfer_condition')):?>
                                <input id="transfer_condition" value="transfer_condition" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                <label for="checkbox8">
                                    <a href="#transfer_condition_form"><?=lang('cms.transCon')?></a>
                                </label>
                            <?php endif;?>
                        </div>
                    </div>

                    <div class="col-md-3 col-md-offset-0">
                        <div class="checkbox checkbox-info checkbox-circle">
                            <input id="bank" value="bank" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                            <label for="checkbox8">
                                <a href="#bank_form"><?=lang('player.ui07')?></a>
                            </label>
                            <br/>

                            <input id="players" value="players" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                            <label for="checkbox8">
                                <a href="#players_form"><?=lang('player.ui08')?></a>
                            </label>
                            <br/>

                            <?php if ($this->permissions->checkPermissions('player_communication_preference') && $this->utils->isEnabledFeature('enable_communication_preferences')) : ?>
                                <input id="communication_preference" value="communication_preference" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                <label for="checkbox8">
                                    <a href="#communication_preferences_form"><?=lang('Communication Preference')?></a>
                                </label>
                                <br/>
                            <?php endif; ?>
                        </div>
                    </div>

                    <input type="hidden" value="<?=$player['playerId']?>" id="player_id">
                    <input type="hidden" value="<?=$player['username']?>" id="username">
                </fieldset>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row" style="margin:6px;">
        <fieldset style="padding:6px;">
            <legend><h4><b><?=lang('player.ap01');?></b></h4></legend>
            <div class='col-md-12'>
                <?php if ($this->permissions->checkPermissions('lock_player')) {?>
                    <?php $playerStatus =  $this->utils->getPlayerStatus($player['playerId']); ?>
                    <?php if ($playerStatus == 0) {?>
                        <a href="javascript:void(0);"
                            data-toggle="tooltip"
                            title=""
                            data-original-title="<?=lang('role.25')?>" class="btn btn-sm btn-danger navbar-btn block blockAction"
                            onclick="blockPlayer(<?=$player['playerId']?>)"
                        >
                            <i class="fa fa-lock"></i>
                            <?=lang('tool.pm08')?>
                        </a>

                        <div class="modal fade in" id="block_player" tabindex="-1" role="dialog" aria-labelledby="label_block_player">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="label_block_player"></h4>
                                    </div>
                                    <div class="modal-body"></div>
                                    <div class="modal-footer"></div>
                                </div>
                            </div>
                        </div>
                    <?php } elseif ($playerStatus == Player_model::BLOCK_STATUS) {?>
                        <a href="/player_management/unblockPlayer/<?=$player['playerId']?>"
                            class="btn btn-success navbar-btn btn-sm unblock blockAction"
                            onclick="return confirm('<?=lang("player.confirm.unblock")?>');"
                        >
                            <i class="fa fa-unlock-alt"></i>
                            <?=lang('tool.pm09')?>
                        </a>
                    <?php } elseif ($playerStatus == Player_model::SUSPENDED_STATUS) { ?>
                        <a href="/player_management/unblockPlayer/<?=$player['playerId']?>"
                            class="btn btn-success navbar-btn btn-sm unblock blockAction"
                            onclick="return confirm('<?=lang("player.confirm.unblock")?>');"
                        >
                            <i class="fa fa-unlock-alt"></i>
                            <?=lang('tool.unsuspended')?>
                        </a>
                    <?php } elseif ($playerStatus == Player_model::STATUS_BLOCKED_FAILED_LOGIN_ATTEMPT) { ?>
                        <a href="/player_management/unblockPlayer/<?=$player['playerId']?>"
                            class="btn btn-success navbar-btn btn-sm unblock blockAction"
                            onclick="return confirm('<?=lang("Are you sure you want to reset the login attempt?")?>');"
                        >
                            <i class="fa fa-unlock-alt"></i>
                            <?=lang('Reset Login Attempt')?>
                        </a>
                    <?php } ?>
                <?php }?>

                <a href="/player_management/resetbalance/<?=$player['playerId']?>" class="btn btn-primary navbar-btn btn-sm" onclick="return confirm('<?=lang("confirm.refresh.balance")?>');">
                    <i class="fa fa-refresh"></i>
                    <?=lang('lang.refreshbalance')?>
                </a>
                <?php if ($this->permissions->checkPermissions('payment_player_adjustbalance')) {?>
                    <a href="<?=site_url('payment_management/adjust_balance') . '/' . $player['playerId']?>" class="btn btn-primary navbar-btn btn-sm">
                        <i class="icon-equalizer"></i>
                        <?=lang('pay.05')?>
                    </a>
                <?php }
                ?>
                <?php if ($this->utils->loadExternalSystemLibObject(PT_API)) {?>
                    <a href="<?=site_url('player_management/revertBrokenGame') . '/' . $player['playerId']?>" class="btn btn-primary navbar-btn btn-sm">
                        <i class="fa fa-undo"></i>
                        <?=lang('member.action.revertBrokenGame')?>
                    </a>
                <?php }
                ?>
                <?php if ($this->permissions->checkPermissions('send_message_sms')) {?>
                    <button onclick="sbe_messages_send_message('<?=$player['playerId']?>', '<?=$player['username']?>')" class="btn btn-primary btn-sm">
                        <i class="icon-bubble2"></i> <?=lang('lang.send.message')?>
                    </button>
                    <a id="show-sms-box" onclick="showSMSbox()" href="javascript:void(0)" class="btn btn-primary btn-sm">
                        <i class="fa fa-mobile"></i> <span class="hidden-xs"><?=lang('Send SMS')?></span>
                    </a>
                <?php }?>
                <a href="javascript:void(0)" class="btn btn-primary btn-sm show_bigwallet_details" data-playerid="<?php echo $player['playerId']; ?>">
                    <i class="fa -money"></i>
                    <?php echo lang('Wallet Details'); ?>
                </a>

                <?php if ($this->permissions->checkPermissions('new_deposit')) {?>
                    <a href="<?=site_url('payment_management/newDeposit')?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> <span class="hidden-xs"><?=lang('lang.newDeposit')?></span>
                    </a>
                <?php }?>
                <?php if ($this->permissions->checkPermissions('new_withdrawal')) {?>
                    <a href="<?=site_url('payment_management/newWithdrawal')?>" class="btn btn-primary btn-sm"><i class="fa fa-minus"></i><span class="hidden-xs"><?=lang('lang.newWithdrawal')?></span></a>
                <?php }?>

                <?php if ($this->permissions->checkPermissions('manually_add_bonus')) {?>
                    <a href="/marketing_management/manually_add_bonus/<?=$player['playerId']?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-star"></i> <span class="hidden-xs"> <?=lang('transaction.transaction.type.' . Transactions::ADD_BONUS)?></span>
                    </a>
                <?php }?>

                <?php if ($this->permissions->checkPermissions('player_notes')) {?>
                    <button type="button" class="btn btn-primary btn-sm" onclick="player_notes(<?=$player['playerId']?>)">
                        <i class="fa fa-sticky-note-o"></i> <?=lang('Player Remarks')?>
                    </button>

                    <div class="modal fade in" id="player_notes" tabindex="-1" role="dialog" aria-labelledby="label_player_notes">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="label_player_notes"></h4>
                                </div>
                                <div class="modal-body"></div>
                            </div>
                        </div>
                    </div> <!--  modal for level name setting }}}4 -->
                <?php }?>

                <?php if ($this->utils->isEnabledFeature('create_ag_demo') && !empty($isAGGameAccountDemoAccount)) {?>
                    <a href="/player_management/createGameProviderAccount/<?=$player['playerId'] . '/' . AG_API . '/true'?>" data-toggle="tooltip" data-placement="top" class='btn btn-sm btn-primary' title="<?=lang('It will create a new ag demo account to replace old account')?>" class="pull-right" onclick="return confirm('<?=sprintf(lang('Are you sure you want to switch player to demo account?'), 'AG API')?>')"><i class="fa fa-refresh"></i> <?=lang('Change AG to Demo Account')?></a>
                <?php }?>

                <?php if ($this->utils->isEnabledFeature('create_agin_demo') && !empty($isAGINGameAccountDemoAccount)) {?>
                    <a href="/player_management/createGameProviderAccount/<?=$player['playerId'] . '/' . AGIN_API . '/true'?>" data-toggle="tooltip" data-placement="top" class='btn btn-sm btn-primary' title="<?=lang('It will create a new agin demo account to replace old account')?>" class="pull-right" onclick="return confirm('<?=sprintf(lang('Are you sure you want to switch player to demo account?'), 'AGIN API')?>')"><i class="fa fa-refresh"></i> <?=lang('Change AGIN to Demo Account')?></a>
                <?php }?>

                <?php
                if ($this->permissions->checkPermissions('disable_cashback')) {
                    if ($player['disabled_cashback'] == 1) {?>
                        <a href="<?=site_url('player_management/set_cashback_status/' . $player['playerId'] . '/enable')?>" class="btn btn-success btn-sm">
                            <i class="fa fa-thumbs-up"></i> <?=lang('Enable Cashback')?>
                        </a>
                    <?php } else {?>
                        <a href="<?=site_url('player_management/set_cashback_status/' . $player['playerId'] . '/disable')?>" class="btn btn-danger btn-sm">
                            <i class="fa fa-thumbs-down"></i> <?=lang('Disable Cashback')?>
                        </a>
                    <?php
                    }
                }
                ?>

                <?php
                if ($this->permissions->checkPermissions('disable_promotion')) {
                    if ($player['disabled_promotion'] == 1) {?>
                        <a href="<?=site_url('player_management/set_promotion_status/' . $player['playerId'] . '/enable')?>" class="btn btn-success btn-sm">
                            <i class="fa fa-thumbs-up"></i> <?=lang('Enable Promotion')?>
                        </a>
                    <?php } else {?>
                        <a href="<?=site_url('player_management/set_promotion_status/' . $player['playerId'] . '/disable')?>" class="btn btn-danger btn-sm">
                            <i class="fa fa-thumbs-down"></i> <?=lang('Disable Promotion')?>
                        </a>
                    <?php
                    }
                }?>

                <?php
                if ($this->permissions->checkPermissions('disable_player_withdrawal')) {
                    if ($player['enabled_withdrawal'] == 1) {?>
                        <a href="<?=site_url('player_management/set_withdrawal_status/' . $player['playerId'] . '/disable')?>" class="btn btn-danger btn-sm">
                            <i class="fa fa-thumbs-down"></i> <?=lang('Disable Player Withdrawal')?>
                        </a>
                    <?php } else {?>
                        <a href="<?=site_url('player_management/set_withdrawal_status/' . $player['playerId'] . '/enable')?>" class="btn btn-success btn-sm">
                            <i class="fa fa-thumbs-up"></i> <?=lang('Enable Player Withdrawal')?>
                        </a>
                    <?php
                    }
                }?>

                <?php if ($this->permissions->checkPermissions('manual_trigger_check_vip_conditions')) {?>
                    <a href="<?=site_url('player_management/manuallyUpgradeLevel/' . $player['playerId'])?>"  class="btn btn-primary btn-sm">
                        <i class="fa fa-caret-square-o-up"></i> <span class="hidden-xs"><?=lang('Manually Upgrade Level')?></span>
                    </a>
                    <a href="<?=site_url('player_management/manuallyDowngradeLevel/' . $player['playerId'])?>"  class="btn btn-primary btn-sm">
                        <i class="fa fa-caret-square-o-up"></i> <span class="hidden-xs"><?=lang('Manually Downgrade Level')?></span>
                    </a>
                <?php }?>

                <?php if ($this->permissions->checkPermissions('telesales_call')) {?>
                    <a id="make_tele_call" href="javascript:void(0)" target="_blank" class="btn btn-primary btn-sm" onclick='makeTeleCall("<?=site_url('/api/call_player_tele/' . $player['playerId'])?>")'>
                        <i class="fa fa-phone"></i> <span class="hidden-xs"><?=lang('Call Phone')?></span>
                    </a>
                <?php }?>
                <?php if ($this->utils->isEnabledFeature('show_upload_documents') && $this->permissions->checkPermissions('kyc_attached_documents')) {?>
                    <a href="javascript:void(0)" onclick="modal('/player_management/player_attach_document/<?=$player['playerId']?>','Attached Document')" class="btn btn-primary btn-sm">
                        <i class="fa fa-search"></i> <span class="hidden-xs"><?=lang('attached_file')?></span>
                    </a>
                <?php }?>
                <?php if ($this->utils->isEnabledFeature('show_c6_authentication') && $this->utils->isEnabledFeature('enable_c6_acuris_api_authentication') && $this->permissions->checkPermissions('show_c6_authentication')) {?>
                    <a href="javascript:void(0)" onclick="modal('/player_management/player_c6_authentication/<?=$player['playerId']?>','<?=lang('C6 Authentication')?>')" class="btn btn-primary btn-sm">
                        <i class="fa fa-search"></i> <span class="hidden-xs"><?=lang('C6 Authentication')?></span>
                    </a>
                <?php }?>
                <?php if ($this->utils->isEnabledFeature('show_pep_authentication') && $this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') && $this->permissions->checkPermissions('show_pep_authentication')) {?>
                    <a href="javascript:void(0)" onclick="modal('/player_management/player_pep_authentication/<?=$player['playerId']?>','<?=lang('PEP Authentication')?>')" class="btn btn-primary btn-sm">
                        <i class="fa fa-search"></i> <span class="hidden-xs"><?=lang('PEP Authentication')?></span>
                    </a>
                <?php }?>

                <?php if ($this->utils->isEnabledFeature('linked_account') && $this->permissions->checkPermissions('linked_account')) { ?>
                    <!--
                    <a class="btn btn-primary navbar-btn btn-sm" href="javascript:void(0)" onclick="showAddLinkedAccountModal()">
                        <i class="glyphicon glyphicon-link"></i><?=lang('Add Linked Account')?>
                    </a>
                    -->
                <?php } ?>
                <?php if ($this->utils->isEnabledMDB()) { ?>
                    <a href="<?=site_url('/player_management/sync_player_to_mdb/' . $player['playerId'])?>" class="btn btn-success btn-sm">
                        <i class="fa fa-refresh"></i> <?=lang('Sync To Currency')?>
                    </a>
                <?php } ?>
                <?php if ($this->utils->isEnabledFeature('enable_show_trigger_XinyanApi_validation_btn')) { ?>
                    <a href="<?=site_url('player_management/triggerRegisterEventForXinyanApi/' . $player['playerId'])?>"  class="btn btn-primary btn-sm" id="xinyan_api_btn">
                        <i class="fa fa-caret-square-o-up"></i> <span class="hidden-xs"><?=lang('Trigger Xinyan Validation')?></span>
                    </a>
                <?php } ?>
            </div>
        </fieldset>
        <br/>
    </div>

    <!-- Status -->
    <div class="row" style="margin:6px;">
        <fieldset style="padding:6px;">
            <legend><h4><b><?=lang('Status');?></b></h4></legend>
            <div class='col-md-2'>
                <?php
                    switch( $this->utils->getPlayerStatus($player['playerId'])){
                        case 0:
                            $statusTag = '<span class="text-success">' .lang('lang.active').'</span>';
                            break;
                        case 1:
                            $statusTag = '<span class="text-danger">' .lang('Blocked').'</span>';
                            break;
                        case 5:
                            $statusTag = '<span class="text-danger">' .lang('Suspended').'</span>';
                            break;
                        case 7:
                            $statusTag = '<span class="text-danger">' .lang('Self Exclusion').'</span>';
                            break;
                        case 8:
                            $statusTag = '<span class="text-danger">' .lang('Failed Login Attempt').'</span>';
                            break;
                    }
                ?>
                <p> <?=lang('Status');?> : <?=$statusTag?> </p>
            </div>

            <div class='col-md-2'>
                <p><?php echo lang('Cashback'); ?>:
                    <?php echo $player['disabled_cashback'] == 0 ? '<span class="text-success">' . lang('Enabled') . '</span>' : '<span class="text-danger">' . lang('Disabled') . '</span>'; ?>
                </p>
            </div>

            <div class='col-md-2'>
                <p><?php echo lang('Promotion'); ?>:
                    <?php echo $player['disabled_promotion'] == 0 ? '<span class="text-success">' . lang('Enabled') . '</span>' : '<span class="text-danger">' . lang('Disabled') . '</span>'; ?>
                </p>
            </div>

            <?php if ($this->utils->isEnabledFeature('show_kyc_status')) {?>
                <div class='col-md-2'>
                    <p><?php echo lang('KYC'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_kyc/<?=$player['playerId']?>','<?=lang('player kyc')?>');"><span class="text-success" id="kyc_status"><?=lang($kyc_level)?> / <?=lang($kyc_status)?></span></a>
                    </p>
                </div>
            <?php }?>

            <?php if ($this->utils->isEnabledFeature('show_pep_status') && $this->utils->isEnabledFeature('show_risk_score')) {?>
                <div class='col-md-2'>
                    <p><?php echo lang('PEP'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_pep/<?=$player['playerId']?>','<?=lang('player PEP')?>');"><span class="text-success" id="pep_status"><?=lang(str_replace('%20', ' ', $pep_status))?></span></a>
                    </p>
                </div>
            <?php }?>

            <?php if ($this->utils->isEnabledFeature('show_c6_status') && $this->utils->isEnabledFeature('show_risk_score')) {?>
                <div class='col-md-2'>
                    <p><?php echo lang('C6'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_c6/<?=$player['playerId']?>','<?=lang('Player C6')?>');"><span class="text-success" id="c6_status"><?=lang(str_replace('%20', ' ', $c6_status))?></span></a>
                    </p>
                </div>
            <?php }?>

            <?php if ($this->utils->isEnabledFeature('show_risk_score')) {?>
                <div class='col-md-2'>
                    <p><?php echo lang('risk score'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_risk_score/<?=$player['playerId']?>','<?=lang('risk score')?>');"><span class="text-success" id="risk_score"><?=$risk_level?> / <?=$risk_score?></span></a>
                    </p>
                </div>
            <?php }?>

            <div class='col-md-2'>
                <p><?php echo lang('Withdrawal Status'); ?>:
                    <?php echo $player['enabled_withdrawal'] != 0 ? '<span class="text-success">' . lang('Enabled') . '</span>' : '<span class="text-danger">' . lang('Disabled') . '</span>'; ?>
                </p>
            </div>

            <?php if ($this->utils->isEnabledFeature('show_allowed_withdrawal_status') && $this->utils->isEnabledFeature('show_risk_score') && $this->utils->isEnabledFeature('show_kyc_status')) {?>
                <div class='col-md-2'>
                    <p><?php echo lang('KYC Withdrawal Status'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_allowed_withdrawal_status/<?=$player['playerId']?>','<?=lang('KYC Withdrawal Status')?>');"><span class="text-success" id="allowed_withdrawal_status"><?=$allowed_withdrawal_status?></span></a>
                    </p>
                </div>
            <?php }?>
        </fieldset>
    </div>
    <br/>
</div>

<div class="modal fade" id="recoverWcModal" style="margin-top:50px !important;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= lang('recover.wc') ?></h4>
            </div>
            <input type="hidden" id="hiddenId">
            <div class="modal-body">
                <?= lang('conf.recover.wc'); ?>
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" class="btn btn-default"><?= lang('lang.no'); ?></a>
                <a class="btn btn-primary" id="recoverBtn"><i class="fa"></i> <?= lang('lang.yes'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php if($this->utils->isEnabledFeature('enable_show_trigger_XinyanApi_validation_btn')): ?>
    <script> //get XinyanApi status and enable or disabled btn
        $(document).ready(function() {
            $.post(baseUrl + 'player_management/getXinyanApiStatus/' + playerId,function(data){
                if(data.success) {
                    if(data.status == '1' || data.status == '4'){
                        $("#xinyan_api_btn").attr("disabled", true);
                    }else{
                        $("#xinyan_api_btn").attr("disabled", false);
                    }
                } else {
                    $("#xinyan_api_btn").attr("disabled", false);
                }
            },"json");
        });
    </script>
<?php endif;?>

<script type="text/javascript">
    function manualVerify(status, playerId){
        var res = (status == 1) ? 'Unverified' : 'Verified';
        var update_stat = (status == 1) ? 0 : 1;
        if(confirm('Are you sure you want to change the status to '+res+'?')){
            $.post('/player_management/updatePlayerDetailsVerification/'+update_stat+'/'+playerId, function(data){
                location.reload();
            });
        }
    }

    function manualSubscribe(status, playerId){
        var res = (status == 1) ? 'Unsubscribe' : 'Subscribe';
        var update_stat = (status == 1) ? 0 : 1;
        if(confirm('Are you sure you want to change the status to '+res+'?')){
            $.post('/player_management/updatePlayerNewsletterSubscription/'+update_stat+'/'+playerId, function(){
                location.reload();
            });
        }
    }

    function resetFields(){
        $('#message-subject-sms')
            .val("")
            .next('span').html('');
        $('#message-body-sms')
            .val("")
            .next('span').html('');
    }
</script>


<!-- ################################################################## START Signup Information ################################################################## -->
<div class="row" id="signup_form">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#signup" id="hide_sign_up" class="btn btn-primary btn-sm">
                        <i class="glyphicon glyphicon-chevron-up" id="hide_si_up"></i>
                    </a> &nbsp;
                    <strong><?=lang('player.ui03')?></strong>
                </h4>
            </div>

            <div class="panel-body" id="signupinfo_panel_body">
                <div class="row">
                    <div class="col-md-12">

                        <div class="grid-wrapper sample hide">
                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    aa.title Player Dispatch Account Group
                                </div>
                                <div class="grid-field-value sub-field">
                                    aa.value
                                </div>
                            </div>
                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    bb.title
                                </div>
                                <div class="grid-field-value sub-field">
                                    bb.value
                                </div>
                            </div>
                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    cc.title
                                </div>
                                <div class="grid-field-value sub-field">
                                    cc.value
                                </div>
                            </div>
                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    dd.title
                                </div>
                                <div class="grid-field-value sub-field">
                                    dd.value
                                </div>
                            </div>
                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    ee.title
                                </div>
                                <div class="grid-field-value sub-field">
                                    ee.value
                                </div>
                            </div>
                        </div>

                        <div class="grid-wrapper">
                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.01')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <strong><?=$player['username']?></strong> (ID: <?=$player['playerId']?>)
                                        <?php if ($player['blocked'] == 1) {?>
                                            <span class="text-danger"><?php echo lang('Blocked'); ?></span>
                                        <?php }?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.if_online')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <?php if ($playeronline):?>
                                            <?=lang('icon.online')?>
                                            <?php if ($this->permissions->checkPermissions('force_player_logout')) :?>
                                                <a href="javascript:void(0);" onclick="kickout()" class="btn btn-xs btn-primary pull-right"><?=lang('player.ol03')?></a>
                                            <?php endif;?>
                                    <?php else: ?>
                                        <?=lang('icon.offline')?>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.42')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                <?=!$player['last_login_time'] || strtotime($player['last_login_time']) < 0 ? lang('lang.norecord') : $player['last_login_time']?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.85')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                <?=!$player['last_logout_time'] || strtotime($player['last_logout_time']) < 0 ? lang('lang.norecord') : $player['last_logout_time']?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <span data-orig-title="Signup Date"><?=lang('player.38')?></span>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <?=$player['playerCreatedOn']?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.ui10')?>
                                </div>
                                <div class="grid-field-value sub-field">

                                <a class="<?=$player_registrationIp['text_color']?>"
                                   href="/player_management/searchAllPlayer/?search_reg_date=off&ip_address=<?=$player['registrationIP']?>" target="_blank"
                                   data-toggle="tooltip" data-original-title="<?=lang('player.ur05')?>">
                                    <?=$player_registrationIp['ip']?><?=$player_registrationIp['cityCountry']?>
                                </a>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.18')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <span id="invitation-code-container"><?=$player['invitationCode']?></span>
                                    <?php if (trim($player['invitationCode']) == '' || $player['invitationCode'] == "0"): ?>
                                        <button href="javascript:void(0);" onclick="generateReferralCode(<?=$player_id?>);" id="generate-referral-code" type="button" class="btn btn-xs btn-primary pull-right"><?=lang('Generate Referral Code')?></button>
                                    <?php endif ?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.86')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <?php if ($referral_count != 0 && $this->permissions->checkPermissions('friend_referral_player')): ?>
                                        <a href="<?=site_url('player_management/friendReferral/' . $player['playerId'])?>" target="_blank">
                                            <?=$referral_count?>
                                        </a>
                                    <?php else: ?>
                                        <?=$referral_count?>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.70')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <?php if (empty($refereePlayerId)) : ?>
                                        <?=lang('lang.norecord')?>
                                    <?php else: ?>
                                        <a href="<?=site_url('player_management/userInformation/' . $refereePlayerId)?>" target="_blank"
                                           data-toggle="tooltip" data-original-title="<?=lang('player.ur04')?>">
                                            <?=$refereePlayer->username?>
                                        </a>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.24')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                <span id="recorded-affiliate"><?=(empty($affiliate)) ? lang('lang.norecord') : $affiliate?></span>
                                    <?php if ($this->permissions->checkPermissions('assign_player_under_affiliate')) : ?>
                                        <span id="success-glyph" class="glyphicon glyphicon-ok text-success"></span>
                                        <span id="ajax-status"><?=lang('text.loading')?></span>

                                        <button style="margin-bottom:2px;" type="button" id="save-player-affiliate" title="<?=lang('player.ui70')?>"class="btn btn-success btn-xs pull-right"><i class="glyphicon glyphicon-floppy-disk"></i><?=lang('player.ui63')?></button>
                                        <button style="margin-bottom:2px;" type="button" id="cancel-player-affiliate" title="<?=lang('player.ui65')?>"class="btn btn-danger btn-xs pull-right"><i class="glyphicon glyphicon-remove"></i><?=lang('player.ui65')?></button>

                                        <?php if (empty($affiliate)): ?>
                                            <button style="margin-bottom:2px;" type="button" id="add-player-affiliate" title="<?=lang('player.ui71')?>"class="btn btn-info btn-xs pull-right"><i class="glyphicon glyphicon-edit"></i><?=lang('player.ui62')?></button>
                                        <?php endif;?>
                                        <div>
                                            <select id="affiliates-options"  name="affiliates-options"  class="form-control input-sm" style="display:none;"> </select>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                <span id="_under_agent"><?=lang('Under Agent')?></span>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <span id="recorded-agent"><?=(empty($agent)) ? lang('lang.norecord') : $agent?></span>
                                    <?php if (empty($agent) && $this->permissions->checkPermissions('assign_player_under_agent')) : ?>
                                        <div class="pull-right">
                                            <a href="javascript:void(0);" class="btn btn-xs btn-danger"
                                                onclick="modal('/player_management/adjustParentAgent/<?=$player['playerId']?>','<?=lang('Set Parent Agent')?>')">
                                                <?=lang('Set Agent')?>
                                            </a>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('player.96')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <div id="player-tagged-info">
                                        <div id="recorded-tag" class="col col-md-10 no-gutter">
                                            <?=lang('player.tp12')?>
                                        </div>
                                        <?php if ($this->permissions->checkPermissions('edit_player_tag')): ?>
                                            <button style="margin-bottom:2px;" type="button" id="add-player-tag" title="<?=lang('con.plm72')?>"class="btn btn-info btn-xs pull-right"><i class="glyphicon glyphicon-edit"></i><?=lang('con.plm72')?></button>
                                        <?php endif;?>
                                    </div>
                                    <div id="player-tagged-form">
                                        <?php if ($this->permissions->checkPermissions('edit_player_tag')): ?>
                                            <span id="success-glyph-tag" class="glyphicon glyphicon-ok text-success"></span>
                                            <span id="ajax-status-tag"><?=lang('text.loading')?></span>
                                            <button style="margin-bottom:2px;" type="button" id="save-player-tag" title="<?=lang('player.ui63')?>"class="btn btn-success btn-xs pull-right"><i class="glyphicon glyphicon-floppy-disk"></i><?=lang('player.ui63')?></button>
                                            <button style="margin-bottom:2px;" type="button" id="cancel-player-tag" title="<?=lang('player.ui65')?>"class="btn btn-danger btn-xs pull-right"><i class="glyphicon glyphicon-remove"></i><?=lang('player.ui65')?></button>
                                            <input type="hidden" id="tags-options" name="tags-options" sbe-ui-toogle="tagsinput" data-freeInput="false" />
                                            <select id="tags-list"  name="tags-list" class="form-control input-sm"></select>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <?=lang('pay.playgroup')?>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <span id="recorded-level">
                                        <?=lang($player['groupName'])?> - <?=lang($player['levelName'])?>
                                    </span>
                                    <?php if ($this->permissions->checkPermissions('edit_player_vip_level')) : ?>
                                        <span id="success-glyph-level" class="glyphicon glyphicon-ok text-success"></span>
                                        <span id="ajax-status-level"><?=lang('text.loading')?></span>
                                        <button style="margin-bottom:2px;" type="button" id="save-player-level" title="<?=lang('player.ui63')?>"class="btn btn-success btn-xs pull-right"><i class="glyphicon glyphicon-floppy-disk"></i><?=lang('player.ui63')?></button>
                                        <button style="margin-bottom:2px;" type="button" id="cancel-player-level" title="<?=lang('player.ui65')?>"class="btn btn-danger btn-xs pull-right"><i class="glyphicon glyphicon-remove"></i><?=lang('player.ui65')?></button>
                                        <button style="margin-bottom:2px;" type="button" id="adjust-player-level" title="<?=lang('player.46')?>"class="btn btn-info btn-xs pull-right"><i class="glyphicon glyphicon-tasks"></i><?=lang('tool.pm01')?></button>
                                        <select id="levels-options"  name="levels-options" class="form-control input-sm"> </select>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <span><?=lang('Dispatch Account Group');?></span>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <span id="recorded-dispatch-account-level">
                                        <?=(empty($dispatch_account)) ? lang('lang.norecord') : $dispatch_account?>
                                    </span>
                                    <?php if ($this->permissions->checkPermissions('edit_player_dispatch_account_level')) : ?>
                                        <span id="success-dispatch-account-glyph-level" class="glyphicon glyphicon-ok text-success"></span>
                                        <span id="ajax-dispatch-account-status-level"><?=lang('text.loading')?></span>
                                        <button style="margin-bottom:2px;" type="button" id="save-dispatch-account-level" title="<?=lang('player.ui63')?>"class="btn btn-success btn-xs pull-right">
                                            <i class="glyphicon glyphicon-floppy-disk"></i><?=lang('player.ui63')?>
                                        </button>
                                        <button style="margin-bottom:2px;" type="button" id="cancel-dispatch-account-level" title="<?=lang('player.ui65')?>"class="btn btn-danger btn-xs pull-right">
                                            <i class="glyphicon glyphicon-remove"></i><?=lang('player.ui65')?>
                                        </button>
                                        <button style="margin-bottom:2px;" type="button" id="adjust-dispatch-account-level" title="<?=lang('player.100')?>"class="btn btn-info btn-xs pull-right">
                                            <i class="glyphicon glyphicon-tasks"></i><?=lang('tool.pm11')?>
                                        </button>
                                        <select id="dispatch-account-levels-options"  name="dispatch-account-levels-options" class="form-control input-sm"> </select>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <span id="_password_label"><?=lang('player.56')?></span>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <?php if ($this->permissions->checkPermissions('reset_player_login_password')) : ?>
                                        <a href="javascript:void(0);" class="btn btn-xs btn-danger" onclick="modal('/player_management/resetPassword/<?=$player['playerId']?>','<?=lang('player.ur01')?>')"><?=lang('lang.reset')?></a>
                                    <?php else:?>
                                        <button class="btn btn-xs btn-danger" disabled><?=lang('lang.reset')?></button>
                                    <?php endif;?>

                                    <?php if ($this->permissions->checkPermissions('login_as_player')) : ?>
                                        <a href="<?php echo '/player_management/login_as_player/' . $player['playerId']; ?>" class="btn btn-primary btn-xs" target="_blank"><?php echo lang('Login as Player'); ?></a>
                                    <?php endif;?>
                                </div>
                            </div>

                            <div class="grid-field-wrapper">
                                <div class="grid-field-title sub-field">
                                    <span id="_password_label"><?=lang('Withdrawal Password')?></span>
                                </div>
                                <div class="grid-field-value sub-field">
                                    <?php if ($this->utils->getConfig('withdraw_verification') == 'withdrawal_password' && $this->permissions->checkPermissions('reset_players_withdrawal_password')) : ?>
                                        <a href="javascript:void(0);" class="btn btn-xs btn-danger" onclick="modal('/player_management/resetWithdrawalPassword/<?=$player['playerId']?>','<?=lang('Withdrawal password')?>')"><?=lang('lang.reset')?></a>
                                    <?php else:?>
                                        <button class="btn btn-xs btn-danger" disabled><?=lang('lang.reset')?></button>
                                    <?php endif;?>
                                </div>
                            </div>

                            <?php if($this->utils->isEnabledFeature('display_newsletter_subscribe_btn')) : ?>
                                <div class="grid-field-wrapper">
                                    <div class="grid-field-title sub-field">
                                        <?=lang('a_reg.52')?>
                                    </div>
                                    <div class="grid-field-value sub-field">
                                        <?php  if($player['newsletter_subscription'] == 0):?>
                                            <span class="text-danger"><?=lang('Not Subscribed')?></span>
                                        <?php  else:?>
                                            <span class="text-success"><?=lang('Subscribed')?></span>
                                        <?php  endif; ?>
                                        <?php
                                            $subscription = $player['newsletter_subscription'];
                                            if($subscription == 1){
                                                $btnStats = 'danger';
                                                // Set to subscribed
                                                $btnVal = lang('Set to unsubscribed');
                                            }else{
                                                $btnStats = 'success';
                                                $btnVal = lang('Set to subscribed');
                                            }
                                        ?>
                                        <?php if ($this->permissions->checkPermissions('adjust_newsletter_subscription_status')) : ?>
                                            <button type="button" onclick="manualSubscribe(<?=$subscription?>, <?=$player['playerId']?>);" class="btn btn-<?=$btnStats?> btn-xs pull-right">
                                                <i class="fa fa-pencil-square-o"></i><?=$btnVal?>
                                            </button>
                                        <?php  endif; ?>
                                    </div>
                                </div>
                            <?php endif;?>

                            <?php if($this->utils->isEnabledFeature('verification_reference_for_player')) : ?>
                                <div class="grid-field-wrapper">
                                    <div class="grid-field-title sub-field">
                                        <?=lang('Account Verification')?>
                                    </div>
                                    <div class="grid-field-value sub-field">
                                        <?php if($player['manual_verification']): ?>
                                            <span class="label label-success"><?=lang('Verified')?></span>
                                            <?php
                                                $btn_stat = 'danger';
                                                $lang_stat = lang('Reset');
                                            ?>
                                        <?php else:?>
                                            <span class="label label-danger"><?=lang('Not Verified')?></span>
                                            <?php
                                                $btn_stat = 'success';
                                                $lang_stat = lang('Set to Verified');
                                            ?>
                                        <?php  endif;?>
                                        <?php if($this->permissions->checkPermissions('adjust_player_account_verify_status')): ?>
                                            <button class="btn btn-<?=$btn_stat?> btn-xs pull-right"
                                                    onclick="manualVerify(<?=$player['manual_verification']?>, <?=$player['playerId']?>);">
                                                <i class="fa fa-pencil-square-o"></i><?=$lang_stat?>
                                            </button>
                                        <?php  endif;?>
                                    </div>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7" id="player_details" style="display: none;"></div>
</div>
<!-- ################################################################## END Signup Information ################################################################## -->

<!-- ################################################################## START Personal Information ################################################################## -->
<div class="row" id="personal_form">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#personal_info" id="hide_personal_info" class="btn btn-primary btn-sm">
                        <i class="glyphicon glyphicon-chevron-up" id="hide_pi_up"></i>
                    </a> &nbsp;<strong><?=lang('player.ui04')?></strong>

                    <?php if ($this->permissions->checkPermissions('edit_player_personal_information')) {?>
                        <a href="<?=site_url('player_management/editPlayerPersonalInfo/' . $player['playerId'])?>" class="btn btn-xs btn-info pull-right">
                            <i class="glyphicon glyphicon-edit"></i> <?=lang('lang.edit')?>
                        </a>
                    <?php }
                    ?>
                </h4>
            </div>

            <div class="panel-body" id="personal_panel_body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="margin-bottom:0;">
                                <tr>
                                    <th class="active col-md-2"><?=lang('player.04')?></th>
                                    <td><?=$player['firstName'] == '' ? lang('lang.norecord') : $player['firstName']?></td>

                                    <th class="active col-md-2"><?=lang('player.05')?></th>
                                    <td><?=$player['lastName'] == '' ? lang('lang.norecord') : $player['lastName']?></td>

                                    <th class="active col-md-2"><?=lang('player.57')?></th>
                                    <td><?=$player['gender'] == '' ? lang('lang.norecord') : lang($player['gender'])?></td>
                                </tr>
                                <tr>
                                    <th class="active col-md-2"><?=lang('player.17')?></th>
                                    <td><?=$player['birthdate'] == '' ? lang('lang.norecord') : $player['birthdate']?></td>

                                    <th class="active col-md-2"><?=lang('player.11')?></th>
                                    <td><?=$age == '' ? lang('lang.norecord') : $age?></td>

                                    <th class="active col-md-2"><?=lang('player.58')?></th>
                                    <td><?=$player['birthplace'] == '' ? lang('lang.norecord') : $player['birthplace']?></td>
                                </tr>
                                <tr>
                                    <th class="active col-md-2"><?=lang('player.61')?></th>
                                    <td><?=$player['citizenship'] == '' ? lang('lang.norecord') : $player['citizenship']?></td>

                                    <th class="active col-md-2"><?=lang('player.62')?></th>
                                    <td><?=$player['language'] == '' ? lang('lang.norecord') : ucfirst($player['language'])?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"><?=lang('player.20')?></th>
                                    <td>
                                        <?php if (empty($player['country'])) { ?>
                                            <?=$player['residentCountry'] ? $player['residentCountry'] : lang('lang.norecord')?>
                                        <?php } else { ?>
                                            <?=$player['country'] ? $player['country'] : lang('lang.norecord')?>
                                        <?php } ?>
                                    </td>
                                    <th class="active col-md-2"><?=lang('a_reg.37.placeholder')?></th>
                                    <td><?=$player['region'] ? $player['region'] : lang('lang.norecord')?></td>

                                    <th class="active col-md-2"><?=lang('player.19')?></th>
                                    <td><?=$player['city'] ? $player['city'] : lang('lang.norecord')?></td>
                                </tr>

                                <tr>
                                    <th class="active col-md-2"><?=lang('player.60')?></th>
                                    <td><?=$player['zipcode'] ? $player['zipcode'] : lang('lang.norecord')?></td>

                                    <th class="active col-md-2"><?=lang('player.59')?></th>
                                    <td><?=$player['address'] ? $player['address'] : lang('lang.norecord')?></td>

                                    <th class="active col-md-2"><?=lang('address_2')?></th>
                                    <td><?=$player['address2'] ? $player['address2'] : lang('lang.norecord')?></td>
                                </tr>
                                <tr>
                                    <?php if ($this->permissions->checkPermissions('player_verification_question')) {?>
                                        <th class="active col-md-2"><?=lang('player.66')?></th>
                                        <td><?=$player['secretQuestion'] == '' ? lang('lang.norecord') : (lang($player['secretQuestion']) ?: $player['secretQuestion'])?></td>
                                    <?php } ?>
                                    <?php if ($this->permissions->checkPermissions('player_verification_questions_answer')) {?>
                                        <th class="active col-md-2"><?=lang('player.77')?></th>
                                        <td><?=$player['secretAnswer'] == '' ? lang('lang.norecord') : str_replace('%20', ' ', $player['secretAnswer'])?></td>
                                    <?php } ?>
                                    <?php if ( $this->utils->getConfig('multiple_currency_enabled')) { ?>
                                        <th class="active col-md-2"><?=lang('Currency')?></th>
                                        <td><?=$player['playerCurrency'] ? $player['playerCurrency'] : lang('lang.norecord')?></td>
                                    <?php } ?>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7" id="player_details" style="display: none;"></div>
</div>
<!-- ################################################################## END Personal Information ################################################################## -->

<!-- ################################################################## START Contact Information ################################################################## -->
<?php if ($this->permissions->checkPermissions('player_basic_info')) { ?>
    <div class="row" id="contact_form">
        <div class="col-md-12" id="toggleView">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#contact_info" id="hide_contact_info" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-chevron-up" id="hide_pi_up"></i>
                        </a> &nbsp;<strong><?=lang('reg.74')?></strong>
                    </h4>
                </div>

                <div class="panel-body" id="contact_panel_body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="margin-bottom:0;">
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.06')?></th>
                                        <td>
                                            <?php if ($this->permissions->checkPermissions('player_contact_information_email')) : ?>
                                                <?php echo $player['email'] == '' ? lang('lang.norecord') :  $player['email']; ?>
                                            <?php else : ?>
                                                <?php echo $player['email'] == '' ? lang('lang.norecord') :  $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['email'])); ?>
                                            <?php endif; ?>
                                            <?php if ($player['verified_email']) {?>
                                                <span class="label label-success pull-right"><i class='fa fa-envelope'></i> <?=lang('Verified')?></span>
                                            <?php } elseif ($player['email'] != '' && $player['verified_email'] == '0') {?>
                                                <?php if ($this->permissions->checkPermissions('verify_player_email')): ?>
                                                    <a href="javascript:void(0);" onclick="updateEmailStatusToVerified()"><span class = "pull-right" style = "color:0015FF;">
                                                        <i class="fa fa-envelope" style="color:0015FF"></i>&nbsp;<?=lang('reg.68')?></span>
                                                    </a><br>
                                                    <a href="javascript:void(0);" onclick="sendEmailVerification()"><span class = "pull-right" style = "text-align:center;color:0015FF;">
                                                        <i class="fa fa-envelope" style="color:blue"></i>&nbsp; Send Verification</span>
                                                    </a><br>
                                                <?php endif ?>
                                                <span class="label label-danger pull-right"><i class='fa fa-envelope'></i> <?=lang('Not Verified')?></span>
                                            <?php } ?>
                                        </td>

                                        <th class="active col-md-2"><?=lang('player.63')?></th>
                                        <td>
                                            <?php if ($this->permissions->checkPermissions('player_contact_information_contact_number')) : ?>
                                                <?php echo $player['contactNumber'] == '' ? lang('lang.norecord') : $player['contactNumber']; ?>
                                            <?php else : ?>
                                                <?php echo $player['contactNumber'] == '' ? lang('lang.norecord') : $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['contactNumber'])); ?>
                                            <?php endif; ?>
                                            <?php if ($player['verified_phone'] != '' && $player['verified_phone'] != 0) {?>
                                                <span class="label label-success pull-right"><i class='fa fa-phone'></i> <?=lang('Verified');?></span>
                                            <?php } elseif ($player['contactNumber'] != '' && $player['verified_phone'] == '0') {?>
                                                <?php if ($this->permissions->checkPermissions('verify_player_contact_number')): ?>
                                                    <a href="javascript:void(0);" onclick="updatePhoneStatusToVerified()"><span class = "pull-right" style = "color:0015FF;">
                                                        <i class="fa fa-envelope" style="color:0015FF"></i> <?=lang('reg.68')?></span>
                                                    </a><br>
                                                    <a href="javascript:void(0);" onclick="sendSMSVerification()"><span class = "pull-right" style = "text-align:center;color:0015FF;">
                                                        <i class="fa fa-envelope" style="color:0015FF"></i> <?=lang('Send Verification SMS');?></span>
                                                    </a><br>
                                                <?php endif; ?>
                                                <span class="label label-danger pull-right"><i class='fa fa-phone'></i> <?=lang('Not Verified')?></span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php if(!$this->utils->getConfig('use_custom_im_account_fields')): ?>
                                        <tr>
                                            <?php $im1 = $this->config->item('Instant Message 1', 'cust_non_lang_translation'); ?>
                                            <th class="active col-md-2"><?= ($im1) ? $im1 : lang('Instant Message 1') ?></th>
                                            <td>
                                                <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                                                    <?= empty($player['imAccount']) ? lang('lang.norecord') : $player['imAccount']; ?>
                                                <?php else : ?>
                                                    <?= empty($player['imAccount']) ? lang('lang.norecord') : $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['imAccount'])); ?>
                                                <?php endif; ?>
                                            </td>

                                            <?php $im2 = $this->config->item('Instant Message 2', 'cust_non_lang_translation'); ?>
                                            <th class="active col-md-2"><?= ($im2) ? $im2 : lang('Instant Message 2') ?></th>
                                            <td>
                                                <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                                                    <?= empty($player['imAccount2']) ? lang('lang.norecord') : $player['imAccount2']; ?>
                                                <?php else : ?>
                                                    <?= empty($player['imAccount2']) ? lang('lang.norecord') : $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['imAccount2'])); ?>
                                                <?php endif; ?>
                                            </td>

                                            <?php $im3 = $this->config->item('Instant Message 3', 'cust_non_lang_translation'); ?>
                                            <th class="active col-md-2"><?= ($im3) ? $im3 : lang('Instant Message 3') ?></th>
                                            <td>
                                                <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                                                    <?= empty($player['imAccount3']) ? lang('lang.norecord') : $player['imAccount3']; ?>
                                                <?php else : ?>
                                                    <?= empty($player['imAccount3']) ? lang('lang.norecord') : $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['imAccount3'])); ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <th class="active col-md-2"><?=lang('imAccount')?></th>
                                            <td>
                                                <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                                                    <?= empty($player['imAccount']) ? lang('lang.norecord') : $player['imAccount']; ?>
                                                <?php else : ?>
                                                    <?= empty($player['imAccount']) ? lang('lang.norecord') : $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['imAccount'])); ?>
                                                <?php endif; ?>
                                            </td>
                                            <th class="active col-md-2"><?=lang('imAccount2')?></th>
                                            <td>
                                                <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                                                    <?= empty($player['imAccount2']) ? lang('lang.norecord') : $player['imAccount2']; ?>
                                                <?php else : ?>
                                                    <?= empty($player['imAccount2']) ? lang('lang.norecord') : $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['imAccount2'])); ?>
                                                <?php endif; ?>
                                            </td>
                                            <th class="active col-md-2"><?=lang('imAccount3')?></th>
                                            <td>
                                                <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                                                    <?= empty($player['imAccount3']) ? lang('lang.norecord') : $player['imAccount3']; ?>
                                                <?php else : ?>
                                                    <?= empty($player['imAccount3']) ? lang('lang.norecord') : $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['imAccount3'])); ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="active col-md-2"><?=lang('imAccount4')?></th>
                                            <td>
                                                <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                                                    <?= empty($player['imAccount4']) ? lang('lang.norecord') : $player['imAccount4']; ?>
                                                <?php else : ?>
                                                    <?= empty($player['imAccount4']) ? lang('lang.norecord') : $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['imAccount4'])); ?>
                                                <?php endif; ?>
                                            </td>
                                            <th class="active col-md-2"><?=lang('imAccount5')?></th>
                                            <td>
                                                <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                                                    <?= empty($player['imAccount5']) ? lang('lang.norecord') : $player['imAccount5']; ?>
                                                <?php else : ?>
                                                    <?= empty($player['imAccount5']) ? lang('lang.norecord') : $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $player['imAccount5'])); ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                       <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php }?>
<!-- ################################################################## END Contact Information ################################################################## -->

<!-- Linked Account Start -->
<?php //$this->load->view('player_management/linked_account/player_info_linked_account_details'); ?>
<!-- Linked Account End -->

<!-- ################################################################## START Account Information ################################################################## -->
<div class="row" id="account_form">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#balance_info" id="hide_balance_info" class="btn btn-primary btn-sm">
                        <i class="glyphicon glyphicon-chevron-up" id="hide_bi_up"></i>
                    </a>
                    &nbsp;<strong><?=lang('player.ui05')?></strong> (<?=$this->utils->getCurrentCurrency()['currency_code'] ?>)

                    <a href="javascript:void(0);" id="refresh-account-info" data-toggle="tooltip" title="<?=lang('lang.refresh')?>" class="btn btn-sm btn-default pull-right" >
                        <i class="glyphicon glyphicon-refresh"></i>
                    </a>
                    <img class="pull-right account-info-loader"src="<?=$this->utils->imageUrl('ajax-loader.gif')?>" style="margin-right:10px;display:none;"/>
                </h4>
            </div>
            <div class="panel-body" id="balance_panel_body">
                <script>
                    var noRecord = '<?=lang('lang.norecord')?>';

                    $(document).ready(function(){
                        /*Refreshes Account Information*/
                        $("#refresh-account-info").click(function(){
                            ACCOUNT_INFORMATION.refresh();
                            WITHDRAWAL_CONDITION.refresh();
                            return false;
                        });

                        /*Refreshes Withdrawal Condition*/
                        $("#refresh-withdrawal-condition").click(function(){
                            WITHDRAWAL_CONDITION.refresh();
                            ACCOUNT_INFORMATION.refresh();
                            return false;
                        });


                        $("#transferAllToMainWallet").on('click', function(){
                            if (confirm("<?=lang('transfer.all.main')?>")) {
                                var buttonText = "<?=lang('cashier.10')?>";

                                smButtonLoadStart($(this), buttonText);
                                setTimeout(function(){
                                    $.post('/api/retrieveAllSubWalletBalanceToMainBallance/'+"<?=$player['playerId']?>", function(data){
                                        var status = data.status == 'success' ? 'success' : 'danger';
                                        var icon = 'check';
                                        if(status != 'success'){
                                            $('#transferAllToMainWallet').addClass("disabled");
                                            icon = 'warning'
                                        }

                                        ACCOUNT_INFORMATION.refresh();

                                        $.notify({
                                            message: data.msg
                                        },{
                                            type: status,
                                            mouse_over: 'pause',
                                            template:   '<div id="'+status+'_message_prompt" data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert">' +
                                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                            '<span data-notify="icon"><i class="fa fa-'+icon+'" aria-hidden="true"></i></span> ' +
                                            '<span data-notify="title">{1}</span> ' +
                                            '<span id="'+status+'_message_text" data-notify="message">{2}</span>' +
                                            '</div>'
                                        });

                                        buttonLoadEnd($("#transferAllToMainWallet"), buttonText);
                                    });
                                },500);
                            }
                        });

                        function smButtonLoadStart(button, buttonText) {
                            button.html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> '+ buttonText);
                        }
                        function buttonLoadEnd(button, buttonText) {
                            button.html('<i class="fa fa-exchange"></i> '+ buttonText);
                        }

                        var ACCOUNT_INFORMATION = (function() {
                            var walletInfo     = $("#wallet-info"),
                                repeatedLoad   = false,
                                acctInfoLoader = $(".account-info-loader");

                            /*Intial Settings*/
                            acctInfoLoader.hide()

                            function getAccountInformation(){
                                if(repeatedLoad){
                                    acctInfoLoader.show()
                                }

                                $.ajax({
                                    url : '/player_management/getAccountInformation/' + playerId,
                                    type : 'GET',
                                    dataType : "json"
                                }).done(function (obj) {
                                    $("#player-frozen").html(Number(obj.frozen).format(2));
                                    $("#main-wallet-total-bal-amt").html(Number(obj.mainWallet.totalBalanceAmount).format(2));
                                    makeSubwalletRows(obj.subWallet,obj.playerAccount);
                                    $("#total-no-deposit").html(Number(obj.totalDeposits.totalNumberOfDeposit).format(0));
                                    $("#total-deposit").html(Number(obj.totalDeposits.totalDeposit).format(2));
                                    $("#total-no-widthdrawal").html(Number(obj.totalWithdrawal.totalNumberOfWithdrawal).format(0)) ;
                                    $("#total-withdrawal").html(Number(obj.totalWithdrawal.totalWithdrawal).format(2));
                                    $("#total-dep-bonus").html(Number(obj.totalDepositBonus).format(2));
                                    $("#total-cashback-bonus").html(Number(obj.totalCashbackBonus).format(2));
                                    $("#average-deposits").html(Number(obj.averageDeposits).format(2));
                                    $("#average-withdrawals").html(Number(obj.averageWithdrawals).format(2));
                                    $("#total-referal-bonus").html(Number(obj.totalReferralBonus).format(2));
                                    $("#first-last-deposit-first").html((obj.firstLastDeposit.first) ? obj.firstLastDeposit.first : "<?=lang('lang.norecord')?>" );
                                    $("#first-last-withdraw-first").html((obj.firstLastWithdraw.first) ? obj.firstLastWithdraw.first : "<?=lang('lang.norecord')?>" );
                                    $("#total-promo-bonus").html(Number(obj.totalPromoBonus).format(2));
                                    $("#first-last-deposit-last").html((obj.firstLastDeposit.last) ? obj.firstLastDeposit.last : "<?=lang('lang.norecord')?>" );
                                    $("#first-last-withdraw-last").html((obj.firstLastWithdraw.last) ? obj.firstLastWithdraw.last : "<?=lang('lang.norecord')?>" );
                                    $("#total-bonus-received").html(Number(obj.totalBonusReceived).format(2));

                                    repeatedLoad = true;
                                    acctInfoLoader.hide();

                                }).fail(function (jqXHR, textStatus) {
                                    if(jqXHR.status<300 || jqXHR.status>500){
                                        alert(textStatus);
                                    }
                                });
                            }

                            function makeSubwalletRows(subWallet,playerAccount){
                                if(repeatedLoad){
                                    walletInfo.find(".sub-wallet").remove();
                                }

                                var subWalletLength =  subWallet.length;
                                var subwallets;
                                for(var i=0; i < subWalletLength; i++ ){
                                    subwallets += '<tr class="sub-wallet"><th>'+subWallet[i].game+' <?=lang("player.uw06")?></th>';
                                    subwallets += '<td align="right">'+Number(subWallet[i].totalBalanceAmount).format(2)+'</td></tr>';
                                }

                                walletInfo.append(subwallets);
                                repeatedLoad = true;
                            }

                            /**
                             * Number.prototype.format(n, x)
                             *
                             * @param integer n: length of decimal
                             * @param integer x: length of sections
                             */
                            Number.prototype.format = function(n, x) {
                                var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
                                return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
                            };

                            return {
                                refresh:function() {
                                    getAccountInformation();
                                }
                            }
                        }());

                        var WITHDRAWAL_CONDITION = (function() {

                            /* Initiate Withdrawal Condition Table */
                            var withdrawalCondTable = $('#withdrawal-condition-table').DataTable({
                                searching: true,
                                autoWidth: false,
                                dom:"<'panel-body'<'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
                                stateSave: false,

                                buttons: [
                                    {
                                        extend: 'colvis',
                                        postfixButtons: [ 'colvisRestore' ]
                                    }
                                ],
                                columnDefs: [
                                    { sortable: false, targets: [0] },
                                    <?php if (!$this->permissions->checkPermissions('cancel_member_withdraw_condition')) {?>
                                        { "targets": [ 0 ], className: "noVis hidden" },
                                    <?php } ?>
                                ],
                                order: [[7, 'desc']]
                            }).draw(false),

                            /* Initiate Deposit Condition Table */
                            depositCondTable = $('#deposit-condition-table').DataTable({
                                searching: true,
                                autoWidth: false,
                                dom:"<'panel-body'<'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
                                stateSave: false,

                                buttons: [
                                    {
                                        extend: 'colvis',
                                        postfixButtons: [ 'colvisRestore' ]
                                    }
                                ],
                                columnDefs: [
                                    { sortable: false, targets: [0] },
                                    <?php if (!$this->permissions->checkPermissions('cancel_member_withdraw_condition')) {?>
                                    { "targets": [ 0 ], className: "noVis hidden" },
                                    <?php } ?>
                                ],
                                order: [[7, 'desc']]
                            }).draw(false),

                            GET_WITHDRAWAL_CONDITION_URL =  '<?php echo site_url('player_management/getWithdrawalCondition') ?>',
                            GET_SUMMARIZE_WITHDRAWAL_CONDITION_URL =  '<?php echo site_url('player_management/computeWithdrawalCondition') ?>',
                            repeatedLoad =false,
                            withdrawCondLoader = $("#withdawal-condition-loader"),
                            summaryWithCondCont = $("#summary-condition-container"),
                            refreshWithConBtn = $("#refresh-withdrawal-condition"),
                            hasRows=false;


                            var forCancelIds = Array(),reasonToCancel = '';

                            function getWithdrawalCondition(){
                                if(repeatedLoad){
                                    if(hasRows){
                                        withdrawCondLoader.show();
                                    }
                                }

                                $.ajax({
                                    url : GET_WITHDRAWAL_CONDITION_URL+'/'+playerId,
                                    type : 'GET',
                                    dataType : "json"
                                }).done(function (obj) {

                                    var arr = obj.withdrawalCondition;

                                    <?php if($this->utils->isEnabledFeature('disabled_withdraw_condition_share_betting_amount')):?>
                                        var totalPlayerBet = obj.totalPlayerBet;
                                        var totalRequiredBet = obj.totalRequiredBet;
                                        var un_finished = 0;
                                    <?php endif;?>

                                    if(arr){
                                        hasRows = true;
                                        refreshWithConBtn.show();

                                        /*Clear the table rows first to prevent appending rows when refresh*/
                                        withdrawalCondTable.clear().draw();
                                        depositCondTable.clear().draw();

                                        for (var i = 0; i < arr.length; i++) {

                                            var transactions,
                                                promoCode,
                                                depositCondition,
                                                nonfixedDepositAmtCondition,
                                                bonusReleaseRule,
                                                currentBet,
                                                withdrawRequirement,
                                                unfinished_status,
                                                obj=arr[i];

                                            currentBet = (obj.currentBet != null && Number(obj.currentBet)) ? Number(obj.currentBet) : 0;

                                            transactions = "<?=lang('lang.norecyet')?>";
                                            var promoName = obj.promoName || obj.promoTypeName;

                                            if(obj.source_type == '<?=Withdraw_condition::SOURCE_DEPOSIT?>'){
                                                transactions = "<?=lang('withdraw_conditions.source_type.' . Withdraw_condition::SOURCE_DEPOSIT)?>";
                                            }else if(obj.source_type == '<?=Withdraw_condition::SOURCE_BONUS?>'){
                                                  transactions = "<?=lang('withdraw_conditions.source_type.' . Withdraw_condition::SOURCE_BONUS)?>";
                                            }else if(obj.source_type == '<?=Withdraw_condition::SOURCE_CASHBACK?>'){
                                                  transactions = "<?=lang('withdraw_conditions.source_type.' . Withdraw_condition::SOURCE_CASHBACK)?>";
                                            }else if(obj.source_type == '<?=Withdraw_condition::SOURCE_NON_DEPOSIT?>'){
                                                transactions = "<?=lang('Non-deposit')?>";
                                            }


                                            promoName = promoName || "<?=lang('pay.noPromo')?>";

                                            promoCode=(obj.promoCode) ? obj.promoCode : "<i><?=lang('pay.noPromo')?></i>";
                                            promoCode=(obj.promoCode) ? obj.promoCode : "<i><?=lang('pay.noPromo')?></i>";

                                            var wallet_name=gameSystemMap[obj.wallet_type];
                                            if(!wallet_name){
                                                wallet_name='';
                                            }

                                            var bonusAmount = 0;
                                            var conditionAmount = 0;
                                            var deposit_min_limit = 0;

                                            if(obj.withdraw_condition_type == '<?=Withdraw_condition::WITHDRAW_CONDITION_TYPE_BETTING?>'){
                                                unfinished_status =( numeral(obj.is_finished).format() < 1 ) ? "<?=lang('player.ub13')?>" : "<?=lang('player.ub14')?>";
                                                conditionAmount = (obj.conditionAmount) ? obj.conditionAmount : 0;
                                                bonusAmount = (numeral(obj.trigger_amount).format() == 0.0 ? numeral(obj.bonusAmount).format() : numeral(obj.trigger_amount).format());
                                                <?php if ($enabled_show_withdraw_condition_detail_betting) {?>
                                                var withdraw_condition_row=[
                                                        '<input type="checkbox" class="withdraw-cancel-checkbox" title="<?=lang("sys.ga.conf.select.to.cancel")?>" value="'+obj.withdrawConditionId+'" > &nbsp;&nbsp;<i class="fa fa-times-circle withdraw-cancel-icon" style="color:#D32A0E;cursor:pointer;" title="<?=lang("sys.ga.conf.cancel.this")?>"  value="'+obj.withdrawConditionId+'" data-placement="bottom" ></i>',
                                                        transactions,
                                                        wallet_name,
                                                        obj['promoBtn'],
                                                        promoCode,
                                                        numeral(obj.walletDepositAmount).format(),
                                                        bonusAmount,
                                                        obj.started_at,
                                                        numeral(conditionAmount).format(),
                                                        (!obj.note) ? obj.pp_note : obj.note,
                                                        numeral(currentBet).format(),
                                                        unfinished_status
                                                ];
                                                <?php } else {?>
                                                var withdraw_condition_row=[
                                                        '<input type="checkbox" class="withdraw-cancel-checkbox" title="<?=lang("sys.ga.conf.select.to.cancel")?>" value="'+obj.withdrawConditionId+'" > &nbsp;&nbsp;<i class="fa fa-times-circle withdraw-cancel-icon" style="color:#D32A0E;cursor:pointer;" title="<?=lang("sys.ga.conf.cancel.this")?>"  value="'+obj.withdrawConditionId+'" data-placement="bottom" ></i>',
                                                        transactions,
                                                        wallet_name,
                                                        obj['promoBtn'],
                                                        promoCode,
                                                        numeral(obj.walletDepositAmount).format(),
                                                        bonusAmount,
                                                        obj.started_at,
                                                        numeral(conditionAmount).format(),
                                                        (!obj.note) ? obj.pp_note : obj.note
                                                ];
                                                <?php }?>
                                                withdrawalCondTable.row.add( withdraw_condition_row ).draw( false );
                                            }

                                            if(obj.withdraw_condition_type == '<?=Withdraw_condition::WITHDRAW_CONDITION_TYPE_DEPOSIT?>'){
                                                unfinished_status =( numeral(obj.is_finished).format() > 0 &&  (numeral(obj.currentDeposit).format() >= numeral(obj.conditionDepositAmount).format()) ) ? "<?=lang('player.ub14')?>" : "<?=lang('player.ub13')?>";
                                                deposit_min_limit = (obj.conditionDepositAmount) ? obj.conditionDepositAmount : 0;
                                                bonusAmount = (numeral(obj.trigger_amount).format() == 0.0 ? numeral(obj.bonusAmount).format() : numeral(obj.trigger_amount).format());
                                                <?php if ($enabled_show_withdraw_condition_detail_betting) {?>
                                                var deposit_condition_row=[
                                                        '<input type="checkbox" class="deposit-cancel-checkbox" title="<?=lang("sys.ga.conf.select.to.cancel")?>" value="'+obj.withdrawConditionId+'" > &nbsp;&nbsp;<i class="fa fa-times-circle deposit-cancel-icon" style="color:#D32A0E;cursor:pointer;" title="<?=lang("sys.ga.conf.cancel.this")?>"  value="'+obj.withdrawConditionId+'" data-placement="bottom" ></i>',
                                                        transactions,
                                                        wallet_name,
                                                        obj['promoBtn'],
                                                        promoCode,
                                                        numeral(obj.walletDepositAmount).format(),
                                                        bonusAmount,
                                                        obj.started_at,
                                                        numeral(deposit_min_limit).format(),
                                                        (!obj.note) ? obj.pp_note : obj.note,
                                                        numeral(currentBet).format(),
                                                        unfinished_status
                                                ];
                                                <?php } else {?>
                                                var deposit_condition_row=[
                                                        '<input type="checkbox" class="deposit-cancel-checkbox" title="<?=lang("sys.ga.conf.select.to.cancel")?>" value="'+obj.withdrawConditionId+'" > &nbsp;&nbsp;<i class="fa fa-times-circle deposit-cancel-icon" style="color:#D32A0E;cursor:pointer;" title="<?=lang("sys.ga.conf.cancel.this")?>"  value="'+obj.withdrawConditionId+'" data-placement="bottom" ></i>',
                                                        transactions,
                                                        wallet_name,
                                                        obj['promoBtn'],
                                                        promoCode,
                                                        numeral(obj.walletDepositAmount).format(),
                                                        bonusAmount,
                                                        obj.started_at,
                                                        numeral(deposit_min_limit).format(),
                                                        (!obj.note) ? obj.pp_note : obj.note
                                                ];
                                                <?php }?>
                                                depositCondTable.row.add( deposit_condition_row ).draw( false );
                                            }
                                        }//loop end

                                        <?php if($this->utils->isEnabledFeature('disabled_withdraw_condition_share_betting_amount')):?>
                                            var un_finished =  parseFloat(totalRequiredBet) - parseFloat(totalPlayerBet),
                                                summary     =  "<table class='table table-hover table-bordered'>";

                                            if(un_finished < 0) un_finished = 0;

                                            summary += "<tr><th class='active col-md-8'><b><?=lang('pay.totalRequiredBet')?>:</b></th><td align='right'>"+totalRequiredBet.format(2)+"</td></tr>";
                                            summary += "<tr><th class='active col-md-8'><b><?=lang('pay.currTotalBet')?>:</b></th><td align='right'> "+totalPlayerBet.format(2)+"</td></tr>";
                                            summary += "<tr><th class='active col-md-8'><b><?=lang('mark.unfinished')?>:</b></th><td align='right'> "+un_finished.format(2)+" </td><tr>";
                                            summary += "</table>";

                                            summaryWithCondCont.html(summary);
                                        <?php else:?>
                                            // -- Compute for totalRequiredBet, totalPlayerBet, un_finished
                                            computeSummary();
                                        <?php endif;?>

                                        attachEventsListener();

                                    }else{
                                        withdrawalCondTable.clear().draw();
                                        depositCondTable.clear().draw();
                                        refreshWithConBtn.hide();
                                    }

                                    withdrawCondLoader.hide();
                                    repeatedLoad = true;

                                }).fail(function (jqXHR, textStatus) {
                                    /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
                                    //never refresh again
                                    // location.reload();
                                    if(jqXHR.status>=300 && jqXHR.status<500){
                                        // location.reload();
                                    }else{
                                        alert(textStatus);
                                    }
                                });
                            }

                            function computeSummary(){
                                $.ajax({
                                    url : GET_SUMMARIZE_WITHDRAWAL_CONDITION_URL+'/'+playerId,
                                    type : 'GET',
                                    dataType : "json"
                                }).done(function (obj) {

                                    var arr = obj.summarizedWithdrawalCondition;
                                    if(arr){

                                        var totalRequiredBet =  parseFloat(arr.totalRequiredBet),
                                            totalPlayerBet   =  parseFloat(arr.totalPlayerBet),
                                            un_finished      =  parseFloat(arr.unfinished),
                                            summary          =  "<table class='table table-hover table-bordered'>";

                                        if(un_finished < 0) un_finished = 0;

                                        summary += "<tr><th class='active col-md-8'><b><?=lang('pay.totalRequiredBet')?>:</b></th><td align='right'>"+totalRequiredBet.format(2)+"</td></tr>";
                                        summary += "<tr><th class='active col-md-8'><b><?=lang('pay.currTotalBet')?>:</b></th><td align='right'> "+totalPlayerBet.format(2)+"</td></tr>";
                                        summary += "<tr><th class='active col-md-8'><b><?=lang('mark.unfinished')?>:</b></th><td align='right'> "+un_finished.format(2)+" </td><tr>";
                                        summary += "</table>";

                                        summaryWithCondCont.html(summary);

                                    }

                                }).fail(function (jqXHR, textStatus) {
                                    /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
                                    //never refresh again
                                    // location.reload();
                                    if(jqXHR.status>=300 && jqXHR.status<500){
                                        // location.reload();
                                    }else{
                                        alert(textStatus);
                                    }
                                });
                            }

                            function attachEventsListener(){
                                forCancelIds = Array();

                                //For paging use delegate
                                $('#withdrawal-condition-table').delegate(".withdraw-cancel-checkbox", "click", function(){

                                    var id =$(this).val();
                                    if($(this).prop('checked')){
                                        forCancelIds.push(id);
                                    }else{
                                        var i =  forCancelIds.indexOf(id);
                                        forCancelIds.splice(i, 1);
                                    }

                                });

                                $('#withdrawal-condition-table').delegate(".withdraw-cancel-icon", "click", function(){

                                    var id =$(this).attr('value');
                                    if(jQuery.inArray(id, forCancelIds) == -1){
                                        forCancelIds.push(id);
                                    }
                                    $(this).prev('input:checkbox').prop('checked', true);
                                    showConfirmation();
                                });

                                //tooltips
                                $('#withdrawal-condition-table').delegate(".withdraw-cancel-checkbox", "mouseover", function(){
                                    $('.withdraw-cancel-checkbox').tooltip({placement : "right"});
                                });

                                $('#withdrawal-condition-table').delegate("td", "mouseover", function(){
                                    $('.withdraw-cancel-checkbox').tooltip({placement : "right"});
                                    $('.withdraw-cancel-icon').tooltip({placement : "right"});
                                });

                                //For paging use delegate
                                $('#deposit-condition-table').delegate(".deposit-cancel-checkbox", "click", function(){

                                    var id =$(this).val();
                                    if($(this).prop('checked')){
                                        forCancelIds.push(id);
                                    }else{
                                        var i =  forCancelIds.indexOf(id);
                                        forCancelIds.splice(i, 1);
                                    }

                                });

                                $('#deposit-condition-table').delegate(".deposit-cancel-icon", "click", function(){

                                    var id =$(this).attr('value');
                                    if(jQuery.inArray(id, forCancelIds) == -1){
                                        forCancelIds.push(id);
                                    }
                                    $(this).prev('input:checkbox').prop('checked', true);
                                    showConfirmation();
                                });

                                //tooltips
                                $('#deposit-condition-table').delegate(".deposit-cancel-checkbox", "mouseover", function(){
                                    $('.withdraw-cancel-checkbox').tooltip({placement : "right"});
                                });

                                $('#deposit-condition-table').delegate("td", "mouseover", function(){
                                    $('.withdraw-cancel-checkbox').tooltip({placement : "right"});
                                    $('.withdraw-cancel-icon').tooltip({placement : "right"});
                                });


                                $('#cancel-wc-items, #cancel-dc-items').tooltip({placement : "top"});
                                $('#cancel-wc-items, #cancel-dc-items').click(function(){

                                    if(!forCancelIds.length){
                                        alert("<?=lang('cancel_deposit')?>");
                                        return;
                                    }
                                    showConfirmation();
                                });

                                $('#conf-cancel-action').click(function(){
                                    $('#reason-to-cancel').next('div.help-block').html('');
                                });
                            }

                            function showConfirmation(){
                                $('#conf-modal').modal('show');
                                var items = (forCancelIds.length > 1) ? "<?=lang('sys.dasItems')?>" : "<?=lang('sys.dasItem')?>";
                                $('#conf-msg-ask').html("<?=lang('sys.ga.conf.cancel')?> "+forCancelIds.length+" "+items+" ?");
                                $('#conf-msg-reason').html("<?=lang('pay.reason')?>");
                                return;
                            }

                            function cancelWithdrawalCondition(){

                                var cancelManualStatus = "<?= Withdraw_condition::DETAIL_STATUS_CANCELLED_MANUALLY ?>";

                                if($('#reason-to-cancel').val() != ''){

                                    $('#conf-yes-action').attr("disabled", true);
                                    var data = {
                                        forCancelIds  : forCancelIds,
                                        reasonToCancel : $('#reason-to-cancel').val(),
                                        playerId :  playerId,
                                        cancelManualStatus : cancelManualStatus
                                    };
                                    $.ajax({
                                        url : '<?php echo site_url('player_management/cancelWithdrawalCondition') ?>',
                                        type : 'POST',
                                        data : data,
                                        dataType : "json"
                                    }).done(function (data) {
                                        if (data.status == "success") {
                                            $('#conf-yes-action').attr("disabled", false);
                                            forCancelIds = Array();
                                            $('#reason-to-cancel').next('div.help-block').html('');
                                            $('#reason-to-cancel').val('');
                                            $('#conf-modal').modal('hide');
                                            WITHDRAWAL_CONDITION.refresh();
                                        }else{
                                            // location.reload();
                                        }

                                    }).fail(function (jqXHR, textStatus) {
                                        $(this).attr("disabled", false);
                                        throw textStatus;
                                    });

                                }else{
                                    $('#reason-to-cancel').next('div.help-block').html('<?=lang("pay.reason")?> is required');

                                }
                            }

                            function getDepositCondtion(promoName,depositCondition,bonusReleaseRule,withdrawRequirement){
                                if(promoName){
                                    var row = "<?=lang('cms.depCon')?> <br/>";
                                    row += depositCondition+"<br/>";
                                    row += "(<?=lang('cms.bonus')?>)<br/>"
                                    row += bonusReleaseRule+"<br/>";
                                    row += "(<?=lang('promo.betCondition')?>)<br/>";
                                    row += withdrawRequirement+"<br/>";
                                    return row;
                                }else{
                                    var row = "<i><?=lang('pay.noPromo')?></i>";
                                    return row;
                                }
                            }

                            /**
                             * Number.prototype.format(n, x)
                             *
                             * @param integer n: length of decimal
                             * @param integer x: length of sections
                             */
                            Number.prototype.format = function(n, x) {
                                var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
                                return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
                            };

                            return {
                                refresh:function() {
                                    getWithdrawalCondition();
                                },
                                cancel:function(){
                                    cancelWithdrawalCondition();
                                }
                            }
                        }());

                        /*Load or initiate the existing data*/
                        ACCOUNT_INFORMATION.refresh();
                        WITHDRAWAL_CONDITION.refresh();

                        $('#conf-yes-action').click(function(){
                            WITHDRAWAL_CONDITION.cancel();
                        });
                    });//END READY
                </script>

                <div id="conf-modal" class="modal fade bs-example-modal-md" data-backdrop="static" data-keyboard="false"  tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header panel-heading">
                                <h3 id="myModalLabel"><?=lang('sys.pay.conf.title');?></h3>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="help-block" id="conf-msg-ask"></div>
                                        <div class="form-group">
                                            <label class="control-label" id="conf-msg-reason" ></label>
                                            <textarea class="form-control" id="reason-to-cancel"rows="3"></textarea>
                                            <div class="help-block" style="color:#F04124"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default"  id="conf-cancel-action" data-dismiss="modal"><?=lang('pay.bt.cancel');?></button>
                                <button type="button" id="conf-yes-action" class="btn btn-primary"><?=lang('pay.bt.yes');?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <div class="col-md-3">
                                <table class="table table-bordered" id="wallet-info">
                                    <thead>
                                        <tr>
                                            <th colspan="2">
                                                <center>
                                                    <?=lang('Wallet Balance'); ?>
                                                    <div class="pull-right">
                                                        <a class="btn btn-portage btn-xs show_bigwallet_details" data-playerid="<?=$player['playerId']; ?>">
                                                            <?=lang('Details'); ?>
                                                        </a>
                                                        <a href="/player_management/resetbalance/<?=$player['playerId'];?>" class="btn btn-scooter btn-xs" onclick="return confirm('<?=lang("confirm.refresh.balance")?>');">
                                                            <i class="fa fa-refresh"></i>
                                                        </a>
                                                        <?php if ($this->permissions->checkPermissions('payment_player_adjustbalance')) {?>
                                                            <a href="/payment_management/adjust_balance/<?=$player['playerId']?>" class="btn btn-scooter btn-xs" target="_blank">
                                                                <i class="icon-equalizer"></i>
                                                            </a>
                                                        <?php }?>
                                                        <?php if ($this->permissions->checkPermissions('transfer_all_back_to_main_wallet')) {?>
                                                            <button id="transferAllToMainWallet" class="btn btn-scooter btn-xs">
                                                                <i class="fa fa-exchange"></i>
                                                            </button>
                                                        <?php }?>
                                                    </div>
                                                </center>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="active"><center><?=lang("Wallet Name")?></center></th>
                                            <th class="active"><center><?=lang("Balance")?></center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th><?=lang('player.ui20')?></th>
                                            <td align="right"><span id="main-wallet-total-bal-amt"></span></td>
                                        </tr>
                                        <tr>
                                            <th><?=lang('cashier.pendingBalance')?></th>
                                            <td align="right"><span id="player-frozen"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-9">
                                <table class="table table-bordered" style="margin-bottom:0;">
                                    <tr>
                                        <th colspan="2"><center><?=lang('player.74')?></center></th>
                                        <th colspan="2"><center><?=lang('pay.withinfo')?></center></th>
                                        <th colspan="2"><center><?=lang('pay.bonusinfo')?></center></th>
                                    </tr>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.ui14')?></th>
                                        <td><span id="total-no-deposit"></span></td>

                                        <th class="active col-md-2"><?=lang('player.ui17')?></th>
                                        <td><span id="total-no-widthdrawal"></span></td>

                                        <th class="active col-md-2"><?=lang('player.totalPlayerGroupDepositBonus')?></th>
                                        <td><span class="player-currency"></span> <span id="total-dep-bonus"> </span></td>
                                    </tr>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.ui15')?></th>
                                        <td><span class="player-currency"></span> <span id="total-deposit"></span></td>

                                        <th class="active col-md-2"><?=lang('player.ui18')?></th>
                                        <td><span class="player-currency"></span> <span id="total-withdrawal"></span></td>

                                        <th class="active col-md-2"><?=lang('player.ui23')?></th>
                                        <td><span class="player-currency"></span> <span id="total-cashback-bonus"></span></td>
                                    </tr>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.ui16')?></th>
                                        <td><span class="player-currency"></span> <span id="average-deposits"> </span></td>

                                        <th class="active col-md-2"><?=lang('player.ui19')?></th>
                                        <td><span class="player-currency"></span> <span id="average-withdrawals"></span></td>

                                        <th class="active col-md-2"><?=lang('player.ui24')?></th>
                                        <td><span class="player-currency"></span> <span id="total-referal-bonus"> </span></td>
                                    </tr>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.firstDepositDateTime')?></th>
                                        <td><span id="first-last-deposit-first"></span>

                                        </td>

                                        <th class="active col-md-2"><?=lang('player.firstWithdrawDateTime')?></th>
                                        <td><span id="first-last-withdraw-first"></span></td>

                                        <th class="active col-md-2"><?=lang('player.totalPromoBonus')?></th>
                                        <td><span class="player-currency"></span> <span id="total-promo-bonus"> </span></td>

                                    </tr>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.lastDepositDateTime')?></th>
                                        <td><span id="first-last-deposit-last"></span></td>

                                        <th class="active col-md-2"><?=lang('player.lastWithdrawDateTime')?></th>
                                        <td><span id="first-last-withdraw-last"></span></td>

                                        <th class="active col-md-2"><?=lang('player.totalBonusReceived')?></th>
                                        <td><b><i><span class="player-currency"></span> <span id="total-bonus-received"></span></i></b></td>
                                    </tr>
                                </table>

                                <br/>
                                <table class="table table-bordered" style="margin-bottom:0;">
                                    <thead>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('Available Points')?></th>
                                        <td><span id="available-points"><?=$available_points?></span>
                                        </td>
                                    </thead>
                                </table>


                                <?php if ($this->permissions->checkPermissions('available_payment_account_for_player')): ?>
                                    <br/>
                                    <table class="table table-bordered" style="margin-bottom:0;">
                                        <thead>
                                        <tr>
                                            <th colspan="6"><center><?=lang('con.plm73')?></center></th>
                                        </tr>
                                        <tr>
                                            <th class="active"><?=lang('ID')?></th>
                                            <th class="active"><?=lang('con.plm74')?></th>
                                            <th class="active"><?=lang('pay.bankname')?></th>
                                            <th class="active"><?=lang('pay.acctname')?></th>
                                            <th class="active"><?=lang('cashier.69')?></th>
                                            <th class="active"><?=lang('con.plm75')?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (!empty($paymentaccounts)) {
                                            foreach ($paymentaccounts as $paymentaccount) {?>
                                                <tr>
                                                    <td><?=$paymentaccount->id?></td>
                                                    <td><?=lang($paymentaccount->payment_flag)?></td>
                                                    <td><?=lang($paymentaccount->payment_type)?></td>
                                                    <td><?=$paymentaccount->payment_account_name ?: '<i class="text-muted">' . lang('lang.norecyet') . '</td>'?></td>
                                                    <td><?=$paymentaccount->payment_account_number ?: '<i class="text-muted">' . lang('lang.norecyet') . '</td>'?></td>
                                                    <td><?=$paymentaccount->payment_branch_name ?: '<i class="text-muted">' . lang('lang.norecyet') . '</td>'?></td>
                                                </tr>
                                            <?php
                                            }
                                        }?>
                                        </tbody>
                                    </table>
                                <?php endif?>
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7" id="player_details" style="display: none;"></div>
</div>
<!-- ################################################################## END Account Information ################################################################## -->

<!-- ################################################################## START Withdrawal Information ################################################################## -->
<div class="panel panel-primary" id="withdraw_condition_form">
    <div class="panel-heading">
        <h4 class="panel-title"> <a href="#withdraw_condition_info" id="hide_withrawal_info" class="btn btn-primary btn-sm">
                <i class="glyphicon glyphicon-chevron-up" id="hide_withrawal_up"></i></a> &nbsp;
            <strong><?=lang('pay.withdrawalCondition')?></strong>
        </h4>
    </div>
    <div class="table-responsive" id="withrawal_panel_body" >
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#withdrawal-condition-table-tab"><?=lang('Wagering Requirements')?></a></li>
            <li><a data-toggle="tab" href="#deposit-condition-table-tab"><?=lang('Minimum Deposit Requirements')?></a>
        </ul>

        <div class="tab-content">
            <div id="withdrawal-condition-table-tab" class="tab-pane panel panel-default fade in active">
                <div class="panel-body">
                    <?php if ($this->permissions->checkPermissions('cancel_member_withdraw_condition')) {?>
                        <button type="button" value="" title="<?=lang('sys.ga.conf.cancel.selected')?> " id="cancel-wc-items"  class="btn btn-danger btn-sm">
                            <i class="glyphicon glyphicon-remove-circle" style="color:white;"  data-placement="bottom" ></i>
                            <?=lang('lang.cancel');?>
                        </button>
                    <?php }
                    ?>

                    <table class="table table-hover table-bordered table-condensed" id="withdrawal-condition-table">
                        <thead>
                        <tr>
                            <th><?=lang('sys.pay.action');?></th>
                            <th><?=lang('pay.transactionType')?></th>
                            <th><?=lang('Sub-wallet')?></th>
                            <th><?=lang('pay.promoName')?></th>
                            <th><?=lang('cms.promocode')?></th>
                            <th><?=lang('cashier.53')?></th>
                            <th><?=lang('Bonus')?></th>
                            <th><?=lang('pay.startedAt')?></th>
                            <th><?=lang('pay.withdrawalAmountCondition')?></th>
                            <th><?=lang('Note')?></th>
                            <?php if ($enabled_show_withdraw_condition_detail_betting) {?>
                                <th><?=lang('Betting Amount')?></th>
                                <th><?=lang('lang.status')?></th>
                            <?php }?>
                        </tr>
                        </thead>
                        <!--################Dynamically adding rows################-->
                    </table>

                    <div id="withdraw_condition_actions" class="col-md-2">
                        <button type="button" class="btn btn-info btn-xs" id="check_withdraw_condition"><?php echo lang('Manually Check'); ?></button>
                    </div>
                    <script>
                        $(function (){
                            $("#check_withdraw_condition").click(function(e){
                                if(confirm("<?php echo lang('Do you want check withdraw condition? it will take a while.'); ?>")){
                                    //to check withdraw condition
                                    window.location.href="<?php echo site_url('/player_management/check_withdraw_condition/' . $player['playerId']); ?>";
                                }
                                e.preventDefault();
                            });
                        });
                    </script>
                    <div id="summary-condition-container" class="col-md-3">
                        <!--#####Load the summary here######-->
                    </div>

                    <div style="margin:10px;height:30px;">
                        <a href="#" id="refresh-withdrawal-condition" data-toggle="tooltip" title="<?=lang('lang.refresh')?>" class="btn btn-sm btn-default " >
                            <i class="glyphicon glyphicon-refresh"></i>
                        </a>
                        <img id="withdawal-condition-loader"src="<?=$this->utils->imageUrl('ajax-loader.gif')?>" />
                    </div>
                </div>
            </div>

            <div id="deposit-condition-table-tab" class="tab-pane panel panel-default fade">
                <div class="panel-body">
                    <?php if ($this->permissions->checkPermissions('cancel_member_withdraw_condition')) {?>
                        <button type="button" value="" title="<?=lang('sys.ga.conf.cancel.selected')?> " id="cancel-dc-items"  class="btn btn-danger btn-sm">
                            <i class="glyphicon glyphicon-remove-circle" style="color:white;"  data-placement="bottom" ></i>
                            <?=lang('lang.cancel');?>
                        </button>
                    <?php }
                    ?>
                    <table class="table table-hover table-bordered table-condensed" id="deposit-condition-table">
                        <thead>
                        <tr>
                            <th><?=lang('sys.pay.action');?></th>
                            <th><?=lang('pay.transactionType')?></th>
                            <th><?=lang('Sub-wallet')?></th>
                            <th><?=lang('pay.promoName')?></th>
                            <th><?=lang('cms.promocode')?></th>
                            <th><?=lang('cashier.53')?></th>
                            <th><?=lang('Bonus')?></th>
                            <th><?=lang('pay.startedAt')?></th>
                            <th><?=lang('pay.mindepamt').' '.lang('Conditions')?></th>
                            <th><?=lang('Note')?></th>
                            <?php if ($enabled_show_withdraw_condition_detail_betting) {?>
                                <th><?=lang('Betting Amount')?></th>
                                <th><?=lang('lang.status')?></th>
                            <?php }?>
                        </tr>
                        </thead>
                        <!--################Dynamically adding rows################-->
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ################################################################## END Withdrawal Information ################################################################## -->

<!-- ################################################################## START Transfer Information ################################################################## -->
<?php
    if($this->utils->isEnabledFeature('enabled_transfer_condition')){
        include dirname(__FILE__) . '/user_information/transfer_condition_details.php';
    }
?>
<!-- ################################################################## END Transfer Information ################################################################## -->

<!-- ################################################################## START Game Information ################################################################## -->
<div class="panel panel-primary" id="game_form">
    <div class="panel-heading">
        <h4 class="panel-title"> <a href="#game_info" id="hide_game_info" class="btn btn-primary btn-sm">
                <i class="glyphicon glyphicon-chevron-up" id="hide_game_up"></i></a> &nbsp;
            <strong><?=lang('player.ui06')?></strong>
            <a href="/player_management/refreshAllGames/<?=$player['playerId']?>" class="btn btn-info btn-xs pull-right"><i class="glyphicon glyphicon-refresh"></i> <?=lang('lang.refresh')?></a>
        </h4>
    </div>
    <div class="table-responsive" id="game_panel_body">
        <table class="table table-bordered table-condensed">
            <thead>
            <tr>
                <?php if($this->utils->getConfig('use_total_hour')){ ?>
                    <th rowspan="2" style="vertical-align: middle;"><?=lang('player.ui29')?></th>
                    <th rowspan="2" style="vertical-align: middle;"><?=lang('cashier.78')?></th>
                    <th rowspan="2" style="vertical-align: middle;"><?=lang('Status')?></th>
                    <th colspan="1" style="text-align: center;"><?=lang('player.ui26')?></th>
                    <th colspan="1" style="text-align: center;"><?=lang('player.ui27')?></th>
                    <th colspan="1" style="text-align: center;"><?=lang('player.ui28')?></th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;"><?=lang('Result Percentage')?></th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;"><?=lang('mark.resultAmount')?></th>
                <?php }else{ ?>
                    <th rowspan="2" style="vertical-align: middle;"><?=lang('player.ui29')?></th>
                    <th rowspan="2" style="vertical-align: middle;"><?=lang('cashier.78')?></th>
                    <th rowspan="2" style="vertical-align: middle;"><?=lang('Status')?></th>
                    <th colspan="3" style="text-align: center;"><?=lang('player.ui26')?></th>
                    <th colspan="4" style="text-align: center;"><?=lang('player.ui27')?></th>
                    <th colspan="4" style="text-align: center;"><?=lang('player.ui28')?></th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;"><?=lang('player.ui25')?></th>
                <?php } ?>
            </tr>
            <tr>
                <?php if($this->utils->getConfig('use_total_hour')){ ?>
                    <th style="text-align: right;"><?=lang('system.word32')?></th>
                    <th style="text-align: right;"><?=lang('system.word32')?></th>
                    <th style="text-align: right;"><?=lang('system.word32')?></th>
                <?php }else{ ?>
                    <th style="text-align: right;"><?=lang('player.mp03')?></th>
                    <th style="text-align: right;"><?=lang('lang.average')?></th>
                    <th style="text-align: right;"><?=lang('system.word32')?></th>
                    <th style="text-align: right;"><?=lang('player.mp03')?></th>
                    <th style="text-align: right;"><?=lang('cms.percentage')?></th>
                    <th style="text-align: right;"><?=lang('lang.average')?></th>
                    <th style="text-align: right;"><?=lang('system.word32')?></th>
                    <th style="text-align: right;"><?=lang('player.mp03')?></th>
                    <th style="text-align: right;"><?=lang('cms.percentage')?></th>
                    <th style="text-align: right;"><?=lang('lang.average')?></th>
                    <th style="text-align: right;"><?=lang('system.word32')?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($game_data['game_platforms'] as $game_platform): ?>
                <tr>
                    <td><?=$game_platform['system_code']?></td>
                    <?php if (!$game_platform['register']): ?>
                        <td align="left">
                            <a href="/player_management/createGameProviderAccount/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.create'), $game_platform['system_code'])?>" class="pull-right" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.create'), $game_platform['system_code'])?>')"><i class="fa fa-user-plus"></i></a>
                        </td>
                        <td align="center">
                            <i class="text-muted"><?=lang('lang.norec')?></i>
                        </td>
                    <?php else: ?>
                        <td align="left">
                            <b class="text-info"><?php echo $game_platform['login_name']; ?></b>
                            <?php if ($this->permissions->checkPermissions('force_create_game_account')) {
                                ?>
                                <a href="/player_management/createGameProviderAccount/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.create'), $game_platform['system_code'])?>" class="pull-right" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.create'), $game_platform['system_code'])?>')"><i class="fa fa-user-plus"></i></a>

                            <?php }?>
                        </td>
                        <td>
                            <ul class="list-inline pull-right">
                                <li><a href="/player_management/reset_player/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.reset'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.reset'), $game_platform['system_code'])?>')"><i class="fa fa-undo"></i></a></li>
                                <li><a href="/player_management/syncPassword/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.sync'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.sync'), $game_platform['system_code'])?>')"><i class="fa fa-refresh"></i></a></li>
                                <li><a href="/player_management/update_game_info_view/<?=$player['playerId']?>/<?=$game_platform['id']?>/<?=$game_platform['system_code']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.update'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.update'), $game_platform['system_code'])?>')"><i class="fa fa-pencil-square"></i></a></li>

                                <?php if ($this->permissions->checkPermissions('block_player')) {?>
                                    <?php if ($game_platform['blocked'] == 0): ?>
                                        <li><a href="/player_management/blockGameProviderAccount/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.block'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.block'), $game_platform['system_code'])?>')"><i class="fa fa-lock"></i></a></li>
                                    <?php elseif ($game_platform['blocked'] == 1): ?>
                                        <li><a href="/player_management/unblockGameProviderAccount/<?=$player['playerId']?>/<?=$game_platform['id']?>" title="<?=sprintf(lang('gameplatformaccount.title.unblock'), $game_platform['system_code'])?>" onclick="return confirm('<?=sprintf(lang('gameplatformaccount.confirm.unblock'), $game_platform['system_code'])?>')"><i class="fa fa-unlock-alt"></i></a></li>
                                    <?php endif?>
                                <?php }?>
                                <?php if ($this->permissions->checkPermissions('set_player_bet_limit_for_api')) {?>
                                    <?php if(!empty($this->utils->getConfig('api_with_set_bet_limit')) && in_array($game_platform['id'], $this->utils->getConfig('api_with_set_bet_limit'))){ ?>
                                     <li><a href="/player_management/setMemberBetSetting/<?=$player['playerId']?>/<?=$game_platform['id']?>" data-toggle="tooltip" data-placement="right" title="<?=sprintf(lang('gameplatformaccount.title.set_bet_setting'), $game_platform['system_code'])?>"><i class="fa fa-bars"></i></a></li>
                                    <?php } ?>
                                 <?php }?>
                            </ul>
                            <?php if ($game_platform['blocked'] == 1) {?>
                                <b class="text-danger"><?=lang('sys.ip16')?></b>
                            <?php } elseif ($game_platform['is_demo_flag'] == 1) {?>
                                <b class="text-warning"><?=lang('Demo')?></b>
                            <?php } elseif ($game_platform['blocked'] == 0) {?>
                                <b class="text-success"><?=lang('status.normal')?></b>
                            <?php }?>
                        </td>
                    <?php endif?>
                    <?php if($this->utils->getConfig('use_total_hour')){ ?>
                        <td align="right" class="info"><?=number_format((isset($game_platform['bet']['sum']) ? $game_platform['bet']['sum'] : 0), 2)?></td>
                        <td align="right" class="success"><?=number_format((isset($game_platform['gain']['sum']) ? $game_platform['gain']['sum'] : 0), 2)?></td>
                        <td align="right" class="danger"><?=number_format((isset($game_platform['loss']['sum']) ? $game_platform['loss']['sum'] : 0), 2)?></td>
                    <?php }else{ ?>
                        <td align="right" class="info"><?=isset($game_platform['bet']['count']) ? $game_platform['bet']['count'] : 0?></td>
                        <td align="right" class="info"><?=number_format((isset($game_platform['bet']['ave']) ? $game_platform['bet']['ave'] : 0), 2)?></td>
                        <td align="right" class="info"><?=number_format((isset($game_platform['bet']['sum']) ? $game_platform['bet']['sum'] : 0), 2)?></td>
                        <td align="right" class="success"><?=isset($game_platform['gain']['count']) ? $game_platform['gain']['count'] : 0?></td>
                        <td align="right" class="success"><?=number_format(isset($game_platform['gain']['percent']) ? $game_platform['gain']['percent'] : 0, 2)?>%</td>
                        <td align="right" class="success"><?=number_format((isset($game_platform['gain']['ave']) ? $game_platform['gain']['ave'] : 0), 2)?></td>
                        <td align="right" class="success"><?=number_format((isset($game_platform['gain']['sum']) ? $game_platform['gain']['sum'] : 0), 2)?></td>
                        <td align="right" class="danger"><?=isset($game_platform['loss']['count']) ? $game_platform['loss']['count'] : 0?></td>
                        <td align="right" class="danger"><?=number_format(isset($game_platform['loss']['percent']) ? $game_platform['loss']['percent'] : 0, 2)?>%</td>
                        <td align="right" class="danger"><?=number_format((isset($game_platform['loss']['ave']) ? $game_platform['loss']['ave'] : 0), 2)?></td>
                        <td align="right" class="danger"><?=number_format((isset($game_platform['loss']['sum']) ? $game_platform['loss']['sum'] : 0), 2)?></td>
                    <?php } ?>
                    <?php if($this->utils->getConfig('use_total_hour')){ ?>
                        <td align="right" class="bg-warning"><strong><?=number_format((isset($game_platform['result_percentage']['percent']) ? $game_platform['result_percentage']['percent'] : 0), 2)?>%</strong></td>
                    <?php } ?>
                    <td align="right" class = "bg-warning"
                        <?php if (!isset($game_platform['gain_loss']['sum']) || $game_platform['gain_loss']['sum'] == 0): ?>
                        <?php elseif ($game_platform['gain_loss']['sum'] < 0): ?>
                            class="text-danger"
                        <?php elseif ($game_platform['gain_loss']['sum'] > 0): ?>
                            class="text-success warning"
                        <?php endif?>
                        ><strong><?=number_format((isset($game_platform['gain_loss']['sum']) ? $game_platform['gain_loss']['sum'] : 0), 2)?></strong></td>
                </tr>
            <?php endforeach?>
            </tbody>
            <tfoot>
            <tr>
                <?php if($this->utils->getConfig('use_total_hour')){ ?>
                    <th colspan="3"></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_bet_sum'], 2)?></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_gain_sum'], 2)?></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_loss_sum'], 2)?></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_result_percent'], 2)?>%</th>
                    <th style="text-align: right;"
                        <?php if ($game_data['total_gain_loss_sum'] < 0): ?>
                            class="text-danger"
                        <?php elseif ($game_data['total_gain_loss_sum'] > 0): ?>
                            class="text-success"
                        <?php endif?>
                        ><?=number_format($game_data['total_gain_loss_sum'], 2)?></th>
                <?php }else{ ?>
                    <th colspan="3"></th>
                    <th style="text-align: right;"><?=$game_data['total_bet_count']?></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_bet_ave'], 2)?></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_bet_sum'], 2)?></th>
                    <th style="text-align: right;"><?=$game_data['total_gain_count']?></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_gain_percent'], 2)?>%</th>
                    <th style="text-align: right;"><?=number_format($game_data['total_gain_ave'], 2)?></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_gain_sum'], 2)?></th>
                    <th style="text-align: right;"><?=$game_data['total_loss_count']?></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_loss_percent'], 2)?>%</th>
                    <th style="text-align: right;"><?=number_format($game_data['total_loss_ave'], 2)?></th>
                    <th style="text-align: right;"><?=number_format($game_data['total_loss_sum'], 2)?></th>
                    <th style="text-align: right;"
                        <?php if ($game_data['total_gain_loss_sum'] < 0): ?>
                            class="text-danger"
                        <?php elseif ($game_data['total_gain_loss_sum'] > 0): ?>
                            class="text-success"
                        <?php endif?>
                        ><?=number_format($game_data['total_gain_loss_sum'], 2)?></th>
                <?php } ?>
            </tr>
            </tfoot>
        </table>
    </div>
    <div class="panel-footer"></div>
</div>
<!-- ################################################################## END Game Information ################################################################## -->

<!-- ################################################################## START Bank Information ################################################################## -->
<div class="row" id="bank_form">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#bank_info" id="hide_bank_info" class="btn btn-primary btn-sm">
                        <i class="glyphicon glyphicon-chevron-up" id="hide_bank_up"></i>
                    </a> &nbsp;<strong><?=lang('player.ui07')?></strong>
                    <?php
                    // player_bank_info_control
                    if ($this->permissions->checkPermissions('add_player_bank_info')) {?>
                        <a href="javascript:void(0);" onclick="modal('/player_management/addPlayerBankInfo/<?=$player['playerId']?>/1','<?=lang('player.ui58')?>')" class="btn btn-info btn-xs pull-right"><i class="input-xs glyphicon glyphicon-plus"></i> <?=lang('player.ui58')?></a>
                        <a href="javascript:void(0);" onclick="modal('/player_management/addPlayerBankInfo/<?=$player['playerId']?>/0','<?=lang('player.ui57')?>')" class="btn btn-success btn-xs pull-right" style="margin-right:2px;"><i class="input-xs glyphicon glyphicon-plus"></i> <?=lang('player.ui57')?></a>
                    <?php }
                    ?>
                </h4>
            </div>

            <div class="panel panel-body" id="bank_panel_body">
                <div class="row">
                    <div class="col-md-12">
                        <label><?=lang('player.ui34')?>: </label>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="bankInfoDepositTable" style="margin: 0px 0 0 0; width: 100%;">
                                <thead>
                                    <th></th>
                                    <th><?=lang('player.ui35')?></th>
                                    <th><?=lang('player.ui36')?></th>
                                    <th><?=lang('player.ui37')?></th>
                                    <th><?=lang('Province')?></th>
                                    <th><?=lang('City')?></th>
                                    <th><?=lang('Branch')?></th>
                                    <th><?=lang('player.ui38')?></th>
                                    <th><?=lang('Verify Status')?></th>
                                    <th><?=lang('lang.status')?></th>
                                    <th><?=lang('lang.action')?></th>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($deposit_bankdetails)) {
                                    $default = false;
                                    if (!empty($deposit_bankdetails)) {
                                        $default = $this->player_manager->checkIfValueExists($deposit_bankdetails, 'isDefault', '1');
                                    }

                                    foreach ($deposit_bankdetails as $key => $value) { ?>
                                        <tr>
                                            <td></td>
                                            <td><?=lang($value['bankName'])?></td>
                                            <td><?=$value['bankAccountFullName']?></td>
                                            <td><?=$value['bankAccountNumber']?></td>
                                            <td><?=$value['province']?></td>
                                            <td><?=$value['city']?></td>
                                            <td><?=$value['branch']?></td>
                                            <td><?=$value['bankAddress'] == '' ? lang('lang.norecord') : $value['bankAddress']?></td>
                                            <td>
                                                <?php if($value['verified'] == 0 && $this->permissions->checkPermissions('set_financial_account_to_verified')):?>
                                                    <a onclick="verifyFinancialAccount('<?=$value['playerBankDetailsId']?>', '<?=$player['playerId']?>')">
                                                        <?=lang('Set to Verified')?>
                                                    </a>
                                                <?php elseif($value['verified'] == 0):?>
                                                    <?=lang('Unverified')?>
                                                <?php elseif($value['verified'] == 1):?>
                                                    <span class="text-success"><b><?=lang('Verified')?></b></span>
                                                <?php endif;?>
                                            </td>
                                            <td><?=($value['status'] == 0) ? lang('lang.active') : lang('Blocked')?></td>
                                            <td>
                                                <?php if ($value['isDefault'] == 0 && $default == false && $value['status'] == 0) {?>
                                                    <a
                                                        class="btn btn-primary btn-xs disabled-deposit-btn"
                                                        href="<?=site_url('player_management/playerBankInfoSetDefault/' . $value['playerBankDetailsId'] . '/1/' . $player['playerId'])?>"
                                                        <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?> onclick="return disabledBankDefaultBtn(<?=$value['dwBank']?>);"
                                                    ><?=lang('player.ui55')?></a>
                                                <?php } elseif ($value['isDefault'] == 1) {?>
                                                    <a
                                                        class="btn btn-danger btn-xs"
                                                        href="<?=site_url('player_management/playerBankInfoSetDefault/' . $value['playerBankDetailsId'] . '/0/' . $player['playerId'])?>"
                                                        <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                    ><?=lang('player.ui56')?></a>
                                                <?php } ?>

                                                <?php if ($this->permissions->checkPermissions('edit_player_bank_info')):?>
                                                    <a
                                                        class="btn btn-info btn-xs"
                                                        href="javascript:void(0);"
                                                        onclick="modal('/player_management/editPlayerBankInfo/<?=$value['playerBankDetailsId']?>','<?=lang('lang.edit') . ' ' . lang('player.ui07')?>')"
                                                        <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                    ><?=lang('lang.edit')?></a>
                                                <?php endif; ?>

                                                <?php if ($this->permissions->checkPermissions('enable_disable_player_bank_info')):?>
                                                    <?php if ($value['status'] == 0) {?>
                                                        <a
                                                            class="btn btn-danger btn-xs"
                                                            href="<?=site_url('player_management/playerBankInfoChangeStatus/' . $value['playerBankDetailsId'] . '/1/' . $player['playerId'])?>"
                                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                        ><?=lang('lang.deactivate')?></a>
                                                    <?php } else {?>
                                                        <a
                                                            class="btn btn-warning btn-xs"
                                                            href="<?=site_url('player_management/playerBankInfoChangeStatus/' . $value['playerBankDetailsId'] . '/0/' . $player['playerId'])?>"
                                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                        ><?=lang('lang.activate')?></a>
                                                    <?php } ?>
                                                <?php endif; ?>

                                                <?php if ($this->permissions->checkPermissions('delete_player_bank_info')):?>
                                                    <a
                                                        class="btn btn-danger btn-xs"
                                                        href="#"
                                                        onclick="deletePlayerBankInfo(<?=$value['playerBankDetailsId']?>, '<?=lang($value['bankName'])?>', '<?=$value['playerId']?>');"
                                                        <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                    ><?=lang('lang.delete')?></a>
                                                <?php endif; ?>
                                            </td>
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

                <br/>

                <div class="row">
                    <div class="col-md-12">
                        <label><?=lang('player.ui39')?>: </label>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="bankInfoWithdrawalTable" style="margin: 0px 0 0 0; width: 100%;">
                                <thead>
                                    <th></th>
                                    <th><?=lang('player.ui35')?></th>
                                    <th><?=lang('player.ui36')?></th>
                                    <th><?=lang('player.ui37')?></th>
                                    <th><?=lang('Province')?></th>
                                    <th><?=lang('City')?></th>
                                    <th><?=lang('Branch')?></th>
                                    <th><?=lang('player.ui38')?></th>
                                    <th><?=lang('Verify Status')?></th>
                                    <th><?=lang('lang.status')?></th>
                                    <th><?=lang('lang.action')?></th>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($withdrawal_bankdetails)) {
                                    $default = false;
                                    if (!empty($withdrawal_bankdetails)) {
                                        $default = $this->player_manager->checkIfValueExists($withdrawal_bankdetails, 'isDefault', '1');
                                    }

                                    foreach ($withdrawal_bankdetails as $key => $value) {?>
                                        <tr>
                                            <td></td>
                                            <td><?=lang($value['bankName'])?></td>
                                            <td><?=$value['bankAccountFullName']?></td>
                                            <td><?=$value['bankAccountNumber']?></td>
                                            <td><?=$value['province']?></td>
                                            <td><?=$value['city']?></td>
                                            <td><?=$value['branch']?></td>
                                            <td><?=$value['bankAddress'] == '' ? lang('lang.norecord') : $value['bankAddress']?></td>
                                            <td>
                                                <?php if($value['verified'] == 0 && $this->permissions->checkPermissions('set_financial_account_to_verified')):?>
                                                    <a onclick="verifyFinancialAccount('<?=$value['playerBankDetailsId']?>', '<?=$player['playerId']?>')">
                                                        <?=lang('Set to Verified')?>
                                                    </a>
                                                <?php elseif($value['verified'] == 0):?>
                                                    <?=lang('Unverified')?>
                                                <?php elseif($value['verified'] == 1):?>
                                                    <span class="text-success"><b><?=lang('Verified')?></b></span>
                                                <?php endif;?>
                                            </td>
                                            <td><?=($value['status'] == 0) ? lang('lang.active') : lang('Blocked')?></td>
                                            <td>
                                                <?php if ($value['isDefault'] == 0 && $default == false) {?>
                                                    <a
                                                        class="btn btn-primary btn-xs disabled-withdrawal-btn"
                                                        href="<?=site_url('player_management/playerBankInfoSetDefault/' . $value['playerBankDetailsId'] . '/1/' . $player['playerId'] . '/1')?>"
                                                        <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?> onclick="return disabledBankDefaultBtn(<?=$value['dwBank']?>);"
                                                    ><?=lang('player.ui55')?></a>
                                                <?php } elseif ($value['isDefault'] == 1) {?>
                                                    <a
                                                        class="btn btn-danger btn-xs"
                                                        href="<?=site_url('player_management/playerBankInfoSetDefault/' . $value['playerBankDetailsId'] . '/0/' . $player['playerId'] . '/1')?>"
                                                        <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                    ><?=lang('player.ui56')?></a>
                                                <?php } ?>

                                                <?php if ($this->permissions->checkPermissions('edit_player_bank_info')):?>
                                                    <a
                                                        class="btn btn-info btn-xs"
                                                        href="javascript:void(0);"
                                                        onclick="modal('/player_management/editPlayerBankInfo/<?=$value['playerBankDetailsId']?>','<?=lang('lang.edit') . ' ' . lang('player.ui07')?>')"
                                                        <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                    ><?=lang('lang.edit')?></a>
                                                <?php endif; ?>

                                                <?php if ($this->permissions->checkPermissions('enable_disable_player_bank_info')):?>
                                                    <?php if ($value['status'] == 0) {?>
                                                        <a
                                                            class="btn btn-danger btn-xs"
                                                            href="<?=site_url('player_management/playerBankInfoChangeStatus/' . $value['playerBankDetailsId'] . '/1/' . $player['playerId'])?>"
                                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                        ><?=lang('lang.deactivate')?></a>
                                                    <?php } else {?>
                                                        <a
                                                            class="btn btn-warning btn-xs"
                                                            href="<?=site_url('player_management/playerBankInfoChangeStatus/' . $value['playerBankDetailsId'] . '/0/' . $player['playerId'])?>">
                                                            <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                            <?=lang('lang.activate')?></a>
                                                    <?php } ?>
                                                <?php endif; ?>

                                                <?php if ($this->permissions->checkPermissions('delete_player_bank_info')):?>
                                                    <a
                                                        class="btn btn-danger btn-xs"
                                                        href="#"
                                                        onclick="deletePlayerBankInfo(<?=$value['playerBankDetailsId']?>, '<?=lang($value['bankName'])?>', '<?=$value['playerId']?>');"
                                                        <?=($value['verified'] == 1) ? '' : 'disabled="disabled"'?>
                                                    ><?=lang('lang.delete')?></a>
                                                <?php endif; ?>
                                            </td>
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
    </div>
    <div class="col-md-7" id="player_details" style="display: none;"></div>
</div>
<!-- ################################################################## END Bank Information ################################################################## -->

<!-- ################################################################## START Responsible Gaming Info ################################################################## -->
<?php include dirname(__FILE__) . '/user_information/responsible_gaming_details.php'; ?>
<!-- ################################################################## END Responsible Gaming Info ################################################################## -->

<!-- ################################################################## START Communication Preference Info ################################################################## -->
<?php if ($this->permissions->checkPermissions('player_communication_preference') && $this->utils->isEnabledFeature('enable_communication_preferences')) { ?>
    <div class="panel panel-primary" id="communication_preferences_form">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a href="#log_info" id="hide_comm_pref_info" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-chevron-up" id="hide_comm_pref_up"></i></a>
                <strong><?=lang('Communication Preference')?></strong>
            </h4>
        </div>
        <div class="panel panel-body" id="comm_pref_panel_body">
            <div class="col-md-12">
                <form class="form-inline">
                <?php foreach ($config_comm_pref as $comm_pref_key => $comm_pref_value): ?>
                    <?php
                        $value = lang('No');
                        $bool_value = "true";
                        if($current_comm_pref && isset($current_comm_pref->$comm_pref_key) && $current_comm_pref->$comm_pref_key == "true") {
                            $value = lang('Yes');
                            $bool_value = "false";
                        };
                     ?>

                    <div class="form-group col-md-3" style="margin-bottom: 10px" >
                        <label ><?=lang($comm_pref_value)?>: </label>
                        <input type="text" value="<?=$value?>" class="form-control" style="width: 5em" disabled>
                        <?php if ($this->permissions->checkPermissions('edit_player_communication_preference') && !$hide_comm_pref_btn): ?>
                        <button class="btn btn-primary btn-xs form-control pref-data-<?=$comm_pref_key?>" data-key="pref-data-<?=$comm_pref_key?>" data-value="<?=$bool_value?>"><?=$value == lang('Yes') ? lang('Cancel') : lang('Add as preference')?></button>
                        <?php endif ?>
                    </div>


                <?php endforeach ?>

                </form>
            </div>
            <div class="comm-pref-text"><?=lang("Communication Preference can't be changed when the player has Self-exclusion or Time out limit")?></div>
        </div>
        <div class="panel-footer"></div>
    </div>

    <div class="modal fade in" id="comm_pref_notes" tabindex="-1" role="dialog" aria-labelledby="label_comm_pref_notes">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="label_comm_pref_notes"><?=lang('Notes')?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <textarea name="comm_pref_notes" id="comm_pref_notes_content" class="form-control input-sm" rows="7" placeholder="<?=lang('Add notes')?>..."></textarea>
                        <span class="help-block" style="color:#F04124"></span>
                    </div>
                    <button data-value="" data-key="" class="btn btn-primary pull-right" id="comm_pref_submit"><?=lang('lang.submit')?></button>
                    <div class="clearfix"></div>
                </div>

            </div>
        </div>
    </div>
<?php }?>
<!-- ################################################################## END Communication Preference Info ################################################################## -->

<!-- ################################################################## START Member's Logs ################################################################## -->
<div class="panel panel-primary" id="players_form">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a href="#log_info" id="hide_log_info" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-chevron-up" id="hide_log_up"></i></a>
            <strong><?=lang('player.ui08')?></strong>
        </h4>
    </div>
    <div class="panel panel-body" id="log_panel_body" style="margin-bottom: 0;">
        <ul class="nav nav-tabs">
            <?php if ($this->utils->isEnabledFeature('deposit_withdraw_transfer_list_on_player_info')): ?>
                <li><a href="#" data-load="/player_management/depositHistory/" data-callback="depositHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Deposit List'); ?></a></li>
                <li><a href="#" data-load="/player_management/withdrawHistory/" data-callback="withdrawHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Withdraw List'); ?></a></li>
                <li><a href="#" data-load="/player_management/transferHistory/" data-callback="transferHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Transfer List'); ?></a></li>
            <?php endif; ?>
            <li><a href="#" data-load="/player_management/balance_history/" data-callback="balance_history" data-params='{"player_id":<?=$player_id?>}'><?=lang('Balance History'); ?></a></li>
            <?php if (!$this->utils->isEnabledFeature('hide_old_adjustment_history')): ?>
                <li><a href="#" data-load="/player_management/adjustment_history_tab/" data-callback="adjustmentHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Old Adjustment History')?></a></li>
            <?php endif; ?>
            <li><a href="#" data-load="/player_management/adjustment_history_tab_v2/" data-callback="adjustmentHistoryV2" data-params='{"player_id":<?=$player_id?>}'><?=lang('pay.adjustHistory')?></a></li>
            <?php if ($this->permissions->checkPermissions('transaction_report')): ?>
                <li><a href="#" data-load="/player_management/transactionHistory/" data-callback="transactionHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('pay.transhistory')?></a></li>
            <?php endif; ?>
            <?php if ($this->permissions->checkPermissions('view_player_update_history')): ?>
                <li><a href="#" data-load="/player_management/personalHistory/<?=$player_id?>" data-callback="personalHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('member.playerUpdateHistory')?></a></li>
            <?php endif ?>
            <li><a href="#" data-load="/player_management/bankHistory/" data-callback="bankHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('player.ui41')?></a></li>
            <li><a href="#" data-load="/player_management/promoStatus/" data-callback="promoStatus" data-params='{"player_id":<?=$player_id?>}'><?=lang('player.ui49')?></a></li>
            <?php if ($this->permissions->checkPermissions('gamelogs')): ?>
                <li><a href="#" data-load="/player_management/gamesHistory/" data-callback="gamesHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('player.ui48')?></a></li>
                <li><a href="#" data-load="/player_management/gamesHistory/2" data-callback="gamesHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Unsettle Game History'); ?></a></li>
            <?php endif; ?>
            <li><a href="#" data-load="/player_management/friendReferralStatus/" data-callback="friendReferralStatus" data-params='{"player_id":<?=$player_id?>}'><?=lang('player.friendReferralStatus')?></a></li>
            <li><a href="#" data-load="/player_management/chatHistory/" data-callback="chatHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('cs.messagehistory')?></a></li>
            <li><a href="#" data-load="/player_management/ipHistory/" data-callback="ipHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('player.ui75')?></a></li>
            <li><a href="#" data-load="/player_management/dupAccounts/" data-callback="dupAccounts" data-params='{"player_id":<?=$player_id?>}'><?=lang('pay.duplicateAccountList')?></a></li>
            <?php if ($this->utils->isEnabledFeature('linked_account') && $this->permissions->checkPermissions('linked_account')): ?>
                <li><a href="#" data-load="/player_management/linked_account/<?=$player_id?>" data-callback="linkedAccount" data-params='{"player_id":<?=$player_id?>}'><?=lang('Linked Account')?></a></li>
            <?php endif ?>
            <li><a href="#" data-load="/player_management/cancelled_withdrawal/" data-callback="cancelledWithdrawalCondition" data-params='{"player_id":<?=$player_id?>}'><?=lang('Withdrawal Condition History')?></a></li>
            <?php if($this->utils->isEnabledFeature('enabled_transfer_condition')): ?>
                <li><a href="#" data-load="/player_management/cancelledTransferCondition/" data-callback="cancelledTransferCondition" data-params='{"player_id":<?=$player_id?>}'><?=lang('Transfer Condition History')?></a></li>
            <?php endif;?>
            <?php if ($this->utils->isEnabledFeature('show_kyc_status') && $this->permissions->checkPermissions('view_kyc_history')):?>
                <li><a href="#" data-load="/player_management/kyc_history/" data-callback="kycHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('KYC History')?></a></li>
            <?php endif; ?>
            <?php if ($this->utils->isEnabledFeature('show_risk_score') && $this->permissions->checkPermissions('view_risk_score_history')): ?>
                <li><a href="#" data-load="/player_management/risk_score_history/" data-callback="riskScoreHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Risk Score History')?></a></li>
            <?php endif; ?>
            <li><a href="#" data-load="/player_management/rgHistory/" data-callback="rgHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Responsible Gaming History Log')?></a></li>
            <?php if ($this->permissions->checkPermissions('player_communication_preference') && $this->utils->isEnabledFeature('enable_communication_preferences')): ?>
                <li><a href="#" data-load="/player_management/communicationPreferenceHistory/" data-callback="communicationPreferenceHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('Communication Preference History')?></a></li>
            <?php endif; ?>
        </ul>
        <div id="changeable_table" style="margin-top: 30px;"></div>
    </div>
    <div class="panel-footer"></div>
</div>
<!-- ################################################################## END Member's Logs ################################################################## -->
<div id="msg-conf-modal" class="modal fade bs-example-modal-md" data-backdrop="static" tabindex="-1" role="dialog" data-keyboard="false" aria-hidden="true"  >
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header panel-heading">
            <button type="button" class="close" id="x-cancel-action" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?=lang('sys.user.bank.edit.title');?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="help-block" id="conf-msg"><?=lang('sys.user.bank.edit.content');?></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirm-action" class="btn btn-primary"><?=lang('aff.ok');?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#bankInfoDepositTable').DataTable({
            dom:"<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                }
            ],
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        });

        $('#bankInfoWithdrawalTable').DataTable({
            dom:"<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                }
            ],
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        });

        <?php if ($this->permissions->checkPermissions('reset_player_login_password') && $this->utils->getConfig('enabled_show_password')) {?>
            $('#_password_label').dblclick(function(){
                alert("<?php echo $hide_password; ?>");
            });
        <?php }?>

        // -- Communication Preference button behavior
        $('button[data-key^="pref-data-"]').click(function(e){
            e.preventDefault();

            var $key = $(this).data('key');
            var $value = $(this).data('value');

            $('#comm_pref_submit').data('value',$value).data('key',$key);
            $('#comm_pref_notes').modal('show');
        });

        $('#comm_pref_submit').click(function(e){

            if ($.trim($('#comm_pref_notes_content').val()) == '') {
                $('#comm_pref_notes_content').next('span').html('<?=lang("cu.7")?> is required');
                return false;
            } else {
                $('#comm_pref_notes_content').next('span').html('');
            }

            var $key = $(this).data('key');
            var $value = $(this).data('value');
            var $tmp_text_val = "<?=lang('Yes')?>";
            var $tmp_button_val = "false";
            var $tmp_button_text = "<?=lang('Cancel')?>";
            var post_data = {
                player_id: <?=$player_id?>,
                notes: $('#comm_pref_notes_content').val(),
            };

            post_data[$key] = $value;

            if($value == false || $value == "false"){
                $tmp_text_val = "<?=lang('No')?>";
                $tmp_button_val = "true";
                $tmp_button_text = "<?=lang('Add as preference')?>";
            }

            $(this).prop('disabled',true);

            $.post("<?php echo site_url('player_management/updateCommunicationPreference') ?>", post_data, function(data){

                $('#comm_pref_submit').prop('disabled',false);

                if(data.status != "success"){
                    alert(data.message);
                    return false;
                }

                alert(data.message);

                $('button[data-key="'+$key+'"]').prev().val($tmp_text_val);
                $('button[data-key="'+$key+'"]').data('value',$tmp_button_val);
                $('button[data-key="'+$key+'"]').html($tmp_button_text);
                $('button[data-key="'+$key+'"]').html($tmp_button_text);
                $('#comm_pref_notes_content').val('');
            });

            $('#comm_pref_notes').modal('hide');
        });



        //*******************ADD AFFILIATE START**************************************
        var ADD_AFFILIATE  = (function() {

            var add = $("#add-player-affiliate"),
                save = $("#save-player-affiliate"),
                cancel = $("#cancel-player-affiliate"),
                recordedAff = $("#recorded-affiliate"),
                affiliateOptions = $("#affiliates-options"),
                ajaxStatus = $("#ajax-status"),
                successGlyph = $("#success-glyph"),
                chosenAffiliate = "",
                GET_AFFILIATES_URL = '<?php echo site_url('affiliate_management/getAffiliates') ?>/',
                ADD_AFFILIATE_REF_URL = '<?php echo site_url('player_management/addPlayerAffiliateRef') ?>/',
                REFRESH_PAGE_URL = '<?php echo site_url('player_management/userInformation') ?>/<?=$player_id?>',
                affiliateOk = false,
                LANG = {
                    SELECT_AFFILIATE : '<?=lang('player.ui64')?>',
                    EDIT_TEXT : '<i class="glyphicon glyphicon-edit"></i>'+'<?php echo lang('Edit') ?>',
                    EDIT_TITLE : '<?=lang('aff.ai63')?>'
                };
            //initial settings
            closeEditForm();
            hideAjaxStatus();
            disableSaveButton();
            hideSuccessGlyph();
            add.click(function () {
                recordedAff.hide();
                showAjaxStatus();
                getAllAffiliates();

            });
            cancel.click(function () {
                closeEditForm();
                disableSaveButton();
                chosenAffiliate = "";
            });
            save.click(function () {
                addPlayerAffiliateRef();
                disableCancelButton();
            });

            affiliateOptions.on('change', function () {
                chosenAffiliate = affiliateOptions.val();
                ableSaveButton();
            });

            function getAllAffiliates() {
                hideSuccessGlyph();
                $.ajax({
                    url : GET_AFFILIATES_URL,
                    type : 'GET',
                    dataType : "json"
                }).done(function (data) {
                    removeOptions();
                    appendSelectPlaceholder();
                    var affiliates = data.data.affiliates,
                        affiliatesLength = affiliates.length;
                    for (var i = 0; i < affiliatesLength; i++) {
                        affiliateOptions.append('<option value="' + affiliates[i].affiliateId + '">' + affiliates[i].username + '</option>');
                    }
                    makeAffOptionsSearchable();
                    hideAjaxStatus();
                    showEditForm();
                }).fail(function (jqXHR, textStatus) {
                    window.location.href = REFRESH_PAGE_URL;
                });
            }

            function makeAffOptionsSearchable(){

                affiliateOptions.multiselect({
                    enableFiltering: true,
                    includeSelectAllOption: true,
                    selectAllJustVisible: false,
                    buttonWidth: '100%',


                    buttonText: function(options, select) {
                        if (options.length === 0) {
                            return '';
                        }
                        else {
                            var labels = [];
                            options.each(function() {
                                if ($(this).attr('label') !== undefined) {
                                    labels.push($(this).attr('label'));
                                }
                                else {
                                    labels.push($(this).html());
                                }
                            });
                            return labels.join(', ') + '';
                        }
                    }
                });
            }


            function addPlayerAffiliateRef() {

                if (!chosenAffiliate) {
                    return false;
                }
                disableSaveButton();

                var data = {
                    playerId : playerId,
                    affiliateId : chosenAffiliate
                };
                // console.log(data);
                $.ajax({
                    url : ADD_AFFILIATE_REF_URL,
                    type : 'POST',
                    data : data,
                    dataType : "json",
                    cache : false
                }).done(function (data) {
                    if (data.status == "success") {

                        recordedAff.html(data.data.affUsername)
                            .show();
                        closeEditForm();
                        // removeEditButton()
                        showSuccessGlyph();
                        ableSaveButton();
                        ableCancelButton();
                        changeAddButtonTextToEdit();
                        add.hide();

                    }
                    if (data.status == "error") {
                        ableSaveButton();
                        ableCancelButton();
                        alert("<?=lang('text.error')?>");

                    }

                }).fail(function (jqXHR, textStatus) {
                    window.location.href = REFRESH_PAGE_URL;
                });
            }

            function changeAddButtonTextToEdit(){
                add.html(LANG.EDIT_TEXT);
                add.attr('title', LANG.EDIT_TITLE);
                add.attr('data-original-title', LANG.EDIT_TITLE);
            }

            function showSuccessGlyph() {
                successGlyph.show();
            }

            function hideSuccessGlyph() {
                successGlyph.hide();
            }

            function removeEditButton() {
                add.remove();
            }

            function disableCancelButton() {
                cancel.prop('disabled', true);
            }

            function ableCancelButton() {
                cancel.prop('disabled', false);
            }

            function disableSaveButton() {
                save.prop('disabled', true);
            }

            function ableSaveButton() {
                save.prop('disabled', false);
            }

            function showAjaxStatus() {
                ajaxStatus.show();
            }

            function hideAjaxStatus() {
                ajaxStatus.hide();
            }

            function showEditForm() {
                affiliateOptions.parent().show();
                cancel.show();
                add.hide();
                // affiliateOptions.show();
                save.show();
            }

            function closeEditForm() {
                affiliateOptions.parent().hide();
                cancel.hide();
                save.hide();
                recordedAff.show();
                affiliateOptions.hide();
                add.show();
            }

            function removeOptions() {
                affiliateOptions.html("");
            }

            function appendSelectPlaceholder() {
                affiliateOptions.append('<option value="" selected disabled>'+LANG.SELECT_AFFILIATE+'</option>');
            }

        }());
        //////*******************ADD AFFILIATE END**************************************

        //////*******************ADJUST MEMBER LEVEL START**************************************
        var ADJUST_MEMBER_LEVEL = (function() {
            var adjLevel = $('#adjust-player-level'),
                levelsOptions = $('#levels-options'),
                save = $("#save-player-level"),
                cancel = $("#cancel-player-level"),
                recordedLevel =$("#recorded-level"),
                ajaxStatus =$("#ajax-status-level"),
                adjust_dispatch_account_Level = $('#adjust-dispatch-account-level'),
                dispatch_account_levels_options = $('#dispatch-account-levels-options'),
                save_dispatch_account = $("#save-dispatch-account-level"),
                cancel_dispatch_account = $("#cancel-dispatch-account-level"),
                recorded_dispatch_account_level = $("#recorded-dispatch-account-level"),
                ajax_dispatch_account_status =$("#ajax-dispatch-account-status-level"),

                GET_ALL_PLAYER_LEVELS_URL= '<?=site_url('player_management/getAllPlayerLevels');?>',
                GET_ALL_DISPATCH_ACCOUNT_LEVELS_URL= '<?=site_url('player_management/getAllDispatchAccountLevels');?>',
                ADJUST_PLAYER_LEVEL_URL= '<?=site_url('player_management/doAdjustPlayerLevelThruAjax');?>',
                ADJUST_DISPATCH_ACCOUNT_LEVEL_URL= '<?=site_url('player_management/doAdjustDispatchAccountLevelThruAjax');?>',
                successGlyph = $("#success-glyph-level"),
                success_dispatch_account_glyph = $("#success-dispatch-account-glyph-level"),
                chosenLevel = "",
                choser_dispatch_account_level = "",
                LANG = {
                    SELECT_LEVEL : "<?=lang('player.ui74')?>"
                };
            adjLevel.click(function () {
                getAllPlayerLevels();
                showEditForm();
                recordedLevel.hide();
                showAjaxStatus();
                hideSuccessGlyph();

            });
            cancel.click(function () {
                hideAjaxStatus();
                closeEditForm();
                disableSaveButton();
                chosenTag = "";
                chosenTagId ="";
            });
            save.click(function () {
                updatePlayerLevel();
                disableCancelButton();
            });

            levelsOptions.on('change', function () {
                chosenLevel=levelsOptions.val();
                ableSaveButton();
            });

            adjust_dispatch_account_Level.click(function () {
                getAllDispatchAccountLevels();
                showDispatchAccountEditForm();
                recorded_dispatch_account_level.hide();
                showAjaxDispatchAccountStatus();
                hideSuccessDispatchAccountGlyph();

            });
            cancel_dispatch_account.click(function () {
                hideAjaxDispatchAccountStatus();
                closeDispatchAccountEditForm();
                disableDispatchAccountSaveButton();
                chosenTag = "";
                chosenTagId ="";
            });
            save_dispatch_account.click(function () {
                updateDispatchAccountLevel();
                disableDispatchAccountCancelButton();
            });
            dispatch_account_levels_options.on('change', function () {
                choser_dispatch_account_level=dispatch_account_levels_options.val();
                ableDispatchAccountSaveButton();
            });

            //Initial settings
            closeEditForm();
            hideAjaxStatus();
            disableSaveButton();
            hideSuccessGlyph();

            closeDispatchAccountEditForm();
            hideAjaxDispatchAccountStatus();
            disableDispatchAccountSaveButton();
            hideSuccessDispatchAccountGlyph();

            function getAllPlayerLevels() {

                $.ajax({
                    url : GET_ALL_PLAYER_LEVELS_URL+'/'+playerId,
                    type : 'GET',
                    dataType : "json"
                }).done(function (data) {
                    removeOptions();
                    appendSelectPlaceholder();
                    var playerLevels = data.playerLevels,
                        currenPlayerLevel = data.currenPlayerLevel[0],
                        playerLevelsLength = playerLevels.length;
                    for (var i = 0; i < playerLevelsLength; i++) {
                        if(currenPlayerLevel && currenPlayerLevel.vipsettingcashbackruleId == playerLevels[i].vipsettingcashbackruleId){
                            levelsOptions.append('<option value="' + playerLevels[i].vipsettingcashbackruleId+ '" selected >' + playerLevels[i].groupName + ' - '+ playerLevels[i].vipLevelName +'    </option>');
                        }else{
                            levelsOptions.append('<option value="' + playerLevels[i].vipsettingcashbackruleId+ '" >' + playerLevels[i].groupName + ' - '+ playerLevels[i].vipLevelName +'    </option>');
                        }
                    }
                    hideAjaxStatus();
                    recordedLevel.show();
                    showEditForm();
                }).fail(function (jqXHR, textStatus) {
                    /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
                    if(jqXHR.status>=300 && jqXHR.status<500){
                        // location.reload();
                    }else{
                        alert(textStatus);
                    }
                });
            }

            function getAllDispatchAccountLevels() {

                $.ajax({
                    url : GET_ALL_DISPATCH_ACCOUNT_LEVELS_URL+'/'+playerId,
                    type : 'GET',
                    dataType : "json"
                }).done(function (data) {
                    removeDispatchAccountOptions();
                    appendDispatchAccountSelectPlaceholder();
                    var dispatch_account_levels = data.dispatch_account_levels,
                        current_disparch_account_level = data.current_disparch_account_level[0],
                        dispatch_account_levels_length = dispatch_account_levels.length;
                    for (var i = 0; i < dispatch_account_levels_length; i++) {
                        if(current_disparch_account_level && current_disparch_account_level.id == dispatch_account_levels[i].id){
                            dispatch_account_levels_options.append('<option value="' + dispatch_account_levels[i].id+ '" selected >' + dispatch_account_levels[i].group_name + ' - '+ dispatch_account_levels[i].level_name +'    </option>');
                        }else{
                            dispatch_account_levels_options.append('<option value="' + dispatch_account_levels[i].id+ '" >' + dispatch_account_levels[i].group_name + ' - '+ dispatch_account_levels[i].level_name +'    </option>');
                        }
                    }
                    hideAjaxDispatchAccountStatus();
                    recorded_dispatch_account_level.show();
                    showDispatchAccountEditForm();
                }).fail(function (jqXHR, textStatus) {
                    /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
                    if(jqXHR.status>=300 && jqXHR.status<500){
                        // location.reload();
                    }else{
                        alert(textStatus);
                    }
                });
            }

            function updatePlayerLevel() {

                if (!chosenLevel) {
                    return false;
                }
                disableSaveButton();

                var data = {
                    playerId : playerId,
                    newPlayerLevel: chosenLevel
                };
                $.ajax({
                    url : ADJUST_PLAYER_LEVEL_URL,
                    type : 'POST',
                    data : data,
                    dataType : "json",
                    cache : false
                }).done(function (data) {
                    if (data.status == "success") {
                        var currenPlayerLevel =data.currenPlayerLevel[0];
                        recordedLevel.html(currenPlayerLevel.groupName + ' - '+ currenPlayerLevel.vipLevelName);
                        closeEditForm();
                        ableCancelButton();
                        showSuccessGlyph();
                    }
                    if (data.status == "error") {
                        ableSaveButton();
                        ableCancelButton();
                        alert("<?=lang('text.error')?>");
                    }

                }).fail(function (jqXHR, textStatus) {
                    if(jqXHR.status>=300 && jqXHR.status<500){
                        // location.reload();
                    }else{
                        alert(textStatus);
                    }
                });
            }

            function showSuccessGlyph() {
                successGlyph.show();
            }

            function hideSuccessGlyph() {
                successGlyph.hide();
            }

            function removeEditButton() {
                addTag.remove();
            }

            function disableCancelButton() {
                cancel.prop('disabled', true);
            }

            function ableCancelButton() {
                cancel.prop('disabled', false);
            }

            function disableSaveButton() {
                save.prop('disabled', true);
            }

            function ableSaveButton() {
                save.prop('disabled', false);
            }

            function showAjaxStatus() {
                ajaxStatus.show();
            }

            function hideAjaxStatus() {
                ajaxStatus.hide();
            }

            function showEditForm() {
                cancel.show();
                adjLevel.hide();
                levelsOptions.show();
                save.show();
            }

            function closeEditForm() {
                levelsOptions.hide()
                cancel.hide();
                save.hide();
                recordedLevel.show();
                adjLevel.show();
            }

            function removeOptions() {
                levelsOptions.html("");
            }

            function appendSelectPlaceholder() {
                levelsOptions.append('<option value="" selected disabled>'+LANG.SELECT_LEVEL+'</option>');
            }

            function updateDispatchAccountLevel() {

                if (!choser_dispatch_account_level) {
                    return false;
                }
                disableDispatchAccountSaveButton();

                var data = {
                    playe_id : playerId,
                    new_dispatch_account_level: choser_dispatch_account_level
                };
                $.ajax({
                    url : ADJUST_DISPATCH_ACCOUNT_LEVEL_URL,
                    type : 'POST',
                    data : data,
                    dataType : "json",
                    cache : false
                }).done(function (data) {
                    if (data.status == "success") {
                        var current_disparch_account_level =data.current_disparch_account_level[0];
                        recorded_dispatch_account_level.html(current_disparch_account_level.group_name + ' - '+ current_disparch_account_level.level_name);
                        closeDispatchAccountEditForm();
                        ableDispatchAccountCancelButton();
                        showSuccessDispatchAccountGlyph();
                    }
                    if (data.status == "error") {
                        ableDispatchAccountSaveButton();
                        ableDispatchAccountCancelButton();
                        alert("<?=lang('text.error')?>");
                    }

                }).fail(function (jqXHR, textStatus) {
                    if(jqXHR.status>=300 && jqXHR.status<500){
                        // location.reload();
                    }else{
                        alert(textStatus);
                    }
                });
            }

            function showSuccessDispatchAccountGlyph() {
                success_dispatch_account_glyph.show();
            }

            function hideSuccessDispatchAccountGlyph() {
                success_dispatch_account_glyph.hide();
            }

            function disableDispatchAccountCancelButton() {
                cancel_dispatch_account.prop('disabled', true);
            }

            function ableDispatchAccountCancelButton() {
                cancel_dispatch_account.prop('disabled', false);
            }

            function disableDispatchAccountSaveButton() {
                save_dispatch_account.prop('disabled', true);
            }

            function ableDispatchAccountSaveButton() {
                save_dispatch_account.prop('disabled', false);
            }

            function showAjaxDispatchAccountStatus() {
                ajax_dispatch_account_status.show();
            }

            function hideAjaxDispatchAccountStatus() {
                ajax_dispatch_account_status.hide();
            }

            function showDispatchAccountEditForm() {
                cancel_dispatch_account.show();
                adjust_dispatch_account_Level.hide();
                dispatch_account_levels_options.show();
                save_dispatch_account.show();
            }

            function closeDispatchAccountEditForm() {
                dispatch_account_levels_options.hide();
                cancel_dispatch_account.hide();
                save_dispatch_account.hide();
                recorded_dispatch_account_level.show();
                adjust_dispatch_account_Level.show();
            }

            function removeDispatchAccountOptions() {
                dispatch_account_levels_options.html("");
            }

            function appendDispatchAccountSelectPlaceholder() {
                dispatch_account_levels_options.append('<option value="" selected disabled>'+LANG.SELECT_LEVEL+'</option>');
            }
        }());
        //////*******************ADJUST MEMBER LEVEL END**************************************

        //////*******************PLAYER TAGGING START**************************************
        var PLAYER_TAGGING = (function () {
            var playerTaggedInfo = $('#player-tagged-info'),
                playerTaggedForm = $('#player-tagged-form'),
                addTag = $('#add-player-tag'),
                playerTaggedInput = $('#tags-options'),
                tagList = $('#tags-list'),
                save = $("#save-player-tag"),
                cancel = $("#cancel-player-tag"),
                recordedTag = $("#recorded-tag"),
                ajaxStatus = $("#ajax-status-tag"),
                GET_ALLTAGS_URL = '<?=site_url('player_management/getAllTags') ?>',
                TAG_PLAYER_URL = '<?=site_url('player_management/tagPlayer') ?>',
                successGlyph = $("#success-glyph-tag"),
                tag_list = <?=($tag_list) ? json_encode($tag_list) : "{}" ?>,
                chosenTag = <?=($taggedStatus) ? json_encode($taggedStatus) : "[]" ?>,
                not_tagged_text = "<?=lang('player.tp12')?>",
                has_playerTagged = "<?=($taggedStatus) ? 0 : 1?>";

            LANG = {
                SELECT_TAG: "<?=lang('player.ui72')?>",
                NO_TAG: "<?=lang('player.ui73')?>"
            };

            addTag.click(function () {
                getAllTags();
                showAjaxStatus();
                hideSuccessGlyph();
            });

            cancel.click(function () {
                hideAjaxStatus();
                closeEditForm();
                disableSaveButton();
            });

            save.click(function () {
                addUpdateTag();
                disableCancelButton();
            });

            tagList.on('change', function () {
                var option = $(this).find('option:selected');

                playerTaggedInput.tagsinput('add', {id: tagList.val(), text: option.text(), color: option.data('color')});
                ableSaveButton();
            });

            playerTaggedInput.on('itemRemoved', function(){
                ableSaveButton();
            });

            //Initial settings
            closeEditForm();
            hideAjaxStatus();
            disableSaveButton();
            hideSuccessGlyph();
            renderPlayerTagged();

            function getAllTags() {
                $.ajax({
                    url: GET_ALLTAGS_URL + '/' + playerId,
                    type: 'GET',
                    dataType: "json"
                }).done(function (data) {
                    removeOptions();
                    playerTaggedInput.tagsinput('removeAll');

                    appendSelectPlaceholder();
                    var tags = data.tags,
                        tagStatus = data.tagStatus,
                        tagsLength = tags.length;

                    for (var i = 0; i < tagsLength; i++) {
                        tagList.append('<option value="' + tags[i].tagId + '" data-color="' + tags[i].tagColor + '">' + tags[i].tagName + '</option>');
                        if(tagStatus && ($.inArray(tags[i].tagId, tagStatus) >= 0)){
                            playerTaggedInput.tagsinput('add', {id: tags[i].tagId, text: tags[i].tagName, color: tags[i].tagColor});
                        }
                    }

                    hideAjaxStatus();
                    showEditForm();
                }).fail(function (jqXHR, textStatus) {
                    if (jqXHR.status >= 300 && jqXHR.status < 500) {
                        // location.reload();
                    } else {
                        alert(textStatus);
                    }
                });
            }

            function addUpdateTag() {
                disableSaveButton();

                var select_items = playerTaggedInput.tagsinput('items');
                var tagIds = [];
                $.each(select_items, function(id, item){
                    tagIds.push(item['id']);
                });

                var data = {
                    playerId: playerId,
                    tagId: tagIds
                };

                $.ajax({
                    url: TAG_PLAYER_URL,
                    type: 'POST',
                    data: data,
                    dataType: "json",
                    cache: false
                }).done(function (data) {
                    if (data.status == "success") {
                        closeEditForm();
                        ableCancelButton();
                        showSuccessGlyph();
                        renderPlayerTagged(data.tagStatus);
                    }else{
                        ableSaveButton();
                        ableCancelButton();
                        alert("<?=lang('text.error')?>");
                    }
                }).fail(function (jqXHR, textStatus) {
                    /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
                    if (jqXHR.status >= 300 && jqXHR.status < 500) {
                        // location.reload();
                    } else {
                        alert(textStatus);
                    }
                });
            }

            function showSuccessGlyph() {
                successGlyph.show();
            }

            function hideSuccessGlyph() {
                successGlyph.hide();
            }

            function removeEditButton() {
                addTag.remove();
            }

            function disableCancelButton() {
                cancel.prop('disabled', true);
            }

            function ableCancelButton() {
                cancel.prop('disabled', false);
            }

            function disableSaveButton() {
                save.prop('disabled', true);
            }

            function ableSaveButton() {
                save.prop('disabled', false);
            }

            function showAjaxStatus() {
                ajaxStatus.show();
            }

            function hideAjaxStatus() {
                ajaxStatus.hide();
            }

            function showEditForm() {
                addTag.hide();
                recordedTag.hide();

                tagList.show();
                playerTaggedInput.show();
                save.show();
                cancel.show();

                playerTaggedInfo.hide();
                playerTaggedForm.show();
            }

            function closeEditForm() {
                tagList.hide()
                playerTaggedInput.hide();
                save.hide();
                cancel.hide();

                recordedTag.show();
                addTag.show();

                playerTaggedInfo.show();
                playerTaggedForm.hide();
            }

            function removeOptions() {
                tagList.html("");
            }

            function appendSelectPlaceholder() {
                tagList.append('<option value="" selected disabled>' + LANG.SELECT_TAG + '</option>');
            }

            function renderPlayerTagged(tagStatus){
                if(tagStatus === undefined){
                    tagStatus = chosenTag;
                }
                if(!$.isArray(tagStatus)){
                    recordedTag.html(not_tagged_text);
                    return;
                }

                recordedTag.html('');
                $.each(tagStatus, function(idx, tagId){
                    $.each(tag_list, function(idx2, tagEntry){
                        //console.log(tagEntry,tagId);
                        if(tagEntry['tagId'] !== tagId){
                            return;
                        }
                        var span = $('<span>').addClass('tag label label-info');
                        span.text(tagEntry['tagName']).css('background-color', tagEntry['tagColor']);

                        var a = $('<a>').addClass('tag tag-component').attr('href', '<?=$this->utils->getSystemUrl('admin')?>/player_management/taggedlist?tag=' + tagId + '&search_reg_date=false');
                        recordedTag.append(a.append(span));
                    });
                });
            }
        }());
        //////*******************PLAYER TAGGING END**************************************
    });//End document ready

    function checkStatus(username,gameId,playerId) {
        $('#btn-checkStatus').text("<?=lang('text.loading')?>").prop('disabled', true);
        $.post("<?=site_url('player_management/is_online/')?>/" + gameId + "/" + username + "/" + playerId, function(data) {
            $('#game-' + gameId).html(data);
        });
    }

    function verifyFinancialAccount(bank_details_id, player_id){
        if(confirm('<?=lang('Set Financial Account to Verified?')?>')){
            window.location = base_url + "player_management/playerBankInfoSetToVerified/" + bank_details_id + "/" + player_id;
        }
    }

    function deletePlayerBankInfo(bank_details_id, bankname, player_id) {
        if (confirm("<?=lang('sys.gd4')?>" + bankname + '?')) {
            window.location = base_url + "player_management/deletePlayerBankInfo/" + bank_details_id + "/" + player_id;
        }
    }

    // ------------------------------------------------ START CHANGEABLE TABLE SECTION ------------------------------------------------ //
    $(function () {
        $('[data-load]').click(function (e) {

            e.preventDefault();
            var el = $(this);
            var url = el.data('load');
            var params = el.data('params');
            var callback = el.data('callback');
            var player_id = params['player_id'];

            if (el.parent('li').hasClass('active')) {
                return;
            }

            $('#changeable_table').load(url, params, function (data) {

                if (el.parent('ul.nav')) {
                    el.parents('ul.nav').find('li').removeClass('active');
                    el.parent('li').addClass('active');
                }

                $('#changeable_table .dateInput').each(function () {
                    initDateInput($(this));
                });

                if (callback) {
                    eval(callback)(player_id);
                }
            });
        });
    });

    function kickout(){
        var url="<?=site_url('player_management/kickPlayer/'. $player['playerId'])?>";
        if(confirm("<?php echo lang('confirm.request'); ?>")){
            window.location.href=url;
        }
    }

    function updatePhoneStatusToVerified(){
        var url="<?=site_url('player_management/updatePhoneStatusToVerified/' . $player['playerId'])?>";
        if(confirm("<?php echo lang('confirm.request'); ?>")){
            window.location.href=url;
        }
    }

    function updateEmailStatusToVerified(){
        var url="<?=site_url('player_management/updateEmailStatusToVerified/' . $player['playerId'])?>";
        if(confirm("<?php echo lang('confirm.request'); ?>")){
            window.location.href=url;
        }
    }

    function sendEmailVerification(){
        var url="<?=site_url('player_management/sendEmailVerification/' . $player['playerId'])?>";
        if(confirm("<?php echo lang('confirm.request'); ?>")){
            window.location.href=url;
        }
    }

    function sendSMSVerification(){
        var url="<?=site_url('player_management/sendSMSVerification/' . $player['playerId'])?>";
        if(confirm("<?php echo lang('confirm.request'); ?>")){
            window.location.href=url;
        }
    }

    /**
     * Generates referral code for players with empty referral codes
     *
     * @param  {int} player_id
     * @return void
     */
    function generateReferralCode(player_id){
        if($.trim($('#invitation-code-container').text()) != '' || $.trim($('#invitation-code-container').text()) != "0"){

            var url = '/player_management/generateReferralCode/'+player_id;

            if(confirm("<?php echo lang('Are you sure you want to continue?'); ?>")){
                $.ajax({
                    url: url,
                    beforeSend: function() {
                        $('#generate-referral-code').text("<?=lang('text.loading')?>").attr('disabled','true');
                    },
                    success: function(result){

                        if(result['status'] == "1"){
                            BootstrapDialog.show({
                                type: BootstrapDialog.TYPE_SUCCESS,
                                message: result['message'],
                                onhide: function(){
                                    $('#invitation-code-container').text(result['data']);
                                    $('#generate-referral-code').hide();
                                }
                            });
                        }else{
                            BootstrapDialog.show({
                                type: BootstrapDialog.TYPE_DANGER,
                                message: result['message'],
                            });
                        }

                        $('#generate-referral-code').text("<?=lang('Generate Referral Code')?>").removeAttr('disabled');
                    },
                    error: function(){
                         BootstrapDialog.danger({
                            "type": BootstrapDialog.TYPE_DANGER,
                            "message": '<?=lang('error.default.db.message')?>',
                            "onhide": function(){
                                window.location.reload(true);
                            }
                        });
                    }
                });
            }
        }
        else{
            alert("<?=lang('referral_code_exists')?> ");
            return;
        }
    }

    // ------------------------------------------------ END CHANGEABLE TABLE SECTION ------------------------------------------------ //
    function player_notes(player_id) {
        var dst_url = "/player_management/player_notes/" + player_id;
        open_modal('player_notes', dst_url, "<?php echo lang('Player Remarks'); ?>");
    }

    function disabledBankDefaultBtn(dwBank){
        //dwBank = 0 deposit
        //dwBank = 1 withdrawal
        if(dwBank == '0'){
            $('.disabled-deposit-btn').addClass('disabled');
        }else if(dwBank == '1'){
            $('.disabled-withdrawal-btn').addClass('disabled');
        }
        return true;
    }
</script>
