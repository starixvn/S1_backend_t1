<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $platformName;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- <link rel="icon" href="/favicon.ico" type="image/x-icon" /> -->
<?php if (isset($game_favicon)) : ?>
<link rel="shortcut icon" href="<?php echo $game_favicon ?>" type="image/x-icon" />
<?php else : ?>
<link rel="shortcut icon" href="<?= $this->utils->getPlayerCenterFaviconURL() ?>" type="image/x-icon" />
<?php endif; ?>
<style>
*{padding:0;margin:0;}
html , body {height:100%;}
iframe{border:none;}
</style>
</head>
<body onload="document.FRM.submit()">
<form action="<?php echo $url ?>" id="FRM" method="POST" name="FRM" target="sa_iframe">
<input name="username" type="hidden" value="<?php echo $username ?>">
<input name="token" type="hidden" value="<?php echo $token ?>" >
<input name="lobby" type="hidden" value="<?php echo $lobby ?>" >
<input name="lang" type="hidden" value="<?php echo $lang ?>">
<input name="mobile" type="hidden" value="<?php echo $mobile ?>">
<input name="options" type="hidden" value="<?php echo $options ?>">
</form>
<iframe name="sa_iframe" width="100%" height="100%" src="about:blank"></iframe>
</body>
</html>