<?php

$current_url = $this->utils->getSystemUrls()['player'];

$system_navigation_container_count = 2;

if($this->authentication->isLoggedIn()){
    $this->CI->load->model(['agency_model']);

    $player_id = $this->utils->getLoggedPlayerId();
    $bigWallet = $this->utils->getBigWalletByPlayerId($playerId);

    $subwalletsBalance = array();

    foreach ($bigWallet['sub'] as $apiId => $subWallet) {
        $subwalletsBalance[$apiId] = $subWallet['total_nofrozen'];
    }

    $total_balance = $bigWallet['main']['total_nofrozen'] + array_sum($subwalletsBalance) + $bigWallet['main']['frozen'];
    $unread_message_count = $this->utils->unreadMessages($player['playerId']);

    $agency_agent = $this->CI->agency_model->get_agent_by_binding_player_id($player_id);
    if(!empty($agency_agent)){
        $system_navigation_container_count++;
    }

    $this->load->vars(['player_binding_agency_agent' => $agency_agent]);
}else{
    $total_balance = 0;
    $unread_message_count = 0;

    $this->load->vars(['player_binding_agency_agent' => NULL]);
}
?>
<header>
    <div class="col col-md-6">
        <div class="player-center-logo pull-left">
            <a href="/"><img src="<?=$playercenter_logo?>"/></a>
        </div>
        <div class="main-nav-toggle pull-left">
            <a href="javascript: void(0);"><i class="main-nav-toggle-icon"></i></a>
        </div>
        <div class="main-action-buttons">
            <ul class="list-unstyled">
                <li class="inline-block">
                    <a id="playercenter_message" href="<?php echo site_url('player_center2/messages')?>">
                        <i class="fa fa-envelope-o" aria-hidden="true"></i>
                        <span class="button-text"><?php echo lang("playercenter.messages") ?></span>
                        <span class="count-tip <?=($unread_message_count) ? '':'hidden'?>"><?=$unread_message_count?></span>
                    </a>
                </li>
                <li class="inline-block dropdown system-nav-toggle">
                    <a href="javascript: void(0);" id="system-nav-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                        <span class="button-text"><?php echo lang("lang.menu") ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right system-nav-contain-<?=$system_navigation_container_count?>" aria-labelledby="system-nav-toggle">
                        <?php include $template_path . '/includes/components/system-nav-toggle.php';?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="col col-md-6">
        <div class="player-stats-block pull-right">
            <?php if($this->authentication->isLoggedIn()): ?>
                <ul class="list-unstyled">
                    <li class="inline-block dropdown player-overview-toggle">
                        <a href="javascript: void(0);" id="player-overview-toggle" data-toggle="dropdown" aria-expanded="false">
                            <?php echo lang("Hello") ?>, <?=playerProperty($player, 'username')?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="player-overview-toggle">
                            <?=$this->CI->load->widget('overview');?>
                        </div>
                    </li>
                    <li class="inline-block player-balance"><?=lang("lang.balance") ?>: <span class="_player_balance_span"><?=$this->utils->formatCurrencyNumber($total_balance)?></span><a href="javascript: void(0);" class="player-stats-refresh-balance"><i class="fa fa-refresh"></i></a></li>
                    <li class="inline-block"><button class="btn btn-warning btn-logout" onclick="window.location.href='<?=site_url('iframe_module/iframe_logout/1')?>'"><i class="fa fa-power-off"></i><?=lang("nav.logOut")?></button></li>
                </ul>
            <?php else: ?>
                <ul class="list-unstyled">
                    <li class="inline-block"><a class="btn btn-info btn-login" href="<?=site_url('iframe/auth/login')?>" class="login ml10"><?php echo lang("Login") ?></a></li>
                    <li class="inline-block"><a class="btn btn-primary btn-register" href="<?=site_url('player_center/iframe_register')?>" class="registration"><?php echo lang("Registration") ?></a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</header>