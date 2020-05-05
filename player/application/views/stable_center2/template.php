<?php
$html_lang_code = $this->language_function->getCurrentLangForPromo();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if($metaDataInfo) : ?>
    <meta name="description" content="<?=$metaDataInfo['description']?>">
    <meta name="keywords" content="<?=$metaDataInfo['keyword']?>">
    <?php endif; ?>
    <title><?=($metaDataInfo) ? $metaDataInfo['title'] : lang($this->utils->getPlayertitle());?></title>

	<link rel="icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" type="image/x-icon" />

    <?=$active_template->renderStyles(); ?>
    <?=$active_template->renderScripts(); ?>

	<?php # Template add_js and add_css content ?>
	<?= (isset($_styles)) ? $_styles : ""; ?>
	<?= (isset($_scripts)) ? $_scripts : ""; ?>
	<?=$this->utils->startEvent('Load custom header'); ?>
	<?=$this->utils->getPlayerCenterCustomHeader()?>
	<?=$this->utils->endEvent('Load custom header'); ?>


<script type="text/javascript">
    var DEPOSIT_PROCESS_MODE1 = <?=DEPOSIT_PROCESS_MODE1?>;
    var DEPOSIT_PROCESS_MODE2 = <?=DEPOSIT_PROCESS_MODE2?>;
    var DEPOSIT_PROCESS_MODE3 = <?=DEPOSIT_PROCESS_MODE3?>;
</script>

<?=$this->CI->load->widget('lang')?>

<?=$this->utils->getTrackingScriptWithDoamin('player', 'gtm', 'header');?>
<?=$this->utils->getTrackingScriptWithDoamin('player', 'ga');?>

</head>

<body lang="<?=$html_lang_code?>">
	<?php if ($this->utils->isEnabledFeature('enabled_player_center_preloader')) : ?>
	<section class="preloader">
		<div class="preloader-container spinner">
			<span class="main-site-loader">
				<span class="preloader">
					<span class="splash-spacer"></span>
					<span class="splash-logo"><img src="<?=$playercenter_logo?>" /></span>
					<?php if ($this->utils->isEnabledFeature('enabled_player_center_spinner_loader')) : ?>
						<span class="spinner-animation-wrapper">
							<span class="show-spinner-animation">
								<span class="splash-spinner spinner"></span>
								<span class="splash-spinner ball"></span>
							</span>
							<span class="splash-message"><?php echo lang("text.loading") ?></span>
						</span>
					<?php endif; ?>
				</span>
			</span>
		</div>
	</section>

    <script type="text/javascript">
        $(function(){
            $(".preloader").addClass("preloader-out");
        })
    </script>
	<?php endif; ?>

<?=$this->utils->processHeaderTemplate();?>

<?php include VIEWPATH . '/resources/common/includes/flash_message.php';?>

<?php
$this->utils->startEvent('Load main content');
if(!empty($content_template)) {
	include 'includes/content_template/'.$content_template;
} else {
	echo $main_content;
}
$this->utils->endEvent('Load main content');
?>

<?php
if ($this->authentication->isLoggedIn()) {
    echo $this->CI->load->widget('sidebar');
}
?>

<?=$this->utils->processFooterTemplate();?>
<?=$this->utils->getDevVersionInfo();?>

<?=$this->utils->getAnalyticCode('player')?>

<script type="text/javascript">
	<?php $this->utils->startEvent('Load custom script'); ?>
	<?= $this->utils->getPlayerCenterCustomScript();?>
	<?php $this->utils->endEvent('Load custom script'); ?>
</script>

<?=$this->utils->startEvent('Load custom header'); ?>
<?=$this->utils->getPlayerCenterCustomFooter()?>
<?=$this->utils->endEvent('Load custom header'); ?>

<?php if($this->utils->isEnabledFeature('enable_embedded_lottery_sdk')){
    include VIEWPATH . '/resources/includes/lottery_sdk.php';
}
?>

<?php if($this->utils->isEnabledFeature('enable_agency_support_on_player_center')){
    include VIEWPATH . '/resources/includes/agency_sdk.php';
}
?>

<?=$this->utils->getTrackingScriptWithDoamin('player', 'gtm', 'footer');?>
</body>
</html>