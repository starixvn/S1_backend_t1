<div role="tabpanel" class="tab-pane active" id="signupInfo">
    <div class="row">
        <div class="col-md-3">
            <fieldset>
                <div class="form-group form-group-sm">
                    <label><?=lang('player.01')?> :</label>
                    <div class="input-group">
                        <div class="form-control"><?=$player['username']?></div>
                        <?php if ($this->permissions->checkPermissions('send_message_sms') || $this->permissions->checkPermissions('telesales_call')):?>
                            <div class="input-group-addon">
                                <div class="dropdown">
                                    <a id="notify" data-toggle="dropdown" href="javascript:void(0)">
                                        <i class="fa fa-commenting"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="notify">
                                        <?php if ($this->permissions->checkPermissions('send_message_sms')):?>
                                            <li>
                                                <a onclick="sbe_messages_send_message('<?=$player['playerId']?>', '<?=$player['username']?>')" href="javascript:void(0)"> <!-- TODO -->
                                                    <i class="icon-bubble2"></i> <?=lang('lang.send.message')?>
                                                </a>
                                            </li>
                                            <li>
                                                <?php if(!empty($player['contactNumber'])): ?>
                                                    <a onclick="showSMSbox()" href="javascript:void(0)">
                                                        <i class="fa fa-mobile"></i> <?=lang('Send SMS')?>
                                                    </a>
                                                <?php else: ?>
                                                    <a class="btn btn-sm" disabled>
                                                        <i class="fa fa-mobile"></i> <?=lang('Send SMS')?>
                                                    </a>
                                                <?php endif; ?>
                                            </li>
                                        <?php endif;?>
                                        <?php if ($this->permissions->checkPermissions('telesales_call')):?>
                                            <li>
                                                <?php if(!empty($player['contactNumber'])): ?>
                                                    <a onclick='makeTeleCall("/api/call_player_tele/<?=$player['playerId']?>")'>
                                                        <i class="fa fa-phone"></i> <?=lang('Telesales Call')?>
                                                    </a>
                                                <?php else: ?>
                                                    <a class="btn btn-sm" disabled>
                                                        <i class="fa fa-phone"></i> <?=lang('Telesales Call')?>
                                                    </a>
                                                <?php endif; ?>
                                            </li>
                                        <?php endif;?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </fieldset>
            <br>
            <fieldset style="border-color: transparent;">
                <div class="form-group form-group-sm">
                    <label><?=lang('player.vipgrplvl')?> :</label>
                    <div class="input-group">
                        <div class="form-control playerVipLevel" id="player_viplevel">
                            <?=(empty($player_viplevel)) ? lang('lang.norecord') : lang($player_viplevel['groupName']). " - ".lang($player_viplevel['vipLevelName'])?>
                        </div>
                        <?php $ableEditVip = $this->permissions->checkPermissions('edit_player_vip_level');?>
                        <!--        after click "Edit"          -->
                            <?php if ($ableEditVip) : ?>
                                <select id="vip_level-list" style="width:100%; display: none;">
                                    <?php foreach($all_vip_levels as $vip_level):?>
                                        <?php if(!empty($player_viplevel['vipsettingcashbackruleId']) && $player_viplevel['vipsettingcashbackruleId'] == $vip_level['vipsettingcashbackruleId']): ?>
                                            <option value="<?=$vip_level['vipsettingcashbackruleId']?>" selected="selected">
                                                <?=lang($vip_level['groupName'])?> - <?=lang($vip_level['vipLevelName'])?>
                                            </option>
                                        <?php else: ?>
                                            <option value="<?=$vip_level['vipsettingcashbackruleId']?>">
                                                <?=lang($vip_level['groupName'])?> - <?=lang($vip_level['vipLevelName'])?>
                                            </option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                </select>
                            <?php endif; ?>
                        <!--        after click "Edit" end         -->
                        <?php if ($this->permissions->checkPermissions('manual_trigger_check_vip_conditions') || $ableEditVip):?>
                            <div class="input-group-addon playerVipLevel">
                                <div class="dropdown">
                                    <a id="vip_level-nav" data-toggle="dropdown" href="javascript:void(0)">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="vip_level-nav">
                                        <?php if ($this->permissions->checkPermissions('manual_trigger_check_vip_conditions')):?>
                                            <li>
                                                <a href="/player_management/manuallyUpgradeLevel/<?=$player['playerId']?>">
                                                    <i class="fa fa-level-up"></i> <?=lang('Check Upgrade Condition')?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/player_management/manuallyDowngradeLevel/<?=$player['playerId']?>">
                                                    <i class="fa fa-level-down"></i> <?=lang('Check Downgrade Condition')?>
                                                </a>
                                            </li>
                                        <?php endif;?>
                                        <?php if ($ableEditVip) : ?>
                                            <li>
                                                <a onclick="showVipLevelList()" href="javascript:void(0)">
                                                    <i class="fa fa-edit"></i> <?=lang('player.46')?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!--        after click "Edit"          -->
                            <?php if ($ableEditVip) : ?>
                                <a class="input-group-addon editVipLevel" style="display: none;" onclick="hideVipLevelList();">
                                    <i class="fa fa-times"></i>
                                </a>
                                <div class="input-group-btn editVipLevel" style="display: none;">
                                    <button class="btn btn-sm btn-scooter" id="vip_level-save_btn" onclick="updatePlayerVipLevel()">
                                        <?=lang("lang.save")?>
                                    </button>
                                </div>
                            <?php endif;?>
                        <!--        after click "Edit" end         -->
                    </div>

                    <label><?=lang('Dispatch Account Level');?> :</label>
                    <div class="input-group">
                        <div class="form-control playerDispatchLevel" id="player_dispatchlevel">
                            <?=(empty($player_dispatch_account)) ? lang('lang.norecord') : $player_dispatch_account['group_name']." - ".$player_dispatch_account['level_name']?>
                        </div>
                        <?php $ableEditDispatch = $this->permissions->checkPermissions('edit_player_dispatch_account_level')?>
                        <?php if ($ableEditDispatch) : ?>
                            <!--        after click "Edit"          -->
                                <select id="dispatch_level-list" style="width:100%; display: none;">
                                    <?php foreach($all_dispatch_levels as $dispatch_level):?>
                                        <?php if(!empty($player_dispatch_account['id']) && $player_dispatch_account['id'] == $dispatch_level['id']): ?>
                                            <option value="<?=$dispatch_level['id']?>" selected="selected">
                                                <?=lang($dispatch_level['group_name'])?> - <?=lang($dispatch_level['level_name'])?>
                                            </option>
                                        <?php else: ?>
                                            <option value="<?=$dispatch_level['id']?>">
                                                <?=lang($dispatch_level['group_name'])?> - <?=lang($dispatch_level['level_name'])?>
                                            </option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                </select>
                            <!--        after click "Edit" end         -->
                            <a class="input-group-addon playerDispatchLevel" onclick="showDispatchLevelList()">
                                <i class="fa fa-edit"></i>
                            </a>
                            <!--        after click "Edit"          -->
                                <a class="input-group-addon editDispatchLevel" style="display: none;" onclick="hideDispatchLevelList();">
                                    <i class="fa fa-times"></i>
                                </a>
                                <div class="input-group-btn editDispatchLevel" style="display: none;">
                                    <button class="btn btn-sm btn-scooter" id="dispatch_level-save_btn" onclick="updatePlayerDispatchLevel()">
                                        <?=lang("lang.save")?>
                                    </button>
                                </div>
                            <!--        after click "Edit" end         -->
                        <?php endif;?>
                    </div>
                </div>
            </fieldset>
            <br>
            <fieldset>
                <div class="sub-field">
                    <label><?=lang('player.56')?> :</label>
                    <?php if ($this->permissions->checkPermissions('reset_player_login_password')): ?>
                        <a class="btn btn-xs btn-wisppink" href="javascript:void(0);" onclick="modal('/player_management/resetPassword/<?=$player['playerId']?>','<?=lang('player.ur01')?>')">
                            <?=lang('lang.reset')?>
                        </a>
                    <?php else: ?>
                        <button class="btn btn-xs btn-wisppink" disabled>
                            <?=lang('lang.reset')?>
                        </button>
                    <?php endif; ?>

                    <?php if ($this->permissions->checkPermissions('login_as_player')): ?>
                        <a class="btn btn-xs btn-zircon" href="/player_management/login_as_player/<?=$player['playerId'];?>" target="_blank">
                            <?=lang('Login as Player'); ?>
                        </a>
                    <?php else: ?>
                        <button class="btn btn-xs btn-zircon" disabled>
                            <?=lang('Login as Player'); ?>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="sub-field">
                    <label><?=lang('Withdrawal Password')?> :</label>
                    <?php if ($this->utils->getConfig('withdraw_verification') == 'withdrawal_password' && $this->permissions->checkPermissions('reset_players_withdrawal_password')) : ?>
                        <a class="btn btn-xs btn-wisppink" href="javascript:void(0);" onclick="modal('/player_management/resetWithdrawalPassword/<?=$player['playerId']?>','<?=lang('Withdrawal password')?>')">
                            <?=lang('lang.reset')?>
                        </a>
                    <?php else: ?>
                        <button class="btn btn-xs btn-wisppink" disabled>
                            <?=lang('lang.reset')?>
                        </button>
                    <?php endif;?>
                </div>
            </fieldset>
        </div>
        <div class="col-md-3">
            <fieldset style="border-color: transparent;">
                <div class="form-group form-group-sm">
                    <label><?=lang('viewuser.03');?> :</label>
                    <div class="form-control">
                        <?php if ($player['online']):?>
                            <?=lang('icon.online')?>
                            <?php if ($this->permissions->checkPermissions('force_player_logout')) :?>
                                <a href="javascript:void(0);" onclick="kickout()" class="pull-right"><?=lang('player.ol03')?></a>
                            <?php endif;?>
                        <?php else:?>
                            <?=lang('icon.offline')?>
                        <?php endif;?>
                    </div>
                </div>
            </fieldset>
            <br>
            <fieldset>
                <div class="form-group form-group-sm">
                    <label><?=lang('player.38')?> :</label>
                    <div class="form-control">
                        <?=$player['playerCreatedOn']?>
                    </div>

                    <label><?=lang('player.42')?> :</label>
                    <div class="form-control">
                        <?=empty($player['last_login_time']) || strtotime($player['last_login_time']) < 0 ? lang('lang.norecord') : $player['last_login_time']?>
                    </div>

                    <label><?=lang('player.ui10')?> :</label>
                    <div class="form-control">
                        <?php if(empty($player_registrationIp)): ?>
                            <?=lang('lang.norecord')?>
                        <?php else: ?>
                            <a class="<?=$player_registrationIp['text_color']?>"
                               href="/player_management/searchAllPlayer/?search_reg_date=off&ip_address=<?=$player['registrationIP']?>" target="_blank"
                               data-toggle="tooltip" data-original-title="<?=lang('player.ur05')?>">
                                <?=$player_registrationIp['ip']?><?=$player_registrationIp['cityCountry']?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="col-md-3">
            <fieldset>
                <div class="form-group form-group-sm">
                    <?php $no_promoter = (empty($player_agent) && empty($player_affiliate) && empty($refereePlayerId));?>

                    <label><?=lang('player.70')?> :</label>
                    <div class="form-control">
                        <?php if (empty($refereePlayerId)) : ?>
                            <?=lang('lang.norecord')?>
                        <?php else:?>
                            <a href="/player_management/userInformation/<?=$refereePlayerId?>">
                                <?=$refereePlayer->username?>
                            </a>
                        <?php endif;?>
                    </div>

                    <label><?=lang('player.24')?> :</label>
                    <div class="input-group display-flex">
                        <div class="form-control playerAffiliate" id="player_affiliate">
                            <?=(empty($player_affiliate)) ? lang('lang.norecord') : $player_affiliate?>
                        </div>
                        <?php $ableAssignAffiliate = ($no_promoter && !empty($all_affiliates) && $this->permissions->checkPermissions('assign_player_under_affiliate'));?>
                        <?php if ($ableAssignAffiliate) : ?>
                            <!--        after click "Edit"          -->
                                <?=form_dropdown('affiliate_id', $all_affiliates, array(), 'id="affiliate-list" style="width:100%; display: none;"');?>
                            <!--        after click "Edit" end         -->
                            <a class="btn btn-xs btn-zircon playerAffiliate" id="affiliate-assign_btn" onclick="showAffiliateList()">
                                <?=lang('Assign Affiliate')?>
                            </a>
                            <!--        after click "Edit"          -->
                                <a class="input-group-addon editAffiliate" style="display: none;" onclick="hideAffiliateList();">
                                    <i class="fa fa-times"></i>
                                </a>
                                <div class="input-group-btn editAffiliate" style="display: none;">
                                    <button class="btn btn-sm btn-scooter" id="affiliate-save_btn" onclick="updatePlayerAffiliate()">
                                        <?=lang("lang.save")?>
                                    </button>
                                </div>
                            <!--        after click "Edit" end         -->
                        <?php endif; ?>
                    </div>

                    <label><?=lang('Under Agent')?> :</label>
                    <div class="input-group display-flex">
                        <div class="form-control playerAgent" id="player_agent">
                            <?=(empty($player_agent)) ? lang('lang.norecord') : $player_agent?>
                        </div>
                        <?php $ableAssignAgent = ($no_promoter && !empty($all_agents) && $this->permissions->checkPermissions('assign_player_under_agent'));?>
                        <?php if ($ableAssignAgent) : ?>
                            <!--        after click "Edit"          -->
                                <?=form_dropdown('agent_id', $all_agents, array(), 'id="agent-list" style="width:100%; display: none;"');?>
                            <!--        after click "Edit" end         -->
                            <a class="btn btn-xs btn-zircon playerAgent" id="agent-assign_btn" onclick="showAgentList()">
                                <?=lang('Assign Agent')?>
                            </a>
                            <!--        after click "Edit"          -->
                                <a class="input-group-addon editAgent" style="display: none;" onclick="hideAgentList();">
                                    <i class="fa fa-times"></i>
                                </a>
                                <div class="input-group-btn editAgent" style="display: none;">
                                    <button class="btn btn-sm btn-scooter" id="agent-save_btn" onclick="updatePlayerAgent()">
                                        <?=lang("lang.save")?>
                                    </button>
                                </div>
                            <!--        after click "Edit" end         -->
                        <?php endif; ?>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="col-md-3">
            <fieldset style="border-color: transparent;">
                <div class="form-group form-group-sm">
                    <label><?=lang('Player Tag')?> :</label>
                    <div class="input-group">
                        <div class="form-control playerTag" id="player_tags">
                            <?php if(empty($player_tags)): ?>
                                <?=lang('Select Tag')?>
                            <?php else: ?>
                                <div style="margin-top: 4px;"><?=player_tagged_list($player['playerId'])?></div>
                            <?php endif; ?>
                        </div>
                        <?php $ableEditTags = $this->permissions->checkPermissions('edit_player_tag');?>
                        <?php if ($ableEditTags) : ?>
                            <!--        after click "Edit"          -->
                                <select name="tag_list[]" id="tag-list" multiple="multiple" class="form-control input-md" style="display: none;">
                                    <?php foreach($all_tag_list as $tag):?>
                                        <?php if(is_array($player_tags) && in_array($tag['tagId'], $player_tags)): ?>
                                            <option value="<?=$tag['tagId']?>" data-color="<?=$tag['tagColor']?>" selected="selected">
                                                <?=$tag['tagName']?>
                                            </option>
                                        <?php else: ?>
                                            <option value="<?=$tag['tagId']?>" data-color="<?=$tag['tagColor']?>">
                                                <?=$tag['tagName']?>
                                            </option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                </select>
                            <!--        after click "Edit" end         -->
                            <a class="input-group-addon playerTag" onclick="showTagList()">
                                <i class="fa fa-edit"></i>
                            </a>
                            <!--        after click "Edit"          -->
                                <a class="input-group-addon editTag" style="display: none;" onclick="hideTagList();">
                                    <i class="fa fa-times"></i>
                                </a>
                                <div class="input-group-btn editTag" style="display: none;">
                                    <button class="btn btn-sm btn-scooter" id="tag-save_btn" onclick="updatePlayerTags()">
                                        <?=lang("lang.save")?>
                                    </button>
                                </div>
                            <!--        after click "Edit" end         -->
                        <?php endif;?>
                    </div>
                </div>
            </fieldset>
            <br>
            <?php if($this->utils->isEnabledFeature('display_newsletter_subscribe_btn') || $this->utils->isEnabledFeature('verification_reference_for_player')) : ?>
                <fieldset>
                    <div class="form-group form-group-sm">
                        <?php if($this->utils->isEnabledFeature('display_newsletter_subscribe_btn')) : ?>
                            <label><?=lang('Newsletter Subscription')?> :</label>
                            <div class="display-flex">
                                <?php $subscribed = $player['newsletter_subscription'];?>
                                <?php if($subscribed): ?>
                                    <div class="form-control text-success"><?=lang('newsletter.subscribed')?></div>
                                    <?php
                                        $btn_status = "wisppink";
                                        $btn_text = lang('newsletter.un-sub');
                                    ?>
                                <?php else: ?>
                                    <div class="form-control text-danger"><?=lang('newsletter.not-subscribed')?></div>
                                    <?php
                                        $btn_status = "zircon";
                                        $btn_text = lang('newsletter.sub');
                                    ?>
                                <?php endif; ?>
                                <?php if ($this->permissions->checkPermissions('adjust_newsletter_subscription_status')) : ?>
                                    <a class="btn btn-xs btn-<?=$btn_status?>" onclick="manualSubscribe(<?=$subscribed?>);">
                                        <?=$btn_text?>
                                    </a>
                                <?php  endif;?>
                            </div>
                        <?php endif;?>

                        <?php if($this->utils->isEnabledFeature('verification_reference_for_player')): ?>
                            <label><?=lang('Account Verification')?> :</label>
                            <div class="display-flex">
                                <?php if($player['manual_verification']): ?>
                                    <div class="form-control text-success"><?=lang('Verified')?></div>
                                    <?php
                                        $btn_status = "wisppink";
                                        $btn_text = lang('Reset');
                                    ?>
                                <?php else:?>
                                    <div class="form-control text-danger"><?=lang('Not Verified')?></div>
                                    <?php
                                        $btn_status = "zircon";
                                        $btn_text = lang('Set to Verified');
                                    ?>
                                <?php endif;?>
                                <?php if($this->permissions->checkPermissions('adjust_player_account_verify_status')): ?>
                                    <a class="btn btn-xs btn-<?=$btn_status?>" onclick="manualVerify(<?=$player['manual_verification']?>);">
                                        <?=$btn_text?>
                                    </a>
                                <?php  endif;?>
                            </div>
                        <?php endif;?>
                    </div>
                </fieldset>
                <br>
            <?php endif;?>
            <fieldset style="border-color: transparent;">
                <div class="form-group form-group-sm">
                    <label><?=lang('player.18')?> :</label>
                    <div class="display-flex">
                        <div class="form-control">
                            <?=$player['invitationCode']?>
                        </div>
                        <?php if(empty($player['invitationCode'])): ?>
                            <button href="javascript:void(0);" onclick="generateReferralCode();" id="generate-referral-code" type="button" class="btn btn-xs btn-zircon">
                                <?=lang('Generate Referral Code')?>
                            </button>
                        <?php endif; ?>
                    </div>

                    <label><?=lang('player.86')?> :</label>
                    <div class="form-control">
                        <?php if ($referral_count != 0 && $this->permissions->checkPermissions('friend_referral_player')): ?>
                            <a target="_blank" href="/player_management/friendReferral/<?=$player['playerId']?>">
                                <?=$referral_count?>
                            </a>
                        <?php else:?>
                            <?=$referral_count?>
                        <?php endif;?>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</div>


