<?php
$language = $this->session->userdata('currentLanguage');
if (!empty($language)) {
	echo "<script type='text/javascript'>\n";
	echo "var current_lang = " . json_encode($language) . "\n";
	echo "</script>\n";
}
?>

<script type="text/javascript" src="http://cache.download.banner.drunkenmonkey88.com/integrationjs.php"></script>
<script type="text/javascript">
 iapiSetCallout('Login', calloutLogin);
 iapiSetCallout('Logout', calloutLogout);

 var base_url = "/";
 var imgloader = "/resources/images/ajax-loader-big2.gif";
 //login(1);

 function login(realMode) {
 	// console.log('casino login');

 	var div = document.getElementById("loader");
 	div.innerHTML = '<br/><br/><img src="' + imgloader + '"><br/>Loading. Please wait...';
 	iapiLogin(document.getElementById("loginform").username.value.toUpperCase(), document.getElementById("loginform").password.value, realMode, "en");
 	//var username = $('#username').val();
	//logout(1,1);
    //iapiLogin(document.getElementById("gamename").value.toUpperCase(),document.getElementById("pt_pw").value);
    //window.location = base_url+"player_controller/playGames";
 }

 function loginToWebsite(gameType) {
 	// console.log('casino login');
 	if(gameType == 'pt'){
	 	var div = document.getElementById("loader");
	 	div.innerHTML = "<br/><br/><br/><br/><br/><br/><br/><div style='margin-left:10px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>You must log in to <br/>website first!</div>";
	 }else{
	 	var div = document.getElementById("loader_ag");
	 	div.innerHTML = "<br/><br/><br/><br/><br/><br/><br/><div style='margin-left:10px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>You must log in to <br/>website first!</div>";
	 }
 }

 function notAvailableForThisLevel(gameType) {
 	if(gameType == 'pt'){
 		var div = document.getElementById("loader");
 		div.innerHTML = "<br/><br/><br/><br/><br/><div style='margin-left:10px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>This game is not available<br/> in your level</div>";
 	}else{
 		var div = document.getElementById("loader_ag");
 		div.innerHTML = "<br/><br/><br/><br/><br/><div style='margin-left:10px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>This game is not available<br/> in your level</div>";
 	}

 }

 function blockedGame(gameType) {
 	if(gameType == 'pt'){
 		var div = document.getElementById("loader");
 		div.innerHTML = "<br/><br/><br/><br/><br/><div style='margin-left:10px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>You are not allowed to <br/> play this game!</div>";
 	}else{
 		var div = document.getElementById("loader_ag");
 		div.innerHTML = "<br/><br/><br/><br/><br/><div style='margin-left:10px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>You are not allowed to <br/> play this game!</div>";
 	}

 }

 function temporyNotAvailable(gameType) {
 	// console.log(current_lang);
 	if(gameType == 'pt'){
 		var div = document.getElementById("loader");
 		div.innerHTML = "<br/><br/><br/><br/><br/><div style='margin-left:10px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>This game is temporary <br/> unavailable!</div>";
 	}
 	else if(gameType == 'ag'){
 		var div = document.getElementById("loader_ag");
 		div.innerHTML = "<br/><br/><br/><br/><br/><div style='margin-left:-5px; margin-top:20px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>This game is temporary <br/> unavailable!</div>";
 	}else{
 		var div = document.getElementById("loader_bbin");
 		div.innerHTML = "<br/><br/><br/><br/><br/><div style='margin-left:-5px; margin-top:20px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>This game is temporary <br/> unavailable!</div>";
 	}

 }

 function logout(allSessions, realMode) {
  iapiLogout(allSessions, realMode);
 }

