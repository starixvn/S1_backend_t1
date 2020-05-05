<div class="password_recovery_email_wrapper">
    <div class="panel panel-default password_recovery_email">
        <div class="panel-heading">
            <?=lang('Find password by email')?>
            <a href="/iframe/auth/login" class="close" aria-hidden="true">×</a>
        </div>
        <div class="panel-body">
            <form id="recoveryForm" action="/iframe_module/password_recovery_reset_code" method="post" autocomplete="off">
                <div class="form-group">
                    <input type="hidden" name="title" value="<?=lang('Find password by email')?>" />
                    <input type="text" id="username" name="username" class="form-control" value="<?=set_value('username')?>" placeholder="<?=lang('forgot.02')?>" required="required" autofocus="autofocus" autocomplete="username"/>
                    <input type="text" id="email" name="email" class="form-control" value="<?=set_value('email')?>" placeholder="<?=lang('Email Address')?>" required="required" autocomplete="email"/>
                    <input type='text' name='login_captcha' id='captcha' class='form-control' placeholder='<?php echo lang('label.captcha'); ?>' autocomplete="one-time-code"/>
                    <div class="captcha-wrapper">
                        <a href="javascript:void(0)" id="refreshCaptcha"><i class="glyphicon glyphicon-refresh"></i></a>
                        <img id='image_captcha' src='<?php echo site_url('/iframe/auth/captcha?' . random_string('alnum')); ?>' width='110' height='34'>
                    </div>
                    <button id="nextButton" class="btn btn-primary"><?=lang('forgot.03')?></button>
                    <div id="ajaxError" class="alert-danger">Error msg</div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function() {
	var refreshCaptcha = function(){
		$('#image_captcha').attr('src','<?php echo site_url('/iframe/auth/captcha'); ?>?'+Math.random());
	}

	var sendResetCode = function() {
		$("#ajaxError").hide();
		var url = '<?=site_url('/iframe_module/find_password_email')?>/';
		var username = $("#username").val();
		var captcha = $("#captcha").val();
		var email = encodeURIComponent($("#email").val());
		url += username + '/' + email + '/' + captcha;
		$.ajax({
			url : url
		}).done(function(data){
			if(data.message) {
				$("#ajaxError").text(data.message).fadeIn(200);

				if(data.hasOwnProperty('captcha_text_align')){
					$("#ajaxError").attr('style','text-align:' + data.captcha_text_align + ';');
					$("#ajaxError").show();
				}

				$("#captcha").val('');
				$('#refreshCaptcha').click();
			}
			if(data.success) {
				$("#recoveryForm").submit();
			}
		});
		return false;
	};

	$('#refreshCaptcha').click(refreshCaptcha);
	$('#nextButton').click(sendResetCode);
});
</script>