<script type="text/javascript">
    var dialog_title = "<?=lang('userinfo.tab01');?>";
    var button = '<button class="btn btn-sm btn-scooter" onclick="refresh_signup_info()"><?=lang('OK')?></button>';

    function refresh_signup_info() {
        changeUserInfoTab(1);
        $('#simpleModal').modal('hide');
    }

    function kickout(){
        var title = '<?=lang("user_info.modal.note");?>';
            content = '<?=lang("user_info.modal.confirmKickPlayer");?>';
            button = '<a class="btn btn-sm btn-danger" href="/player_management/kickPlayer/' + playerId + '"><?=lang('Yes')?></a><button class="btn btn-sm btn-primary" data-dismiss="modal" aria-label="Close"><?=lang('Cancel')?></button>'

        confirm_modal(title, content, button);
    }

    function manualSubscribe(status){
        var res = (status == 1) ? 'Unsubscribe' : 'Subscribe';
        var update_stat = (status == 1) ? 0 : 1;
        if(confirm('Are you sure you want to change the status to '+res+'?')){
            $.post('/player_management/updatePlayerNewsletterSubscription/'+update_stat+'/'+playerId, function(){
                location.reload();
            });
        }
    }

    function manualVerify(status){
        var res = (status == 1) ? 'Unverified' : 'Verified';
        var update_stat = (status == 1) ? 0 : 1;
        if(confirm('Are you sure you want to change the status to '+res+'?')){
            $.post('/player_management/updatePlayerDetailsVerification/'+update_stat+'/'+playerId, function(data){
                location.reload();
            });
        }
    }

    function generateReferralCode(){
        if($.trim($('#invitation-code-container').text()) != '' || $.trim($('#invitation-code-container').text()) != "0"){

            var url = '/player_management/generateReferralCode/'+ playerId;
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


    function showVipLevelList() {
        $(".playerVipLevel").hide();
        $("#vip_level-save_btn").prop('disabled', false);
        $(".editVipLevel").show();
        $('#vip_level-list').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            buttonContainer: '<div class="editVipLevel"/>',
            buttonClass: 'form-control',
            maxHeight: 250,
        });
        $(".multiselect-vipList").show();
    }

    function hideVipLevelList() {
        $(".editVipLevel").hide();
        $(".multiselect-vipList").hide();
        $(".playerVipLevel").show();
    }

    function updatePlayerVipLevel() {
        chosenVipLevel = $("#vip_level-list").val();

        if (!chosenVipLevel) {
            return false;
        }
        $("#vip_level-save_btn").prop('disabled', true);

        var data = {
            playerId : playerId,
            newPlayerLevel: chosenVipLevel
        };
        $.ajax({
            url : '/player_management/adjustVipLevelThruAjax',
            type : 'POST',
            data : data,
            dataType : "json",
            cache : false
        }).done(function (data) {
            if (data.status == "success") {
                var current_player_level = data.current_player_level[0];
                $("#player_viplevel").html('<b>' + current_player_level.groupName + '</b> - '+ current_player_level.vipLevelName);

                hideVipLevelList();
                success_modal_custom_button(dialog_title, "<?=lang('user_info.modal.vipupdated')?>", button);
            } else if (data.status == "error") {
                $("#vip_level-save_btn").prop('disabled', false);
                alert(data.message);
            }
        }).fail(function (jqXHR, textStatus) {
            if(jqXHR.status<300 || jqXHR.status>500){
                alert(textStatus);
            }
        });
    }


    function showDispatchLevelList() {
        $(".playerDispatchLevel").hide();
        $(".editDispatchLevel").show();

        $("#dispatch_level-save_btn").prop('disabled', false);
        $('#dispatch_level-list').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            buttonContainer: '<div class="editDispatchLevel"/>',
            buttonClass: 'form-control',
            maxHeight: 250,
        });

        $(".multiselect-dispatchList").show();
    }

    function hideDispatchLevelList() {
        $(".editDispatchLevel").hide();
        $(".multiselect-dispatchList").hide();
        $(".playerDispatchLevel").show();
    }

    function updatePlayerDispatchLevel() {
        chosenDispatchLevel = $("#dispatch_level-list").val();

        if (!chosenDispatchLevel) {
            return false;
        }
        $("#dispatch_level-save_btn").prop('disabled', true);

        var data = {
            playerId : playerId,
            newDispatchLevel: chosenDispatchLevel
        };
        $.ajax({
            url : '/player_management/adjustDispatchAccountLevelThruAjax',
            type : 'POST',
            data : data,
            dataType : "json",
            cache : false
        }).done(function (data) {
            if (data.status == "success") {
                var current_dispatch_account_level = data.current_dispatch_account_level[0];
                $("#player_dispatchlevel").html('<b>' + current_dispatch_account_level.group_name + '</b> - '+ current_dispatch_account_level.level_name);

                hideDispatchLevelList();
                success_modal_custom_button(dialog_title, "<?=lang('user_info.modal.dispatchupdated')?>", button);
            } else if (data.status == "error") {
                $("#dispatch_level-save_btn").prop('disabled', false);
                alert(data.message);
            }
        }).fail(function (jqXHR, textStatus) {
            if(jqXHR.status<300 || jqXHR.status>500){
                alert(textStatus);
            }
        });
    }


    function showAffiliateList() {
        $(".playerAffiliate").hide();
        $(".editAffiliate").show();

        $("#affiliate-save_btn").prop('disabled', false);
        $('#affiliate-list').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            buttonContainer: '<div class="editAffiliate"/>',
            buttonClass: 'form-control',
            maxHeight: 250,
        });

        $(".multiselect-affiliateList").show();
    }

    function hideAffiliateList() {
        $(".editAffiliate").hide();
        $(".multiselect-affiliateList").hide();
        $(".playerAffiliate").show();
    }

    function updatePlayerAffiliate() {
        chosenAffiliate = $("#affiliate-list").val();

        if (!chosenAffiliate) {
            return false;
        }
        $("#affiliate-save_btn").prop('disabled', true);

        var data = {
            playerId : playerId,
            affiliateId: chosenAffiliate
        };
        $.ajax({
            url : '/player_management/assignPlayerAffiliateThruAjax',
            type : 'POST',
            data : data,
            dataType : "json",
            cache : false
        }).done(function (data) {
            if (data.status == "success") {
                var current_affiliate = data.current_affiliate;
                $("#player_affiliate").html(current_affiliate);

                finishSetupPromoter();
                success_modal_custom_button(dialog_title, "<?=lang('user_info.modal.affiliateupdated')?>", button);
            } else if (data.status == "error") {
                $("#affiliate-save_btn").prop('disabled', false);
                alert(data.message);
            }
        }).fail(function (jqXHR, textStatus) {
            if(jqXHR.status<300 || jqXHR.status>500){
                alert(textStatus);
            }
        });
    }

    function showAgentList() {
        $(".playerAgent").hide();
        $(".editAgent").show();

        $("#agent-save_btn").prop('disabled', false);
        $('#agent-list').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            buttonContainer: '<div class="editAgent"/>',
            buttonClass: 'form-control',
            maxHeight: 250,
        });

        $(".multiselect-agentList").show();
    }

    function hideAgentList() {
        $(".editAgent").hide();
        $(".multiselect-agentList").hide();
        $(".playerAgent").show();
    }

    function updatePlayerAgent() {
        chosenAgent = $("#agent-list").val();

        if (!chosenAgent) {
            return false;
        }
        $("#agent-save_btn").prop('disabled', true);

        var data = {
            playerId : playerId,
            agentId: chosenAgent
        };
        $.ajax({
            url : '/player_management/assignPlayerAgentThruAjax',
            type : 'POST',
            data : data,
            dataType : "json",
            cache : false
        }).done(function (data) {
            if (data.status == "success") {
                var current_agent = data.current_agent;
                $("#player_agent").html(current_agent);

                finishSetupPromoter();
                success_modal_custom_button(dialog_title, "<?=lang('user_info.modal.agentupdated')?>", button);
            } else if (data.status == "error") {
                $("#agent-save_btn").prop('disabled', false);
                alert(data.message);
            }
        }).fail(function (jqXHR, textStatus) {
            if(jqXHR.status<300 || jqXHR.status>500){
                alert(textStatus);
            }
        });
    }


    function showTagList() {
        $(".playerTag").hide();
        $(".editTag").show();

        $("#tag-save_btn").prop('disabled', false);
        $('#tag-list').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            buttonContainer: '<div class="editTag"/>',
            buttonClass: 'form-control',
            maxHeight: 250,
        });

        $(".multiselect-tagList").show();
    }

    function hideTagList() {
        $(".editTag").hide();
        $(".multiselect-tagList").hide();
        $(".playerTag").show();
    }

    function updatePlayerTags() {
        $("#tag-save_btn").prop('disabled', true);

        var tagIds = $("#tag-list").val();
        var data = {
            playerId: playerId,
            tagId: tagIds
        };

        $.ajax({
            url: '/player_management/adjustPlayerTaglThruAjax',
            type: 'POST',
            data: data,
            dataType: "json",
            cache: false
        }).done(function (data) {
            if (data.status == "success") {
                $("#player_tags").html('<div style="margin-top: 4px;">'+data.currentPlayerTags+'</div>');
                hideTagList();
                success_modal_custom_button(dialog_title, "<?=lang('user_info.modal.tagupdated')?>", button);
            } else if (data.status == "empty") {
                $("#player_tags").html(data.message);
                hideTagList();
            } else if (data.status == "error") {
                $("#tag-save_btn").prop('disabled', false);
                alert(data.message);
                success_modal_custom_button(dialog_title, "<?=lang('user_info.modal.tagupdated')?>", button);
            }
        }).fail(function (jqXHR, textStatus) {
            if(jqXHR.status<300 || jqXHR.status>500){
                alert(textStatus);
            }
        });
    }

    function finishSetupPromoter(){
        hideAgentList();
        $("#agent-assign_btn").hide();
        hideAffiliateList();
        $("#affiliate-assign_btn").hide();
    }
</script>
