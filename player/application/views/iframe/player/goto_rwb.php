<!DOCTYPE>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $platformName;?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link rel="icon" href="/favicon.ico" type="image/x-icon" />
		<style>
		*{padding:0;margin:0;}
		html , body {height:100%;}
		iframe{border:none;}
		 #qnxFrame {
			width:100% !important; /* This one is used */
			height:100% !important; /* This one is used */ 
		}
		body {
			overflow:hidden;
		}
		body.spb-bg {
			background: #e6e6e6;
		}
		
		</style>
		<?php if ($setType == "href") {?>
			<style>
				hr.top-red {
					background: #ba2020;
					height: 9px;
					border: 0;
					width: 100%;
				}
				.fr-wrapper {
				    position: relative;
				    background: url('<?php echo $bgImage ?>') no-repeat;
				    background-size: contain;
				    height: 550px;
				    width: 100%;
				}
				.fr-wrapper a#rwb_iframe {
					background: #ba2020;
				    background: -moz-linear-gradient(top, #ba2020 0%, #850404 100%);
				    background: -webkit-linear-gradient(top, #ba2020 0%,#850404 100%);
				    background: linear-gradient(to bottom, #ba2020 0%,#850404 100%);
				    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ba2020', endColorstr='#850404',GradientType=0 );
				    color: #fff;
				    border-radius: 40px;
				    border: 3px solid rgb(255,255,255, 0.8);
				    font-family: "Helvetica Neue", Helvetica, Arial, "Microsoft Yahei", 微软雅黑, STXihei, 华文细黑, sans-serif !important;
				    font-size: 20px;
				    cursor: pointer;
				    display: block;
				    margin: 0 auto;
				    width: 145px;
				    text-align: center;
				    height: 39px;
				    line-height: 38px;
				}
				.fr-wrapper div {
					padding-top: 13%;
				}
				.fr-wrapper a#rwb_iframe:hover {
					background: #ad1313;
					background: -moz-linear-gradient(top, #ad1313 0%, #700404 100%);
					background: -webkit-linear-gradient(top, #ad1313 0%,#700404 100%);
					background: linear-gradient(to bottom, #ad1313 0%,#700404 100%);
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ad1313', endColorstr='#700404',GradientType=0 );
				}
			</style>
		<?php }?>
	</head>
	<body class="spb-bg">
		<hr class="top-red">
		<div class="fr-wrapper">
			<?php if ($setType == "iframe") {?>
				<div id="rwb_iframe" c></div>
			<?php }else{ ?>
				<div>
					<a id="rwb_iframe"><?= lang('Sportsbook') ?></a>
				</div>
			<?php } ?>
		</div>
	</body>
	<script>
		!function(n,e,t,r){
			n.qnx=n.qnx||function(){(n.qnx.q=n.qnx.q||[]).push(arguments)};
			var a=e.createElement(t),c=e.getElementsByTagName(t)[0];
			a.async=1,a.src=r,c.parentNode.insertBefore(a,c)
		}
		(window,document,"script","https://apistage.rwbinter.com/scripts/integration.bundle.js");
		qnx("setClient", "<?php echo $integration_key ?>");
		qnx("setType", "<?php echo $setType ?>"); 
		qnx("setAuth",
		{
		userId: "<?php echo $userId ?>",
		authToken: "<?php echo $authToken ?>"
		});
		qnx("setContainer", "#rwb_iframe");
		qnx("setLang", "<?php echo $language ?>");
		qnx("setTimezone", "GMT +3)");
		qnx("navigateTo", "/events/sports)");
		qnx("setBackUrl", "<?php echo $setBackUrl ?>");
		qnx("start");
	</script>
	<script type="text/javascript">
		document.onreadystatechange = function () {
			var state = document.readyState
			if (state == 'complete') {
			  document.getElementById("rwb_iframe").click();
			} 
		}
		setTimeout(function(){ 
			document.getElementById("rwb_iframe").click(); 
		}, 3000);
	</script>
</html>