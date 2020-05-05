<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $platformName;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<style>
*{padding:0;margin:0;}
html , body {height:100%;width: 100%;}
body {
	/*background-image: url('http://www.yongji.t1t.games/assets/img/sport_bg.jpg');*/
	
 	background-image: url('<?php echo $image_url;?>');
	background-repeat: no-repeat;
	background-size: 130% auto;
	background-position: center center;
	background-color: <?php echo $bg_color;?>;
}
iframe{border:none;width:1015px; height: 100%;}
.sports-container{margin: 0 auto; width: 1015px; height: 100%;}
</style>
</head>
<body onload="document.agshaba_form.submit()">
<form name="agshaba_form" method="POST" action="<?php echo $url;?>" target="agshaba_iframe"></form>

<div class="sports-container">
	<iframe name="agshaba_iframe"  src="about:blank" align="center"></iframe>
</div>

</body>
</html>