function calloutLogin(response) {
	if (response.errorCode) {
		// console.log("Login failed, " + response.errorText);
		// console.log(response.errorCode);
		var div = document.getElementById("loader");
 		div.innerHTML = "<br/><br/><br/><br/><div style='margin-left:-5px; margin-top:30px; padding:5px; font-size: 12px; background:#fff8c4 ; border: 1px solid #a1a1a1; border-radius: 5px; opacity:0.7; filter:alpha(opacity=60);'>Connection is busy, <br/>Please try again!</div>";
	}
	else {
			window.location = base_url+"online/playPTGames/1";
	}
}

function calloutLogout(response) {
	 if (response.errorCode) {
		// console.log("Login failed, " + response.errorText);
		// console.log(response.errorCode);
	 	alert("Logout failed, " + response.errorCode);
	 }
	 else {
	 	alert("Logout OK");
	 }
}
</script>

<div class="row">
	<div class="col-lg-12">
		<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
		<!-- Indicators -->
		<!-- <ol class="carousel-indicators">
		<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
		<li data-target="#carousel-example-generic" data-slide-to="1"></li>
		<li data-target="#carousel-example-generic" data-slide-to="2"></li>
		</ol> -->

		<!-- Wrapper for slides -->
		<!-- <div class="carousel-inner" role="listbox">
			<div class="item active">
				<img src="<?=IMAGEPATH . '/home/casino-banner.jpg'?>" style="width: 100%; height: 335px;"/>
			</div>
			<div class="item">
				<img src="<?=IMAGEPATH . '/home/casino-banner.jpg'?>" style="width: 100%; height: 335px;"/>
			</div>
			<div class="item">
				<img src="<?=IMAGEPATH . '/home/casino-banner.jpg'?>" style="width: 100%; height: 335px;"/>
			</div>
		</div> -->

		<ol class="carousel-indicators">
            <?php $cnt = 0;
if (!empty($casinobanner)) {
	foreach ($casinobanner as $key) {
		if ($cnt == 0) {?>
                        <li data-target="#carousel-example-generic" class="active" data-slide-to="<?=$cnt?>"></li>
                    <?php } else {?>
                        <li data-target="#carousel-example-generic" data-slide-to="<?=$cnt?>"></li>
                    <?php }
		$cnt++;
	}
}
?>
        </ol>

        <div class="carousel-inner" role="listbox">

            <?php $cnt = 0;
if (!empty($casinobanner)) {
	foreach ($casinobanner as $key) {
		?>

            <?php if ($cnt == 0) {
			?>
                      <div class="item active">
                        <img src="<?=PROMOCMSBANNERPATH . $key['bannerName'];?>"  style="width: 100%; height: 318px;">
                      </div>
              <?php } else {?>
                      <div class="item">
                        <img src="<?=PROMOCMSBANNERPATH . $key['bannerName'];?>"  style="width: 100%; height: 318px;">
                      </div>

              <?php }
		$cnt++;
	}
}
?>

            <?php if (!$casinobanner): ?>
                <img src="<?=IMAGEPATH . '/home/casino-banner.jpg'?>" style="width: 100%; height: 335px;"/>
            <?php endif?>
        </div>

		<!-- Controls -->
		<!-- <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
		</a> -->
		</div>
	</div>
</div>

<div class="panel-game-wrapper">
	<form id="loginform">
	<?php
