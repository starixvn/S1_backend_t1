<div class="row" style="padding-top:10%;padding-bottom:10%; ">
	<div class="col-md-4 col-md-offset-4">

<?php

$messages='';
if (!empty($snackbar)) {
	?>
		<span class='pull-right' style="color:#FF0000">
 <?php

	foreach ($snackbar as $message) {
		$messages .= $message;
	}
	?>
		</span>
<?php
}
?>

<?php if($messages):?>
<div class="alert alert-danger alert-dismissible  hidden-xs  hidden-sm " role="alert" style="margin-bottom:50px;top:50px;">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<span id="message"><?=$messages?></span>
</div>
<?php endif;?>

		<div class="panel panel-primary">
			<?php $bootstrap_style = $this->config->item('default_player_bootstrap_css');
			if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

				<div class="panel-heading" style="text-align:center;">
					<label>玩家登录 Player Login</label>
				</div>

			<?php } else { ?>

				<div class="panel-heading" style="text-align:left;">
					<label><?=lang('lang.login_title');?></label>
				</div>

			<?php } ?>
			
			<div class="panel-body" style="padding-bottom:0;">
				<form method="POST" action="<?=site_url('iframe/auth/login')?>" role="form">
			<input type="hidden" name="referer" value="<?php echo set_value('referer', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '') ?>">

					<div class="form-group">

						<div class="input-group">
							<div class="input-group-addon" style="min-width: 80px;">

							<?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>
								
									<div class="paper2-div">你的账号</div>
									<span>Your Name</span>
								
							<?php	} else { ?>

									<span class="glyphicon glyphicon-user"></span> &nbsp;
									<span><?php echo $this->config->item('default_prefix_for_username'); ?></span>

							<?php } ?>

							</div>
							<input type="text" name="username" required class="form-control" placeholder="<?=lang('sys.item1')?>" value="<?php echo set_value('username') ?>">
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon" style="min-width: 80px;">
							<?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>
							
								<div class="paper2-div">密码</div>
								<span>Password</span>
								
							<?php	} else { ?>

								<span class="glyphicon glyphicon-lock"></span>

							<?php } ?>
							</div>
							<input type="password" required name="password" class="form-control" placeholder="<?=lang('sys.item2')?>">
						</div>
					</div>

					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon" style="min-width: 80px;">
							<?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>
							
								<div class="paper2-div">语言</div>
								<span>Language</span>
								
							<?php	} else { ?>

								<span class="glyphicon glyphicon-globe"></span>

							<?php } ?>
							</div>
							<select name="language" id="lang_select" class="form-control" >
								<option value="1" <?php echo ($currentLang == '1') ? 'selected' : ''; ?> >English</option>
								<option value="2" <?php echo ($currentLang == '2') ? 'selected' : ''; ?> >中文</option>
								<option value="3" <?php echo ($currentLang == '3') ? 'selected' : ''; ?> >Indonesian</option>
								<option value="4" <?php echo ($currentLang == '4') ? 'selected' : ''; ?> >Vietnamese</option>
							</select>
						</div>
					</div>

					<?php if ($this->operatorglobalsettings->getSettingIntValue('captcha_registration')):?>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon" style="min-width: 80px; height: 38px padding: 0;">
								<img id='image_captcha' src='<?php echo site_url('/iframe/auth/captcha?' . random_string('alnum')); ?>' width='120' onclick="refreshCaptcha()">
							</div>
							<input required name='login_captcha' id='captcha' type="text" class="form-control" placeholder="<?php echo lang('label.captcha'); ?>" style="height: 46px;">
						</div>

					</div>
				   <?php endif; ?>

				<center>
						<div class="form-group">
						<?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>
								
								<input type="submit" class="btn btn-primary" value="立即登陆 Login" style="text-transform:uppercase; width:30%;">

						<?php } else { ?>

								<input type="submit" class="btn btn-primary" value="<?=lang('lang.logIn');?>" style="text-transform:uppercase; width:30%;">

						<?php } ?>
						</div>
					</center>
				</form>
			</div>
		</div>
		<center><label class="text-info"><?=lang('reg.createAcc');?> <a href="<?=site_url('iframe_module/iframe_register');?>"><?=lang('reg.clickHere');?></a> | <a href="<?=site_url('iframe_module/forgot_password');?>"><?=lang('lang.forgotpasswd');?> <span class="text-info">|</span></a><a href="<?=$this->utils->getPlayerMessageUrl()?>" target="_blank">	<span class="glyphicon glyphicon-comment"></span> 在线帮助</a></label></center>
		<div style="height:50px;"></div>
	</div>

</div>

<script type="text/javascript">
	function refreshCaptcha(){
        //refresh
        $('#image_captcha').attr('src','<?php echo site_url('/iframe/auth/captcha'); ?>?'+Math.random());
    }
<?php if (isset($snackbar) && !empty($snackbar)) {
	?>
    $(function() {
        <?php foreach ($snackbar as $message) {?>
            $.snackbar({content: "<?php echo str_replace("\n", '', $message); ?>"});
        <?php }
	?>
    });
<?php }
?>
</script>