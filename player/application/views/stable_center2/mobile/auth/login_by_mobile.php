<style type="text/css">
#btn_send_sms_code{
    width: 160px;
    height: 38px;
/*     margin: 2px auto;
 */    background: #ff9800;
    border-radius: 3px;
    border: 1px;
    line-height: 40px;
    text-align: center;
    font-size: 14px;
    color: #FFF;
}
#btn_login{
    width: 280px;
    height: 48px;
    margin: 32px auto;
    background: #ee9a07;
    border-radius: 3px;
    border: 1px;
    line-height: 48px;
    text-align: center;
    font-size: 17px;
    color: #FFF;
}
</style>
<form method="POST" action="<?=site_url('player_center/mobile_login')?>" role="form" id="frm_login">
    <div class="input" style="margin-top: 80px;">
        <input id="login_mobile_number" name="contact_number" placeholder="<?php echo lang('Mobile number'); ?>" minlength="11" maxlength="11" type="text" class="input_01">
        <ul id="dltext1">
            <i></i><?php echo lang("Mobile can't be empty");?>
        </ul>
        <div class="send_sms_container">
            <input type="button" class="hy" id="btn_send_sms_code" value="<?php echo lang('Send SMS Code');?>" >
            <input id="sms_verify_code" name="sms_verification_code" placeholder="<?php echo lang('SMS Code'); ?>" type="text" class="input_01">
        </div>
        <ul id="dltext2">
            <i></i><?php echo lang("SMS Code can't be empty");?>
        </ul>

        <center>
            <span id="login_errormsg" class="hide" style="float: none;"></span>
        </center>

        <input type="submit" class="hy" id="btn_login" value="<?php echo lang('Login Now');?>" >
        <div class="lefttext" style="margin-bottom: 10px;"><a class="threepage" href="<?=site_url('/iframe/auth/login')?>" style="cursor:pointer;"><?php echo lang('Username login');?></a></div>
    </div>
</form>

<script>

    function send_sms_code(){
        //validate mobile
        var mobile_number=$('#login_mobile_number').val();
        if(mobile_number==''){
            alert("<?php echo lang('Please fill in mobile number');?>");
            return;
        }
        // var captcha=$('#captcha_for_mobile').val();

        $("#btn_login").prop('disabled', true);
        $("#login_mobile_number").prop('disabled', true);
        // $("#captcha_for_mobile").prop('disabled', true);
        //clear sms_verify_code
        $("#sms_verify_code").val('');

        $("#loading").removeClass('hide');
        $('#login_errormsg').removeClass();
        $('#login_errormsg').addClass('hide');

        //send sms
        $.ajax({
            url:'/pub/send_sms_verification/'+mobile_number+'/only_exists',
            data:{},
            dataType: 'jsonp',
            method: 'GET',
            cache: false,
            success: function(data){

                if(data['success']){

                    var btnmoema=$("#btn_send_sms_code");
                    btnmoema.addClass("disabled").prop('disabled', true);
                    var countdownn=<?php echo $this->utils->getConfig('sms_cooldown_time');?>;
                    btnmoema.val("重新发送（"+countdownn+"s）");
                    var mysint=setInterval(function(){
                        countdownn--;
                        btnmoema.val("重新发送（"+countdownn+"s）");
                        if(countdownn<0){
                            clearInterval(mysint);
                            btnmoema.val("<?php echo lang('Send Again');?>");
                            btnmoema.removeClass("disabled").prop('disabled', false);
                        }
                    },1000);

                    $("#btn_login").prop('disabled', false);
                    $("#login_mobile_number").prop('disabled', false);
                    $('#login_errormsg').removeClass('hide');
                    $('#login_errormsg').addClass('text-success');
                    $('#login_errormsg').html("<?php echo lang('Sent code to SMS');?>");
                }else{
                    // refreshCaptcha('image_captcha_for_mobile_login');
                    //$('#login_mobile_number').focus();
                    $('#login_errormsg').removeClass('hide');
                    $('#login_errormsg').addClass('text-danger');
                    $('#login_errormsg').html(data['message']);
                    $("#btn_login").prop('disabled', false);
                    $("#login_mobile_number").prop('disabled', false);
                    // $("#captcha_for_mobile").prop('disabled', false);
                }
                $("#loading").addClass('hide');
            },
            error: function(){
                // refreshCaptcha('image_captcha_for_mobile_login');
                //$('#login_mobile_number').focus();
                $('#login_errormsg').removeClass('hide');
                $('#login_errormsg').addClass('text-danger');
                $('#login_errormsg').html("<?php echo lang('Send SMS code failed');?>");
                $("#btn_login").prop('disabled', false);
                $("#login_mobile_number").prop('disabled', false);
                // $("#captcha_for_mobile").prop('disabled', false);
                $("#loading").addClass('hide');
            }
        });
    }

    $(document).ready(function () {

        var btn = $(".user_btn,.menu_btn,.menu_btn_two");
        var menu = $(".menu_right,.menu_left");
        var leftmove = "left_move";
        var mmove = "menu_move";
        var leftm = $(".menu_left");
        var rightm = $(".menu_right");
        var tap = "click"; //"touchstart";//
        //换页效果

        $('#btn_send_sms_code').click(function(){
            send_sms_code();
        });

        $("#btn_login").click(function (e) {

            e.preventDefault();

            var dltext1 = $("#login_mobile_number").val();
            var dltext2 = $("#sms_verify_code").val();
            // var captcha = $("#captcha").val();

            if (dltext1.length == 0) {
                $("#dltext1").show();
                return false;
            } else {
                $("#dltext1").hide();
            }
            if (dltext2.length == 0) {
                $("#dltext2").show();
                return false;
            } else {
                $("#dltext2").hide();
            }

            // if ( ! $('.captcha').hasClass('hide') && captcha.length == 0) {
            //     $("#dltext3").show();
            //     return false;
            // } else {
            //     $("#dltext3").hide();
            // }

            // $('#login_errormsg').addClass('hide');
            $('#login_errormsg').removeClass();
            $('#login_errormsg').addClass('hide');
            $("#loading").removeClass('hide');

            $.ajax({
            	url: $('#frm_login').attr('action'),
            	type: 'POST',
            	dataType: 'json',
            	data: $('#frm_login').serialize(),
            	success: function(data){

            		$("#loading").addClass('hide');

            		if( data['success']) {
                        window.location = data['next_url'];
                    }else{
            			// var message = JSON.parse(data.msg);

                        $('#login_errormsg').removeClass('hide');
                        $('#login_errormsg').addClass('text-danger');
                        $('#login_errormsg').html(data['message']);

                        // if( message.login != undefined ){

                        //     alert(message.login);

                        // }else{

                        //     alert(message.replace(/<(?:.|\n)*?>/gm, ''));

                        // }

                        // $('.captcha').removeClass('hide');
                        // $('#captcha').val('');
                        // $('#captcha').prop('disabled', false);
                        // $('#image_captcha').attr('src','<?= site_url('/iframe/auth/captcha')?>?' + Math.random());
            			// location.reload(true);
            			return;
            		}
            	},
                error: function(){
                    $("#loading").addClass('hide');
                    $('#login_errormsg').removeClass('hide');
                    $('#login_errormsg').addClass('text-danger');
                    $('#login_errormsg').html("<?php echo lang('Login failed, try it later');?>");
                }
            });

            // $('#frm_login').submit();

        });

    });

</script>