<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=lang('reg.affilate');?></title>

    <!-- Theme switcher -->
    <?php $user_theme = !empty($this->session->userdata('affiliate_theme')) ? $this->session->userdata('affiliate_theme') : 'flatly';?>
    <?php 
        if($_SERVER['SERVER_NAME']=='aff.vip-win007.com'){
           $user_theme = $this->session->userdata['affiliate_theme'] = "win007"; 
        }


    ?>
    <link href="<?=$this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css')?>" rel="stylesheet">
    <link href="<?=$this->utils->cssUrl('Hover-master/hover.css?v=3.01.01.0031')?>" rel="stylesheet" media="all">

    <style type="text/css">
    	.panel-heading{
    		background-color: #434343 !important;
    	}
    	/*.panel-body{
    		border-left: 1px solid #f2970e;
    		border-right: 1px solid #f2970e;
    	}*/
    	.panel-primary{
    		border: 1px solid #434343 !important;
    		/*border-right: 1px solid #f2970e;
    		border-bottom: 1px solid #f2970e;*/
    	}

    	#xcbetLogo{
    		width: 54%;
    	}
    	#affiliate-register-form{
    		border-radius: 35px;
    	}

    	.hvr-bounce-in {
			background-color: #edd051 !important;
			border-color: transparent !important;
			/*border-radius: 35px;*/
			display: inline-block;
			vertical-align: middle;
			-webkit-transform: perspective(1px) translateZ(0);
			transform: perspective(1px) translateZ(0);
			box-shadow: 0 0 1px transparent;
			-webkit-transition-duration: 0.5s;
			transition-duration: 0.5s;
		}
		.hvr-bounce-in:hover, .hvr-bounce-in:focus, .hvr-bounce-in:active {
			background-color: #ffb015 !important;
    		border-color: transparent !important;
    		/*border-radius: 35px;*/
			-webkit-transform: scale(1.2);
			transform: scale(1.1);
			-webkit-transition-timing-function: cubic-bezier(0.47, 2.02, 0.31, -0.36);
			transition-timing-function: cubic-bezier(0.47, 2.02, 0.31, -0.36);
		}
		.form-control:hover, .form-control:active, .form-control:focus{
			/*border-color:#A8D8E8;*/
			border-color:#ecf0f1;
		}

    </style>
</head>
<body>
	<div class="container" style="padding-top: 10%; padding-bottom: 10%;">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">
							<b>
								<img class="brand-img" width="49" height="45" id="xcbetLogo" src="xcbet/images/logo.png">
				                
			                </b>
			                <div style="text-align: center">
			                	<?=lang('reg.affilate');?>
			                </div>
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
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-globe"></span>
									</div>
									<select class="form-control" name="language" id="language" onchange="changeLanguage();">
							        	<option value="1" <?php echo ($this->session->userdata('afflang') ? $this->session->userdata('afflang') == '1' : $this->config->item('default_lang') == 'english') ? ' selected="selected"' : ''; ?>>English</option>
							        	<option value="2" <?php echo ($this->session->userdata('afflang') ? $this->session->userdata('afflang') == '2' : $this->config->item('default_lang') == 'chinese') ? ' selected="selected"' : ''; ?>>中文</option>
							        	<option value="3" <?php echo ($this->session->userdata('afflang') ? $this->session->userdata('afflang') == '3' : $this->config->item('default_lang') == 'indonesian') ? ' selected="selected"' : ''; ?>>Indonesian</option>
							        	<option value="4" <?php echo ($this->session->userdata('afflang') ? $this->session->userdata('afflang') == '4' : $this->config->item('default_lang') == 'vietnamese') ? ' selected="selected"' : ''; ?>>Vietnamese</option>
							        </select>
								</div>
							</div>
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
								<input type="submit" class="hvr-bounce-in btn btn-primary text-uppercase col-md-4 col-md-offset-4" value="<?=lang('lang.logIn');?>">
							</div>
						</form>
					</div><!-- END OF PANEL-BODY -->
					<div class="panel-footer">
						<div class="text-center">
							<strong><?=lang('reg.acreateAcc');?>? <a href="<?=site_url('affiliate/register');?>"><?=lang('reg.clickHere');?></a></strong>
						</div>
					</div>
				</div><!-- END OF PANEL -->
			</div><!-- END OF COL -->
		</div><!-- END OF ROW -->
	</div><!-- END OF CONTAINER -->

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