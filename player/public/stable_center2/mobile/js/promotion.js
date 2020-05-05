var Promotion = {
	
	init: function(){

		var self = this;

		self.pageEvents();
		self.getMyOfferList(base_url + 'api/myPromotion', 'myprom1');
		self.getMyOfferList(base_url + 'api/getPromotions', 'myprom2');

	},

	pageEvents: function(){

		var self = this;

		$('.menu_jyclass').click(function(){
		    var vr=$(this).attr('vr');
		    $(".table_main").hide();
		    $("#main_"+vr).fadeIn();
		    $('.menu_jyclass').removeClass('jy_cur');
		    $(this).addClass('jy_cur');
		}); 

		$('body').on('click', "#mypromtion ul li a", function(e){
			e.preventDefault();
			self.getMyOfferList($(this).attr('href'), 'myprom1');
		});

		$('body').on('click', "#all_promotion ul li a", function(e){
			e.preventDefault();
			self.getMyOfferList($(this).attr('href'), 'myprom2');
		});

		$('body').on('click', 'a[id^="veiw_details_"]', function(e){
			e.preventDefault();

			var element = 'dialog-content_' + $(this).data('promo');

			Dialog.show(element);
			
		});

	},

	selectmyprom: function(i){
	    $(".mypromtable").hide();
	    $(".myprom"+i).fadeIn();
	    $(".qb_main_yh .main_bottom .menu_div").removeClass("selected");
	    $(".qb_main_yh .main_bottom .menu_div").eq(i-1).addClass("selected");
	},

	getMyOfferList: function(url, element){

		var self = this;

		Loading.show(element);

		$.ajax({
			url: url,
			type: 'GET',
			success: function(data){
				$('.' + element).html(data);
			}
		});

	}

}