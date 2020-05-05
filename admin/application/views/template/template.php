<style>
    #navbar-customized-focus .navbar-nav > li > a:focus{
        background-color:#006687;
        color:#ffffff;
    }
</style>
<?php
    $this->session->set_userdata('current_url', current_url());
    $user_theme = !empty($this->session->userdata('admin_theme')) ? $this->session->userdata('admin_theme') : $this->config->item('sbe_default_theme');

    $currentLang = $this->session->userdata('login_lan');
    $_SESSION['lang'] = $currentLang;

    if (!isset($company_title)) {
    	$company_title = lang('lang.sb');
    }
    $playerUrl = $this->utils->activePlayerSidebar();
    $csUrl = $this->utils->activeCSSidebar();
    $paymentUrl = $this->utils->activePaymentSidebar();
    $marketingUrl = $this->utils->activeMarketingSidebar();
    $cmsUrl = $this->utils->activeCMSSidebar();
    $affUrl = $this->utils->activeAffiliateSidebar();
    $reportUrl = $this->utils->activeReportSidebar();
    $systemUrl = $this->utils->activeSystemSidebar();
    $agencyUrl = $this->utils->activeAgencySidebar();
    $standard_js = [
        ((strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)) ? $this->utils->jsUrl('jquery-1.11.1.min.js') : $this->utils->jsUrl('jquery-2.1.4.min.js'),
        $this->utils->jsUrl('pub/pubutils.js'),
        $this->utils->thirdpartyUrl('bootstrap/3.3.7/bootstrap.min.js'),
        $this->utils->thirdpartyUrl('bootstrap-notify/bootstrap-notify.min.js'),
    ];

    if(!isset($donotLoadExtraJS) || !$donotLoadExtraJS){
        $standard_js[]=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/moment.min.js');
        $standard_js[]=$this->utils->thirdpartyUrl('bootstrap-daterangepicker-master/daterangepicker.js');
        $standard_js[]=$this->utils->thirdpartyUrl('numeral/numeral.min.js');
        $standard_js[]=$this->utils->thirdpartyUrl('bootstrap-dialog/js/bootstrap-dialog.min.js');
        $standard_js[]=$this->utils->jsUrl('polyfiller.js');
        $standard_js[]=$this->utils->jsUrl('jquery.cookie.min.js');
    }

    $isSuperModeOnMDB = $this->utils->isSuperModeOnMDB();
    $show_notification_data = $this->utils->getSBENotificationCount();
