<ul class="sidebar-nav" id="sidebar">
<?php

$permissions = $this->permissions->getPermissions();

if ($permissions != null) {
	foreach ($permissions as $value) {
		switch ($value) {
        case 'view_news_category' : ?>
            <li>
              <a class="list-group-item" id="view_news_category" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="<?=BASEURL . 'cms_management/viewNewsCategory'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.08');?>">
                    <i class="icon-newspaper <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text"<?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                        <?=lang('cms.newscategory');?>
                    </span>
                </a>
        <?php break;
		case 'view_news': ?>
              <li>
              <a class="list-group-item" id="view_news" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="<?=BASEURL . 'cms_management/viewNews'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.02');?>">
                    <i class="icon-newspaper <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text"<?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                        <?=lang('cms.02');?>
                    </span>
                </a>
            </li>
        <?php break;
		case 'bannercms': ?>
            <li>
                <a class="list-group-item" id="view_banner_mgr" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'cmsbanner_management/viewBannerManager'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.03');?>">
                    <i class="glyphicon glyphicon-picture <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                        <?=lang('cms.03');?>
                    </span>
                </a>
            </li>
        <?php break;
		case 'emailcms': ?>
            <li>
                <a class="list-group-item" id="view_emailcms" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'cms_management/viewEmailTemplateManager'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('email.template.manager');?>">
                    <i class="icon-mail3 <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                        <?=lang('email.template.manager');?>
                    </span>
                </a>
            </li>
            <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
                <li>
                    <a class="list-group-item" id="view_msgtpl" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'cms_management/viewMsgtpl'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.msg.template');?>">
                        <i class="icon-cog <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                        <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                            <?=lang('cms.msg.template');?>
                        </span>
                    </a>
                </li>
            <?php endif?>
        <?php break;
        case 'smtp_setting': ?>
            <li>
                <a class="list-group-item " id="view_smtp_setting" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'cms_management/smtp_setting'?>" data-toggle="tooltip" data-placement="right" title="SMTP Setting">
                    <i class="icon-cog <?=($this->session->userdata('sidebar_status') == 'active') ? 'active' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?>>
                        <?=lang('smtp.setting.title')?>
                    </span>
                </a>
            </li>
        <?php break;
		case 'staticSites': ?>
            <?php if($this->utils->isSuperSiteOrNoMDB()): ?>
                <li>
                    <a class="list-group-item " id="view_static_site" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'cms_management/staticSites'?>" data-toggle="tooltip" data-placement="right" title="Static sites">
                        <i class="glyphicon glyphicon-globe <?=($this->session->userdata('sidebar_status') == 'active') ? 'active' : 'pull-right';?>" id="icon"></i>
                        <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?>>
                            <?=lang('Static sites')?>
                        </span>
                    </a>
                </li>
            <?php endif?>
        <?php break;
        case 'player_center_settings': ?>
            <li>
                <a class="list-group-item " id="view_player_center_settings" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'cms_management/player_center_settings'?>" data-toggle="tooltip" data-placement="right" title="Player Center Settings">
                    <i class="glyphicon glyphicon-cog <?=($this->session->userdata('sidebar_status') == 'active') ? 'active' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?>>
                        <?=lang('Player Center Settings')?>
                    </span>
                </a>
            </li>
        <?php break;
        case 'sms_manager': ?>
            <li>
                <a role="button" aria-expanded="false" data-toggle="collapse" class="list-group-item" id="sms_manager" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="#collapseSubmenu"  aria-controls="collapseSubmenu" data-placement="right" title="<?=lang('cms.09');?>">
                    <i class="icon-cog <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text"<?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> ><?=lang('cms.09');?></span>
                </a>
                <div class="collapse" id="collapseSubmenu">
                    <div class="col-md-6">
                        <ul class="sidebar-nav" id="sidebar">
                            <li>
                                <a id="sms_manager_views" class="list-group-item" style="border: 0px;" href="<?=site_url('cms_management/sms_manager_views')?>" data-toggle="tooltip" data-placement="right" title="<?= lang('cms.09') ?>">
                                    <?=lang('cms.10');?>
                                </a>
                            </li>
                            <li>
                                <a id="sms_activity_views" class="list-group-item" style="border: 0px;" href="<?=site_url('cms_management/sms_activity_views')?>" data-toggle="tooltip" data-placement="right" title="<?= lang('cms.09') ?>">
                                    <?=lang('cms.11');?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        <?php break;
        case 'playercenter_notif_mngmt': ?>
            <?php if ($this->utils->isEnabledFeature('cashier_custom_error_message')) : ?>
                <li>
                    <a class="list-group-item " id="view_notif_mngmt" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'cms_management/notificationManagementSettings'?>" data-toggle="tooltip" data-placement="right" title="Static sites">
                        <i class="fa fa-bell-o <?=($this->session->userdata('sidebar_status') == 'active') ? 'active' : 'pull-right';?>" id="icon"></i>
                        <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?>>
                            <?=lang('Cashier Notification Manager')?>
                        </span>
                    </a>
                </li>
            <?php endif; ?>
        <?php break;
        case 'metadata_manager': ?>
        <li>
            <a class="list-group-item " id="metadata_manager" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'cms_management/viewMetaData'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.12')?>">
                <i class="glyphicon glyphicon-cog <?=($this->session->userdata('sidebar_status') == 'active') ? 'active' : 'pull-right';?>" id="icon"></i>
                <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?>>
                    <?=lang('cms.12')?>
                </span>
            </a>
        </li>
        <?php break;
		}
	}
}
?>

</ul>
<ul id="sidebar_menu" class="sidebar-nav">
    <li class="sidebar-brand">
        <a id="menu-toggle" href="#" onclick="Template.changeSidebarStatus();">
            <span id="main_icon" class="icon-arrow-<?=($this->session->userdata('sidebar_status') == 'active') ? 'left' : 'right';?> pull-right"></span></span>
        </a>
    </li>
</ul>

