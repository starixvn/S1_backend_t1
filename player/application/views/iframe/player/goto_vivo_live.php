<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>VIVO</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<style>
*{padding:0;margin:0;}
html , body {height:100%;}
iframe{border:none;}
</style>
</head>
<body onload="document.FRM.submit()">
<form action="<?php echo $url ?>" id="FRM" method="POST" name="FRM" target="vivo_iframe">
<!-- <input name="token" type="hidden" value="<?php echo $token ?>">
<input name="operatorID" type="hidden" value="<?php echo $operatorID ?>">
<input name="logoSetup" type="hidden" value="logoSetup" >
<input name="language" type="hidden" value="<?php echo $language ?>">
<input name="serverID" type="hidden" value="<?php echo $serverID ?>">
<input name="isPlaceBetCTA" type="hidden" value="<?php echo $isPlaceBetCTA ?>"> -->
</form>
<iframe name="vivo_iframe" width="100%" height="100%" src="about:blank"></iframe>
</body>
</html>