<?php
$session_lang=$this->session->userdata('agency_lang');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=lang('Agency System');?></title>

    <!-- Theme switcher -->
    <?php $user_theme = !empty($this->session->userdata('agency_theme')) ? $this->session->userdata('agency_theme') : 'flatly';?>
    <link href="<?=$this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css')?>" rel="stylesheet">
	<link href="<?=$this->utils->cssUrl('font-awesome.min.css')?>" rel="stylesheet">
    <?php if($this->utils->isEnabledFeature('enable_player_center_style_support_on_agency')): ?>
        <link href="<?=$this->utils->getPlayerCmsUrl($this->utils->getActivePlayerCenterTheme())?>" rel="stylesheet">
    <?php endif; ?>

    <?=$this->utils->getTrackingScriptWithDoamin('agency', 'gtm', 'header');?>
    <?=$this->utils->getTrackingScriptWithDoamin('agency', 'ga');?>
</head>
<body class="agency-center login">
	<div class="container" style="padding-top: 10%; padding-bottom: 10%;">
		<div class="row">
            <a href="<?= $this->utils->getSystemUrl('www') ?>"><div class="logo"></div></a>
			<div class="col-md-4 col-md-offset-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">
							<b>
								<img class="brand-img" width="49" height="45" src="<?=get_site_login_logo()?>">
				                <?=lang('Agency System');?>
			                </b>
		                </h3>
					</div>
					<div class="panel-body">
						<?php if ($this->session->userdata('result')) {?>
					        <div class="alert alert-<?=$this->session->userdata('result')?> alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				                <b><?=lang('lang.message');?></b>
				                <p><?=$this->session->userdata('message')?></p>
							</div>
							<?php $this->session->unset_userdata('result')?>
							<?php $this->session->unset_userdata('messages')?>
						<?php }
?>
						<form method="POST" action="<?=site_url('agency/login')?>" id="login_form">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-user"></span>
									</div>
									<input type="text" name="username" class="form-control" placeholder="<?=lang('login.Username');?>" required>
								</div>
							</div>
                            <?php if($this->utils->isEnabledFeature('enabled_readonly_agency')){?>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th-list"></span>
                                    </div>
                                    <input type="text" name="readonly_username" class="form-control" placeholder="<?=lang('Readonly Account');?>" required>
                                </div>
                            </div>
                            <?php }?>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-lock"></span>
									</div>
									<input type="password" name="password" class="form-control" placeholder="<?=lang('login.Password');?>" required>
								</div>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-globe"></span>
									</div>
									<select class="form-control" name="language" id="language" onchange="changeLanguage();">
                                        <option value="1" <?php echo ($session_lang ? $session_lang == '1' : $current_language_name == 'english') ? ' selected="selected"' : ''; ?>>English</option>
                                        <option value="2" <?php echo ($session_lang ? $session_lang == '2' : $current_language_name == 'chinese') ? ' selected="selected"' : ''; ?>>中文</option>
                                        <option value="3" <?php echo ($session_lang ? $session_lang == '3' : $current_language_name == 'indonesian') ? ' selected="selected"' : ''; ?>>Indonesian</option>
                                        <option value="4" <?php echo ($session_lang ? $session_lang == '4' : $current_language_name == 'vietnamese') ? ' selected="selected"' : ''; ?>>Vietnamese</option>
                                        <option value="5" <?php echo ($session_lang ? $session_lang == '5' : $current_language_name == 'korean') ? ' selected="selected"' : ''; ?>>Korean</option>
                                        <option value="6" <?php echo ($session_lang ? $session_lang == '6' : $current_language_name == 'thai') ? ' selected="selected"' : ''; ?>>Thai</option>
							        </select>
								</div>
							</div>
				<?php
				if(!empty($availableCurrencyList) && !$isCurrencyDomain){
				?>
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-addon">
							<span class="fa fa-money"></span>
						</div>
						<select class="form-control" id="currency_list" onchange="changeCurrency(this);">
							<option value="super" ><?=lang('All')?></option>
						<?php
						foreach ($availableCurrencyList as $key => $value) {
						?>
							<option value="<?=$key?>" <?php echo ($activeCurrencyKeyOnMDB == $key) ? 'selected' : '' ?> ><?=$value['symbol']?> <?=lang($value['name'])?></option>
						<?php
						}
						?>
						</select>
					</div>
				</div>
				<?php
				}
				?>
							<?php if ($useCaptchaOnLogin): ?>
							<div class="form-group">
									<div class="input-group">
										<input type='text' name='login_captcha' style="width:41%" id='captcha' class=' form-control' placeholder='<?php echo lang('label.captcha'); ?>' required>
										<a href="javascript:void(0)" onclick="refreshCaptcha()" class="btn  btn-default"><span class="glyphicon glyphicon-refresh "></span></a>
										<span ><img id='image_captcha' class="" src='<?php echo site_url('/agency/captcha?' . random_string('alnum')); ?>' style="height:44px;width:40%;"></span>
									</div>
							</div>
							<?php endif;?>
							<div class="form-group">
								<input type="button" class="btn btn-primary text-uppercase col-md-4 col-md-offset-4" value="<?=lang('lang.logIn');?>" onclick="login_submit(this)">