if (isset($playerGamePasswordPT)) {
	echo "<input type='hidden' name='username' id='pt_pw' value='" . $this->authentication->getUsername() . "' />";
	echo "<input type='hidden' name='password' id='gamename' value='" . $playerGamePasswordPT . "' />";
}
?>
	</form>

    <div class="panel-games">
        <div class="panel panel-casino panel-pt">
            <div class="panel-heading">PT <?=lang('lang.games');?></div>
            <div class="panel-body">
            	<div style="position:absolute; text-align:center; padding-left:50px; color:#000; font-weight:bold;  text-shadow: 2px 2px #fff;" id="loader"></div>
                <?php if (!$this->authentication->isLoggedIn()) {?>
                	<button class="btn btn-og2" onclick="loginToWebsite('pt')">Login First!</button>
                <?php } else {
	if (!empty($playerLevelAllowedGame)) {
		foreach ($playerLevelAllowedGame as $key => $value) {

			if ($value['gameType'] == 1) {
				if ($playerBlockedGame[0]['blocked'] == 0) {?>

		                		 		<button class="btn btn-og2" onclick="login(1)"><?=lang('lang.gamelogIn');?></button>

			          			<?php } else {?>

			                			<button class="btn btn-og2" onclick="blockedGame('pt')"><?=lang('lang.gamelogIn');?></button>

			          <?php }
			} elseif ($value['gameType'] != 2 && $value['gameType'] != 1) {?>

			                			<button class="btn btn-og2" onclick="notAvailableForThisLevel('pt')"><?=lang('lang.gamelogIn');?></button>

			         <?php }
			?>

		          <?php }

	} else {?>
	                	<button class="btn btn-og2" onclick="notAvailableForThisLevel('pt')"><?=lang('lang.gamelogIn');?></button>
	            <?php }
}
?>
            </div>
        </div>
    </div>

    <div class="panel-games">
        <div class="panel panel-casino panel-ag">
            <div class="panel-heading">AG <?=lang('lang.games');?></div>
            <div class="panel-body">
            	<div style="position:absolute; text-align:center; padding-left:50px; color:#000; font-weight:bold;  text-shadow: 2px 2px #fff;" id="loader_ag"></div>
            	<?php //var_dump($playerBlockedGame[1]['blocked']);exit();
if (!$this->authentication->isLoggedIn()) {?>
	                	<button class="btn btn-og2" onclick="loginToWebsite('ag')">Login First!</button>
	                <?php } else {
	if (!empty($playerLevelAllowedGame)) {
		foreach ($playerLevelAllowedGame as $key => $value) {
			if ($value['gameType'] == 2) {
				if ($playerBlockedGame[1]['blocked'] == 0) {?>
			                	 		<?php //echo 'LINK: '.INVOKEURL_AG.'forwardGame.do?params='.$params.'&key='.$keys; ?>
			                	 		<form method="post" action="<?=INVOKEURL_AG . 'forwardGame.do?params=' . $params . '&key=' . $keys?>">
			                	 			<!-- <button class="btn btn-og2" onclick="">Game Log In</button> -->
			                	 			<input type="submit" value="Game Log In" class="btn btn-og2" />
			                	 			<!-- <button class="btn btn-og2" onclick="temporyNotAvailable('ag')"><?=lang('lang.gamelogIn');?></button> -->
			                	 		</form>
			                	 		<?php } else {?>
			                					<button class="btn btn-og2" onclick="blockedGame('ag')"><?=lang('lang.gamelogIn');?></button>
			             					<?php }
				?>
				                <?php } elseif ($value['gameType'] != 2 && $value['gameType'] != 1) {?>
				                			<button class="btn btn-og2" onclick="notAvailableForThisLevel('ag')"><?=lang('lang.gamelogIn');?></button>
				                <?php }
		}
	} else {?>
		            <button class="btn btn-og2" onclick="notAvailableForThisLevel('ag')"><?=lang('lang.gamelogIn');?></button>
	            <?php }
}
?>
            </div>
        </div>
    </div>
    <div class="panel-games">
        <div class="panel panel-casino panel-bbin">
            <div class="panel-heading">BBIN <?=lang('lang.games');?></div>
            <div class="panel-body">
            	<div style="position:absolute; text-align:center; padding-left:50px; color:#000; font-weight:bold;  text-shadow: 2px 2px #fff;" id="loader_bbin"></div>
            	<?php if (!$this->authentication->isLoggedIn()) {?>
                	<button class="btn btn-og2">Login First!</button>
                <?php } else {?>
                	<button class="btn btn-og2" onclick="temporyNotAvailable('bbin')"><?=lang('lang.gamelogIn');?></button>
                <?php }
?>
            </div>
        </div>
    </div><span class="clearfix"></span>

</div>
