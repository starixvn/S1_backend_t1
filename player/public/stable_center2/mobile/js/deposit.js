var Deposit = {
	currentTabRequest: '',
	init: function(){

		var self = this;

		$(".jy_menu_right li").click(function(){
	        $(".jy_menu_right li").removeClass("jy_cur");
	        $(this).addClass("jy_cur");
	        var tabindex=$(this).index();

	        var url = $(this).data('url');

	        self.loadContent(url);
	    });

	    $("body").on('click', '.select_form', function(){
	        $(this).find(".zizhulist").toggle();
	    });
	    
	    $("body").on('click', '.zizhulist ul li', function(){
	        var str=$(this).text(),
	        	val = $(this).attr('val');
	        $(this).parent().parent().parent().find("span").html(str);
	        $(this).parent().parent().parent().find(".field").val(val);
	        $(this).parent().parent().fadeOut();
	    }); 

	     $('body').on('click', '.sub_btn', function(e){
	     	e.preventDefault();
	     	var form = $(this).parent().parent().parent('form');
	     	self.conFirmMannualDeposit(form);
	     });

	     var bank_listlong=false;
	    $('body').on('click', ".bank_list_main span", function(){
	        if(bank_listlong){
	            $(".bank_list_main .bank_list").animate({height:"110px"},300);
	            bank_listlong=false;
	            $(this).removeClass("bank_hide");
	        }else{
	            $(".bank_list_main .bank_list").animate({height:"355px"},300);
	            bank_listlong=true;
	            $(this).addClass("bank_hide");
	        }
	    });

	    $('body').on("click", ".bank_list li", function(){
	        $(".bank_list li a").removeClass("cur");
	        $(this).find("a").addClass("cur");

	        $('input[name="bank"]').val($(this).data('code'));

	    });

	    $('body').on("click", ".btn_back", function(){
	        self.loadContent(self.currentTabRequest);
	    });

	},

	loadContent: function(url){

		var self = this;

		Loading.show('deposit_content');

		$.ajax({
			url: url,
			type: 'GET',
			success: function(data){
				self.currentTabRequest = url;
				$('#deposit_content').html(data);
			}
		});

	},

	conFirmMannualDeposit: function(form){

		var self = this;

		Loading.show('deposit_content');

		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: form.serialize(),
			success: function(data){

				if( data.status == "error" ){
					var status = '';
					if( data.status == "error" ) status = 'failed';

					var result = base_url + 'player_center/transferResult/' + status;
					$.post(result, { message: data.msg }, function(html){
						$('#deposit_content').html(html);
					});
					return false;
				}

				$('#deposit_content').html(data);
			}
		});

	},

	prefferedAccountEvent: function(element){

		var self = this;

		if( element == "other_fields" ){
			$('#' + element).removeClass('hide');
			$('#preffered_account').addClass('hide');
		}else{
			$('#' + element).removeClass('hide');
			$('#other_fields').addClass('hide');
		}

	}

}