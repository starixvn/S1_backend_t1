<ul class="sidebar-nav" id="sidebar">
<?php
$permissions = $this->permissions->getPermissions();

if ($permissions != null) {
	foreach ($permissions as $value) {
		switch ($value) {
    		case 'chat':
    			?>
    				<li>
    					<a class="list-group-item" id="view_messages" style="border: 0px;margin-bottom:0.1px;" href="<?php echo site_url('cs_management/messages'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cs.messages');?>">
    						<i id="icon" class="fa fa-comment <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>"></i>
                       		<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
    							<?=lang('cs.messages');?>
    						</span>
    					</a>
    				</li>
    			<?php

    			break;
    		case 'live_chat':
    			if ($this->utils->isEnabledLiveChat()) {
    				?>
              		<li>
      					<a class="list-group-item" id="view_live_chat" style="border: 0px;margin-bottom:0.1px;"
      					href="<?php echo site_url('redirect/gotolivechat'); ?>" target='_blank'
      					data-toggle="tooltip" data-placement="right" title="<?=lang('cs.livechat');?>">
    						<i id="icon" class="fa fa-comments-o <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>"></i>
                       		<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
    							<?=lang('cs.livechat');?>
    						</span>
    					</a>
      				</li>
    		<?php

    			}
    			break;
    		case 'player_live_chat_link':
    			if ($this->utils->isEnabledLiveChat()) {
    				?>
              		<li>
      					<a class="list-group-item" id="view_live_chat" style="border: 0px;margin-bottom:0.1px;"
      					href="<?php echo site_url('cs_management/livechat_link'); ?>"
      					data-toggle="tooltip" data-placement="right" title="<?=lang('cs.livechat');?>">
    						<i id="icon" class="fa fa-comment-o <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>"></i>
                       		<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
    							<?=lang('Live chat link');?>
    						</span>
    					</a>
      				</li>
    			<?php

    			}
    			break;
    		case 'support_ticket':
    				?>
              		<li>
      					<a class="list-group-item" id="view_live_chat" style="border: 0px;margin-bottom:0.1px;"
      					href="<?php echo site_url('cs_management/go_support_ticket'); ?>"
      					data-toggle="tooltip" data-placement="right" title="<?=lang('Support Ticket');?>">
    						<i id="icon" class="fa fa-question-circle <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>"></i>
                       		<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
    							<?=lang('Support Ticket');?>
    						</span>
    					</a>
      				</li>
    		<?php
    			break;
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
