<?php
$this->session->set_userdata('current_url', current_url());
$user_theme = !empty($this->session->userdata('agency_theme')) ? $this->session->userdata('agency_theme') : 'flatly';
$currentLang = !empty($this->session->userdata('login_lan')) ? $this->session->userdata('login_lan'): '2';

$this->utils->debug_log('CURRENTLANG', $currentLang);
$availableCurrencyList=$this->utils->getAvailableCurrencyList();
$activeCurrencyKeyOnMDB=$this->utils->getActiveCurrencyKeyOnMDB();
$isCurrencyDomain=$this->utils->isCurrencyDomain();

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
</style>
<nav class="navbar navbar-default" style="margin-bottom: 0px; border-radius: 0px;">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" style='margin-top:-10px;' href="<?=site_url('agency')?>">
                <?php $logo = get_site_navbar_logo(); ?>
                <?php $site = lang('Agency System'); ?>
                <?php
                if( !$this->utils->isEnabledFeature('hide_header_logo_in_agency') ) {
                    if($logo == '') { ?>
                        <img class="brand-img" style="display:inline;" src="<?=$this->utils->imageUrl('og-login-logo.png')?>"><?=$site;?>
                    <?php } else { ?>
                        <img class="brand-img" style="display:inline;" src="<?=$logo;?>"><?=$site;?>
                    <?php }
                }else{ ?>
                    <span class="brand-img" style="line-height: 40px;" ></span> <?=$site;?>
                <?php } ?>
            </a>
        </div>
		<ul class="nav navbar-right navbar-nav">
			<li style="margin-right: 10px;">
				<select class="form-control input-sm user-option" name="language" id="language" onchange="changeLanguage();" style="margin-top: 12px;">
		        	<option value="1"<?php echo (($this->session->userdata('agency_lang') ? : $currentLang) == '1') ? ' selected="selected"' : '';?>>English</option>
		        	<option value="2"<?php echo (($this->session->userdata('agency_lang') ? : $currentLang) == '2') ? ' selected="selected"' : '';?>>中文</option>
		        	<option value="5"<?php echo (($this->session->userdata('agency_lang') ? : $currentLang) == '5') ? ' selected="selected"' : '';?>>Korean</option>
		        </select>
	        </li>
            <?php
            if(!empty($availableCurrencyList) && !$isCurrencyDomain){
            ?>
				<li style="margin-right: 10px;">
                    <div class="custom-dropdown">
                        <select class="form-control input-sm user-option" id="currency_list" onchange="changeCurrency(this);" style="margin-top: 12px;">
                            <option value="super" ><?=lang('All')?></option>
                        <?php
                        foreach ($availableCurrencyList as $key => $value) {
                        ?>
                            <option value="<?=$key?>" <?php echo ($activeCurrencyKeyOnMDB == $key) ? 'selected' : '' ?> ><?=lang($value['name'])?></option>
                        <?php
                        }
                        ?>
                        </select>
                    </div>
		        </li>
            <?php
            }
            ?>
	        <li>
	        	<a href="#" class="dropdown-toggle user-option" data-toggle="dropdown" href="#"><i class="glyphicon glyphicon-adjust"></i> <span class="caret"></span></a>

		        <ul class="dropdown-menu user-option" role="menu">
		            <li <?php if($user_theme == 'flatly') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/flatly'?>">Flatly</a></li>
		            <li <?php if($user_theme == 'paper') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/paper'?>">Paper</a></li>
		            <li <?php if($user_theme == 'readable') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/readable'?>">Readable</a></li>
		            <li <?php if($user_theme == 'journal') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/journal'?>">Journal</a></li>
		            <li <?php if($user_theme == 'spacelab') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/spacelab'?>">SpaceLab</a></li>
		            <li <?php if($user_theme == 'slate') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/slate'?>">Slate</a></li>
		            <li <?php if($user_theme == 'cerulean') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/cerulean'?>">Cerulean</a></li>
		            <li <?php if($user_theme == 'lumen') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/lumen'?>">Lumen</a></li>
		            <li <?php if($user_theme == 'yeti') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/yeti'?>">Yeti</a></li>
		            <li <?php if($user_theme == 'simplex') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/simplex'?>">Simplex</a></li>
		            <!-- <li <?php if($user_theme == 'united') echo 'class="active"'; ?>><a href="<?=BASEURL . 'agency/switchTheme/united'?>">United</a></li> -->
		        </ul>
	        </li>
	        <?php if(!empty($this->session->userdata('agent_name'))) {
                $welcomeTitle=lang('nav.welcome') . ', ' . $this->session->userdata('agent_name');
                if($readonlyLogged){
                    $welcomeTitle=$welcomeTitle.'-'.$readonly_sub_account.' <span class="text-warning">'.lang('Readonly').'</span>';
                }
            ?>
			<li>
				<a href="#" class="dropdown-toggle user-option" data-toggle="dropdown" href="#"><?=$welcomeTitle?> <span class="caret"></span></a>

		        <ul class="dropdown-menu user-option" role="menu">
                    <?php if(!$readonlyLogged){ ?>
		            <li><a href="#"><i class="glyphicon glyphicon-credit-card"></i> <?=lang('Available')?><br><?=$this->utils->formatCurrencyNoSym($_agent['available_credit'])?> / <?=$this->utils->formatCurrencyNoSym($_agent['credit_limit'])?></a></li>
                    <?php }?>
		            <li><a href="<?=BASEURL . 'agency/logout'?>"><i class="glyphicon glyphicon-off"></i> <?=lang('nav.logOut');?></a></li>
		        </ul>
			</li>
			<?php } ?>
		</ul>
    </div>
