<?php
$this->utils->startEvent('Load scripts URL');
$base_path = base_url() . $this->utils->getPlayerCenterTemplate();

$standard_js = [
	$this->utils->getPlayerCmsUrl('/resources/third_party/jquery/jquery-3.1.1.min.js'), # jquery is included by default
    $this->utils->getPlayerCmsUrl('/resources/third_party/bootstrap/3.3.7/bootstrap.min.js'),
    // $this->utils->getPlayerCmsUrl('/resources/third_party/webshim/1.15.8/polyfiller.min.js'),
    $this->utils->getPlayerCmsUrl('/resources/third_party/bootstrap-datepicker/1.7.0/bootstrap-datepicker.min.js'),
    $this->utils->getPlayerCmsUrl('/common/js/main.js'),
    $this->utils->getPlayerCmsUrl($base_path . '/js/template-script.js')
];

if ($this->utils->isEnabledFeature('enable_dynamic_javascript')) {
	$addJs = $this->utils->getAddJs();
	if( !empty($addJs) ) {
		foreach ($addJs as $file => $value) {
			array_push($standard_js, $value);
		}
	}
}

$standard_css = [
	$this->utils->getPlayerCmsUrl('/resources/third_party/bootstrap/3.3.7/bootstrap.min.css'),
	$this->utils->getPlayerCmsUrl($this->utils->getPlayerMinifyCssPath('/stable_center2/css/font-awesome.min.css')),
    $this->utils->getPlayerCmsUrl('/resources/third_party/bootstrap-datepicker/1.7.0/bootstrap-datepicker3.min.css'),
	$this->utils->getPlayerCmsUrl($this->utils->getPlayerMinifyCssPath($base_path . '/style.css')),
    $this->utils->getPlayerCmsUrl($this->utils->getPlayerMinifyCssPath($this->utils->getActivePlayerCenterTheme())),
	$this->utils->getAnyCmsUrl('/includes/css/custom-style.css'),
];
$this->utils->endEvent('Load scripts URL');

$metaDataInfo = $this->utils->getMetaDataInfo();

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if ($metaDataInfo) : ?>
	<meta name="description" content="<?= $metaDataInfo['description'] ?>">
	<meta name="keywords" content="<?= $metaDataInfo['keyword'] ?>">
<?php endif; ?>
	<title><?= ($metaDataInfo) ? $metaDataInfo['title'] : lang($this->utils->getPlayertitle());?></title>

	<link rel="icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" type="image/x-icon" />

	<?php foreach ($standard_css as $css_url): ?>
    <link href="<?=$css_url?>" rel="stylesheet"/>
    <?php endforeach; ?>

	<?php foreach ($standard_js as $js_url): ?>
    <script type="text/javascript" src="<?=$js_url?>"></script>
	<?php endforeach; ?>

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

<body class="<?=($this->authentication->isLoggedIn()) ? 'player-logged' : 'player-not-logged'?>">

<?php include $template_path . '/includes/header.php';?>

<?=$this->utils->processHeaderTemplate();?>

<div class="player-center-main-content">
<?php
$this->utils->startEvent('Load main content');
if(!empty($content_template)) {
	include $template_path . '/includes/content_template/'.$content_template;
} else {
	echo $main_content;
}
$this->utils->endEvent('Load main content');
?>
</div>

<?=$this->utils->processFooterTemplate();?>

<?=$this->utils->startEvent('Load custom header'); ?>
<?=$this->utils->getPlayerCenterCustomFooter()?>
<?=$this->utils->endEvent('Load custom header'); ?>

<?php include VIEWPATH . '/resources/common/includes/flash_message.php';?>

<?php
if ($this->authentication->isLoggedIn()) {
    echo $this->CI->load->widget('sidebar');
}
?>

<?=$this->utils->getDevVersionInfo();?>

<?=$this->utils->getAnalyticCode('player')?>

<?php if($this->utils->isEnabledFeature('enable_embedded_lottery_sdk')){
    include VIEWPATH . '/resources/includes/lottery_sdk.php';
}
?>

<?php if($this->utils->isEnabledFeature('enable_agency_support_on_player_center')){
    include VIEWPATH . '/resources/includes/agency_sdk.php';
}
?>

<script type="text/javascript">
    <?php $this->utils->startEvent('Load custom script'); ?>
    <?= $this->utils->getPlayerCenterCustomScript();?>
    <?php $this->utils->endEvent('Load custom script'); ?>
</script>
</body>
</html>