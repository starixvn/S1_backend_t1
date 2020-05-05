<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,minimum-scale=1,maximum-scale=1" content="user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo @$platformName; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- <link rel="shortcut icon" href="/<?=$this->utils->getPlayerCenterTemplate()?>/fav.ico" type="image/x-icon" /> -->
<link rel="shortcut icon" href="<?= !empty($favicon_brand) ? $favicon_brand : $this->utils->getPlayerCenterFaviconURL(); ?>" type="image/x-icon" />
<style>
*{padding:0;margin:0;}
html , body {height:100%;overflow:hidden;}
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
$loggedPlayerUsername=$this->authentication->getUsername();
echo $this->utils->generateStatCode($loggedPlayerUsername);

$min_width = !empty($set_min_width) ? 'style="min-width:'.$default_frame_min_width.';"' : '';
?>
function submit(){
    document.getElementById("submit").click();
}
</script>
</head>
<body onload="submit()">
<?php if(isset($empty) && $empty){ echo lang('goto_game.sysMaintenance'); } else {?>
<?php if (isset($getPlayerGameHistoryURL)) { ?>
<div style="background-color: #000">
	<a href="<?=$getPlayerGameHistoryURL?>" target="_blank" class="btn btn-info"><i class="glyphicon glyphicon-time"></i> <?=lang('player.ui48')?></a>
	<a href="<?=site_url('/player_center2/deposit')?>" target="_blank" class="btn btn-info"><i class="glyphicon glyphicon-open"></i> <?=lang('cashier.13')?></a>
	<a href="<?=site_url('/player_center/withdraw')?>" target="_blank" class="btn btn-info"><i class="glyphicon glyphicon-save"></i> <?=lang('cashier.14')?></a>
</div>
<?php } ?>
<iframe id="iframe" allowfullscreen="true" name="iframe" width="100%" height="100%" <?php echo $min_width; ?> src=""></iframe>
<form id="submitForm" method="post" target="<?=$redirection=='newtab' ? '_top' : 'iframe' ?>" action="<?php echo $url ?>">
    <input name="Payload"   value="<?php echo $payload; ?>">
    <input id="submit" type="submit" value="Submit">
</form>
<?php } ?>
</body>
</html>
