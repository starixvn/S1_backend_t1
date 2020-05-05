<?php 
	$defaultScrolling = "no";
	if (isset($isScrolling)) {
	    $isScrolling = $isScrolling ? "yes" : $defaultScrolling;
	} else {
	    $isScrolling = $defaultScrolling;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, minimumscale=1, maximum-scale=1, user-scalable=no">
<title><?php echo @$platformName; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="icon" href="<?= !empty($favicon_brand) ? $favicon_brand : $this->utils->getPlayerCenterFaviconURL(); ?>" type="image/x-icon" />
<script type="text/javascript">
<?php
	echo $this->utils->getConfig('iframe_custom_script') ?: null;
?>
</script>
<style>
*{padding:0;margin:0;}
html , body {height:100%;overflow:hidden;}
iframe{border:none;}
</style>
</head>
<body>
<div style="position:relative; width:100%; height:100%;">
<iframe id="embedgameIframe" <?php echo isset($iframeName) && !empty($iframeName) ? 'name="' . $iframeName . '"' : ''; ?> src="<?php echo $url; ?>" allowfullscreen allow="fullscreen" <?php echo isset($platform_id) && $platform_id != QQKENO_QQLOTTERY_THB_B1_API ? "scrolling=" . $isScrolling : ''; ?> frameBorder="0" style="margin:0; padding:0; white-space:nowrap; border:0; width:100%; height:100%"></iframe>
</div>
</body>
</html>
