<?php

$loggedPlayerId = $this->utils->getLoggedPlayerId();

$bigWallet = $this->utils->getBigWalletByPlayerId($loggedPlayerId);

$subwalletsBalance = array();

foreach ($bigWallet['sub'] as $apiId => $subWallet) {
	$subwalletsBalance[$apiId] = $subWallet['total_nofrozen'];
}

$total_balance = $bigWallet['main']['total_nofrozen'] + array_sum($subwalletsBalance) + $bigWallet['main']['frozen'];

    $current_url = $this->utils->getSystemUrls()['player'];
    $site_links = array();

    if(strpos($current_url, 'beteast')!==false){
        $site_links = array(
                lang('header.home') => array(
                        "href" => $this->utils->getSystemUrl('www', '/'),
                        "icon" => "casino.svg",
                    ),
                lang('Slots') => array(
                        "href" => $this->utils->getSystemUrl('www', '/Slots/'),
                        "icon" => "slot-machine.svg",
                    ),
                lang('Live Casino') => array(
                        "href" => $this->utils->getSystemUrl('www', '/Casino/'),
                        "icon" => "straight-poker.svg",
                    ),
                lang('header.keno') => array(
                        "href" => $this->utils->getSystemUrl('www', '/Lottery/'),
                        "icon" => "lottery-white.svg",
                    ),
                lang('Fishing') => array(
                        "href" => $this->utils->getSystemUrl('www', '/Games/'),
                        "icon" => "gift.svg",
                    ),
                lang('Poker') => array(
                        "href" => $this->utils->getSystemUrl('www', '/Poker/'),
                        "icon" => "soccer-ball-variant.svg",
                    ),
                lang('P2P') => array(
                        "href" => $this->utils->getSystemUrl('www', '/P2P/'),
                        "icon" => "soccer-ball-variant.svg",
                    ),
                lang('Promotions') => array(
                        "href" => $this->utils->getSystemUrl('www', '/promotion.html'),
                        "icon" => "gift.svg",
                    ),
            );

    }else{


        switch ($current_url) {

        case 'http://player.og.local':
        case 'http://player.demo.tripleonetech.com':
        case 'http://www.demo.tripleonetech.com':
                $site_links = array(
                        lang('header.home') => array(
                                "href" => $this->utils->getSystemUrl('www', '/index.html'), // "http://www.demo.tripleonetech.com/index.html",
                                "icon" => "casino.svg",
                            ),
                        lang('mg_slots') => array(
                                "href" => $this->utils->getSystemUrl('www', '/index.html'), //"http://www.demo.tripleonetech.com/index.html",
                                "icon" => "slot-machine.svg",
                            ),
                        lang('Live Casino') => array(
                                "href" => $this->utils->getSystemUrl('www', '/index.html?tab=live-casino'), //"http://www.demo.tripleonetech.com/index.html?tab=live-casino",
                                "icon" => "straight-poker.svg",
                            ),
                        lang('Sportsbook') => array(
                                "href" => $this->utils->getSystemUrl('www', '/index.html?tab=sports'), //"http://www.demo.tripleonetech.com/index.html?tab=sports",
                                "icon" => "soccer-ball-variant.svg",
                            ),
                        lang('header.keno') => array(
                                "href" => $this->utils->getSystemUrl('www', '/index.html?tab=lottery'), //"http://www.demo.tripleonetech.com/index.html?tab=lottery",
                                "icon" => "lottery-white.svg",
                            ),
                        lang('header.fishing') => array(
                                "href" => $this->utils->getSystemUrl('www', '/index.html?tab=fishing'), //"http://www.demo.tripleonetech.com/index.html?tab=fishing",
                                "icon" => "gift.svg",
                            ),
                        lang('Promotions') => array(
                                "href" => $this->utils->getSystemUrl('www', '/index.html?tab=promotions'), //"http://www.demo.tripleonetech.com/index.html?tab=promotions",
                                "icon" => "gift.svg",
                            ),
                    );
            break;
        case 'http://player.demo2.tripleonetech.com':
        case 'http://www.demo2.tripleonetech.com':
        default:
                $site_links = array(
                        lang('header.home') => array(
                                "href" => $this->utils->getSystemUrl('www', '/index.html'), //"http://www.demo2.tripleonetech.com/index.html",
                                "icon" => "casino.svg",
                            ),
                        lang('mg_slots') => array(
                                "href" => $this->utils->getSystemUrl('www', '/slot.html'), //"http://www.demo2.tripleonetech.com/slot.html",
                                "icon" => "slot-machine.svg",
                            ),
                        lang('Live Casino') => array(
                                "href" => $this->utils->getSystemUrl('www', '/live-casino.html'), //"http://www.demo2.tripleonetech.com/live-casino.html",
                                "icon" => "straight-poker.svg",
                            ),
                        lang('Sportsbook') => array(
                                "href" => $this->utils->getSystemUrl('www', '/sports.html'), //"http://www.demo2.tripleonetech.com/sports.html",
                                "icon" => "soccer-ball-variant.svg",
                            ),
                        lang('header.keno') => array(
                                "href" => $this->utils->getSystemUrl('www', '/lottery.html'), //"http://www.demo2.tripleonetech.com/lottery.html",
                                "icon" => "lottery-white.svg",
                            ),
                        lang('header.fishing') => array(
                                "href" => $this->utils->getSystemUrl('www', '/fishing.html'), //"http://www.demo2.tripleonetech.com/fishing.html",
                                "icon" => "gift.svg",
                            ),
                        lang('Promotions') => array(
                                "href" => $this->utils->getSystemUrl('www', '/promotions.html'), //"http://www.demo2.tripleonetech.com/promotions.html",
                                "icon" => "gift.svg",
                            ),
                    );
            break;
        }

    }
    
    if (strpos($current_url, 'century') !== false) {
        if (isset($site_links[lang('header.keno')])) {
            unset($site_links[lang('header.keno')]);
        }
    }

