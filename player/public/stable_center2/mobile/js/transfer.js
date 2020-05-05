var Transfer = {
	
	init: function(){

		var self = this;

		$("div#transfer_from, div#transfer_to").click(function(){
			$(".zizhulist").hide();
	        $(this).find(".zizhulist").toggle();
	    });


		//this is for the transfer_from select	    
	    $("#transfer_from_zizhulist ul li").click(function(){
	        var str=$(this).text(),
	            val = $(this).attr('val');

	        var span = $(this).parent().parent().parent().find("span");

	        span.attr('data-subwalletId', val);
	        span.html(str);

	        self.resetTransferField('transfer_to_zizhulist');

	        // if( val > 0 ){

	        // 	$('#transfer_to_zizhulist ul li').each(function(){
	        // 		if( $(this).attr('val') == "0" ) return true;
		       //  	$(this).addClass('hide');
		       //  });

	        // }

	        if( val != "" ) $('#transfer_to_zizhulist ul li[val="' + val + '"]').addClass('hide');

	        $(this).parent().parent().fadeOut();
	    });


	    //this is for the transfer_to select
	    $("#transfer_to_zizhulist ul li").click(function(){
	        var str=$(this).text(),
	            val = $(this).attr('val');

	        var span = $(this).parent().parent().parent().find("span");

	        span.attr('data-subwalletId', val);
	        span.html(str);

	        self.resetTransferField('transfer_from_zizhulist');

	        // if( val > 0 ){

	        // 	$('#transfer_from_zizhulist ul li').each(function(){
	        // 		if( $(this).attr('val') == "0" ) return true;
		       //  	$(this).addClass('hide');
		       //  });

	        // }

	        if( val != "" ) $('#transfer_from_zizhulist ul li[val="' + val + '"]').addClass('hide');

	        $(this).parent().parent().fadeOut();
	    });


	    //隔行变色
	    $(".zizhulist").each(function(i){
	        $(this).find("li").each(function(i){
	            if(i%2==0){
	                $(this).addClass("suan");
	            }
	        });
	    });

	    $(document).bind('click',function(ev){ 
	      	var ev=ev||window.event;
	      	var elem=ev.target||ev.srcElement;
	      	if(elem.className!="select_form"){
	         	if($(".zizhulist").is(":visible")){
	            	$(".zizhulist").hide();
	         	}
	      	}
	    }); 


	    $('.sub_btn').on('click', function(e){
	    	e.preventDefault();

	    	$('#error_messages').addClass('hide');
	    	$('input[id="transfer_from"]').val($('span[class="transfer_from"]').data('subwalletid'));
			$('input[id="transfer_to"]').val($('span[class="transfer_to"]').data('subwalletid'));

			if( $('input[id="transfer_from"]').val() == "" || $('input[id="transfer_to"]').val() == "" || $('input[name="amount"]').val() == "" ){
				return $('#error_messages').removeClass('hide');
			}

			self.submitTransfer();

	    });

	    $('form[name="transfer_wallet_form"]').submit(function(e) {
	    	e.preventDefault();
	    	$('.sub_btn').trigger('click');
	    });

	    $('a[id^="transfer_"]').on('click', function(){
	    	
	    	var walletID = $(this).data('wallet_id');
	    	self.resetTransferField('transfer_from_zizhulist');
	    	self.resetTransferField('transfer_to_zizhulist');

	    	$('#transfer_from_zizhulist ul li').each(function(){
	    		if( $(this).attr('val') == "" ) return true;  
        		if( $(this).attr('val') == "0" ){
        			$('span.transfer_from').attr('data-subwalletId', $(this).attr('val'));
	        		$('span.transfer_from').html($(this).text());
	        		return true;
        		} 
	        	$(this).addClass('hide');
	        });

	        $('#transfer_to_zizhulist ul li').each(function(){
        		if( $(this).attr('val') != walletID ) return true;
        		var text = $(this).text(),
        			val = $(this).attr('val');

	        	$('span.transfer_to').attr('data-subwalletId', val);
	        	$('span.transfer_to').html(text);
	        });

	        // $('input[name="amount"]').focus();

	    });

	    $('a[id^="transfer_in_"]').on('click', function(e){
	    	e.preventDefault();

	    	var walletID = $(this).data('wallet_id'),
	    		amount = $(this).data('amount');

	    	self.goTransfer(0, walletID, amount, 1);

	    });

	    $('a[id^="transfer_out_"]').on('click', function(e){
	    	e.preventDefault();
	    	
	    	var walletID = $(this).data('wallet_id'),
	    		amount = $(this).data('amount');

	    	self.goTransfer(walletID, 0, amount, 0);

	    });

	    $('body').on('click', '.action--close', function(){
	    	$('.msg_success').addClass('hide');
	    	$('.msg_error').addClass('hide');
	    });

	},

	goTransfer: function(transfer_from, transfer_to, amount, directTransfer){

		var self = this;

		Loading.show('qbmain');

		$.ajax({
			url: base_url + '/player_center/verifyMoneyTransfer/' + player_id,
			type: 'POST',
			data: {
				transfer_from: transfer_from,
				transfer_to: transfer_to,
				amount: amount,
				directTransfer: directTransfer
			},
			success: function(data){

				var message = data.message;

				if( data.msg != undefined && data.msg != "undefined" ) message = data.msg;

				if( data.status == "error" || data.success == false ){
					$('.msg_error').removeClass('hide');
					$('.message_error').html(message);
				}else{
					$('.msg_success').removeClass('hide');
					$('.message_success').html(message);
					Loading.hide('qbmain');
					setTimeout(function(){
						location.reload(true);
					}, 1500);
				}

				Dialog.show('dialog-transfer');
				Loading.hide('qbmain');
				
			}
		});

	},

	resetTransferField: function( element ){

		$('#' + element + ' ul li').each(function(){
        	$(this).removeClass('hide');
        });

	},

	tranBagChange: function(){

		var select_form1v=$(".select_form span").eq(0).text();
	    var select_form2v=$(".select_form span").eq(1).text();
	    $(".select_form span").eq(0).text(select_form2v);
	    $(".select_form span").eq(1).text(select_form1v);	

	},

	showBtnSubmit: function(elem){

		var mt=parseInt($(elem+" .moneyv").val());
	    if(mt>0){
	        $(elem+" .sub_btn").css({"background-position":'0 -53px',cursor:'pointer'});
	        $(elem+" .sub_btn").text(LANG_CONFIRM_SUBMIT);
	    }else{
	        $(elem+" .sub_btn").css({"background-position":"0 0px",cursor:'context-menu'});
	        $(elem+" .sub_btn").text(LANG_FILL_INFO_FIRST);
	    }

	},

	submitTransfer: function(){

		var self = this;

		// $.blockUI({
		// 	message: '<h1>' + Language.cmsLang['text.loading'] + '</h1>',
		// 	overlayCSS: { backgroundColor: '#eee' }
		// });

		Loading.show('qbmain');

		$.ajax({
			url: $('form[name="transfer_wallet_form"]').attr('action'),
			type: 'POST',
			data: $('form[name="transfer_wallet_form"]').serialize(),
			success: function(data){
				
				// Loading.hide('qbmain');

				// $('#qbmain_zz').addClass('hide');
				// $('.response_message').removeClass('hide');

				// if( data.status == "error" || data.success == false ){
				// 	$('.success').addClass('hide');
				// 	$('.failed').removeClass('hide');
				// }else{
				// 	$('.success').removeClass('hide');
				// 	$('.failed').addClass('hide');
				// }

				// var message = data.message;
				// if( data.msg != undefined && data.msg != "undefined" ) message = data.msg;

				// $('.message_response').html(message);

				var message = data.message;

				if( data.msg != undefined && data.msg != "undefined" ) message = data.msg;

				if( data.status == "error" || data.success == false ){
					$('.msg_error').removeClass('hide');
					$('.message_error').html(message);
				}else{
					$('.msg_success').removeClass('hide');
					$('.message_success').html(message);
				}

				Dialog.show('dialog-transfer');
				
				Loading.hide('qbmain');
				setTimeout(function(){
					location.reload(true);
				}, 2000);

			}
		});

	},

	transferAllBalanceToMain: function(message){

		var self = this;
		
		Loading.show('qbmain');
		$.ajax({
			url: base_url + 'api/retrieveAllSubWalletBalanceToMainBallance',
			type: 'GET',
			success: function(data){

				var message = data.message;

				if( data.msg != undefined && data.msg != "undefined" ) message = data.msg;

				if( data.status == "error" || data.success == false ){
					$('.msg_error').removeClass('hide');
					$('.message_error').html(message);
				}else{
					$('.msg_success').removeClass('hide');
					$('.message_success').html(message);
				}

				Dialog.show('dialog-transfer');
				
				Loading.hide('qbmain');
				setTimeout(function(){
					location.reload(true);
				}, 1000);

			}
		});

	}

}