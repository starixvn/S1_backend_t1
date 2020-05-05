<?php
$member_center_href = $this->utils->getSystemUrl('player', '/player_center/dashboard/index#memberCenter', false);
$member_center_data_toggle = "tab";
if($this->utils->isEnabledFeature('always_auto_transfer_if_only_one_game')){
    $member_center_href = $this->utils->getSystemUrl('player', '/player_center/dashboard/cashier', false);
    $member_center_data_toggle = NULL;
}
$memberCenterName = $this->utils->getConfig('playercenter.memberCenterName');
?>
<ul class="main-menu-nav player-center-navigation">
    <li class="home hidden">
        <a href="<?=$this->utils->getSystemUrl('www')?>">
            <span class="glyphicon glyphicon-home"></span><?= lang('header.home') ?></a>
    </li>
    <li class="mc memberCenter <?=$activeNav == 'memberCenter' ? 'active' : ''?>">
        <a id="menu_member_center" href="<?=$member_center_href?>" data-toggle="<?=$member_center_data_toggle?>">
            <span class="glyphicon glyphicon-th"></span><?=lang($memberCenterName ?: "Cashier Center");?></a>
    </li>
    <li class="ai accountInformation">
        <a id="menu_account_info" href="<?=$this->utils->getSystemUrl('player', '/player_center/dashboard/index#accountInformation', false)?>" data-toggle="tab">
            <span class="fa fa-address-book"></span><?php echo lang("Account Information") ?></a>
    </li>
    <li class="scy security <?=$activeNav == 'security' ? 'active' : ''?>">
        <a id="menu_security" href="<?=$this->utils->getSystemUrl('player', '/player_center2/security', false)?>">
            <span class="fa fa-shield"></span><?php echo lang("Security") ?></a>
    </li>
    <li class="ah accountHistory <?=$activeNav == 'report' ? 'active' : ''?>">
        <a id="menu_account_history" href="<?=$this->utils->getSystemUrl('player', '/player_center2/report', false)?>">
            <span class="icon-refferal"></span><?php echo lang("Account History") ?></a>
    </li>
    <li class="vipr viprewards <?=$this->utils->isEnabledFeature('show_player_vip_tab') ? '' : 'hide'?>">
        <a id="menu_vip_group" href="<?=$this->utils->getSystemUrl('player', '/player_center/dashboard/index#viprewards', false)?>" data-toggle="tab">
            <span class="fa fa-star"></span><?php echo lang("VIP Group") ?>
            <span class="notification hide" id="vipLvlPercentageTxtInSidebar"></span>
        </a>
    </li>
    <?php if($this->utils->isEnabledFeature('player_center_realtime_cashback')) : ?>
        <li class="cb cashback <?=$activeNav == 'cashback' ? 'active' : ''?>" >
            <a id="menu_cashback" href="<?=$this->utils->getSystemUrl('player', '/player_center2/cashback', false)?>">
                <span class="glyphicon glyphicon-yen"></span><?php echo lang("Realtime Cashback") ?></a>
        </li>
    <?php endif; ?>
    <?php if (!$this->utils->isEnabledFeature('hidden_promotion_on_navigation')) :?>
    <li class="prm promotions <?=$activeNav == 'promotions' ? 'active' : ''?>" >
        <a id="menu_promotions" href="<?=$this->utils->getSystemUrl('player', '/player_center2/promotion', false)?>">
            <span class="glyphicon glyphicon-tag"></span><?php echo lang("Promotions") ?></a>
    </li>
    <?php endif; ?>
    <?php if ($this->utils->isEnabledFeature('enabled_favorites_and_rencently_played_games')) :?>
    <li class="prm favorite-games">
        <a id="menu_favorite_games" href="<?=$this->utils->getSystemUrl('player', '/player_center/dashboard/index#favorite-games', false)?>" data-toggle="tab">
            <span class="glyphicon glyphicon-heart"></span><?php echo lang("Favorite Games") ?></a>
    </li>
    <?php endif; ?>
    <li class="msg messages <?=$activeNav == 'messages' ? 'active' : ''?> <?= $this->utils->isEnabledFeature('show_player_messages_tab') ? '' : 'hide' ?>">
        <a id="menu_message" href="<?=$this->utils->getSystemUrl('player', '/player_center2/messages', false)?>">
            <span class="glyphicon glyphicon-comment"></span><?php echo lang("Messages") ?><span class="notification notif-msg-count _player_internal_message_count"><?=$this->utils->unreadMessages()?></span></a>
    </li>
    <?php if ($this->utils->isEnabledFeature('enable_shop')) :?>
    <li class="shp shop">
        <a id="menu_shop" href="<?=$this->utils->getSystemUrl('player', '/player_center/dashboard/index#shop', false)?>" data-toggle="tab">
            <span class="fa fa-shopping-basket"></span><?php echo lang("Shop") ?></a>
    </li>
    <?php endif; ?>
    <?php if ($this->utils->isEnabledFeature('enabled_player_referral_tab')) :?>
    <li class="reff refferals <?=isset($activeNav) && $activeNav == 'refferals' ? 'active' : ''?>">
        <a id="menu_refferals" href="<?=$this->utils->getSystemUrl('player', '/player_center2/refferals', false)?>" data-toggle="tab">
            <span class="fa fa-users"></span><?php echo lang("Refer a Friend") ?></a>
    </li>
    <?php endif; ?>
    <?php if($this->utils->isEnabledFeature('responsible_gaming')) : ?>
        <li class="rsg responsible_gaming <?=isset($activeNav) && $activeNav == 'responsible_gaming' ? 'active' : ''?>" >
            <a id="menu_responsible_gaming" href="<?=$this->utils->getSystemUrl('player', '/player_center2/responsible_game', false)?>">
                <span class="glyphicon glyphicon-tag"></span><?php echo lang("Responsible Gaming") ?></a>
        </li>
    <?php endif; ?>
