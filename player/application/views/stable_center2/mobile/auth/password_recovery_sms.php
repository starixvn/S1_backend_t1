<style type="text/css">
/*---- DEFAULT CSS FOR PW RECOVERY SMS SECTION ------*/
.fr-sms-wrapper {
  text-align: center;
  margin-top: 10%;
  margin-bottom: 10%;
}
.fr-sms-wrapper .panel {
  box-shadow: 0 0 0 rgba(0,0,0,0);
  border: 0;
}
.fr-sms-wrapper .panel-heading {
  border: 0;
}
.fr-sms-wrapper .panel-heading h2 {
  margin: 0;
  font-size: 18px;
}
.fr-sms-wrapper .panel-body input {
  padding: 19px 7px;
  border-radius: 0;
  margin-bottom: 15px !important;
}
.fr-sms-wrapper .panel-body input#captcha {
  width: 60%;
  float: left;
}
.fr-sms-wrapper .panel-body div.captcha-wrapper {
  display: inline-block;
  padding-top: 3px;
  float: right;
}
.fr-sms-wrapper .panel-body div.captcha-wrapper a#refreshCaptcha {
  padding-right: 4px;
}
.fr-sms-wrapper .panel-body button {
  clear: both;
  display: block;
  width: 100%;
  background: #d5d5d5;
  color: #000;
  border: 1px #a4a4a4 solid;
  border-radius: 0;
}
</style>

<div class="col-md-4 col-md-offset-4">
    <div class="panel panel-default password-recovery fr-sms-wrapper">
        <div class="panel-heading">
            <h2><?=lang('Find password by SMS')?></h2>
        </div>
        <div class="panel-body">
            <form id="recoveryForm" action="/iframe_module/password_recovery_reset_code" method="post" autocomplete="off">
                <div class="form-group">
                    <input type="hidden" name="title" value="<?=lang('Find password by SMS')?>" />
                    <input type="text" id="username" name="username" class="form-control" value="<?=set_value('username')?>" placeholder="<?=lang('forgot.02')?>" required="required" autofocus="autofocus"/>
                    <input type="text" id="mobile" name="mobile" class="form-control" value="<?=set_value('mobile')?>" placeholder="<?=lang('Mobile number')?>" required="required"/>
                    <input type='text' name='login_captcha' id='captcha' class='form-control' placeholder='<?php echo lang('label.captcha'); ?>' />
                    <div>
                        <a href="javascript:void(0)" id="refreshCaptcha"><i class="glyphicon glyphicon-refresh"></i></a>
                        <img id='image_captcha' src='<?php echo site_url('/iframe/auth/captcha?' . random_string('alnum')); ?>'>
                    </div>
                    <button id="nextButton" class="btn btn-primary"><?=lang('forgot.03')?></button>
                    <div id="ajaxError" class="alert-danger" style="margin-top: 30px; padding: 5px 10px; display: none">Error msg</div>
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
        var url = '<?=site_url('/iframe_module/find_password_sms')?>/';
        var username = $("#username").val();
        var captcha = $("#captcha").val();
        var mobile = $("#mobile").val();
        url += username + '/' + mobile + '/' + captcha;
        $.ajax({
            url : url
        }).done(function(data){
            if(data.message) {
                $("#ajaxError").text(data.message).fadeIn(200);
                $("#captcha").val('');
                $('#refreshCaptcha').click();
            }
            if(data.success) {
                $("#recoveryForm").submit();
            }
        });
        return false;
    };

    $('#refreshCaptcha, #image_captcha').click(refreshCaptcha);
    $('#nextButton').click(sendResetCode);
});
</script>