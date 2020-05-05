<?php
$html_lang_code = $this->language_function->getCurrentLangForPromo();
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1, minimum-scale=1.0, maximum-scale=3.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <title><?= lang($this->utils->getPlayertitle());?></title>
    <link rel="icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" />
    <link rel="apple-touch-icon" href="<?=site_url($template_name . '/mobile/images/touch-icon-iphone.png')?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?=site_url($template_name . '/mobile/images/touch-icon-ipad.png')?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?=site_url($template_name . '/mobile/images/touch-icon-iphone-retina.png')?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?=site_url($template_name . '/mobile/images/touch-icon-ipad-retina.png')?>">
    <link rel="apple-touch-startup-image" href="<?=site_url($template_name . '/mobile/images/milanoo_startup.png')?>" />
    <!-- css -->
    <?=$active_template->renderStyles(); ?>
    <!-- js -->
    <script type="text/javascript">
        var base_url = "<?=site_url()?>";
    </script>
    <?=$active_template->renderScripts(); ?>

    <?php echo $_styles; ?>
    <?php echo $_scripts; ?>
    <style type="text/css">
        .ismobile{
            display: block !important;
        }

        .isweb{
            display: none !important;
        }

        /*.loginbox{
            display: none !important;
        }*/
    </style>
    <script type="text/javascript">
        var imgloader = "/resources/images/ajax-loader-big2.gif";
        var base_url = "<?=site_url()?>";
        var player_id = "<?php echo $this->authentication->getPlayerId(); ?>";

        function number_format(n) {
            n += "";
            var arr = n.split(".");
            var re = /(\d{1,3})(?=(\d{3})+$)/g;
            return arr[0].replace(re,"$1,") + (arr.length == 2 ? "." + arr[1] : "");
        }
    </script>
    <?=$this->CI->load->widget('lang')?>

    <?=$this->load->view('resources/third_party/DateTimePicker');?>
    <?=$this->utils->startEvent('Load custom header'); ?>
    <?=$this->utils->getMobilePlayerCenterCustomHeader()?>
    <?=$this->utils->endEvent('Load custom header'); ?>

    <?=$this->utils->getTrackingScriptWithDoamin('player_mobile', 'gtm', 'header');?>
    <?=$this->utils->getTrackingScriptWithDoamin('player_mobile', 'ga');?>
</head>
<body lang="<?=$html_lang_code?>" >
	<div class="container-fluid">
        <?php include VIEWPATH . '/resources/common/includes/flash_message.php';?>

        <div class="header" id="header_template">
            <div class="head_back">
                <?php if(!$active_template->isHomePage() && !in_array($this->uri->segment(2), ['callback_success'])): ?>
                <div class="back_btnPT" onclick="history.go(-1);"></div>
                <?php endif ?>
            </div>
        <div class="header_title"><?php if($this->utils->isEnabledFeature('enable_mobile_logo_add_link')): ?><a href="<?=$this->utils->getSystemUrl('m')?>"><?=$active_template->renderFunctionTitle()?></a><?php else: ?><?=$active_template->renderFunctionTitle()?><?php endif; ?></div>
            <div class="header_player"><div id="_player_login_area" class="loginbox fn-right pull-right hidden"></div></div>
        </div>

        <div class="row content">
            <?=$main_content?>
        </div>

        <?=$this->load->view($template_name . '/mobile/includes/menu_bar');?>
	</div>
	<?php  echo $this->utils->getAnalyticCode('player'); ?>

<script type="text/javascript">
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload()
    }
};

<?php $this->utils->startEvent('Load custom script'); ?>
<?= $this->utils->getPlayerCenterCustomScript();?>
<?php $this->utils->endEvent('Load custom script'); ?>
</script>
<?=$this->utils->startEvent('Load custom header'); ?>
<?=$this->utils->getMobilePlayerCenterCustomFooter()?>
<?=$this->utils->endEvent('Load custom header'); ?>

<?php if($this->utils->isEnabledFeature('enable_embedded_lottery_sdk')){
    include VIEWPATH . '/resources/includes/lottery_sdk.php';
}
?>

<?php if($this->utils->isEnabledFeature('enable_agency_support_on_player_center')){
    include VIEWPATH . '/resources/includes/agency_sdk.php';
}
?>

<?=$this->utils->getTrackingScriptWithDoamin('player_mobile', 'gtm', 'footer');?>
</body>
</html>