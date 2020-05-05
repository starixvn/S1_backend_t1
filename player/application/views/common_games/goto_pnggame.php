<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title></title>
		<style>
			html,body{
				margin: 0;
				height: 100%;
			}
		</style>

		<script src="<?php echo $script_inc ?>" type="text/javascript"></script>
		<script type="text/javascript">
			function ShowCashier() {
				window.location = '<CASHIERURL>';
			}
			function PlayForReal() {
				window.location.href = '<?php echo $playforreal ?>';
			}
			function Logout() {
				window.close();
			}
			function reloadgame(gameId, t1ttestnew) {
			}
		</script>
	</head>
	<body>
		<div id="pngCasinoGame">

		You either have JavaScript turned off or an old version of 
		<a target="_blank" href="http://get.adobe.com/Flashplayer/">Adobe's FlashPlayer</a>
		</div>
	</body>
</html>



