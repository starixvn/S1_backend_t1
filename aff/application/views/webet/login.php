<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=lang('reg.affilate');?></title>


     <?php if($this->config->item('affiliate_view_template') == 'webet' ): ?>
     	<link href="/webet/dist/css/bootstrap.css" rel="stylesheet" type="text/css">
     	<link href="/webet/css/styles.css" rel="stylesheet" type="text/css" />
    <?php else: ?>
        <!-- Theme switcher -->
        <?php $user_theme = !empty($this->session->userdata('affiliate_theme')) ? $this->session->userdata('affiliate_theme') : 'flatly';?>
        <link href="<?=$this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css')?>" rel="stylesheet">
    <?php endif; ?>

    	<style type="text/css">
		.col-centered {
			float: none;
			margin: 0 auto;
			padding-top: 10%;
		}
		.title-logo {
			display: block;
			text-align: center;
			font-size: 16px;
			font-weight: 600;
			text-transform: uppercase;
		}
		.title-logo img {
			margin-right: 8px;
		}
		.wb-radius {
			border-radius: 14px;
			overflow: hidden;
		}
		.no-border {
			border: transparent;
		}
		.panel-primary > .bg-panel-head {
			background-image: linear-gradient(#444, #333 60%, #222);
    		background-repeat: no-repeat;
    		color: #fff;
		}
		.bg-panel-head {
			padding: 10px 15px;
		    border-top-left-radius: 3px;
		    border-top-right-radius: 3px
		}
		.bg-panel-body {
			background: #141414;
		}
		.bg-panel-footer {
		    padding: 10px 15px;
		    background-color: #676767;
		    border-bottom-right-radius: 3px;
		    border-bottom-left-radius: 3px;
		    text-align: center;
		    color: #fff;
		}
		.bg-panel-footer a {
			color: #fff;
			font-weight: 600;
		}
		.input-group-addon {
			background: transparent;
		}
		.bg-addon {
			background-image: linear-gradient(#444, #333 60%, #222);
    		background-repeat: no-repeat;
    		color: #fff;
    		font-weight: 400;
		}
		.forgot-pass {
			color: #ffffff;
			text-align: center;
			padding: 20px 20px;
		}
		.forgot-pass a {
			font-weight: 600;
			color: #499c00;
		}
	</style>


</head>
<body style="background:#373737">

	<div class="col-md-3 col-centered">
		<div class="panel-primary no-border wb-radius">
			<div class="bg-panel-head">
				<span class="title-logo"><img src="webet/images/logo.png" width="100"><?=lang('reg.affilate');?></span>
			</div>
			<div class="panel-body bg-panel-body">
				<form class="form" method="POST" action="<?=site_url('affiliate/login')?>">

				   <?php if ($this->session->userdata('result')) :?>
				   <aside class="form__alert alert  alert-dismissible" role="alert" style="position:relative;">
				   		<button type="button" class="close" style="position:absolute;top:-25px;right:10px;color:white;border-radius:5px;width:10px;padding:5px;" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<span class="alert__icon ">
							<span class="wbIcon-info"></span>
						</span>

						<div class="alert__content">

			                <b><?=lang('lang.message');?></b>
			                <p><?=$this->session->userdata('message')?></p>
						</div>
						<?php $this->session->unset_userdata('result')?>
						<?php $this->session->unset_userdata('messages')?>
					</aside>
				   <?php endif;?>



					<div class="form-group">
					  <div class="input-group">
					    <span class="input-group-addon bg-addon no-border">
					    	<i class="wbIcon-user-circled"></i>

					    </span>
					   <input type="text" name="username" class="form-control bl" placeholder="<?=lang('login.Username');?>" required >
					  </div>
					</div>

					<div class="form-group">
					  <div class="input-group">
					    <span class="input-group-addon bg-addon no-border">
					    	<i class="wbIcon-lock"></i>

					    </span>
					    <input type="password" name="password" class="form-control bl" placeholder="<?=lang('login.Password');?>" required >
					  </div>
					</div>

					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon" style="background-image:linear-gradient(#444, #333 60%, #222);border:0;">
								<span class="glyphicon glyphicon-globe" style="color:white;"></span>
							</div>
							<select class="form-control bl" name="language" id="language" onchange="changeLanguage();">
					        	<option value="1" <?php echo ($this->session->userdata('afflang') ? $this->session->userdata('afflang') == '1' : $this->config->item('default_lang') == 'english') ? ' selected="selected"' : ''; ?>>English</option>
					        	<option value="2" <?php echo ($this->session->userdata('afflang') ? $this->session->userdata('afflang') == '2' : $this->config->item('default_lang') == 'chinese') ? ' selected="selected"' : ''; ?>>中文</option>
					        </select>
						</div>
					</div>

					<?php if ($useCaptchaOnLogin): ?>
					<div class="form-group">
					  <div class="input-group">

					    <div class="input-group-addon bg-addon no-border">
					    	<img id='image_captcha' class="" src='<?php echo site_url('/affiliate/captcha?' . random_string('alnum')); ?>'  width="100" >
					    	<span class="wbIcon-reload"  onclick="refreshCaptcha()" style="cursor:pointer"></span>

					    </div>

					    <input type="text" name='login_captcha' id='captcha' style="height:44px;" class="form-control bl" placeholder='<?php echo lang('label.captcha'); ?>' required>
					  </div>
					</div>
					<?php endif;?>



					<center><button class="btn btn--primary" type="submit" ><?= lang('http.type.2')?></button></center>
				</form>
			</div>
			<div class="panel-footer bg-panel-footer no-border">
				<?=lang('reg.acreateAcc');?>? <a href="<?=site_url('affiliate/register');?>"><?=lang('reg.clickHere');?></a>
			</div>
		</div>
		<p class="forgot-pass">
			<?= lang('Contact Affiliate Manager')?>
		</p>
	</div>
	<script type="text/javascript" src="<?=$this->utils->jsUrl('jquery-2.1.4.min.js')?>"></script>
   <script type="text/javascript" src="<?=$this->utils->jsUrl('bootstrap.min.js')?>"></script>
	<script type="text/javascript">
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

	</script>

</body>
</html>