?>
<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="renderer" content="webkit" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="description" content="<?php echo isset($description) ? $description : ''; ?>"/>
        <meta name="keywords" content="<?php echo isset($keywords) ? $keywords : ''; ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <title>
            <?php if($this->utils->isEnabledFeature('include_company_name_in_title')) : ?>
                <?=htmlspecialchars($company_title);?> -
            <?php endif; ?>
            <?=$title?>
        </title>

        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link href="<?=$this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css')?>" rel="stylesheet">
        <?php if(!isset($donotLoadExtraCSS) || !$donotLoadExtraCSS){ ?>
            <link rel="stylesheet" type="text/css" href="<?=$this->utils->cssUrl('template.css')?>">
        <?php if($this->utils->getConfig('use_new_sbe_color')) { ?>
            <link rel="stylesheet" type="text/css" href="<?=$this->utils->cssUrl('newSBEColor.css')?>">
        <?php } ?>
            <link rel="stylesheet" type="text/css" href="<?=$this->utils->cssUrl('icons.css')?>">
            <link rel="stylesheet" type="text/css" href="<?=$this->utils->cssUrl('font-awesome.min.css')?>">
            <link rel="stylesheet" type="text/css" href="<?=$this->utils->cssUrl('daterangepicker.css')?>">
            <link rel="stylesheet" type="text/css" href="<?=$this->utils->thirdpartyUrl('bootstrap-dialog/css/bootstrap-dialog.min.css')?>">
        <?php }?>

        <script type="text/javascript">
            var _site_url = "<?=site_url()?>";
        </script>

        <?=$_styles?>

        <?php
            foreach ($standard_js as $js_url) {
                echo '<script type="text/javascript" src="'.$js_url.'"></script>'."\n";
            }
            include __DIR__."/../includes/templatejs.php";
        ?>

        <?php echo $_scripts; ?>
        <style type="text/css">

            .jstree-inp{
                height: 20px;
                font-size: 11px;
                width: 50px;
            }
            #main_content{
                min-height: 500px;
            }
            .text_middle, td.text_middle{
                vertical-align: middle;
            }
            .blink {
                -webkit-animation-name: blinker;
                -webkit-animation-duration: 1s;
                -webkit-animation-timing-function: linear;
                -webkit-animation-iteration-count: infinite;

                -moz-animation-name: blinker;
                -moz-animation-duration: 1s;
                -moz-animation-timing-function: linear;
                -moz-animation-iteration-count: infinite;

                animation-name: blinker;
                animation-duration: 1s;
                animation-timing-function: linear;
                animation-iteration-count: infinite;
            }

            @-moz-keyframes blinker {
                0% { opacity: 1.0; }
                50% { opacity: 0.0; }
                100% { opacity: 1.0; }
            }

            @-webkit-keyframes blinker {
                0% { opacity: 1.0; }
                50% { opacity: 0.0; }
                100% { opacity: 1.0; }
            }

            @keyframes blinker {
                0% { opacity: 1.0; }
                50% { opacity: 0.0; }
                100% { opacity: 1.0; }
            }

            #warning_message_text{
                font-weight: bold;
            }
        </style>
    </head>

    <body data-theme="<?=$user_theme?>" data-lang="<?=$currentLang?>">
        <?php include VIEWPATH . '/includes/player_helper_menu.php'; ?>

        <!-- NAVIGATION -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">
                    <?php if ($this->utils->useSystemDefaultLogo() || !$this->utils->isUploadedLogoExist() || !$this->utils->isLogoOperatorSettingsExist() || !$this->utils->isLogoSetOnDB()) : ?>
                        <img class="smartadmin-logo" style="display: inline;" src="<?php echo $this->utils->getDefaultLogoUrl(); ?>" width="26"> <?=htmlspecialchars($company_title);?>
                    <?php else: ?>
                        <img class="smartadmin-logo" style="margin-top: -10px;margin-left: -10px;" src="<?php echo $this->utils->setSBELogo(); ?>">
                    <?php endif; ?>
                </a>
            </div>

            <div class="collapse navbar-collapse show_notification_data" id="bs-navbar-collapse-1">
                <div class="container-fluid">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="glyphicon glyphicon-globe"></i>
                                <span class="approved_thirdparty_count badge notification_count">
                                    <?=$show_notification_data['sum_notif']?>
                                </span>
                                <span class="caret"></span>
                            </a>
                            <ul id="approved_thirdparty_count" class="dropdown-menu user-option" role="menu">
                                <?php if (isset($show_notification_data['notificatons']['promo'])) { ?>
                                    <li>
                                       <a href="/marketing_management/promoApplicationList?status=<?=Player_promo::TRANS_STATUS_REQUEST.'&transactionDateType='.Player_promo::TRANSACTION_DATE_TYPE_REQUEST_TIME.'&player_promo_status='.Player_promo::TRANS_STATUS_REQUEST?>">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('lang.promo')?></span>
                                            <span class="promo-count-dropdown badge notification_count"><?=$show_notification_data['notificatons']['promo']?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (isset($show_notification_data['notificatons']['messages'])) {?>
                                     <li>
                                       <a href="/cs_management/messages">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('cs.messages')?> </span>
                                            <span class="message-count-dropdown badge notification_count"><?=$show_notification_data['notificatons']['messages']?></span>
                                        </a>
                                    </li>
                                <?php }?>
                                <?php if (isset($show_notification_data['notificatons']['deposit_list'])) {?>
                                    <li>
                                       <a href="<?php echo site_url('/home/nav/deposit_local');?>">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('Local Deposit')?> </span>
                                            <span class="deposit-count-offline-dropdown badge notification_count">
                                                <?=$show_notification_data['notificatons']['deposit_list']['bank_deposit']?>
                                            </span>
                                        </a>
                                    </li>

                                    <?php if (isset($show_notification_data['notificatons']['deposit_list']['thirdparty'])) {?>
                                        <li>
                                            <a href="<?php echo site_url('/home/nav/deposit_3rdparty');?>">
                                                <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('3rdParty Deposit')?></span>
                                                <span class="deposit-count-thrdparty-dropdown badge notification_count">
                                                    <?=$show_notification_data['notificatons']['deposit_list']['thirdparty']?>
                                                </span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if (isset($show_notification_data['notificatons']['withdrawal_request'])) {?>
                                    <li>
                                        <a href="<?php echo site_url('/home/nav/withdrawal/true');?>">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('player.ub06')?></span>
                                            <span class="withdraw-count-dropdown badge notification_count"><?=$show_notification_data['notificatons']['withdrawal_request']?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (isset($show_notification_data['notificatons']['agent_withdraw_request'])) {?>
                                    <li>
                                        <a href="<?php echo site_url('/agency_management/agency_payment?status=1');?>">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('Agency Withdraw')?></span>
                                            <span class="agentWithdrawRequest-count-dropdown badge notification_count"><?=$show_notification_data['notificatons']['agent_withdraw_request']?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (isset($show_notification_data['notificatons']['affiliate_withdraw_request'])) {?>
                                    <li>
                                        <a href="<?php echo site_url('/affiliate_management/paymentSearchPage?status=1');?>">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('Affiliate Withdraw')?></span>
                                            <span class="affiliateWithdrawRequest-count-dropdown badge notification_count"><?=$show_notification_data['notificatons']['affiliate_withdraw_request']?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (isset($show_notification_data['notificatons']['new_player'])) {?>
                                    <li>
                                        <a href="<?php echo site_url('/home/nav/new_player');?>">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('report.sum05')?></span>
                                            <span class="new-player-count-dropdown badge notification_count"><?=$show_notification_data['notificatons']['new_player']?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (isset($show_notification_data['notificatons']['self_exclusion_request'])){?>
                                    <li>
                                        <a href="<?php echo site_url('report_management/responsibleGamingReport');?>">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('Self Exclusion')?></span>
                                            <span class="selfExclusionRequest-count-dropdown badge notification_count"><?=$show_notification_data['notificatons']['self_exclusion_request']?></span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if (isset($show_notification_data['notificatons']['new_games'])): ?>
                                    <li>
                                        <a href="<?php echo site_url('game_description/viewNewGames');?>">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('New Games')?></span>
                                            <span class="newgame-count-dropdown badge notification_count"><?=$show_notification_data['notificatons']['new_games']?></span>
                                        </a>
                                    </li>
                                <?php endif ?>

                                <?php if (isset($show_notification_data['notificatons']['new_player_attachment'])): ?>
                                    <li>
                                        <a href="<?php echo site_url('report_management/viewAttachedFileList');?>">
                                            <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:black;' : '' ?>"><?=lang('Player Attachments')?></span>
                                            <span class="new_player_attachment-count-dropdown badge notification_count"><?=$show_notification_data['notificatons']['new_player_attachment']?></span>
                                        </a>
                                    </li>
                                <?php endif ?>
                            </ul>
                        </li>
                        <li>
                            <a id="user-link" href="<?=site_url('user_management/viewUserSetting')?>" class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <?=$username?> <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?=site_url('user_management/viewUserSetting')?>"><i class="glyphicon glyphicon-cog"></i> <?=lang('lang.settings')?></a></li>
                                <li class="divider"></li>
                                <li><a id="logout" href="<?=site_url('auth/logout')?>" onclick="return confirm('<?=lang('sys.sure')?>')"><i class="glyphicon glyphicon-off"></i> <?=lang('lang.logout')?></a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="navbar-form navbar-right">
                        <div class="form-group">
                            <input id="gotomemberinfo" type="text" class="form-control input-sm" placeholder="<?=lang('header.username')?>">
                        </div>
                        <div class="form-group">
                            <select name="language" id="lang_select" class="form-control input-sm" style="background-color:#ffffff; color:#6f6f6f">
                                <option value="1" <?=($currentLang == '1') ? 'selected' : ''?> >English</option>
                                <option value="2" <?=($currentLang == '2') ? 'selected' : ''?> >中文</option>
                                <option value="3" <?=($currentLang == '3') ? 'selected' : ''?> >Indonesian</option>
                                <option value="4" <?=($currentLang == '4') ? 'selected' : ''?> >Vietnamese</option>
                                <option value="5" <?=($currentLang == '5') ? 'selected' : ''?> >Korean</option>
                                <option value="6" <?=($currentLang == '6') ? 'selected' : ''?> >Thai</option>
                            </select>
                        </div>
                        <?php if(!empty($currency_select_html)){ ?>
                            <div class="form-group">
                                <?=$currency_select_html?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <nav id="navbar-customized-focus" class="navbar-inverse" style="margin-bottom: 0; margin-left: -15px;">
                    <div class="container-fluid">
                        <ul class="nav navbar-nav">
                            <?php if ($this->permissions->checkPermissions('player') && $playerUrl != '/home') {?>
                                <li <?=($activenav == 'player') ? 'class="active"' : ''?> >
                                    <a href="<?=site_url($playerUrl)?>"><?=lang('a_header.player');?></a>
                                </li>
                            <?php }?>

                            <?php if(!$isSuperModeOnMDB){ ?>
                                <?php if ($this->permissions->checkPermissions('cs') && $csUrl != '/home') {?>
                                    <li <?=($activenav == 'cs') ? 'class="active"' : ''?> >
                                        <a href="<?=site_url($csUrl)?>"><?=lang('a_header.cs');?></a>
                                    </li>
                                <?php }?>

                                <?php if ($this->permissions->checkPermissions('payment') && $paymentUrl != '/home') {?>
                                    <li <?=($activenav == 'payment') ? 'class="active"' : ''?> >
                                        <a href="<?=site_url($paymentUrl)?>" ><?=lang('a_header.payment');?></a>
                                    </li>
                                <?php }?>

                                <?php if ($this->permissions->checkPermissions('marketing') && $marketingUrl != '/home') {?>
                                    <li <?=($activenav == 'marketing') ? 'class="active"' : ''?> >
                                        <a href="<?=site_url($marketingUrl)?>"><?=lang('a_header.marketing');?></a>
                                    </li>
                                <?php }?>
                            <?php }?>

                            <?php if ($this->permissions->checkPermissions('cms')  && $cmsUrl != '/home') {?>
                                <li <?=($activenav == 'cms') ? 'class="active"' : ''?> >
                                    <a href="<?=site_url($cmsUrl)?>"><?=lang('a_header.cms');?></a>
                                </li>
                            <?php }?>

                            <?php if ($this->permissions->checkPermissions('affiliate') && !$this->utils->isEnabledFeature('hide_affiliate') && !$this->utils->isEnabledFeature('close_aff_and_agent') && $affUrl != '/home') {?>
                                <li <?=($activenav == 'affiliate') ? 'class="active"' : ''?> >
                                    <a href="<?=site_url($affUrl)?>"><?=lang('a_header.affiliate');?></a>
                                </li>
                            <?php }?>

                           <?php if ($this->permissions->checkPermissions('report') && $reportUrl != '/home') {?>
                                <li <?=($activenav == 'report') ? 'class="active"' : ''?> >
                                    <a href="<?=site_url($reportUrl)?>"><?=lang('a_header.report');?></a>
                                </li>
                            <?php }?>

                            <?php if ($this->permissions->checkPermissions('system') && $systemUrl != '/home') {?>
                                <li <?=($activenav == 'system') ? 'class="active"' : ''?> >
                                    <a href="<?=site_url($systemUrl)?>"><?=lang('a_header.system');?></a>
                                </li>
                            <?php }?>

                            <?php if ($this->permissions->checkPermissions('agency') && $agencyUrl != '/home') {?>
                                <li <?=($activenav == 'agency') ? 'class="active"' : ''?> >
                                    <a href="<?=site_url($agencyUrl)?>"><?=lang('a_header.agency');?></a>
                                </li>
                            <?php }?>
                            <?php  if ($this->permissions->checkPermissions('super_report') && $isSuperModeOnMDB) {?>
                                <li <?=($activenav == 'super_report') ? 'class="active"' : ''?> >
                                    <a href="<?=site_url('super_report_management')?>"><?=lang('Super Report');?></a>
                                </li>
                            <?php }?>

                            <?php if(!$isSuperModeOnMDB){ ?>
                               <?php if ($this->permissions->checkPermissions('theme_management')) {?>
                                    <li <?=($activenav == 'theme_management') ? 'class="active"' : ''?> >
                                        <a href="<?=site_url('theme_management')?>"><?=lang('Theme');?></a>
                                    </li>
                                <?php }?>
    							<?php if ( $this->utils->isAvailableT1LotteryBO() && ( $this->permissions->checkPermissions('t1lottery_bo') || $this->utils->isUserListedInLotteryExtra() ) ) {?>
    								<li <?=($activenav == 't1lottery_bo') ? 'class="active"' : ''?> >
    									<a href="<?=site_url('/redirect/t1lottery_bo')?>" target="_blank"><?=lang('Lottery BO');?></a>
    								</li>
    							<?php }?>
                            <?php } ?>
                            <?php if($isSuperModeOnMDB && $this->permissions->checkPermissions('manage_currency')){ ?>
                                <li <?=($activenav == 'currency') ? 'class="active"' : ''?> >
                                    <a href="<?=site_url('/system_management/manage_currency')?>"><?=lang('Manage Currency');?></a>
                                </li>
                            <?php } ?>
                        </ul>

                        <ul class="nav navbar-nav navbar-right hidden-sm">
                            <li>
                                <a href="http://www.gamegateway.t1t.games/pdf/SmartbackendUserGuide_2.8.22.1001.pdf" target="_blank" >
                                    <i class="glyphicon glyphicon-question-sign text-warning"></i>
                                    <span id="hide_text" >
                                        <?=lang('Guide')?>
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </nav>

        <!-- Page Content -->
        <!-- /.container -->
        <?php if (!empty($sidebar)) {?>
            <div id="sidebar-wrapper" class="<?=($this->session->userdata('sidebar_status') !== 'active') ? 'active' : 'inactive';?>">
                <?=$sidebar?>
            </div>
        <?php }?>
        <div id="page-content-wrapper">
            <span id="sidebar_status" class="<?=($this->session->userdata('sidebar_status') == 'active') ? 'active' : 'inactive';?>"></span>
                <div id="wrapper" class="<?=($this->session->userdata('sidebar_status') == 'active') ? 'active' : 'inactive';?>">
                    <?php
                        $alert_message = $this->session->userdata('message');
                        $alert_type = $this->session->userdata('result');
                        if (!empty($alert_message)) {
                            $this->session->unset_userdata('result');
                            $this->session->unset_userdata('message');
                    ?>
                        <script type="text/javascript">
                            $(document).ready(function(){
                                /*
                                This  prevents loading of SBE login during ajax | The solution is to refresh the page  when unauthorized  or not logged in
                                Note:This will parse all ajax request error and will activate when user not login during ajax
                                work around OGP-1442
                                */
                                $(document).ajaxError(function(event,xhr,options,thrownError){
                                    if(thrownError = "Unauthorized"){
                                        location.reload();
                                    }
                                });
                            });

                            $(function () {
                                $.notify({
                                    // options
                                    message: <?php echo json_encode($alert_message); ?>
                                },{
                                    // settings
                                    <?php echo $alert_type=='danger' || $alert_type=='warning' ? "delay: 0," : "";?>
                                    type: '<?php echo $alert_type; ?>',
                                    // showProgressbar: true,
                                    mouse_over: 'pause',
                                    //OGP-4939: added this template for automation testing, request from qa.
                                    template:   '<div id="<?=$alert_type?>_message_prompt" data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<span data-notify="icon"></span> ' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span id="<?=$alert_type?>_message_text" data-notify="message" style="word-wrap:break-word;word-break:break-all;white-space: pre-wrap;">{2}</span>' +
                                    '<div class="progress" data-notify="progressbar">' +
                                    '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                                    '</div>' +
                                    '<a href="{3}" target="{4}" data-notify="url"></a>' +
                                    '</div>'
                                });
                            })
                        </script>
                    <?php } ?>

                <div id="main_content">
                    <?=$main_content?>
                </div>
                <div class="custom-container">
                    <hr>
                    <!-- Footer -->
                    <footer>
                        <div class="row">
                            <div class="col-lg-12">
                                <center><p><a target="_blank" href="<?=$this->utils->getSystemUrl('www')?>"><?=preg_replace("(^https?://)", "", $this->utils->getSystemUrl('www'))?></a> Copyright &copy; <?=date('Y');?> | <span id="time"></span> </p>  <center>
                                <center><p><?php echo PRODUCTION_VERSION. '-' . $this->utils->getRuntimeEnv(); ?> | <?=$this->utils->getActiveTargetDB()?></p></center>
                            </div>
                        </div>
                        <?php echo $this->utils->getDevVersionInfo(); ?>
                    </footer>
                </div>
            </div>
        </div>

        <!-- DOCUMENT ON READY -->
        <script type="text/javascript">

            $(function() {
                $('#lang_select').change(function(){
                    var lang = $(this).val();
                    $.ajax({
                        'url' : "<?php echo site_url('user_management/setCurrentLanguage'); ?>/"+lang,
                        'type' : 'GET',
                        'dataType' : "json",
                        'success' : function(data){
                            if(data.status=='success'){
                                location.reload();
                            }
                        }
                    });
                });

                $("#alert-success").delay(300).addClass("in").fadeOut(5000);
                $("#menu-toggle").click(function(e) {
                    e.preventDefault();
                    $("#wrapper").toggleClass("active");
                    $("#sidebar_status").attr("class", "active");
                    if ($("#wrapper").hasClass('active')) {
                        $('#sidebar-wrapper').removeClass('active');
                        $(".list-group-item #hide_text").show();
                        $(".list-group-item #icon").removeClass("pull-right");
                        $("#main_icon").attr("class", "icon-arrow-left pull-right");

                    } else {
                        $('#sidebar-wrapper').attr('class', 'active');
                        $(".list-group-item #hide_text").hide();
                        $(".list-group-item #icon").addClass("pull-right");
                        $("#main_icon").attr("class", "icon-arrow-right pull-right");
                        $("#sidebar_status").attr("class", "inactive");
                    }
                });

                <?php if(!isset($donotLoadExtraJS) || !$donotLoadExtraJS){ ?>
                    webshims.polyfill('forms forms-ext');
                <?php }?>

                setInterval(function(){
                    $("#time").text(userTime());
                }, 60000);

                function userTime(){
                    var gmtRe = /GMT([\-\+]?\d{4})/; // Look for GMT, + or - (optionally), and 4 characters of digits (\d)
                    var d = new Date().toString();
                    var tz = gmtRe.exec(d)[0];

                    var date = new Date();
                    var hours = date.getHours();
                    var minutes = date.getMinutes();
                    var ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    minutes = minutes < 10 ? '0'+minutes : minutes;
                    var strTime = hours + ':' + minutes + ' ' + ampm +' '+tz;
                    return strTime;
                }
            });

           $('#gotomemberinfo').keypress(function(e) {
                if (!e) e = window.event;
                var keyCode = e.keyCode || e.which;
                if (keyCode == '13'){
                    var username = document.getElementById('gotomemberinfo').value;
                    $.post('/api/search_multiple_id/'+username, null, function(data) {
                        if (data && data['success']) {
                            window.location.href = data['url'];
                        } else {
                            $.notify({
                                // options
                                message: '<?=lang('lang.player')?> "' + username + '" <?=lang('player.uab10')?>'
                            },{
                                // settings
                                type: 'danger',
                                mouse_over: 'pause',
                                //OGP-4939: added this template for automation testing, request from qa.
                                template:   '<div id="danger_message_prompt" data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert">' +
                                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                                '<span data-notify="icon"></span> ' +
                                                '<span data-notify="title">{1}</span> ' +
                                                '<span id="danger_message_text" data-notify="message">{2}</span>' +
                                                '<div class="progress" data-notify="progressbar">' +
                                                    '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                                                '</div>' +
                                                '<a href="{3}" target="{4}" data-notify="url"></a>' +
                                            '</div>'
                            });
                        }
                    });
                }
            });

            <?php
                $this->load->library('authentication');
                $currSessionId = $this->session->userdata('session_id');
                if (empty($currSessionId)) {
                	$currSessionId = 0;
                }
                $sessionUserId = $this->authentication->getUserId();
                if ($sessionUserId == null) {
                	$sessionUserId = 0;
                }

                $refresh_session_url = $this->utils->site_url_with_host('/admin/' . $sessionUserId . '/' . $currSessionId . '/refresh_session.gif');
                $ping_time = $this->config->item('ping_time');
                $ping = $this->utils->getPing($ping_time, $refresh_session_url);
                echo $ping;
            ?>

            var stopLoader = false;

            $(document).ready(function(){
                resizeSidebar();

                $( window ).resize(function() {
                    resizeSidebar();
                });

                $(document).ajaxError(function(event,xhr,options,exc){
                    stopLoader=true;
                });

                function hideLoader(id,progWrapperId){
                    $("#"+id).css("width", 0 + "100%").hide();
                    $('#'+progWrapperId).hide();
                }

                function showLoader(id,progWrapperId){
                    $("#"+id).css("width", "100%").text("<?php echo lang('text.loading');?>").show();
                    $('#'+progWrapperId).show();
                }

                $(".dataTable").each(function(index) {
                    $(this).on( 'preInit.dt', function () {
                        var id = $(this).attr('id');

                        var progress  = '<div class="progress" id="progress-wrapper-'+id+index+'" style="display:none;">';
                        progress += '<div class="progress-bar  progress-bar-success progress-bar-striped active" id="progress-'+id+index+'" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;height">';
                        progress += '0%';
                        progress += '</div></div>';

                        $(this).closest('.dataTables_wrapper').find('.progress-container').html(progress);
                        $(this).on( 'preXhr.dt', function () {
                            showLoader('progress-'+id+index, 'progress-wrapper-'+id+index);
                        });

                        $(this).on( 'xhr.dt', function () {
                            hideLoader('progress-'+id+index, 'progress-wrapper-'+id+index);
                        });
                    });
                });
            });

            function initBarLoader(tableId) {
                $('#'+tableId).on( 'preInit.dt', function () {
                    var id = $(this).attr('id');

                    var progress  = '<div class="progress" id="progress-wrapper-'+id+'" style="display:none;">';
                    progress += '<div class="progress-bar  progress-bar-success progress-bar-striped active" id="progress-'+id+'" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;height">';
                    progress += '0%';
                    progress += '</div></div>';

                    $(this).closest('.dataTables_wrapper').find('.progress-container').html(progress);
                    $(this).on( 'preXhr.dt', function () {
                        $("#progress-" + id).css("width", "100%").text("<?php echo lang('text.loading');?>").show();
                        $('#progress-wrapper-'+id).show();
                    });

                    $(this).on( 'xhr.dt', function () {
                        $("#progress-" + id).css("width", 0 + "100%").hide();
                        $('#progress-wrapper-'+id).hide();
                    });
                });
            }

            //global function
            var ATTACH_DATATABLE_BAR_LOADER = (function () {
                $(document).ajaxError(function(event,xhr,options,exc){
                    stopLoader=true;
                });

                return {
                    init:function(tableId) {
                        initBarLoader(tableId);
                    }
                }
            })();

            <?php if ($this->utils->getConfig('disable_copy_cut')) { ?>
                $('body').bind('copy cut', function (e) {
                    e.preventDefault();
                });
            <?php } if ($this->utils->getConfig('disable_contextmenu')) { ?>
                $("body").on("contextmenu",function(e){
                    return false;
                });
            <?php } ?>

            //should be array
            var donot_auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('donot_auto_redirect_to_https_list'))?>;
            var auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('auto_redirect_to_https_list'))?>;

            _pubutils.checkAndGoHttps(auto_redirect_to_https_list, donot_auto_redirect_to_https_list);
        </script>

        <?php
            if(!isset($offChat)){
                echo $this->utils->appendAdminSupportLiveChat();
            }
        ?>
        <?php echo $this->utils->getAnalyticCode('admin'); ?>

        <?php echo $this->utils->appendFeedback(); ?>
        <!-- customize admin css -->
        <style type="text/css">
            <?php echo isset($admin_css) ? $admin_css : '';?>
        </style>
    </body>
</html>
