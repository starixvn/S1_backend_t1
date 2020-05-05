<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $platformName;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- <link rel="icon" href="/game_favicon/fav_38.ico" type="image/x-icon" /> -->
<link rel="shortcut icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" type="image/x-icon" />
<style>
*{padding:0;margin:0;}
html , body {height:100%;}
iframe{border:none;}
</style>
</head>
<body onload="document.FRM.submit()">
<form action="<?php echo $url ?>" id="FRM" method="POST" name="FRM" target="ag_iframe">
<input name="brandid" type="hidden" value="<?php echo $brandid ?>">
<input name="keyname" type="hidden" value="<?php echo $gameid ?>">
<input name="token" type="hidden" value="<?php echo $token ?>" >
<input name="mode" type="hidden" value="<?php echo $mode ?>">
<input name="locale" type="hidden" value="<?php echo $lang ?>">
<input name="mobile" type="hidden" value="0">
</form>
<iframe name="ag_iframe" width="100%" height="100%" src="about:blank"></iframe>
</body>
</html>