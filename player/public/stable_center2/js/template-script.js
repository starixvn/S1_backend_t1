/* ================================
Toot Tip
================================== */
$(document).ready(function () {
    //@MPANUGAO
    //Translate the Next, Preview and End Button Text bcoz lang is not working on .js
    // PrevLangVal = "";
    // NextLangVal = "";
    // EndTourLangVal = "";
    // $.post(base_url + "player_center/changeLanguageJson", {changeLangPrev:"Previous",changeLangNext:"Next",changeLangEndTour:"End Tour"} ,function(data) {
    //     PrevLangVal = data.Prev;
    //     NextLangVal = data.Next;
    //     EndTourLangVal = data.EndTour;
    // },'json');

	var popoverTemplate = ['<div class="popover">',
		'<div class="arrow"></div>',
		'<div class="popover-content">',
		'</div>',
		'</div>'].join('');

	var content = ['<div class="clearfix">',
	'<div class="col-md-6">Promotion<br/>Bonus:</div>',
	'<div class="col-md-6 text-right"><span class="fw900 f30">¥0</span></div>',
	'</div>'].join('');

	$('body').popover({
		selector: '[rel=popover]',
		trigger: 'hover',
		content : content,
		template: popoverTemplate,
		placement: "bottom",
		html: true
	});

	// controls withdraw bank list hide/show
	$('.withdrawal-modal-body .show-btn').click(function(){
		$('.withdrawal-modal-body #withdrawBankList').css('height', '');
	});
	$('.withdrawal-modal-body .hide-btn').click(function(){
		var heightPx = $('.withdrawal-modal-body #withdrawBankList').data('height');
		$('.withdrawal-modal-body #withdrawBankList').animate({height : heightPx+'px'});
	});

});

/*$(window).on("load", function() {
  $(".preloader").addClass("preloader-out");
});*/

/* Player Center Navigation Menu */
$('.navigation-menu li a,.security-verifications a,.member-center-deposit-btn,.privilege-icon a,.vip-status a').click(function (e) {
	e.preventDefault();
	//get selected href
	var href = $(this).attr('href');
	var hashIndex = href.indexOf('#');
	var tabName = ''; // contains preceding '#', e.g. "#security". So it also works as a css selector

	if(hashIndex > 0) {
		tabName = href.substr(hashIndex);
	} else {
		tabName = href;
	}

	//set all nav tabs to inactive
	$('.navigation-menu li').removeClass('active');


	if(href) {
		$.get('setDashboardSideBarSession/'+tabName.substr(1), function(data){});

		//get all nav tabs matching the href and set to active
		$('.navigation-menu li a[href="'+href+'"]').closest('li').addClass('active');

		//active tab
		$('.main-content').removeClass('active');
		$('.main-content'+tabName).addClass('active');

		if ($(window).width() < 769) {
			$(tabName).css('left','-'+$(window).width()+'px');
			var left = $(tabName).offset().left;
			$(tabName).css({left:left}).animate({"left":"0px"}, "10");
			var $anchor = $(tabName).offset();
			$('body').animate({ scrollTop: $anchor.top - 150});
		}
	}

});

$(function(){
	// add new bank account
	$(".add-new-bank-account-btn").click(function(e){
		//remove navigation tab active state
		$('.cashier-center-tabs li').removeClass('active');
		//set navigation tab to active
		$('.cashier-center-tabs li a[href="#fm-bank-info"]').closest('li').addClass('active');
		//set active tab
		$('#fm-deposit').removeClass('active');
		$("#fm-bank-info").addClass('active');
	});

	$(".recently-played").click(function(){

		var favoriteButton = $(this);

		favoriteButton.prop('disabled', true);

		$.getJSON('/player_center/favorite', favoriteButton.data(), function (data) {

			if (data.success) {

				var classname ;

				if (data.message == 'added') {
					classname  = 'glyphicon-star';
				} else if (data.message == 'removed') {
					classname  = 'glyphicon-star-empty';
				} else {
					alert(data.message);
					return;
				}

				favoriteButton.removeClass('glyphicon-star glyphicon-star-empty').addClass(classname);

				$("#favourites").load('/player_center/dashboard #favourites .row');

			}

			favoriteButton.prop('disabled', false);

		});
	});

	// $("#favourites").on('click', '.favorite-game', function(){
	// 	if (confirm(public_lang['confirm.delete'])) {
	// 		var button = $(this);
	// 		var url = button.data('url');
	// 		$.getJSON('/player_center/remove_from_favorites', {url:url}, function() {
	// 		  button.parents('.col-sm-4').remove();
	// 		});
	// 	}
	// });
});

function openNav() {
		document.getElementById("overlay").style.width = "100%";
}

function closeNav() {
		document.getElementById("overlay").style.width = "0%";
}

$(document).on("click",".img_thumbnail",function() {
		$src = $(this).find("#player_document_img").attr("src");
		$(".overlay").find(".img_container").find("img").attr("src",$src);
		openNav();
});


//=====_pubutils============================
if(typeof _pubutils == "undefined"){
  _pubutils={
    variables: {
      debugLog: true
    },
    safelog: function(msg){
        //check exists console.log
        if(variables.debugLog && typeof(console)!='undefined' && console.log){
            console.log(msg);
        }
    }
  };
}

_pubutils._init_lock_page=function(){
    if($('#_lock_screen').length<=0){
        $('body').append('<div style="display: none" id="_lock_screen"></div>');
    }
}

_pubutils._lock_page=function(msg){
    _pubutils._init_lock_page();
    $('#_lock_screen').addClass('_overlay_screen').html(msg).fadeTo(0, 0.4).css('display', 'flex');
}

_pubutils._unlock_page=function(){
    _pubutils._init_lock_page();
    $('#_lock_screen').removeClass('_overlay_screen').html('').css('display', 'none');
}

_pubutils._switchPlayerCurrencyOnLogged=function(key){
    var url= '/iframe/auth/change_active_currency_for_logged/'+key+'/false';

    _pubutils._lock_page(lang('Changing Currency'));
    $.ajax(
    {
        url: url,
        cache: false,
        dataType: 'json',
        success: function(data){
            if(data && data['success']){
                // changing_currency=false;
                window.location.reload();
            }else{
                alert(lang('Change Currency Failed'));
                _pubutils._unlock_page();
            }
        },
        error: function(){
            alert(lang('Change Currency Failed'));
            _pubutils._unlock_page();
        }
    }
    );
};

_pubutils._switchPlayerCurrencyOnLogin=function(key){
    var url= '/iframe/auth/change_active_currency/false?__OG_TARGET_DB='+key;

    _pubutils._lock_page(lang('Changing Currency'));
    $.ajax(
    {
        url: url,
        cache: false,
        dataType: 'json',
        success: function(data){
            if(data && data['success']){
                // changing_currency=false;
                window.location.reload();
            }else{
                alert(lang('Change Currency Failed'));
                _pubutils._unlock_page();
            }
        },
        error: function(){
            alert(lang('Change Currency Failed'));
            _pubutils._unlock_page();
        }
    }
    );
};

_pubutils.init=function(){
    $('._select_currecny_on_login').change(function(){
        var key=$(this).val();
        _pubutils._switchPlayerCurrencyOnLogin(key);
    });

    $('._select_currecny_on_logged').change(function(){
        var key=$(this).val();
        _pubutils._switchPlayerCurrencyOnLogged(key);
    });
};

$(function(){
	_pubutils.init();
});
//=====_pubutils============================
