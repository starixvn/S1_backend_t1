<?php
	$cmsVersion = $this->CI->utils->getCmsVersion();  
	
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1, minimum-scale=1.0, maximum-scale=3.0">
	<link rel="stylesheet" type="text/css" href="<?= $this->utils->getSystemUrl('player','/resources/third_party/bootstrap/3.3.7/bootstrap.min.css') ?>">
	<script type="text/javascript" src="<?= $this->utils->getSystemUrl('player','/resources/third_party/jquery/jquery-3.1.1.min.js') ?>"></script> 
	<script type="text/javascript" src="<?= $this->utils->getSystemUrl('player','/resources/third_party/bootstrap/3.3.7/bootstrap.js') ?>"></script> 
	<link href="<?=$this->utils->getSystemUrl('m') . '/includes/css/style-mobile.css?v=' . $cmsVersion;?>" rel="stylesheet" />
</head>
<style type="text/css">
	.login__wrapper {
		padding-top: 20%;
		padding-left: 15px;
    	padding-right: 15px;
	}
	.login__wrapper form#oneworks_form_login button.login_btn.hy {
		width: 100%;
	}

	.login__wrapper form#oneworks_form_login img.currency__flag {
		width: 25px;
		height: 25px;
	}
	.login__wrapper form#oneworks_form_login ul.dropdown-menu {
		width: 25%;
		left: 0;
	    right: 0;
	    margin-left: auto;
	    margin-right: auto;
	}
	.login__wrapper form#oneworks_form_login .form-group span.warning__text {
		display: none;
	}
	.login__wrapper form#oneworks_form_login .form-group span.warning__text p {
		color: #F00;
		margin: 0;
		font-size: 14px;
	}
</style>

<body>
	<div class="container-fluid">
		<div class="row">
			<div class="login__wrapper">
				<form  action="" role="form" id="oneworks_form_login">
					<div class="input">
						<div class="form-group">
							<input type="text" name="username" id="username" placeholder="<?=lang('system.word38')?>" class="input_01">
								<span class="warning__text">
									<p><?=lang("Username can't be empty")?></p>
								</span>
							<!-- </input> -->
							<input type="password" name="password" id="password" placeholder="<?=lang('sys.em3')?>" class="input_01">
							<span class="warning__text">
								<p><?=lang("Password can't be empty")?></p>
							</span>
							<!-- </input> -->
						</div>
						<div class="form-group">
							<button  class="login_btn hy" id="login_c"><?php echo lang('Login Now');?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	// ex:
    // OnebookAppLib.PostMessage("login", {token: "0000", transfer: "https://your.domain/transfer?username=xyz"});
    var OnebookAppLib = (function (global) {
        var OnebookInterface = global.webkit ? global.webkit.messageHandlers.OnebookInterface : global.OnebookInterface;
        function OnebookPostMessage(act, data) {
            var parameter = JSON.stringify({
                    act: act,
                    data: data
                });
            if (OnebookInterface) {
                OnebookInterface.postMessage(parameter);
            } else {
                throw "OnebookInterface is missing";
            }
        }
        return {
            PostMessage: OnebookPostMessage
        }
    })(this);
	
    function AppLogin(data){
        OnebookAppLib.PostMessage("login", data);
    }

	$(document).on("click",".anchor",function(){
		$(".anchor").removeClass("active");
		$(this).addClass("active"); 
		$('#market').find('img').attr("src",$(this).find('img').attr('src'));
		$('#market').find('span').first().text($(this).text());
		$("#flag_selected:hidden").show();
		$('#oneworks_form_login').trigger("reset");
	});

	$(document).on("submit","#oneworks_form_login",function(e){
		e.preventDefault();
		var url = "<?= $this->utils->getSystemUrl('player','/iframe/auth/login') ?>";
		var username = $("#username").val();
		var password = $("#password").val();
		var valid = true;
		if(username.length === 0) {
			valid = false;
			$("#username").next('span').show();
		}
		if(password.length === 0) {
			valid = false;
			$("#password").next('span').show();
		}
		
		if(valid){
			$.ajax({
				type: "POST",
				url: url,
				dataType: 'json',
				data: {
					username:username,
					password:password,
					gamePlatform:58
				},
				success: function(result){
					// console.log(result);
					if(result.success == true){
						// console.log(result.fund_transfer_link);
						AppLogin({
							token: result.sb_token,
							transfer: result.fund_transfer_link,
						});
					} else {
						var message = "<?php echo lang('Username or password incorrect!'); ?>";
						alert(message);
					}	
				}
			});
		}
		
    });
</script>
</html>