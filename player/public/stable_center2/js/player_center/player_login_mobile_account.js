var PlayerLoginMobileAcct = {
	msgSendCodeSuccess: '',
	msgSendAgain: '',
	smsCooldownTime: '',
	msgEmptyNumber: '',
	msgSendCodeFail: '',
	msgLoginFail: '',
	init: function(){

		var self = this;

	},

	sendVerificationCode: function(remote){

		var self = this;
		var mobile_number = $('#login_mobile_number').val();

		//send sms
		if(mobile_number.length > 1){
			//load_alert_message("success",mobile_number);
       	$.ajax({
            url:'/pub/send_sms_verification/'+mobile_number+'/only_exists',
            data:{},
            dataType: 'jsonp',
            method: 'GET',
            cache: false,
            success: function(data){

                console.log(data);

                if(data['success']){
                    // $('#captcha_for_mobile').val('');

                   var btnmoema=$("#btn_send_sms_code");
                    btnmoema.addClass("disabled").prop('disabled', true);
                    var countdownn= self.smsCooldownTime;
                    btnmoema.html("重新发送（"+countdownn+"s）");
                    var mysint=setInterval(function(){
                        countdownn--;
                        btnmoema.html("重新发送（"+countdownn+"s）");
                        if(countdownn<0){
                            clearInterval(mysint);
                            btnmoema.html(self.msgSendCodeSuccess);
                            btnmoema.removeClass("disabled").prop('disabled', false);
                        }
                    },1000);

					load_alert_message("success",self.msgSendCodeSuccess);
                }else{

                    load_alert_message("danger",data['message']);
                    // $("#btn_login_by_mobile").prop('disabled', false);
                    $("#login_mobile_number").val('');
                    $("#login_mobile_number").prop('disabled', false);
                    $("#btn_send_sms_code").prop('disabled', false);
                    // $("#captcha_for_mobile").prop('disabled', false);
                }
            },
            error: function(){
                // refreshCaptcha('image_captcha_for_mobile_login');
                //$('#login_mobile_number').focus();
               	load_alert_message("danger",self.msgSendCodeFail);
                // $("#btn_login_by_mobile").prop('disabled', false);
                $("#login_mobile_number").val('');
                $("#login_mobile_number").prop('disabled', false);
                // $("#captcha_for_mobile").prop('disabled', false);
            }
        });
    	} else {
    		load_alert_message("danger",self.msgEmptyNumber);
    	}

	},
	loginMobileAcct: function(remote){
		var self = this;
		var contact_number = $('#login_mobile_number').val();
		var sms_verification_code=$('#sms_verify_code').val();
		$.ajax({
                url:'/player_center/mobile_login',
                data:{'contact_number': contact_number, 'sms_verification_code': sms_verification_code},
                dataType: 'json',
                method: 'POST',
                cache: false,
                success: function(data){
                	//alert(data);
                    if(data['status'] == "success"){
                        window.location.href=data['data']['next_url'];
                    }else{
                        load_alert_message("danger",data['message']);
                    }
                },
                error: function(){
                    load_alert_message("danger",self.msgLoginFail);
                }
            });
	}

}
$("form").submit(function(e){
    e.preventDefault();
});