</nav>

<?php if(!empty($this->session->userdata('agent_name'))) { ?>
	<div id="navbar" class="navbar navbar-inverse" style="border-radius: 0px;">
		<div class="">
	        <div class="navbar-header">
	          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	            <span class="sr-only">Toggle navigation</span>
	            <span class="glyphicon glyphicon-align-justify glyphicon-chevron-down" style="color:white;"></span>
	          </button>
	        </div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			    <ul class="nav navbar-nav navbar-left user-option">
                    <!-- <li id="rolling_comm_setting"><a href="<?=BASEURL . 'agency/rolling_comm_setting/'. $this->session->userdata('agent_id'); ?>"><?=lang('Rolling Comm Setting');?></a></li> -->
                    <?php if(!$readonlyLogged){?>
                    <?php if ($this->session->userdata('can_view_agents_list_and_players_list')): ?>
	                    <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=lang('Listing');?> <span class="caret"></span></a>
							<ul class="dropdown-menu">
							    <?php if($this->session->userdata('can_have_sub_agent')) { ?>
									<li id="sub_agents_list"><a href="<?=BASEURL . 'agency/sub_agents_list'?>"><?=lang('Agents List');?></a></li>
							    <?php } ?>
			                    <?php // if($this->session->userdata('can_have_players')) { ?>
			                    		<li id="players_list"><a href="<?php echo site_url('agency/players_list');?>"><?=lang('Players List');?></a></li>
			                    <?php // } ?>
							</ul>
						</li>
                    <?php endif ?>
                    <?php }?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=lang('Report');?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li id="agent_report"><a href="<?=site_url('agency/agency_agent_report')?>"><?=lang('Agent Report');?></a></li>
							<li id="player_report"><a href="<?=site_url('agency/agency_player_report')?>"><?=lang('report.s09');?></a></li>
							<li id="game_history"><a href="<?=site_url('agency/game_history')?>"><?=lang('Game History');?></a></li>
                            <?php if(!$this->utils->isEnabledFeature('agent_settlement_to_wallet')) : ?>
							<li id="credit_logs"><a href="<?=site_url('agency/credit_transactions');?>"><?=lang('Credit Transactions');?></a></li>
                            <?php endif; ?>
							<li id="settlement"><a href="<?=site_url('agency/settlement_wl');?>"><?=lang('Agent Win Lose Comm Settlement');?></a></li>
							<!--
							<li id="settlement"><a href="<?=site_url('agency/flattening');?>"><?=lang('Flattening');?></a></li>
							-->
							<?php if ($this->utils->isEnabledFeature('rolling_comm_for_player_on_agency') && $this->utils->isEnabledRollingCommByAgentInSession()){ ?>
							<?php
								# hide player comm settlement on navigation bar
								/* <li id="rolling_comm"><a href="<?=site_url('agency/player_rolling_comm');?>"><?=lang('Player Comm Settlement');?></a></li> */
							?>
							<?php }?>
							<li id="game_report"><a href="<?=site_url('agency/agency_game_report');?>"><?=lang('report.s07');?></a></li>
							<?php /* <li id="invoice"><a href="<?=BASEURL . 'agency/invoice'?>"><?=lang('Invoice');?></a></li> */ ?>
						</ul>
					</li>
                    <?php if(!$readonlyLogged){?>
                    <li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=lang('Setting');?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li id="nav_modify_account">
							    <a href="<?=BASEURL . 'agency/agent_information/'.$this->session->userdata('agent_id');?>">
							        <?=lang('Account Info');?>
							    </a>
							</li>
							<?php if ( ! $this->utils->isEnabledFeature('hide_bet_limit_on_agency') && $this->session->userdata('show_bet_limit_template')): ?>
                    			<li id="nav_bet_limit_template_list"><a href="<?php echo site_url('agency/bet_limit_template_list');?>"><?=lang('Bet Limit Template');?></a></li>
							<?php endif ?>
                    		<li id="nav_tracking_link_list"><a href="<?php echo site_url('agency/tracking_link_list');?>"><?=lang('WeChat Links');?></a></li>
                            <?php if(!$this->utils->isEnabledFeature('hide_agency_t1lotterry_link')) { ?>
                    		    <li id="nav_lottery_bo"><a href="<?php echo site_url('agency/redirect_t1lottery');?>"><?=lang('T1 Lottery BO');?></a></li>
                            <?php } ?>
						</ul>
					</li>
                    <li id="cashier"><a href="<?=BASEURL . 'agency/withdrawRequest/main/'. $this->session->userdata('agent_id'); ?>"><?=lang('agency.Cashier');?></a></li>
                    <?php }?>
                </ul>
			</div>
		</div>
	</div><!--/.navbar-collapse -->
<?php } ?>

