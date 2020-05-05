<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="<?= $this->utils->getSystemUrl('player','/resources/third_party/bootstrap/3.3.7/bootstrap.min.css') ?>">
	<script type="text/javascript" src="<?= $this->utils->getSystemUrl('player','/resources/third_party/jquery/jquery-3.1.1.min.js') ?>"></script> 
	<script type="text/javascript" src="<?= $this->utils->getSystemUrl('player','/resources/third_party/bootstrap/3.3.7/bootstrap.js') ?>"></script> 
	<title></title>
</head>
<style type="text/css">
	/* body {
		background: #000;
	} */


	
	.login-wrapper {
		max-width: 100%;
		margin: 0px auto 160px;
	}
	.login-wrapper .login-content {
		padding-bottom: 20px;
		/* border: 5px solid rgba(255,255,255,0.3) !important; */
	    background-color: rgba(0,0,0,1);
	    /* box-shadow: 0 90px 90px rgba(0,0,0,1); */
	    position: relative;
    	z-index: 999;
    	/* border-radius: 5px; */
    	overflow: hidden;
    	text-align: center;
	}
	.login-wrapper .login-content .text-top {
		color: #fff;
		font-size: 24px;
		display: inline-block;
		padding: 10px 0;
	}
	.login-wrapper .login-content .login-form,
	.login-wrapper .login-content .btn-submit-wrapper,
	.login-wrapper .login-content .remember-me-wrapper,
	.login-wrapper .login-content .login-bottom-content {
		margin: 15px;
	}
	.login-wrapper .login-content .login-form .form-group {
		margin-bottom: 10px;
	}
	.login-wrapper .login-content .login-form .form-group input {
		height: 50px;
	    border: 1px solid #ccc;
	    color: #fff !important;
	    background: transparent;
	    width: 100%;
	    border-radius: 0;
	    border-top: 0;
	    border-left: 0;
	    border-right: 0;
	    outline: none;
	}
	.login-wrapper .login-content .btn-submit-wrapper button {
		background: #b62127;
		border-radius: 0;
		color: #fff;
		font-size: 18px;
		padding: 10px 0;
		width: 100%;
		border: 0;
	}
	.login-wrapper .login-content .btn-submit-wrapper button:focus {
		outline: none;
	}
	.login-wrapper .login-content .remember-me-wrapper {
		margin-bottom: 60px;
	}
	.login-wrapper .login-content .remember-me-wrapper .r-checkbox {
		text-align: left;
		width: 50%;
		float: left;
		color: #fff;
	}
	.login-wrapper .login-content .remember-me-wrapper .r-checkbox input {
		float: left;
    	margin-right: 7px;
	}
	.login-wrapper .login-content .remember-me-wrapper .reg-link-wrapper {
		text-align: right;
		width: 50%;
		float: right;
	}
	.login-wrapper .login-content .remember-me-wrapper .reg-link-wrapper a.reg-link,
	.login-wrapper .login-content .login-bottom-content span a {
		color: #b62127;
		text-decoration: none;
	}
	.login-wrapper .login-content .login-bottom-content {
		text-align: left;
		color: #fff
	}
	.login-wrapper .login-content .login-bottom-content span {
		display: inline-block;
	}
	.login-wrapper .login-content .login-bottom-content span:nth-child(2) {
		border-right: 1px #fff solid;
    	padding: 0 5px 0 5px;
	}
	.login-wrapper .login-content .login-bottom-content span:nth-child(3) {
		padding-left: 2px;
	}
</style>
<body>
	<div class="login-wrapper">
		<div class="login-content">
			<div class="text-top">
				<?php echo lang('Login Your Account');?>
			</div>
			<div class="login-form">
				<div class="alert alert-danger alert-dismissible  hide" role="alert">
					<p class="allert-content"></p>
				</div>
				<form >
					<div class="form-group">
						<input type="text" name="username" id="username" placeholder="<?=lang('system.word38')?>">
					</div>
					<div class="form-group">
						<input type="password" name="password" id="password" placeholder="<?=lang('sys.em3')?>">
					</div>
					<div class="form-group">
						<input type="hidden" name="redirectUri" id="redirectUri" placeholder="">
					</div>
				</form>
			</div>
			<div class="btn-submit-wrapper">
				<button class="btn btn-primary login-btn" id="login"><?php echo lang('Login Now');?></button>
			</div>
			<div class="remember-me-wrapper">
				<!-- <div class="r-checkbox">
					<input type="checkbox" name="remember_me">
					保持登录状态                                
				</div> -->
				<!-- <div class="reg-link-wrapper">
					<a href="<?=site_url('player_center/iframe_register')?>" class="reg-link"><?php echo lang('Free Registration');?></a>
				</div> -->
			</div>
			<!-- <div class="login-bottom-content">
				 <span>登录有问题吗? 请</span>
				 <span><a href="#!" class="customer-service">联系客服</a></span>
				 <span><a href="#!">忘记密码</a></span>
			</div> -->
		</div>
	</div>
</body>
<script type="text/javascript">
	$(document).on("click","#login",function(){
		var url = "<?= $this->utils->getSystemUrl('player','/iframe/auth/login') ?>";
		var username = $("#username").val();
		var password = $("#password").val();
		var redirectUri = "<?= @$redirectUri ?>";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: {
                username:username,
                password:password,
                redirectUri:redirectUri,
                gamePlatform:394
            },
            success: function(data){
            	console.log(data);
                if(data.success == true){
                    window.location.replace(data.redirectUri);
                } else {
                	$('.allert-content').html("<?php echo lang('Username or password incorrect!'); ?>");
                	$('.alert').removeClass("hide").fadeIn().fadeOut(5000);;
                }
            }
        });
    });
</script>
</html>
