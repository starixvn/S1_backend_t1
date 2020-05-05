<div class="col-md-12 player-infomations">
    <h3 class="player-title"><i class="fa fa-user"></i> <?=$player['username']?></h3>

    <!-- Action -->
    <div class="row" style="margin:6px;">
        <fieldset style="padding:6px;">
            <legend>
                <h4><b><?=lang('player.ap01');?></b></h4>
            </legend>
            <div class="col-md-5 actions-left">
                <?php if ($this->permissions->checkPermissions('lock_player')) {?>
                    <?php $playerStatus = $this->utils->getPlayerStatus($player['playerId']); ?>
                    <?php if ($playerStatus == 0) {?>
                        <a href="javascript:void(0);"
                            data-toggle="tooltip"
                            data-original-title="<?=lang('role.25')?>"
                            class="btn btn-sm btn-danger block blockAction"
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
                            class="btn btn-scooter btn-sm unblock blockAction"
                            onclick="return confirm('<?=lang("player.confirm.unblock")?>');"
                        >
                            <i class="fa fa-unlock-alt"></i>
                            <?=lang('tool.pm09')?>
                        </a>
                    <?php } elseif ($playerStatus == Player_model::SUSPENDED_STATUS) { ?>
                        <a href="/player_management/unblockPlayer/<?=$player['playerId']?>"
                            class="btn btn-scooter btn-sm unblock blockAction"
                            onclick="return confirm('<?=lang("player.confirm.unblock")?>');"
                        >
                            <i class="fa fa-unlock-alt"></i>
                            <?=lang('tool.unsuspended')?>
                        </a>
                    <?php } elseif ($playerStatus == Player_model::STATUS_BLOCKED_FAILED_LOGIN_ATTEMPT) { ?>
                        <a href="/player_management/unblockPlayer/<?=$player['playerId']?>"
                            class="btn btn-scooter btn-sm unblock blockAction"
                            onclick="return confirm('<?=lang("Are you sure you want to reset the login attempt?")?>');"
                        >
                            <i class="fa fa-unlock-alt"></i>
                            <?=lang('Reset Login Attempt')?>
                        </a>
                    <?php } ?>
                <?php }?>

                <?php if ($this->permissions->checkPermissions('disable_cashback')) {
                    if ($player['disabled_cashback'] == 1) {?>
                        <a href="<?=site_url('player_management/set_cashback_status/' . $player['playerId'] . '/enable')?>" class="btn btn-scooter btn-sm">
                            <i class="fa fa-thumbs-up"></i> <?=lang('Enable Cashback')?>
                        </a>
                    <?php } else {?>
                        <a href="<?=site_url('player_management/set_cashback_status/' . $player['playerId'] . '/disable')?>" class="btn btn-danger btn-sm">
                            <i class="fa fa-thumbs-down"></i> <?=lang('Disable Cashback')?>
                        </a>
                    <?php
                    }
                } ?>

                <?php if ($this->permissions->checkPermissions('disable_promotion')) {
                    if ($player['disabled_promotion'] == 1) {?>
                        <a href="<?=site_url('player_management/set_promotion_status/' . $player['playerId'] . '/enable')?>" class="btn btn-scooter btn-sm">
                            <i class="fa fa-thumbs-up"></i> <?=lang('Enable Promotion')?>
                        </a>
                    <?php } else {?>
                        <a href="<?=site_url('player_management/set_promotion_status/' . $player['playerId'] . '/disable')?>" class="btn btn-danger btn-sm">
                            <i class="fa fa-thumbs-down"></i> <?=lang('Disable Promotion')?>
                        </a>
                    <?php
                    }
                } ?>

                <?php if ($this->permissions->checkPermissions('disable_player_withdrawal')) {
                    if ($player['enabled_withdrawal'] == 1) {?>
                        <a href="<?=site_url('player_management/set_withdrawal_status/' . $player['playerId'] . '/disable')?>" class="btn btn-danger btn-sm">
                            <i class="fa fa-thumbs-down"></i> <?=lang('Disable Player Withdrawal')?>
                        </a>
                    <?php } else {?>
                        <a href="<?=site_url('player_management/set_withdrawal_status/' . $player['playerId'] . '/enable')?>" class="btn btn-scooter btn-sm">
                            <i class="fa fa-thumbs-up"></i> <?=lang('Enable Player Withdrawal')?>
                        </a>
                    <?php
                    }
                } ?>
            </div>
            <div class="col-md-7">
                <?php if ($this->utils->isEnabledFeature('show_c6_authentication') && $this->utils->isEnabledFeature('enable_c6_acuris_api_authentication') && $this->permissions->checkPermissions('show_c6_authentication')) {?>
                    <a href="javascript:void(0)" onclick="modal('/player_management/player_c6_authentication/<?=$player['playerId']?>','<?=lang('C6 Authentication')?>')" class="btn btn-portage btn-sm">
                        <i class="fa fa-star"></i> <span class="hidden-xs"><?=lang('C6 Authentication')?></span>
                    </a>
                <?php }?>

                <?php if ($this->utils->isEnabledFeature('show_pep_authentication') && $this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') && $this->permissions->checkPermissions('show_pep_authentication')) {?>
                    <a href="javascript:void(0)" onclick="modal('/player_management/player_pep_authentication/<?=$player['playerId']?>','<?=lang('PEP Authentication')?>')" class="btn btn-portage btn-sm">
                        <i class="fa fa-star"></i> <span class="hidden-xs"><?=lang('PEP Authentication')?></span>
                    </a>
                <?php }?>

                <?php if ($this->utils->isEnabledFeature('enable_show_trigger_XinyanApi_validation_btn')) { ?>
                    <a href="<?=site_url('player_management/triggerRegisterEventForXinyanApi/' . $player['playerId'])?>"  class="btn btn-portage btn-sm" id="xinyan_api_btn">
                        <i class="fa fa-caret-square-o-up"></i> <span class="hidden-xs"><?=lang('Trigger Xinyan Validation')?></span>
                    </a>
                    <script> //get XinyanApi status and enable or disabled btn
                        $(document).ready(function() {
                            $.post('/player_management/getXinyanApiStatus/'+playerId, function(data){
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
                        }); //end
                    </script>
                <?php } ?>

                <?php if ($this->permissions->checkPermissions('player_notes')) {?>
                    <button type="button" class="btn btn-portage btn-sm" onclick="player_notes(<?=$player['playerId']?>)">
                        <i class="fa fa-sticky-note"></i> <?=lang('Player Remarks')?>
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
                    </div>
                    <script>
                        function player_notes(player_id) {
                            var dst_url = "/player_management/player_notes/" + player_id;
                            open_modal('player_notes', dst_url, "<?=lang('Player Remarks'); ?>");
                        }
                    </script>
                <?php }?>

                <?php if ($this->utils->isEnabledFeature('show_upload_documents') && $this->permissions->checkPermissions('kyc_attached_documents')) {?>
                    <a href="javascript:void(0)" onclick="modal('/player_management/player_attach_document/<?=$player['playerId']?>','Attached Document')" class="btn btn-portage btn-sm">
                        <i class="fa fa-folder-open"></i> <span class="hidden-xs"><?=lang('user_info.kyc_attachment')?></span>
                    </a>
                <?php }?>

                <?php if ($this->utils->isEnabledMDB()) { ?>
                    <a href="<?=site_url('/player_management/sync_player_to_mdb/' . $player['playerId'])?>" class="btn btn-portage btn-sm">
                        <i class="fa fa-refresh"></i> <?=lang('Sync To Currency')?>
                    </a>
                <?php } ?>
            </div>
        </fieldset>
        <br/>
    </div>

    <!-- Status -->
    <div class="row player-status" style="margin:6px;">
        <fieldset style="padding:6px;">
            <legend>
                <h4><b><?=lang('Status');?></b></h4>
            </legend>

            <div class='col-md-2'>
                <p><?=lang('viewuser.03');?> :
                    <?php if ($player['online']):?>
                        <?=lang('icon.online')?>
                    <?php else:?>
                        <?=lang('icon.offline')?>
                    <?php endif;?>
                </p>
            </div>

            <div class='col-md-2'>
                <?php
                    switch( $this->utils->getPlayerStatus($player['playerId'])){
                        case 0:
                            $statusTag = '<span class="text-success">' .lang('status.normal').'</span>';
                            break;
                        case 1:
                            $statusTag = '<span class="text-danger">' .lang('Blocked').'</span>';
                            break;
                        case 5:
                            $statusTag = '<span class="text-danger">' .lang('Suspended').'</span>';
                            break;
                        case 7:
                            $statusTag = '<span class="text-muted">' .lang('Self Exclusion').'</span>';
                            break;
                        case 8:
                            $statusTag = '<span class="text-danger">' .lang('Failed Login Attempt').'</span>';
                            break;
                    }
                ?>
                <p><?=lang('lang.accountstatus');?> : <?=$statusTag?> </p>
            </div>

            <div class='col-md-2'>
                <p><?=lang('Cashback'); ?>:
                    <?php echo $player['disabled_cashback'] == 0 ? '<span class="text-success">' . lang('Enabled') . '</span>' : '<span class="text-danger">' . lang('Disabled') . '</span>'; ?>
                </p>
            </div>

            <div class='col-md-2'>
                <p><?=lang('Promotion'); ?>:
                    <?php echo $player['disabled_promotion'] == 0 ? '<span class="text-success">' . lang('Enabled') . '</span>' : '<span class="text-danger">' . lang('Disabled') . '</span>'; ?>
                </p>
            </div>

            <div class='col-md-2'>
                <p><?=lang('Withdrawal Status'); ?>:
                    <?php echo $player['enabled_withdrawal'] != 0 ? '<span class="text-success">' . lang('Enabled') . '</span>' : '<span class="text-danger">' . lang('Disabled') . '</span>'; ?>
                </p>
            </div>

            <?php if ($this->utils->isEnabledFeature('show_pep_status') && $this->utils->isEnabledFeature('show_risk_score')) {?>
                <div class='col-md-2'>
                    <p><?=lang('PEP'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_pep/<?=$player['playerId']?>','<?=lang('player PEP')?>');">
                            <span class="text-success" id="pep_status"><?=lang(str_replace('%20', ' ', $pep_status))?></span>
                        </a>
                    </p>
                </div>
            <?php }?>

            <?php if ($this->utils->isEnabledFeature('show_c6_status') && $this->utils->isEnabledFeature('show_risk_score')) {?>
                <div class='col-md-2'>
                    <p><?=lang('C6'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_c6/<?=$player['playerId']?>','<?=lang('Player C6')?>');">
                            <span class="text-success" id="c6_status"><?=lang(str_replace('%20', ' ', $c6_status))?></span>
                        </a>
                    </p>
                </div>
            <?php }?>

            <?php if ($this->utils->isEnabledFeature('show_risk_score')) {?>
                <div class='col-md-2'>
                    <p><?=lang('risk score'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_risk_score/<?=$player['playerId']?>','<?=lang('risk score')?>');">
                            <span class="text-success" id="risk_score"><?=$risk_level?> / <?=$risk_score?></span>
                        </a>
                    </p>
                </div>
            <?php }?>

            <?php if ($this->utils->isEnabledFeature('show_kyc_status')) {?>
                <div class='col-md-2'>
                    <p><?=lang('KYC Score'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_kyc/<?=$player['playerId']?>','<?=lang('player kyc')?>');">
                            <span class="text-success" id="kyc_status"><?=lang($kyc_level)?> / <?=lang($kyc_status)?></span>
                        </a>
                    </p>
                </div>
            <?php }?>

            <?php if ($this->utils->isEnabledFeature('show_allowed_withdrawal_status') && $this->utils->isEnabledFeature('show_risk_score') && $this->utils->isEnabledFeature('show_kyc_status')) {?>
                <div class='col-md-2'>
                    <p><?=lang('KYC Withdrawal Status'); ?>:
                        <a href="javascript:void(0);" onclick="modal('/player_management/player_allowed_withdrawal_status/<?=$player['playerId']?>','<?=lang('KYC Withdrawal Status')?>');">
                            <span class="text-success" id="allowed_withdrawal_status"><?=$allowed_withdrawal_status?></span>
                        </a>
                    </p>
                </div>
            <?php }?>
        </fieldset>
    </div>

    <div class="info-tab">
        <ul class="nav nav-tabs" role="tablist">
            <li id="infotab_1"> <!-- signup_info -->
                <a href="#" onclick="changeUserInfoTab(1);" data-toggle="tab"><?=lang('userinfo.tab01');?></a>
            </li>
            <?php if ($this->permissions->checkPermissions('player_basic_info')): ?>
                <li id="infotab_2"> <!-- basic_info -->
                    <a href="#" onclick="changeUserInfoTab(2);" data-toggle="tab"><?=lang('userinfo.tab02');?></a>
                </li>
            <?php endif;?>
            <?php if(false && $this->utils->isEnabledFeature('show_upload_documents') && $this->permissions->checkPermissions('kyc_attached_documents')) :?>
                <li id="infotab_3"> <!-- kyc_attachment -->
                    <a href="#" onclick="changeUserInfoTab(3);" data-toggle="tab"><?=lang('userinfo.tab03');?></a>
                </li>
            <?php endif;?>
            <?php if($this->utils->isEnabledFeature('responsible_gaming') && $this->permissions->checkPermissions('responsible_gaming_info')) :?>
                <li id="infotab_4"> <!-- responsible_gaming -->
                    <a href="#" onclick="changeUserInfoTab(4);" data-toggle="tab"><?=lang('userinfo.tab04');?></a>
                </li>
            <?php endif;?>
            <li id="infotab_5"> <!-- fin_info -->
                <a href="#" onclick="changeUserInfoTab(5);" data-toggle="tab"><?=lang('userinfo.tab05');?></a>
            </li>
            <li id="infotab_6"> <!-- withdrawal_condition -->
                <a href="#" onclick="changeUserInfoTab(6);" data-toggle="tab"><?=lang('userinfo.tab06');?></a>
            </li>
            <?php if ($this->utils->isEnabledFeature('enabled_transfer_condition')): ?>
                <li id="infotab_7"> <!-- transfer_condition -->
                    <a href="#" onclick="changeUserInfoTab(7);" data-toggle="tab"><?=lang('userinfo.tab07');?></a>
                </li>
            <?php endif;?>
            <li id="infotab_8"> <!-- account_info -->
                <a href="#" onclick="changeUserInfoTab(8);" data-toggle="tab"><?=lang('userinfo.tab08');?></a>
            </li>
            <li id="infotab_9"> <!-- game_info -->
                <a href="#" onclick="changeUserInfoTab(9);" data-toggle="tab"><?=lang('userinfo.tab09');?></a>
            </li>
            <li id="infotab_10"> <!-- player_log -->
                <a href="#" onclick="changeUserInfoTab(10);" data-toggle="tab"><?=lang('userinfo.tab10');?></a>
            </li>
        </ul>
        <div class="tab-content" id="nav_content"></div>
    </div>
</div>

<?php include VIEWPATH . '/player_management/user_information/modals.php'; ?>
<script type="text/javascript">
    var playerId = '<?=$player['playerId']?>';

    $(document).ready(function() {
        changeUserInfoTab(<?=$tab?>);
    });

    function changeUserInfoTab(tab) {
        $('.info-tab li').removeClass('active');
        $('#infotab_' + tab).addClass('active');

        var url = "/player_management/changeUserInfoTab/"+ playerId + "/"+ tab;
        $('#nav_content').html('<center><img src="' + imgloader + '"></center>').load(url);
    }
</script>
