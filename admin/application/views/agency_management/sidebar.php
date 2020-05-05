<?php
/**
 *   filename:   sidebar.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     sidebar view for agency management
 */

// set display mode according to session status
$sidebarIconDisplay = ($this->session->userdata('sidebar_status') == 'active')? '' : 'pull-right';
$sidebarTextDisplay = ($this->session->userdata('sidebar_status') == 'active')?
    'style="display:inline-block;"' : 'style="display:none;"';
$sidebarArrowDisplay = ($this->session->userdata('sidebar_status') == 'active')? 'left' : 'right';
$sidebarItems = array('credit_transactions', 'agency_logs', 'structure_list', 'agent_list',
    'agent_domain_list', 'agency_payment', 'tier_comm_patterns', 'agency_player_report', 'agency_game_report',
    'agency_setting', 'settlement_wl');
$sidebarItemNames = array(lang('Credit Transactions'), lang('Agency Logs'), lang('Template List'),
    lang('Agent List'), lang('Domain List'), lang('Agency Payment'),
    lang('Tier Commission'), lang('report.s09'), lang('report.s07'), lang('Agency Setting'), lang('Settlement'));
$sidebarItemIcons = array('icon-credit-card', 'icon-profile', 'icon-menu', 'icon-text-color',
    'fa fa-sitemap', 'icon-credit-card', 'icon-settings', 'icon-users', 'icon-dice',
    'glyphicon glyphicon-cog', 'icon-pie-chart');
$sidebarItemPermission = array();

$permissions = $this->permissions->getPermissions();
if ($permissions != null) {
    foreach ($permissions as $value) {
        switch ($value) {
        case 'structure_list':
        case 'agent_list':
        case 'credit_transactions':
        case 'settlement_wl':
        case 'agency_logs':
        case 'agency_player_report':
        case 'agency_game_report':
        case 'agent_domain_list':
        case 'tier_comm_patterns':
        case 'agency_payment':
            $sidebarItemPermission[$value] = true;
            break;
        case 'agency_setting':
            $sidebarItemPermission[$value] = false;
            break;
        default:
            break;
        }
    }
}
if (!$this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
    $sidebarItemPermission['tier_comm_patterns'] = false;
}

if ($this->utils->isEnabledFeature('agent_settlement_to_wallet')) {
    $sidebarItemPermission['credit_transactions'] = false;
}

# Disable these items unconditionally
$sidebarItemPermission['agency_player_report'] = false;
$sidebarItemPermission['agency_game_report'] = false;
$sidebarItemPermission['settlement_wl'] = false;
?>

<ul class="sidebar-nav" id="sidebar">
<?php
foreach($sidebarItems as $i=>$item) {
    if (isset($sidebarItemPermission[$item]) && $sidebarItemPermission[$item]) {
?>
    <li>
        <a class="list-group-item" id="<?=$item?>" style="border: 0px;margin-bottom:0.1px;"
            href="<?=site_url('agency_management/'. $item);?>" data-toggle="tooltip"
            data-placement="right" title="<?=lang($sidebarItemNames[$i]);?>">
            <i id="icon" class="<?=$sidebarItemIcons[$i]?> <?=$sidebarIconDisplay?>" > </i>
            <span id="hide_text" <?=$sidebarTextDisplay?> >
                <?=lang($sidebarItemNames[$i]);?>
            </span>
        </a>
    </li>

<?php
    }
}
?>
</ul>
<ul id="sidebar_menu" class="sidebar-nav">
    <li class="sidebar-brand">
        <a id="menu-toggle" href="#" onclick="Template.changeSidebarStatus();">
            <span id="main_icon" class="icon-arrow-<?=$sidebarArrowDisplay?> pull-right">
            </span>
        </a>
    </li>
</ul>
<script type="text/javascript">
    $( document ).ready(function() {
        $('#main_icon').on('click',function(){
            if($("#wrapper").hasClass("active")){
                $.each($('.sidebar-nav li a i'),function( index, value ){
                    $(value).addClass('pull-right');
                });
            }else{
                $.each($('.sidebar-nav li a i'),function( index, value ){
                    $(value).removeClass('pull-right');
                });
            }
        });
    });
</script>
