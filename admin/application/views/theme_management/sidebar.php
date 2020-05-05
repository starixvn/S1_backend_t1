<ul class="sidebar-nav" id="sidebar">
	<li>
		<a class="list-group-item" id="theme_management" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'theme_management/index'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Theme');?>">
			<i class="fa fa-tint fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
			<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
				<?=lang('Theme');?>
			</span>
		</a>
		<?php if($this->utils->isEnabledFeature('enable_dynamic_header')) { ?>
		<a class="list-group-item" id="header_template" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'theme_management/headerIndex'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Header Template');?>">
			<i class="fa fa-header fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
			<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
				<?=lang('Header Template');?>
			</span>
		</a>
		<?php } ?>
		<?php if($this->utils->isEnabledFeature('enable_dynamic_footer')) { ?>
		<a class="list-group-item" id="footer_template" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'theme_management/footerIndex'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Footer Template');?>">
			<i class="fa fa-copyright fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
			<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
				<?=lang('Footer Template');?>
			</span>
		</a>
		<?php } ?>
		<?php if($this->utils->isEnabledFeature('enable_dynamic_registration')) { ?>
		<a class="list-group-item" id="register_template" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'theme_management/registerIndex'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Footer Template');?>">
			<i class="fa fa-registered fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
			<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
				<?=lang('Registration Template');?>
			</span>
		</a>
		<?php } ?>
		<?php if($this->utils->isEnabledFeature('enable_dynamic_javascript')) { ?>
		<a class="list-group-item" id="js_template" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'theme_management/otherJsIndex'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Javascript');?>">
			<i class="fa fa-file-code-o fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
			<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
				<?=lang('Javascript');?>
			</span>
		</a>
		<?php } ?>
		<?php if($this->utils->isEnabledFeature('enable_dynamic_mobile_login')) { ?>
		<a class="list-group-item" id="mobile_login_template" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'theme_management/mobileLoginIndex'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Mobile Login Template');?>">
			<i class="fa fa-mobile fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
			<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
				<?=lang('Mobile Login Template');?>
			</span>
		</a>
		<?php } ?>
		<?php if($this->utils->isEnabledFeature('enable_dynamic_theme_host_template')) { ?>
		<a class="list-group-item" id="theme_hostname_management" style="border: 0px;margin-bottom:0.1px;" href="<?=BASEURL . 'theme_management/themeHostIndex'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Theme Host Template');?>">
			<i class="fa fa-yelp  fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
			<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
				<?=lang('Theme Host Template');?>
			</span>
		</a>
		<?php } ?>
	</li>
</ul>
<ul id="sidebar_menu" class="sidebar-nav">
<li class="sidebar-brand">
	<a id="menu-toggle" href="#" onclick="Template.changeSidebarStatus();">
		<span id="main_icon" class="icon-arrow-<?=($this->session->userdata('sidebar_status') == 'active') ? 'left' : 'right';?> pull-right"></span></span>
	</a>
</li>