<style type="text/css">
.overlay_screen {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999999;
    background-color: #000;

    font-size: 24px;
    font-family: sans-serif;
    color: white;
    text-align: center;
    flex-direction: column;
    justify-content: center;
}
</style>
<div style="display: none" id="_lock_screen"></div>

<script type="text/javascript">
    /*
	function changeLanguage() {
	    var lang = $('#language').val();
	    $.get('/agency/changeLanguage/' + lang, function() {
            location.reload();
	    })
	}
     */

    function _lock_page(msg){
        $('#_lock_screen').addClass('overlay_screen').html(msg).fadeTo(0, 0.4).css('display', 'flex');
    }

    function _unlock_page(){
        $('#_lock_screen').removeClass('overlay_screen').html('').css('display', 'none');
    }

    function changeCurrency(ele){
        //call change active db
        var key=$(ele).val();
        //lock page
        _lock_page("<?=lang('Changing Currency')?>");
        $.ajax(
            '/agency/change_active_currency_for_logged/'+key,
            {
                dataType: 'json',
                cache: false,
                success: function(data){
                    if(data && data['success']){
                        window.location.reload();
                    }else{
                        alert("<?=lang('Change Currency Failed')?>");
                        _unlock_page();
                    }
                },
                error: function(){
                    alert("<?=lang('Change Currency Failed')?>");
                    _unlock_page();
                }
            }
        ).always(function(){
            // _unlock_page();
        });
    }

</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of create_agent.php