</ul>
<?php
$show_agency_menu_in_nav = $this->CI->load->get_var('show_agency_menu_in_nav');
if($show_agency_menu_in_nav){
?>
<ul class="agency-menu-nav agency-center-navigation">
    <li class="rsg agency <?=isset($activeNav) && $activeNav == 'agency' ? 'active' : ''?>" >
        <a id="menu_agency" class="redirect_agency_link" href="javascript:void(0)" data-href="/agency">
            <span class="fa fa-users"></span><?php echo lang("Agency") ?></a>
    </li>
    <li class="rsg tracking_link_list <?=isset($activeNav) && $activeNav == 'tracking_link_list' ? 'active' : ''?>" >
        <a id="menu_tracking_link_list" class="redirect_agency_link" href="javascript:void(0)" data-href="/agency/tracking_link_list">
            <span class="fa fa-weixin"></span><?php echo lang("WeChat Links") ?></a>
    </li>
<?php if($this->utils->isEnabledFeature('enabled_lottery_salary_on_olayer_center')){ ?>
    <li class="rsg lottery_salary <?=isset($activeNav) && $activeNav == 'lottery_salary' ? 'active' : ''?>" href="_blank">
        <a id="menu_lottery_salary" href="/player_center2/lottery/salary">
            <span class="fa fa-dollar"></span><?php echo lang("Daily Salary") ?></a>
    </li>
<?php } ?>
<?php if($this->utils->isEnabledFeature('enabled_lottery_agent_navigation')){ ?>
    <li class="rsg lottery_agent <?=isset($activeNav) && $activeNav == 'lottery_agent' ? 'active' : ''?>" >
        <a id="menu_lottery_agent" href="/player_center2/lottery/agent" href="_blank">
            <span class="glyphicon glyphicon-glass"></span><?php echo lang("Lottery Agent") ?></a>
    </li>
<?php } ?>
</ul>

<form method="POST" action="<?=site_url('/redirect/agency')?>" id="redirect_agency_form" target="_blank" style="display:none;">
    <input type="hidden" name="next" id="redirect_agency_next">
</form>
<script type="text/javascript">
$(window).ready(function(){

    $(".redirect_agency_link").click(function(e){
        e.preventDefault();

        $('#redirect_agency_next').val($(this).data('href'));
        $('#redirect_agency_form').submit();

    });

});
</script>
<?php
} ?>
