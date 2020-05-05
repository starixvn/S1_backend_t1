<ul class="sidebar-nav" id="sidebar">
<?php
$permissions = $this->permissions->getPermissions();

if ($permissions != null) {
	foreach ($permissions as $value) {
		switch ($value) {
		case 'player_list': ?>
            <li>
                <a class="list-group-item" id="view_player_list" style="border: 0px;margin-bottom:0.1px;" href="	<?=BASEURL . 'player_management/viewAllPlayer'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd01');?>">
                    <i class="icon-users <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                        <?=lang('player.sd01');?>
                    </span>
                </a>
            </li>
            <?php break;
		case 'online_player_list': ?>
            <li>
                <a class="list-group-item" id="view_online_player_list" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'player_management/viewOnlinePlayerList'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd07');?>">
                    <i class="icon-user-check <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                        <?=lang('player.sd07');?>
                    </span>
                </a>
            </li>
            <?php break;
		case 'vip_group_setting': ?>
            <?php if($this->permissions->checkPermissions('vip_group_setting')): ?>
                <li>
                    <a class="list-group-item " id="view_vipsetting_list" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'vipsetting_management/vipGroupSettingList'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd02');?>">
                        <i class="icon-diamond <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                        <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                            <?=lang('player.sd02');?>
                        </span>
                    </a>
                </li>
            <?php endif;?>
            <?php break;
		case 'tag_player': ?>
            <?php if($this->permissions->checkPermissions('taggedlist')): ?>
                <li>
                    <a class="list-group-item" id="view_taggedlist" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'player_management/taggedlist'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd03');?>">
                        <i class="icon-price-tags <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                        <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                            <?=lang('player.sd03');?>
                        </span>
                    </a>
                </li>
            <?php endif;?>
            <?php break;
		case 'linked_account': ?>
            <?php if ($this->utils->isEnabledFeature('linked_account')): ?>
                <li>
                    <a class="list-group-item" id="view_linkedAccount" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'player_management/linkedAccount'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd03');?>">
                        <i class="glyphicon glyphicon-link <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                        <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                            <?=lang('Linked Account');?>
                        </span>
                    </a>
                </li>
            <?php endif;?>
            <?php break;
		case 'taggedlist': ?>
            <li>
                <a class="list-group-item" id="view_player_tag_management" style="border: 0px;margin-bottom:0.1px;line-height: 1.42857143;" href="<?=BASEURL . 'player_management/playerTagManagement'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd04');?>">
                    <i class="icon-user2 <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>&nbsp;<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> ><?=lang('player.sd04');?></span>
                </a>
            </li>
            <?php break;
		case 'manual_subtract_balance_tag': ?>
            <li>
                <a class="list-group-item" id="view_manual_adjust_tag_management" style="border: 0px;margin-bottom:0.1px;line-height: 1.42857143;" href="<?=BASEURL . 'player_management/ManualSubtractBalanceTagManagement'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd11');?>">
                    <i class="icon-user2 <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>&nbsp;<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> ><?=lang('player.sd11');?></span>
                </a>
            </li>
            <?php break;
		case 'account_process': ?>
            <li>
                <a class="list-group-item" id="view_account_process" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'player_management/accountProcess'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd05');?>">
                    <i class="icon-user-plus <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                        <?=lang('player.sd05');?>
                    </span>
                </a>
            </li>
            <?php break;
		case 'upload_batch_player': ?>
            <?php if ($this->utils->isEnabledFeature('enabled_batch_upload_player')): ?>
            <li>
                <a class="list-group-item" id="view_upload_player" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'player_management/accountAutoProcess'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Upload Batch Player');?>">
                    <i class="icon-user-plus <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                        <?=lang('Upload Batch Player');?>
                    </span>
                </a>
            </li>
            <?php endif; ?>
            <?php break;
		case 'friend_referral_player': ?>
            <li>
                <a class="list-group-item" id="view_friend_referral" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'player_management/friendReferral'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd06');?>">
                    <i class="icon-bubbles <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                        <?=lang('player.sd06');?>
                    </span>
                </a>
            </li>
            <?php if ($this->utils->isEnabledFeature('switch_to_ibetg_commission')): ?>
            <li>
                <a class="list-group-item" id="friendreferrallevelsetup" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="<?=site_url('player_management/FriendReferralLevelSetup')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd09')?>">
                    <i id="icon" class="icon-settings <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                        <?=lang('player.sd09')?>
                    </span>
                </a>
            </li>
            <li>
                <a class="list-group-item" id="friendreferralmonthlyearnings" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="<?=site_url('player_management/viewFriendReferralMonthlyEarnings')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('player.sd10')?>">
                    <i id="icon" class="icon-wallet <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                        <?=lang('player.sd10')?>
                    </span>
                </a>
            </li>
            <?php endif; ?>
            <?php break;
		case 'responsible_gaming_setting': ?>
            <?php if($this->utils->isEnabledFeature('responsible_gaming')): ?>
            <li>
                <a class="list-group-item" id="view_responsible_gaming_setting" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'player_management/responsibleGamingSetting'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Responsible Gaming Setting');?>">
                    <i class="glyphicon glyphicon-cog <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                        <?=lang('Responsible Gaming Setting');?>
                    </span>
                </a>
            </li>
            <?php endif;?>
            <?php break;
		case 'registration_setting': ?>
            <li>
                <a class="list-group-item" id="view_registration_setting" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'marketing_management/viewRegistrationSettings'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('mark.regsetting');?>">
                    <i class="glyphicon glyphicon-cog <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                        <?=lang('mark.regsetting');?>
                    </span>
                </a>
            </li>
            <?php break;
		default:
			break;
		}
	}
}
?>
</ul>
<ul id="sidebar_menu" class="sidebar-nav">
    <li class="sidebar-brand">
        <a id="menu-toggle" href="#" onclick="Template.changeSidebarStatus();">
        	<span id="main_icon" class="icon-arrow-<?=($this->session->userdata('sidebar_status') == 'active') ? 'left' : 'right';?> pull-right"></span>
        </a>
    </li>
</ul>