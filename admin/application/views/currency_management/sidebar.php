<ul class="sidebar-nav" id="sidebar">
    <?php
$permissions = $this->permissions->getPermissions();

if ($permissions != null) {
	foreach ($permissions as $value) {
		switch ($value) {
        case 'manage_currency' : ?>
            <li>
              <a class="list-group-item" id="view_manage_currency" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="/system_management/manage_currency" data-toggle="tooltip" data-placement="right" title="<?=lang('Manage Currency');?>">
                    <i class="icon-newspaper <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text"<?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                        <?=lang('Manage Currency');?>
                    </span>
                </a>
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