<!-- 								<a href="/agency/register" ><?=lang('Register')?></a>
 -->							</div>
						</form>
					</div><!-- END OF PANEL-BODY -->
					<div class="panel-footer">
						<div class="text-center">
                            <?php if(!$this->utils->isEnabledFeature('hide_registration_link_in_login_form')){ ?>
                            <strong>
                                <?=lang('reg.acreateAcc');?>?
                                <a href="<?=site_url('agency/register');?>"><?=lang('reg.clickHere');?></a>
                            </strong>
                            <?php } ?>
                        </div>
					</div>
				</div><!-- END OF PANEL -->
			</div><!-- END OF COL -->
		</div><!-- END OF ROW -->
	</div><!-- END OF CONTAINER -->

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

    <script type="text/javascript" src="<?=$this->utils->jsUrl('jquery-2.1.4.min.js')?>"></script>
    <script type="text/javascript" src="<?=$this->utils->jsUrl('bootstrap.min.js')?>"></script>
    <script type="text/javascript" src="<?=$this->utils->jsUrl('pub/pubutils.js')?>"></script>
	<script type="text/javascript">
function _lock_page(msg){
    $('#_lock_screen').addClass('overlay_screen').html(msg).fadeTo(0, 0.4).css('display', 'flex');
}

function _unlock_page(){
    $('#_lock_screen').removeClass('overlay_screen').html('').css('display', 'none');
}
function changeCurrency(ele){
	//call change active db
	var key=$(ele).val();
    _lock_page("<?=lang('Changing Currency')?>");
	$.ajax(
		'/agency/change_active_currency?__OG_TARGET_DB='+key,
		{
			dataType: 'json',
			cache: false,
			success: function(data){
				if(data && data['success']){
					window.location.reload();
				}else{
					alert("<?=lang('Change Currency Failed')?>");
				}
			},
			error: function(){
				alert("<?=lang('Change Currency Failed')?>");
			}
		}
	).always(function(){
		_unlock_page();
	});
}

		function changeLanguage() {
		    var lang = $('#language').val();
		    $.get('/agency/changeLanguage/' + lang, function() {
	            location.reload();
		    })
		}

		function refreshCaptcha(){
			//refresh
			$('#image_captcha').attr('src','<?php echo site_url('/agency/captcha'); ?>?'+Math.random());
		}

		function login_submit(button) {
			var $button = $(button);
			$button.prop('disabled', true);
			$('#login_form').submit();
		}

//should be array
var donot_auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('donot_auto_redirect_to_https_list'))?>;
var auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('auto_redirect_to_https_list'))?>;

_pubutils.checkAndGoHttps(auto_redirect_to_https_list, donot_auto_redirect_to_https_list);

	</script>

    <?=$this->utils->getTrackingScriptWithDoamin('agency', 'gtm', 'footer');?>
</body>
</html>
