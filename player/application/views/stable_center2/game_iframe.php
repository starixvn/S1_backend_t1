<!DOCTYPE html>
<html lang="en">
<head>
<?php
    $defaultScrolling = "no";
    if (isset($isScrolling)){
        $isScrolling = $isScrolling ? "yes" : $defaultScrolling;
    }else{
        $isScrolling = $defaultScrolling;
    }

    $defaultOverflowAuto = "hidden";
    if (isset($isOverflowAuto)){
        $isOverflowAuto = $isOverflowAuto ? "auto" : $defaultOverflowAuto;
    }else{
        $isOverflowAuto = $defaultOverflowAuto;
    }

    $defaultAllowFullScreen = "";
    if (!isset($isAllowFullScreen)){
        $isAllowFullScreen = $defaultAllowFullScreen;
    }

    $defaultResponsive = false;
    if (isset($isResponsive)){
        $isResponsive = $isResponsive ?: $defaultResponsive;
    }else{
        $isResponsive = $defaultResponsive;
    }

    $cmsVersion = $this->CI->utils->getCmsVersion();
    
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php if($isResponsive){ ?>
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, minimumscale=1, maximum-scale=1, user-scalable=no">
<?php } ?>

<title><?php echo @$platformName; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- <link rel="icon" href="<?php echo isset($platform_id) && $platform_id == 51  ? '/game_favicon/fav_'.$platform_id.'.ico' : '/favicon.ico'; ?>" type="image/x-icon" /> -->
<link rel="shortcut icon" href="<?= !empty($favicon_brand) ? $favicon_brand : $this->utils->getPlayerCenterFaviconURL(); ?>" type="image/x-icon" />
<style>
*{padding:0;margin:0;}
html , body {height:100%;overflow:<?php echo $isOverflowAuto?>;}
iframe{border:none;}
a.btn.btn-info {
    margin:2px 0;
    border-radius: 25px;
    padding: 5px 15px;
    background: #2693ff;
    background: linear-gradient(to top, #0059B1 0%, #2693ff 100%);
    border: 0;
}
a.btn.btn-info:hover{
    background: linear-gradient(to bottom, #0059B1 0%, #2693ff 100%);
}
</style>
<script type="text/javascript">
<?php
    echo $this->utils->getConfig('iframe_custom_script') ?: null;
?>
</script>
<link href="<?=$this->utils->cssUrl($this->config->item('default_player_bootstrap_css'))?>" rel="stylesheet" />
<link href="<?=$this->utils->getSystemUrl('www') . '/includes/css/custom-style.css?v=' . $cmsVersion;?>" rel="stylesheet" />
</head>
<body>
<?php if (isset($getPlayerGameHistoryURL)) { ?>
<div style="background-color: #000">
	<a href="<?=$getPlayerGameHistoryURL?>" target="_blank" class="btn btn-info"><i class="glyphicon glyphicon-time"></i> <?=lang('player.ui48')?></a>
	<a href="<?=site_url('/player_center/iframe_makeDeposit')?>" target="_blank" class="btn btn-info"><i class="glyphicon glyphicon-open"></i> <?=lang('cashier.13')?></a>
	<a href="<?=site_url('/player_center/withdraw')?>" target="_blank" class="btn btn-info"><i class="glyphicon glyphicon-save"></i> <?=lang('cashier.14')?></a>
</div>
<?php } ?>

<div style="position:relative; width:100%; height:100%;">
<iframe <?php echo isset($iframeName) && !empty($iframeName) ? 'name="' . $iframeName . '"' : ''; ?> src="<?php echo $url; ?>" <?php echo $isAllowFullScreen ?> <?php echo isset($platform_id) && $platform_id != QQKENO_QQLOTTERY_THB_B1_API ? "scrolling=" . $isScrolling  : ''; ?> frameBorder="0" style="margin:0; padding:0; white-space:nowrap; border:0; width:100%; height:100%"></iframe>
</body>
</div>
</html>
