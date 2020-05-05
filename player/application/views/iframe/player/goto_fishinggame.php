<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FISHING GAME</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<style>
*{padding:0;margin:0;}
html , body {height:100%;}
iframe{border:none;}
</style>
</head>
<body onload="document.ag_form.submit()">
<form name="ag_form" method="GET" action="<?php echo $url;?>" target="ag_iframe">
	<input name="params" type="hidden" value="<?php echo $params ?>">
	<input name="key" type="hidden" value="<?php echo $key ?>">
</form>
<iframe name="ag_iframe" width="100%" height="100%" src="about:blank"></iframe>
</body>
</html>