?>
<header class="main-navigation">
    <nav class="navbar navbar-inverse">

        <div class="top-header player-center">
            <div class="container">
                <div class="row">
                    <?php
if ($this->authentication->isLoggedIn()) {
	?>

                        <div class="col-md-6 col-sm-4">
                            <?php if (!isset($playerCenterLanguage) || $playerCenterLanguage == 0) : ?>
                            <p>
                                <a href="#" class="mr5" onclick="changeLanguage(1);"><img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/en-icon.png" alt="English Language" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo lang("Translate to English") ?>"></a>
                                <a href="#" class="mr5" onclick="changeLanguage(2);"><img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/cn-icon.png" alt="Chinese Language" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo lang("Translate to Chinese") ?>"></a>
                                <a href="#" class="mr5" onclick="changeLanguage(5);"><img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/kr-icon.jpg" alt="Korean Language" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo lang("Translate to Korean") ?>"></a>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 col-sm-8 date-lang text-right">
                            <ul class="list-unstyled mb0">
                                <li class="inline-block"><a href="#"><?php echo lang("Hello") ?>, <?=playerProperty($player, 'username')?></a></li>
                                <li class="inline-block obg"><a href="#"><?php echo lang("My Account") ?>: <?=$this->utils->displayCurrency($total_balance)?></a></li>

                                <li class="inline-block"><a class="default-btn-logout" href="<?=site_url('player_center/player_center_logout')?>"><?php echo lang("nav.logOut") ?></a></li>
                            </ul>
                        </div>
                    <?php
} else {
	?>

                        <div class="col-md-6">
                            <?php if (!isset($playerCenterLanguage) || $playerCenterLanguage['language'] == 0) : ?>
                            <p>
                                <a href="#" class="mr5" onclick="changeLanguage(1);"><img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/en-icon.png" alt="English Language" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo lang("Translate to English") ?>"></a>
                                <a href="#" class="mr5" onclick="changeLanguage(2);"><img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/cn-icon.png" alt="Chinese Language" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo lang("Translate to Chinese") ?>"></a>
                                <a href="#" class="mr5" onclick="changeLanguage(5);"><img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/kr-icon.jpg" alt="Korean Language" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo lang("Translate to Korean") ?>"></a>

                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 date-lang text-right">
                            <p>
                                <a href="<?=site_url('iframe/auth/login')?>" class="login ml10"><?php echo lang("Login") ?></a>
                                <a href="<?=site_url('player_center/iframe_register')?>" class="registration"><?php echo lang("Registration") ?></a></p>
                        </div>

                    <?php
}

?>
                </div>
            </div>
        </div>

            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/icons/bars.svg"  alt="Menu"/>
                    </button>
                    <a class="navbar-brand" href="/"><img src="<?=$playercenter_logo?>" /></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse pr0 pl0" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav pull-right">

                    <?php if (!empty($site_links)) : ?>
                        <?php foreach ($site_links as $key => $value) { ?>
                            <li><a href=" <?= $value["href"] ?> "><img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/icons/casino.svg" alt="<?php echo $key ?>"/> <?= $key ?></a></li>
                         <?php } ?>
                    <?php endif; ?>
                    </ul>
                </div><!-- /.navbar-collapse -->

            </div><!-- /.container-fluid -->

    </nav>
</header>