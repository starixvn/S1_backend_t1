if (typeof _pubutils == "undefined") {
	_pubutils = {
		variables: {
			debugLog: true
		},
		safelog: function (msg) {
			//check exists console.log
			if (variables.debugLog && typeof (console) != 'undefined' && console.log) {
				console.log(msg);
			}
		},
		decodeHtmlEntities: function(html, default_lang='default') {
			var txt = document.createElement('textarea');
			txt.setAttribute("id", default_lang + "default_temp_area");
			txt.innerHTML = html;
			var decode_html = txt.value;
			txt = undefined;    //reset txt
			return decode_html;
		}
	};
}

_pubutils.addBookmark = function (e) {
	var bookmarkURL = window.location.href;
	var bookmarkTitle = document.title;

	if ('addToHomescreen' in window && window.addToHomescreen.isCompatible) {
		// Mobile browsers
		addToHomescreen({ autostart: false, startDelay: 0 }).show(true);
	} else if (window.sidebar && window.sidebar.addPanel) {
		// Firefox version < 23
		window.sidebar.addPanel(bookmarkTitle, bookmarkURL, '');
	} else if ((window.sidebar && /Firefox/i.test(navigator.userAgent)) || (window.opera && window.print)) {
		// Firefox version >= 23 and Opera Hotlist
		$(this).attr({
			href: bookmarkURL,
			title: bookmarkTitle,
			rel: 'sidebar'
		}).off(e);
		return true;
	} else if (window.external && ('AddFavorite' in window.external)) {
		// IE Favorite
		window.external.AddFavorite(bookmarkURL, bookmarkTitle);
	} else {
		// Other browsers (mainly WebKit - Chrome/Safari)
		alert('Press ' + (/Mac/i.test(navigator.userAgent) ? 'Cmd' : 'Ctrl') + '+D to bookmark this page.');
	}

	return false;
}

_pubutils.notifyErr = function (err) {

	var notify = $.notify({
		// options
		message: err
	}, {
		// settings
		type: 'danger',
		delay: 0,
		allow_dismiss: true,
		showProgressbar: false,
		mouse_over: 'pause'
	});

	return notify;

}

_pubutils.notifyLoading = function (loadingText) {

	var template = '<div data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss" >×</button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0} progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>';

	var notify = $.notify({
		// options
		message: loadingText
	}, {
		// settings
		type: 'warning',
		delay: 0,
		allow_dismiss: true,
		showProgressbar: true,
		mouse_over: 'pause',
		template: template
	});

	return notify;
}

_pubutils.notifySuccess = function (msg) {

	var notify = $.notify({
		// options
		message: msg
	}, {
		// settings
		type: 'success',
		delay: 20000,
		allow_dismiss: true,
		showProgressbar: false,
		mouse_over: 'pause'
	});

	return notify;

}

_pubutils.closeNotify = function (notifyObj) {
	if (notifyObj) {
		notifyObj.close();
	}
}

_pubutils.inArray = function (str, arr) {
	for (var i = 0; i < arr.length; i++) {
		if (arr[i] == str) {
			return true;
		}
	}

	return false;
};

_pubutils.checkAndGoHttps = function (auto_redirect_to_https_list, donot_auto_redirect_to_https_list) {

	//black list
	if (_pubutils.inArray(window.location.hostname, donot_auto_redirect_to_https_list)) {
		return;
	}

	//white list
	//check https
	//only in domain list
	if (window.location.protocol == 'http:' &&
		_pubutils.inArray(window.location.hostname, auto_redirect_to_https_list)) {
		var url = window.location.href;
		url = 'https' + url.substr(4, url.length);
		//redirect to https
		window.location.href = url;
	}

}

_pubutils.lang = [];

_pubutils._init_lock_page = function () {
	if ($('#_lock_screen').length <= 0) {
		$('body').append('<div style="display: none" id="_lock_screen"></div>');
		$('html, body').removeClass('_overlay_screen-lockbody_scroller');
	}
}

_pubutils._lock_page = function (msg) {
	_pubutils._init_lock_page();
	$('#_lock_screen').addClass('_overlay_screen').html(msg).fadeTo(0, 0.4).css('display', 'flex');
	$('html, body').addClass('_overlay_screen-lockbody_scroller');
}

_pubutils._unlock_page = function () {
	_pubutils._init_lock_page();
	$('#_lock_screen').removeClass('_overlay_screen').html('').css('display', 'none');
}

$(document).ready(function () {

	$('._select_currecny_on_logged').change(function () {
		//call change active db
		var key = $(this).val();
		//lock page
		_pubutils._lock_page(_pubutils.lang['Changing Currency']);
		$.ajax(
			'/auth/change_active_currency_for_logged/' + key,
			{
				dataType: 'json',
				cache: false,
				success: function (data) {
					if (data && data['success']) {
						if (data['redirect_url']) {
							window.location.href = data['redirect_url'];
						} else {
							window.location.reload();
						}
					} else {
						alert(_pubutils.lang['Change Currency Failed']);
						_pubutils._unlock_page();
					}
				},
				error: function () {
					alert(_pubutils.lang['Change Currency Failed']);
					_pubutils._unlock_page();
				}
			}
		).always(function () {
			// _unlock_page();
		});
	});

	$('._select_currecny_on_login').change(function () {
		//call change active db
		var key = $(this).val();
		//lock page
		_pubutils._lock_page(_pubutils.lang['Changing Currency']);
		$.ajax(
			'/auth/change_active_currency?__OG_TARGET_DB=' + key,
			{
				dataType: 'json',
				cache: false,
				success: function (data) {
					if (data && data['success']) {
						window.location.reload();
					} else {
						alert(_pubutils.lang['Change Currency Failed']);
						_pubutils._unlock_page();
					}
				},
				error: function () {
					alert(_pubutils.lang['Change Currency Failed']);
					_pubutils._unlock_page();
				}
			}
		).always(function () {
			// _unlock_page();
		});
	});

})

