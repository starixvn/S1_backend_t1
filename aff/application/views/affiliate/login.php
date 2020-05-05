<?php
$company_title = $this->config->item('aff_page_title');

$page_title = (isset($company_title) ? $company_title.' ' : ''). lang('reg.affilate');

$favicon = $this->config->item('aff_fav_icon_folder');

if(empty($favicon)){
    $favicon = get_site_favicon();
}

$currentLang = isset($_GET['lang']) ? $_GET['lang'] : $this->session->userdata('login_lan');
if(empty($currentLang)){
	$this->load->library('language_function');
	$currentLang=$this->language_function->getCurrentLanguage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$page_title?></title>
    <link rel="icon" href="<?=isset($favicon) ? $this->utils->appendCmsVersionToUri($favicon) : '/favicon.ico' ?>"/>

    <!-- Theme switcher -->
    <?php $user_theme = !empty($this->session->userdata('affiliate_theme')) ? $this->session->userdata('affiliate_theme') : 'flatly';?>
    <?php
        if($_SERVER['SERVER_NAME']=='aff.vip-win007.com'){
           $user_theme = $this->session->userdata['affiliate_theme'] = "win007";
        }
    ?>
    <link href="<?=$this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css')?>" rel="stylesheet">
	<link href="<?=$this->utils->cssUrl('font-awesome.min.css')?>" rel="stylesheet">
    <?php if($this->utils->isEnabledFeature('enable_aff_custom_css')) : ?>
    	<link href="<?=$this->utils->getAnyCmsUrl('includes/css/custom-style-affiliate.css')?>" rel="stylesheet" type="text/css">
	<?php endif; ?>

    <?=$this->utils->startEvent('Load aff custom header'); ?>
    <?=$this->utils->getAffiliateCustomHeader()?>
    <?=$this->utils->endEvent('Load aff custom header'); ?>

    <?=$this->utils->getTrackingScriptWithDoamin('aff', 'gtm', 'header');?>
    <?=$this->utils->getTrackingScriptWithDoamin('aff', 'ga');?>
</head>
<body>
	<div class="container" style="padding-top: 10%; padding-bottom: 10%;">
		<div class="row">
			<a href="<?= $this->utils->getSystemUrl('www') ?>"><div class="logo"></div></a>
			<div class="col-md-4 col-md-offset-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title ">
							<b>
								<div class="brand-img-container">
			                        <?php if ($this->utils->useSystemDefaultLogo() || !$this->utils->isUploadedLogoExist() || !$this->utils->isLogoOperatorSettingsExist() || !$this->utils->isLogoSetOnDB()) : ?>
			                            <img class="brand-img" width="49" height="45" src="<?php echo $this->utils->getDefaultLogoUrl(); ?>" width="30px;">
			                        <?php else: ?>
			                            <img class="brand-img" width="49" height="45" src="<?php echo $this->utils->setSBELogo(); ?>">
			                        <?php endif; ?>

				                <?=$page_title?>
								</div>
			                </b>
		                </h3>
					</div>
					<div class="panel-body">
						<?php if ($this->session->userdata('result')) {?>
					        <div class="alert alert-<?=$this->session->userdata('result')?> alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

								<?php if ($this->utils->getConfig('aff_contact_has_message_header')): ?>
				                <b><?=lang('lang.message');?></b>
				                <br>
								<?php endif ?>

				                <p><?=$this->session->userdata('message')?></p>
							</div>
							<?php $this->session->unset_userdata('result')?>
							<?php $this->session->unset_userdata('messages')?>
						<?php }
?>
						<form method="POST" action="<?=site_url('affiliate/login')?>">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-user"></span>
									</div>
									<input type="text" name="username" class="form-control" placeholder="<?=lang('login.Username');?>" required>
								</div>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-lock"></span>
									</div>
									<input type="password" name="password" class="form-control" placeholder="<?=lang('login.Password');?>" required>
								</div>
							</div>
							<?php if (empty($this->utils->getConfig('hide_select_language'))  && !empty($this->utils->getConfig('visible_options_under_language_selection'))): ?>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-globe"></span>
									</div>
									<div class="custom-dropdown">
									<select class="form-control" name="language" id="language" onchange="changeLanguage();">
										<?php foreach ($this->utils->getConfig('visible_options_under_language_selection') as $lang_option_key => $lang_option): ?>

											<option value="<?=$lang_option?>" <?php echo ($this->session->userdata('afflang') == $lang_option || $currentLang == $lang_option) ? ' selected="selected"' : '';?>>
												<?php switch ($lang_option) {
													case '1':
														echo "English";
														break;
													case '2':
														echo "中文";
														break;
													case '3':
														echo "Indonesian";
														break;
													case '4':
														echo "Vietnamese";
														break;
													case '5':
														echo "Korean";
														break;
													case '6':
														echo "Thai";
														break;
													default:
														echo "English";
														break;
												} ?>
											</option>
											
										<?php endforeach ?>
							        </select>
							        </div>
								</div>
							</div>
							<?php endif ?>
				<?php
				if(!empty($availableCurrencyList)){
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
										<span ><img id='image_captcha' class="" src='<?php echo site_url('/affiliate/captcha?' . random_string('alnum')); ?>' style="height:44px;width:40%;"></span>
									</div>
							</div>
							<?php endif;?>
							<div class="form-group">
								<input type="submit" class="btn btn-primary text-uppercase col-md-4 col-md-offset-4" value="<?=lang('lang.logIn');?>">
							</div>
						</form>
					</div><!-- END OF PANEL-BODY -->
					<div class="panel-footer">
						<?php if(!$this->utils->isEnabledFeature('hide_affiliate_registration_link_in_login_form')){ ?>
							<div class="text-center">
								<strong><?=lang('reg.acreateAcc');?>? <a href="<?=site_url('affiliate/register');?>"><?=lang('reg.clickHere');?></a></strong>
							</div>
						<?php } ?>
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
		'/affiliate/change_active_currency?__OG_TARGET_DB='+key,
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
		    $.get('/affiliate/changeLanguage/' + lang, function() {
	            location.reload();
		    })
		}
		function refreshCaptcha(){
        //refresh
        $('#image_captcha').attr('src','<?php echo site_url('/affiliate/captcha'); ?>?'+Math.random());
       }

		$(function(){
		    var loc = window.location.href; // returns the full URL
		    if(loc) {
		        $('body').addClass('login');
		    }else{
		        $('body').addClass('body-content');
		    };
		});

//should be array
var donot_auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('donot_auto_redirect_to_https_list'))?>;
var auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('auto_redirect_to_https_list'))?>;

_pubutils.checkAndGoHttps(auto_redirect_to_https_list, donot_auto_redirect_to_https_list);

	</script>

    <?=$this->utils->startEvent('Load aff custom footer'); ?>
    <?=$this->utils->getAffiliateCustomFooter()?>
    <?=$this->utils->endEvent('Load aff custom footer'); ?>

    <?=$this->utils->getTrackingScriptWithDoamin('aff', 'gtm', 'footer');?>
</body>
</html>