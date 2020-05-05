<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->utils->getPlayertitle();?></title>

	<link rel="icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" type="image/x-icon" />

    <?=$active_template->renderStyles(); ?>
    <link href="<?=$this->utils->getAnyCmsUrl('/includes/css/custom-style-iframes.css')?>" rel="stylesheet"/>
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

</head>

<body>
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
	//$this->load->view($this->utils->getPlayerCenterTemplate() . '/includes/quick_transfer_sidebar');
}
?>

<?=$this->utils->getDevVersionInfo();?>

<?=$this->utils->getAnalyticCode('player')?>

<script type="text/javascript">
	<?php $this->utils->startEvent('Load custom script'); ?>
	<?= $this->utils->getPlayerCenterCustomScript();?>
	<?php $this->utils->endEvent('Load custom script'); ?>
	<?php if ($this->utils->isEnabledFeature('enabled_player_center_preloader')) : ?>
	$(function(){
		$(".preloader").addClass("preloader-out");
	})
	<?php endif; ?>
</script>

<?php if($this->utils->isEnabledFeature('enable_embedded_lottery_sdk')){
    include VIEWPATH . '/resources/includes/lottery_sdk.php';
}
?>

<?php if($this->utils->isEnabledFeature('enable_agency_support_on_player_center')){
    include VIEWPATH . '/resources/includes/agency_sdk.php';
}
?>
</body>
</html>