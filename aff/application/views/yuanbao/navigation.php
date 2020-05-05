<?php $this->session->set_userdata('current_url', current_url()); ?>
<?php $user_theme = !empty($this->session->userdata('affiliate_theme')) ? $this->session->userdata('affiliate_theme') : 'flatly'; ?>
<?php $currentLang = isset($_GET['lang']) ? $_GET['lang'] : $this->session->userdata('login_lan');
if(empty($currentLang)){
	$currentLang=$this->utils->getConfig('aff_default_language');
}

$affId=$this->session->userdata('affiliateId');
$availSubAff=false;
if($affId){
	$this->load->model(['affiliatemodel']);
	$availSubAff=$this->affiliatemodel->isAvailableSubAffiliate($affId);
}
?>
<style>
@media (max-width: 768px) {
    .navbar-toggle {
        width: 90%;
        float: none;
        margin-left: 5%;
    }
    .user-option {
        text-align: center;
    }
    
}
	
	nav {
    	background-color: #000 !important;
    }
    .navbar-inverse {
    	background: #fff !important;
    	color: #000 !important;
    }
    .navbar navbar-inverse{
    	color: #000 !important;
    }

    .nav-head{
		background-color: #434343 !important;
    }
    .btn-hov:hover{
    	background-color: #ffb015;
    	border-color: #ffb015;
    }
    .btn-hov-invert{
    	background-color: #ffb015;
    	border-color: #ffb015;
    }
    .link-hov:hover{
    	color: #ffb015 !important;
    }
    .btn-hov-invert:hover{
    	background-color: #3498db;
    	border-color: #3498db;
    }
    .panel-heading{
	}
	#main_content{
		background-color: #f5f5f5;
	}
	.navbar-inverse .navbar-nav > li > a{
		color:#434343 !important;
	}
	.navbar{
		background-color: transparent;
		margin-bottom: 0px;
		font-size: 18px;
	}
	.navbar-inverse .navbar-nav > .active > a{
		background-color: #666666 !important;
		color: #fff !important;
	}
	.navbar-inverse .navbar-nav a:hover,
	.navbar-inverse .navbar-nav a:active
	{
		background-color: #9A9A9A !important;
		color: #fff !important;
	}
	#language{
		border: transparent 1px;
	}
	.submit{
		background-color: #3498db;
		border-color: #3498db;
	}
	.cancel{
		background-color: #798d8f;
		border-color: #798d8f;
	}

</style>
<nav class="navbar navbar-default" style="margin-bottom: 0px; border-radius: 0px;">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" style='margin-top:-10px;' href="<?=site_url('affiliate')?>">
                <?php $logo = get_site_navbar_logo(); ?>
                <?php $site = lang('reg.affilate'); ?>
                <?php if($logo == '') { ?>
                    <img class="brand-img" style="display:inline;" height="40" src="../../yuanbao/images/logo.png"> &nbsp&nbsp&nbsp&nbsp&nbsp<?=$site; ?>
                <?php } else { ?>
                    <img class="brand-img" style="display:inline;" height="40" src="<?=$logo;?>"> <?=$site; ?>
                <?php } ?>
            </a>
        </div>
		<ul class="nav navbar-right navbar-nav">
			<?php if (empty($this->utils->getConfig('hide_select_language'))): ?>
				<li style="margin-right: 10px;">
					<select class="form-control input-sm user-option" name="language" id="language" onchange="changeLanguage();" style="margin-top: 12px;">
			        	<option value="1"<?php echo ($this->session->userdata('afflang') == '1' || $currentLang == '1') ? ' selected="selected"' : '';?>>English</option>
			        	<option value="2"<?php echo ($this->session->userdata('afflang') == '2' || $currentLang == '2') ? ' selected="selected"' : '';?>>中文</option>
			        	<option value="3"<?php echo ($this->session->userdata('afflang') == '3' || $currentLang == '3') ? ' selected="selected"' : '';?>>Indonesian</option>
			        	<option value="4"<?php echo ($this->session->userdata('afflang') == '4' || $currentLang == '4') ? ' selected="selected"' : '';?>>Vietnamese</option>
			        	<option value="5"<?php echo ($this->session->userdata('afflang') == '5' || $currentLang == '5') ? ' selected="selected"' : '';?>>Korean</option>
			        </select>
		        </li>
			<?php endif ?>
	        <li>
	        	<a href="#" class="dropdown-toggle user-option" data-toggle="dropdown" href="#"><i class="glyphicon glyphicon-adjust"></i> <span class="caret"></span></a>

		        <ul class="dropdown-menu user-option" role="menu">
		            <li <?php if($user_theme == 'flatly') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/flatly'?>">Flatly</a></li>
		            <li <?php if($user_theme == 'paper') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/paper'?>">Paper</a></li>
		            <li <?php if($user_theme == 'readable') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/readable'?>">Readable</a></li>
		            <li <?php if($user_theme == 'journal') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/journal'?>">Journal</a></li>
		            <li <?php if($user_theme == 'spacelab') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/spacelab'?>">SpaceLab</a></li>
		            <li <?php if($user_theme == 'slate') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/slate'?>">Slate</a></li>
		            <li <?php if($user_theme == 'cerulean') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/cerulean'?>">Cerulean</a></li>
		            <li <?php if($user_theme == 'lumen') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/lumen'?>">Lumen</a></li>
		            <li <?php if($user_theme == 'yeti') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/yeti'?>">Yeti</a></li>
		            <li <?php if($user_theme == 'simplex') echo 'class="active"'; ?>><a href="<?=BASEURL . 'affiliate/switchTheme/simplex'?>">Simplex</a></li>
		        </ul>
	        </li>
	        <?php if(!empty($this->session->userdata('affiliateUsername'))) { ?>
			<li>
				<a href="#" class="dropdown-toggle user-option" data-toggle="dropdown" href="#"><?=lang('nav.welcome') . ', ' . $this->session->userdata('affiliateUsername') . '! '?> <span class="caret"></span></a>

		        <ul class="dropdown-menu user-option" role="menu">
		            <!-- <li><a href="<?=BASEURL . 'affiliate/modifyAccount'?>"><i class="glyphicon glyphicon-cog"></i> Settings</a></li> -->
		            <!-- <li><a href="<?=BASEURL . 'affiliate/modifyAccount'?>"><i class="glyphicon glyphicon-user"></i> <?=lang('nav.modifyAccount');?></a></li> -->
		            <li><a href="<?=BASEURL . 'affiliate/logout'?>"><i class="glyphicon glyphicon-off"></i> <?=lang('nav.logOut');?></a></li>
		        </ul>
			</li>
			<?php } ?>
		</ul>
    </div>
</nav>

<?php if(!empty($this->session->userdata('affiliateUsername'))) { ?>
	<div id="navbar" class="navbar navbar-inverse" style="border-radius: 0px;">
		<div class="container">
	        <div class="navbar-header">
	          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	            <span class="sr-only">Toggle navigation</span>
	            <span class="glyphicon glyphicon-align-justify glyphicon-chevron-down" style="color:white;"></span>
	          </button>
	        </div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			    <ul class="nav navbar-nav navbar-left user-option">
					<li id="trafficStats"><a href="<?=site_url('affiliate/traffic_stats?enable_date=true')?>"><?=lang('nav.traffic');?></a></li>
					<?php if($this->utils->isEnabledFeature('player_stats_on_affiliate')){?>
					<li id="playerStats"><a href="<?=site_url('affiliate/player_stats?enable_date=true')?>"><?=lang('Player Statistics');?></a></li>
					<?php }?>
					<li id="bannerLists"><a href="<?=BASEURL . 'affiliate/bannerLists'?>"><?=lang('nav.banner');?></a></li>
					<li id="cashier"><a href="<?=BASEURL . 'affiliate/cashier'?>"><?=lang('nav.cashier');?></a></li>
					<?php if($this->utils->isEnabledFeature('show_transactions_history_on_affiliate')){ ?>
					<li id="paymentHistory"><a href="<?=BASEURL . 'affiliate/paymentHistory'?>"><?=lang('nav.transaction');?></a></li>
					<?php }?>
					<?php if( ! $this->utils->isEnabledFeature('hide_sub_affiliates_on_affiliate') && $availSubAff){ ?>
					<li id="subaffiliates"><a href="<?=BASEURL . 'affiliate/subaffiliates'?>"><?=lang('nav.sub-affiliates');?></a></li>
					<?php }?>
					<?php if($this->utils->isEnabledFeature('player_list_on_affiliate')){?>
					<li id="playersList"><a href="<?=BASEURL . 'affiliate/playersList'?>"><?=lang('traffic.playerlist');?></a></li>
					<?php }?>

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=lang('Report');?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php if($this->utils->isEnabledFeature('affiliate_monthly_earnings')){?>
							<li id="monthlyEarnings"><a href="<?=BASEURL . 'affiliate/monthlyEarnings'?>"><?=lang('nav.earnings');?></a></li>
							<?php }?>
							<?php if($this->utils->isEnabledFeature('affiliate_player_report')){?>
							<li id="affiliate_player_report"><a href="<?=BASEURL . 'affiliate/affiliate_player_report'?>"><?=lang('report.s09');?></a></li>
							<?php }?>
							<?php if($this->utils->isEnabledFeature('affiliate_game_history')){?>
							<li id="affiliate_game_history"><a href="<?=BASEURL . 'affiliate/affiliate_game_history'?>"><?=lang('report.s07');?></a></li>
							<?php }?>
							<?php if($this->utils->isEnabledFeature('affiliate_credit_transactions')){?>
							<li id="affiliate_credit_transactions"><a href="<?=BASEURL . 'affiliate/affiliate_credit_transactions'?>"><?=lang('Transactions');?></a></li>
							<?php }?>
						</ul>
					</li>
					<li id="modifyAccount"><a href="<?=BASEURL . 'affiliate/modifyAccount'?>"><?=lang('nav.modifyAccount');?></a></li>
			    </ul>
			</div>
		</div>
	</div><!--/.navbar-collapse -->
<?php } ?>

<script type="text/javascript">
	function changeLanguage(newhref=null) {
	    var lang = $('#language').val();

	    $.get('/affiliate/changeLanguage/' + lang, function() {
	    	if(newhref!=null){
            	window.location.href = newhref;
	    	}else{
            	location.reload();
	    	}
	    })
	}

    // $(document).one("ready",function(){
    //     //alert(<?php echo isset($_GET['lang'])?$_GET['lang']:''."!=".$currentLang;?>);
    //     if(<?php echo isset($_GET['lang']) ? "true" : "false";?>){
    //        var clang = <?php echo $currentLang ?>;
    //       	$('#language').val(clang);
    //         var newhref = removeParam(window.location.href);
    //         // if(window.location.href){
    //        	changeLanguage(newhref);
    //         // }
    //     }
    //     function removeParam(uri) {

    //        return uri.substring(0, uri.indexOf('?'));
    //     }
    // });

</script>