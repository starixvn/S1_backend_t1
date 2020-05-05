//utils: safe log, check console.log existed
var utils = window.t1t_utils = (function(){
	var registerMessageEvent=function(act, callback){
		this.messageCallbacks[act]=callback;
	};

	var postMessage=function(msg,target_url, target){
		$.postMessage(msg,target_url, target);
	};

	var devicePostMessage = function(action, data){
		if(typeof navigator !== "object"){
			this.safelog('Device not exists navigator object.');
			return false;
		}

        var ua = navigator.userAgent.toLowerCase();

		var json = {
			"act": action,
			"data": data
		};

		var json_str = JSON.stringify(json);

        if (/iphone|ipad|ipod/.test(ua)) {
            if(typeof webkit !== "object"){
                this.safelog('Device not exists webkit object.');
                return false;
            }

            try {
                window.webkit.messageHandlers.share.postMessage(json_str);
			}catch(e){
                this.safelog('Exception: window.webkit.messageHandlers.share.postMessage():' + e.message);

                return false;
			}
        } else if (/android/.test(ua)) {
            if(typeof android !== "object"){
                this.safelog('Device not exists android object.');
                return false;
            }

            try {
                window.android.postMessage(json_str);
            }catch(e){
                this.safelog('Exception: window.android.postMessage():' + e.message);

                return false;
            }
        } else {
        	// @TODO PC?
            this.safelog('Device postMessage', json);
		}
    };

	// @TODO Not tested yet
	var deviceReceiveMessage = function(json_str){
		var json = null;
		try {
			json = JSON.parse(json_str);
		}catch(e){
			json = null;
		}
	};

	var buildTemplate=function(containerSel,templateStr, vars){
		var container=$(containerSel);
		if(container.length){
			container.html(this.buildTemplateStr(templateStr,vars));
		}
	};

	var buildTemplateStr=function(templateStr, vars){
		var tmpl=_.template(templateStr);
		return tmpl(vars);
	};

	var onlyHost=function(){
		var host=variables.host;
		if(host){
			var idx=host.indexOf(':');
			if(idx>=0){
				var parser = document.createElement('a');
				parser.href =host;
				host=parser.hostname;
				// host=host.substr(0,idx);
			}
		}

		return host;
	};

    var getHosts = function(){
        var current_host = window.location.host;
        var segments = current_host.split('.');
        var subdomain = segments.shift();
        var main_domain = segments.join('.');

        return {
            "www": "www." + main_domain,
            "m": "m." + main_domain,
            "player": "player." + main_domain,
            "aff": "aff." + main_domain,
            "agency": "agency." + main_domain
        };
    };

    var getHost = function(subdomain){
        var current_host = window.location.host;
        var segments = current_host.split('.');
        segments.shift();
        var main_domain = segments.join('.');

        return subdomain + '.' + main_domain;
    };

	var getSystemHost = function(subdomain){
		return (variables.hosts.hasOwnProperty(subdomain)) ? variables.hosts[subdomain] : null;
	};

	var getSystemUrl = function(subdomain, url){
		return ((variables.urls.hasOwnProperty(subdomain)) ? variables.urls[subdomain] : null) + '/' + ((!!url) ? url.replace(/(^\/?)/, '') : '');
	};

	var getAssetUrl=function(uri){
		var url = variables.assetBaseUrl+'/'+uri;
        url = url + ((url.indexOf('?') >= 0) ? '&v=' : '?v=') + variables.cms_version;

		return url;
	};

	var getPlayerCmsUrl = function(uri){
        var url = uri + ((uri.indexOf('?') >= 0) ? '&v=' : '?v=') + variables.cms_version;
        url = getSystemUrl('player', url);

        return url;
    };

	var getApiUrl=function(uri){
		return variables.apiBaseUrl+'/'+uri;
	};

	var getIframeUrl=function(uri){
		if(uri.substr(0,7)=='http://' || uri.substr(0,8)=='https://'){
			return uri;
		}
		if(uri.substr(0,1)!='/'){
			uri='/'+uri;
		}
		return '//'+variables.host+uri;
	};

	var convertMsgIdToString=function(msgId){
		return variables.msgCode[msgId] ? variables.msgCode[msgId] : variables.defaultErrorMessage;
	};

	var buildPushMessage=function(msg, title){
		var notify_opts = {};
        if(!!msg){
            notify_opts['message'] = msg;
        }
        if(!!title){
            notify_opts['title'] = title;
        }

		$.notify(notify_opts, {
            "placement": {
                "from": "bottom",
                "align": "right"
            },
            "delay": 5000,
            "timer": 1000
		});
	};

	var buildErrorMessage=function(msgId){
        msg = this.convertMsgIdToString(msgId);
        $.notify({
            "message": msg
        }, {
        	"type": "danger",
            "placement": {
                "from": "bottom",
                "align": "right"
            },
            "delay": 5000,
            "timer": 1000
        });
	};

	var generateId=function(){
		return  _.uniqueId('_og_id_');
	};

	var buildEmptyIframe= function(id){
		if(!id){
			id= this.generateId();
		}
		return $('<iframe id="'+id+'" width="0" height="0" border="0" style="display:none;border:0px;width:0px;height:0px;"></iframe>');
	};

	var clearIframe= function(iframeId){
		$('#'+iframeId).attr('src','empty.html');
	};

	var init=function(){
		//init message
		var self=this;
		var origin= variables.origin=='*' || variables.origin=='' ? null : variables.origin;
		$.receiveMessage(function(e){
			if(e && e.data){
				try{
				    var json_string = e.data;
                    // preserve newlines, etc - use valid JSON
                    json_string = json_string.replace(/\\n/g, "\\n")
                        .replace(/\\'/g, "\\'")
                        .replace(/\\"/g, '\\"')
                        .replace(/\\&/g, "\\&")
                        .replace(/\\r/g, "\\r")
                        .replace(/\\t/g, "\\t")
                        .replace(/\\b/g, "\\b")
                        .replace(/\\f/g, "\\f");
                    // remove non-printable and other non-valid JSON chars
                    json_strings = json_string.replace(/[\u0000-\u0019]+/g,"");

                    var jsonData=$.parseJSON(json_string);
                    if(jsonData && (typeof(jsonData['success']) != 'undefined' || typeof(jsonData['status']) != 'undefined') && typeof(jsonData['act']) != 'undefined'){
                        self.messageCallbacks[jsonData.act](jsonData);
                    }
				}catch(ex){
					self.safelog(e);
					self.safelog("Sorry, something went wrong. please check the message: ", ex.message, ex.stack);
				}
			}
		}
		,origin);

		if(variables.adjust_domain_to_wwww){
			this.checkAndGoWWW($);
		}

		this.checkAndFixHttpOnly($);
		this.checkAndFixPlayerUrl($);

		// this.isInActiveWindow();

	};

	var safelog=function(msg){
		//check exists console.log
		if(variables.debugLog && typeof(console)!='undefined' && !_.isUndefined(console) && _.isFunction(console.log)){
			console.log.apply(console, Array.prototype.slice.call(arguments));
		}
	};

	var getJSONP=function(url,data, success, error){
		$.ajax({
			url: url,
			type: 'GET',
			data: data,
			dataType: 'jsonp',
			cache: false,
            success: success,
            error: error
		});
	};

	var getJSON=function(url,data, success, error){
		this.callJSON(url,'GET',data,success,error);
	};

	var postJSON=function(url,data, success, error){
		this.callJSON(url,'POST',data,success,error);
	};

	var callJSON=function(url,type,data, success, error){
		$.ajax({
			url: url,
			type: type,
			data: data,
			dataType: 'json',
			cache: false,
			xhrFields: {
			    withCredentials: true
			},
			success:success,
			error: error
		});
	};

    var getJSONWithIframe = function(url, data, success, error){
        this.callJSONWithIframe(url, 'GET', data, success, error);
    };

    var postJSONWithIframe = function(url, data, success, error){
        this.callJSONWithIframe(url, 'POST', data, success, error);
    };

    var callJSONWithIframe = function(url, method, data, success, error){
        var cross_domain_iframe = this.crossDomainForm(null, null, null, success, error);

        var form = $('<form>');
        form.appendTo(cross_domain_iframe.iframe_container);
        form.attr('method', method);
        form.attr('target', cross_domain_iframe.iframe_id);
        form.attr('action', url);

        $.each(data, function(key, value){
            $('<input type="hidden">').attr('name', key).attr('value', value).appendTo(form);
        });

        $('<input type="hidden">').attr('name', 'callback').attr('value', cross_domain_iframe.iframe_callback).appendTo(form);
        $('<input type="hidden">').attr('name', 'act').attr('value', cross_domain_iframe.iframe_callback_name).appendTo(form);

        form.submit();
    };

    var crossDomainForm = function(callIframe_container_id, iframe_id, iframe_callback_name, success, error){
        callIframe_container_id = (!!callIframe_container_id) ? callIframe_container_id : 'callIframe_container_' + Math.random().toString(36).substring(2);
        iframe_id = (!!iframe_id) ? iframe_id : 'callIframe_iframe_' + Math.random().toString(36).substring(2);
        iframe_callback_name = (!!iframe_callback_name) ? iframe_callback_name : 'callIframe_callback_' + Math.random().toString(36).substring(2);

        var callIframe_container = $('<div>').hide();
        callIframe_container.appendTo($('body'));
        callIframe_container.attr('id', callIframe_container_id);

        var iframe = $('<iframe>');
        iframe.appendTo(callIframe_container);
        iframe.attr('id', iframe_id);
        iframe.attr('name', iframe_id);
        iframe[0].contentWindow.name = iframe_id;
        iframe.attr('src', 'javascript: void(0);');

        var iframe_callback_data = null;

        iframe.on('load', function(){
            // Prevent Internal Server Error
            setTimeout(function(){
                if(!iframe_callback_data){
                    if(typeof error === "function") error();
                }
            }, 3000);
        });

        this.registerMessageEvent(iframe_callback_name, function(jsonData){
            iframe_callback_data = jsonData;

            if(typeof success === "function") success(jsonData);

            callIframe_container.remove();
            if(utils.messageCallbacks.hasOwnProperty(iframe_callback_name)){
                delete utils.messageCallbacks[iframe_callback_name];
            }
        });

        return {
            "iframe_container": callIframe_container,
            "iframe_instance": iframe,
            "iframe_id": iframe_id,
            "iframe_callback": "iframe_callback",
            "iframe_callback_name": iframe_callback_name
        };
    };

	var formatCurrency = function (value, add_currency_symbol, enabled_thousands, enabled_decimal, precision, precision_method){
        add_currency_symbol = (add_currency_symbol !== undefined) ? add_currency_symbol : true;
        enabled_thousands = (enabled_thousands !== undefined) ? enabled_thousands : true;
        enabled_decimal = (enabled_decimal !== undefined) ? enabled_decimal : true;
        precision = (precision !== undefined) ? precision : 2;
        precision_method = (precision_method !== undefined) ? precision_method : 'floor';
        // console.log(value, add_currency_symbol, enabled_thousands, enabled_decimal, precision, precision_method);

        if(enabled_decimal && precision > 0){
            switch(precision_method){
                case 'ceil':
                    value = (function(value, precision){
                        precision = Math.pow(10, precision);
                        return Math.ceil(value * precision) / precision;
                    })(value, precision);
                    break;
                case 'floor':
                default:
                    value = (function(value, precision){
                        precision = Math.pow(10, precision);
                        return Math.floor(value * precision) / precision;
                    })(value, precision);
                    break;
            }
        }else{
            value = parseInt(value);
        }

        var arr = value.toString().split(".");
        var digital = (arr.length) ? arr[0] : 0;

        if(enabled_thousands){
            digital = digital.replace(/(\d{1,3})(?=(\d{3})+$)/g,"$1,");
        }

        value = digital + ((arr.length === 2) ? '.' + arr[1] : '');

        return (add_currency_symbol) ? variables.currency.symbol + ' ' + value : value;
	};

    var getCurrencyLabel = function(_options){
        var display_options = $.extend(true, variables.currency_display_options, (typeof _options === "object") ? _options : {});

        var currency = {};
        currency['currency_name'] = (display_options['display_currency_name']) ? '<span class="currency_name">' + variables.currency.currency_name + '</span>' : '';
        currency['currency_short_name'] = (display_options['display_currency_short_name']) ? '<span class="currency_short_name">' + variables.currency.currency_short_name + '</span>' : '';
        currency['currency_code'] = (display_options['display_currency_code']) ? '<span class="currency_code">' + variables.currency.currency_code + '</span>' : '';
        currency['currency_symbol'] = (display_options['display_currency_symbol']) ? '<span class="currency_symbol">' + variables.currency.symbol + '</span>' : '';

        return currency;
    };

    var displayCurrencyLabel = function(_options){
        var currency_display_order = variables.currency_display_order;

        var currency = utils.getCurrencyLabel(_options);

        var html = '<span class="t1t_currency">';
        currency_display_order.forEach(function(field_type){
            if(currency.hasOwnProperty(field_type)){
                html = html + currency[field_type];
            }
        });
        html = html + '</span>';

        return html;
    };

	var displayCurrency = function(_number, _options){
		var currency_display_order = variables.currency_display_order;

        var currency = utils.getCurrencyLabel(_options);

		var num = parseFloat(_number.toString().replace(/,/g, ''));
		num = isNaN(num) ? 0 : num;
        num = num.toFixed(variables.currency.currency_decimals).replace(/./g, function (c, i, a) {
            return i && c !== variables.currency.currency_dec_point && ((a.length - i) % 3 === 0) ? variables.currency.currency_thousands_sep + c : c;
        });
        currency['currency_number'] = '<span class="currency_number">' + num + '</span>';

		var html = '<span class="t1t_currency">';
        currency_display_order.forEach(function(field_type, index){
            if(currency.hasOwnProperty(field_type)){
                html = html + currency[field_type];
            }
		});
        html = html + '</span>';

		return html;
	};

    var getParam= function($, val) {
        var result = "",
            tmp = [];
        var items = location.search.substr(1).split("&");
        for (var index = 0; index < items.length; index++) {
            tmp = items[index].split("=");
            if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
        }
        return result;
    };

    var getAffCodeFromParam= function($){
        var code=this.getParam($, 'code');
        if(code==''){
            code=this.getParam($, 'aid');
            if(code==''){
                code=this.getParam($, 'aff');
            }
        }
        return code;
    };

    var getAffCode= function($){

        var currentHost = window.location.host;
        var arr=currentHost.split('.');
        var code='';

		if(arr[0]!='www' && arr[0]!='player' && arr[0]!='aff' && arr[0]!='m'){
            code=arr[0];
        }else{
            code=this.getAffCodeFromParam($);
        }

        return code;

    };

    var goToMobile= function($){

    	//show html part
    	if(typeof _show_goto_mobile_tip =='function'){
    		_show_goto_mobile_tip();
    	}

    	//go to domain
        var currentHost = window.location.host;
        var arr=currentHost.split('.');
        var currentPath = window.location.pathname;
        var code='';

        if(arr[0]=='m'){
            return;
        }

        code=this.getAffCode($);

        arr[0]='m';

        var url= window.location.protocol+'//'+arr.join('.')+currentPath;
        if(code!=''){
            url=url+'?code='+code;
        }

        window.location.href=url;

    };

    var getCookie= function($,c_name){
		if (document.cookie.length > 0) {
			c_start = document.cookie.indexOf(c_name + "=");
			if (c_start != -1) {
				c_start = c_start + c_name.length + 1;
				c_end = document.cookie.indexOf(";", c_start);
				if (c_end == -1) {
					c_end = document.cookie.length;
				}
				return unescape(document.cookie.substring(c_start, c_end));
			}
		}
		return "";
    };

    var checkAndGoMobile= function($, auto){
    	if( this.getCookie($ , "stay_on_desktop") == 1 ) {
    		//console.log("display to desktop version");
    		return;
    	} else {
	        var currentHost = window.location.host;
	        var arr=currentHost.split('.');
	        if(arr[0]=='m' || arr[0]=='player' || arr[0]=='pay' || arr[0]=='pay2' || arr[0]=='aff' || arr[0]=='agency'){
	        	//console.log("pass m host");
	            return;
	        }

	        var goM = false;

	        //判断是否是手机访问
	        var thisOS=navigator.platform;
	        var os=new Array("iPhone","iPod","iPad","Android","android","Nokia","SymbianOS","Symbian","Windows Phone","Phone","Linux armv71","MAUI","UNTRUSTED/1.0","Windows CE","BlackBerry","IEMobile");
	        for(var i=0;i<os.length;i++) {
	            if(thisOS.match(os[i])) {
	            	//console.log("pass OS mobile");
	            	goM= true;
	            }
	        }

	        if(navigator.platform.indexOf('iPad') != -1 || navigator.userAgent.indexOf('Android') != -1) {
	        	// console.log("pass iPad Android");
	            goM= true;
	        }

	        var check = navigator.appVersion;
	        if(check.match(/Mac/i)) {
	            if(check.match(/mobile/i) ||check.match(/Mobile/i) || check.match(/X11/i)) {
	            	//console.log("pass Mac");
	            	goM= true;
	            }
	        }

	        /*if (goM == true) {
	            if(auto || confirm(variables.langText['confirm_go_to_mobile']+"?")){
	            	// console.log("display to mobile version");
	            	this.goToMobile($);
	            }
	        }*/

	        if (goM == true) {
	            if(auto){
	            	// console.log("display to mobile version");
	            	this.goToMobile($);
	            }
	        }

	        //console.log("Failed to validate mobile version");
	        return;
	    }

    };

    var checkAndGoMobileDir= function($, auto){
        var currentHost = window.location.host;
        var arr=currentHost.split('.');
        if(arr[0]=='player' || arr[0]=='pay'){
        	//console.log("pass m host");
            return;
        }

    	//redirect to /m
    	var currentPath=window.location.pathname;
        var arr=currentPath.split('/');
        if(arr.length>0){
        	for (var i = 0; i < arr.length; i++) {
        		//only check first non-empty
        		if(arr[i]!=''){
			        if(arr[0]=='m'){
        				return;
        			}
        			break;
        		}
        	}
        }
        // var currentHost = window.location.host;
        // var hostArr=currentHost.split('.');
        // if(hostArr[0]=='m'){
        //     return;
        // }

        var goM = false;

        //判断是否是手机访问
        var thisOS=navigator.platform;
        var os=new Array("iPhone","iPod","iPad","Android","android","Nokia","SymbianOS","Symbian","Windows Phone","Phone","Linux armv71","MAUI","UNTRUSTED/1.0","Windows CE","BlackBerry","IEMobile");
        for(var i=0;i<os.length;i++) {
            if(thisOS.match(os[i])) {
            	goM= true;
            }
        }

        if(navigator.platform.indexOf('iPad') != -1 || navigator.userAgent.indexOf('Android') != -1) {
            goM= true;
        }

        var check = navigator.appVersion;
        if(check.match(/Mac/i)) {
            if(check.match(/mobile/i) ||check.match(/Mobile/i) || check.match(/X11/i)) {
            	goM= true;
            }
        }

        // this.safelog('goM:'+(goM ? 'true' : 'false'));
        if (goM == true){
	        var code='';
	        code=this.getAffCode($);

	        var currentHost = window.location.host;
	        var arr=currentHost.split('.');

	        if(arr[0]=='m'){
	        	arr[0]='www';
	        }

	        var url= window.location.protocol+'//'+arr.join('.')+'/m';
	        if(code!=''){
	            url=url+'?code='+code;
	        }

	        this.safelog('redirect to '+url);

            if(auto || confirm(variables.langText['confirm_go_to_mobile']+"?")){
            	// this.goToMobile($);
		        // var currentHost = window.location.host;
		        // var arr=currentHost.split('.');

		        window.location.href=url;

            }
        }

        return;
    };

    var checkBlockStatus= function(){

		this.safelog('blocked: '+variables.block_status+', url:'+variables.block_page_url);
		if(variables.block_status) {
			if(variables.block_page_url) {
				window.location = variables.block_page_url;
			} else {
				// add redirect page if redirect_block_status is empty ?
				window.location = '/';
			}
		}

    };

    var matchAnyReg=function(str, regList, $){
    	var found=false;
    	for (var i = 0; i < regList.length; i++) {
    		var r=new RegExp(regList[i]);

    		// this.safelog(regList[i]+" "+str);

    		if(r.test(str)){
    			found=true;
    			break;
    		}
    	}

    	// this.safelog(found);

    	return found;

    };

    var inArray=function(str, arr){
    	for (var i = 0; i < arr.length; i++) {
    		if(arr[i]==str){
    			return true;
    		}
    	}

    	return false;
    };

    var checkAndGoHttps= function($){
    	//check https
    	//only in domain list
    	if(window.location.protocol=='http:' &&
    			this.inArray(window.location.hostname, variables.auto_redirect_to_https_list) ){
    		var url=window.location.href;
    		url='https'+url.substr(4, url.length);
    		//redirect to https
    		window.location.href=url;
    	}
    };

    var checkAndFixHttpOnly= function($){
        var player_url = "http://" + window.location.hostname.replace('www', 'player');

		$(".http_only").each(function(idx){
            var link = $(this).attr('href');
            if(!link || link == ''){
                link = $(this).attr('src');
            }

            if(link && link != '' && link.substr(0, 4) != 'http'){
                if(link.substr(0, 1) != '/'){
                    link = '/' + link;
                }
                link = player_url + link;
                $(this).attr('href', link);
            }
		});
    };

    var checkAndFixPlayerUrl= function($){
		var player_url= window.location.protocol+"//"+window.location.hostname.replace('www', 'player');

		$("a").each(function(idx){
			var link=$(this).attr('href');

			if(link && link!='' && link.substr(0,4)!='http'){
				if(link.substr(0,1)!='/'){
					link='/'+link;
				}
				//start from /iframe_module or /player_center
				if(link.substr(0,14)=='/iframe_module' || link.substr(0,14)=='/player_center'){
					//only adjust /iframe_module and /player_center
					link=player_url+link;
					$(this).attr('href', link);
				}
			}
		});
    };

    var checkAndGoWWW=function($){

        var currentHost = window.location.host;
        var arr=currentHost.split('.');

        if(arr[0]!='www' && arr[0]!='m' && arr[0]!='player' && arr[0]!='pay' && arr[0]!='aff' && arr[0]!='agency'){
            //invalid sub-domain
            var url= window.location.protocol+'//www.'+arr.join('.');
            var code=this.getAffCode($);
            if(code!=''){
                url=url+'?code='+code;
            }
            window.location.href=url;

        }

    };

    var inHost=function(host){

        var currentHost = window.location.host;
        var arr=currentHost.split('.');

        return (arr[0] === host);
    };

    window._sbe_window_status = 'visible';

    var isInActiveWindow = function(callback){
        if(typeof callback === 'undefined' || callback === '' || callback === null){
            callback = null;

            return (window._sbe_window_status == 'visible');
        }

        var hidden = "hidden";

        // Standards:
        if (hidden in document)
            document.addEventListener("visibilitychange", onchange);
        else if ((hidden = "mozHidden") in document)
            document.addEventListener("mozvisibilitychange", onchange);
        else if ((hidden = "webkitHidden") in document)
            document.addEventListener("webkitvisibilitychange", onchange);
        else if ((hidden = "msHidden") in document)
            document.addEventListener("msvisibilitychange", onchange);
        // IE 9 and lower:
        else if ("onfocusin" in document)
            document.onfocusin = document.onfocusout = onchange;
        // All others:
        else
            window.onpageshow = window.onpagehide
            = window.onfocus = window.onblur = onchange;

        function onchange (evt) {
            var v = "visible", h = "hidden",
                evtMap = {
                  focus:v, focusin:v, pageshow:v, blur:h, focusout:h, pagehide:h
                };

            evt = evt || window.event;
            if(evt.type in evtMap){
                window._sbe_window_status = evtMap[evt.type];
            }else{
                window._sbe_window_status = this[hidden] ? h : v;
            }

            if(callback !== null){
                callback(window._sbe_window_status);
            }
        }

        // set the initial state (but only if browser supports the Page Visibility API)
        if( document[hidden] !== undefined ){
            onchange({type: document[hidden] ? "blur" : "focus"});
        }
    };

    var addJS = function(url, force_load, callback){
        var embedded_url = url + ((force_load) ? ((url.indexOf('?') >= 0) ? '&v=' : '?v=') + ('' + Math.random()).substr(2, 16) : '');
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript';
        g.async = true;
        g.defer = true;
        g.src = embedded_url;

        if(typeof callback === "function"){
            g.onload = function(){
                callback();
            };
        }

        s.parentNode.insertBefore(g, s);
	};

    var getISO639STwoLetterLanguage = function(lang_code){
    	var lang = null;
    	switch(parseInt(lang_code)){
			case 2:
				lang = 'ch';
				break;
			case 3:
				lang = 'id';
				break;
			case 4:
				lang = 'vn';
				break;
			case 5:
				lang = 'kr';
				break;
			case 6:
				lang = 'th';
				break;
			case 1:
			default:
                lang = 'en';
				break;
		}

		return lang;
	};

    var getCurrentTimeStamp = function(){
    	return Math.round((new Date()).getTime() / 1000);
    };

    var is_mobile = function(){
    	return variables.is_mobile;
	};

    var inIframe = function() {
        try {
            return window.self !== window.top;
        } catch (e) {
            return true;
        }
    };

    var custom_parseFloat = function(string, default_val){
        var val = parseFloat(string);
        return (isNaN(val)) ? ((default_val === undefined) ? 0.0 : undefined) : val;
    };

    var custom_parseInt = function(string, default_val){
        var val = parseInt(string);
        return (isNaN(val)) ? ((default_val === undefined) ? 0 : undefined) : val;
    };

    var strap_tags = function(html){
        return $('<span>').html(html).text();
    };

    /**
     * Filter html tag(s).
     * @param {string} theString
     */
    var strap_match_tags = function(theString){
        var _utils = this;
        var returnString = theString;
        /// Ref. to https://regex101.com/r/fogpPP/4/
        var regex = /<\s*(\S+)\s*\S*>(.*)<\/\s*\1\s*>|<\s*[^>]+\s*[^>]*\/>|<\s*[^>]+\s*[\s=" ]+\/?>/gm;
        while ((m = regex.exec(theString)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === regex.lastIndex) {
                regex.lastIndex++;
            }
            var replacement = '';
            if(typeof(m[2]) !== 'undefined'){
                replacement = m[2];
            }
            returnString = _utils.replaceAll(returnString, m[0], replacement);
        }
        // returnString = theString;
        return returnString;
    };

    /**
     * To replace all occurrences of a string.
     * Ref. to ttps://stackoverflow.com/a/17606289
     *
     * @param {string} target
     * @param {string} search
     * @param {string} replacement
     */
    var replaceAll = function(target, search, replacement) {
        return target.replace(new RegExp(search, 'g'), replacement);
    };

    /*
     * To decode string which have htmlentities() in php
     *
     * @param {string} html
     * @param {string} default_lang
     */
    var decodeHtmlEntities = function(html, default_lang='default') {
        var txt = document.createElement('textarea');
        txt.setAttribute("id", default_lang + "default_temp_area");
        txt.innerHTML = html;
        var decode_html = txt.value;
        txt = undefined;    //reset txt
        return decode_html;
    };

    /**
	 *  @typedef {Object} T1T_Utils
	 */
	var utils = {
        "messageCallbacks": {},
        "safelog": safelog,
        "onlyHost": onlyHost,
        "getHosts": getHosts,
        "getHost": getHost,
        "getSystemHost": getSystemHost,
        "getSystemUrl": getSystemUrl,
        "init": init,
        "buildPushMessage": buildPushMessage,
        "buildErrorMessage": buildErrorMessage,
        "convertMsgIdToString": convertMsgIdToString,
        "registerMessageEvent": registerMessageEvent,
        "postMessage": postMessage,
        "buildTemplate": buildTemplate,
        "buildTemplateStr": buildTemplateStr,
        // "popupMesasge": popupMesasge,
        "callJSON": callJSON,
        "getJSON": getJSON,
        "postJSON": postJSON,
        "getJSONP": getJSONP,
		"getJSONWithIframe": getJSONWithIframe,
		"postJSONWithIframe": postJSONWithIframe,
		"callJSONWithIframe": callJSONWithIframe,
        "crossDomainForm": crossDomainForm,
        "getAssetUrl": getAssetUrl,
        "getPlayerCmsUrl": getPlayerCmsUrl,
        "getApiUrl": getApiUrl,
        "getIframeUrl": getIframeUrl,
        "clearIframe": clearIframe,
        "generateId": generateId,
        "buildEmptyIframe": buildEmptyIframe,
        "formatCurrency": formatCurrency,
        "getCurrencyLabel": getCurrencyLabel,
        "displayCurrencyLabel": displayCurrencyLabel,
		"displayCurrency": displayCurrency,
        "checkAndGoMobile": checkAndGoMobile,
        "goToMobile": goToMobile,
        "checkAndGoMobileDir": checkAndGoMobileDir,
        "getAffCodeFromParam": getAffCodeFromParam,
        "getAffCode": getAffCode,
        "getParam": getParam,
        "checkBlockStatus": checkBlockStatus,
        "checkAndGoWWW": checkAndGoWWW,
        "checkAndGoHttps": checkAndGoHttps,
        "checkAndFixHttpOnly": checkAndFixHttpOnly,
        "checkAndFixPlayerUrl": checkAndFixPlayerUrl,
        "matchAnyReg": matchAnyReg,
        "inArray": inArray,
        "getCookie": getCookie,
        "inHost": inHost,
        "isInActiveWindow": isInActiveWindow,
        "addJS": addJS,
        "getISO639STwoLetterLanguage": getISO639STwoLetterLanguage,
		"getCurrentTimeStamp": getCurrentTimeStamp,
        "is_mobile": is_mobile,
        "inIframe": inIframe,
        "devicePostMessage": devicePostMessage,
        "parseFloat": custom_parseFloat,
        "parseInt": custom_parseInt,
        "strap_tags": strap_tags,
        "strap_match_tags": strap_match_tags,
        "replaceAll": replaceAll,
        "decodeHtmlEntities": decodeHtmlEntities
	};

    smartbackend.on('run.t1t.smartbackend', function(){
        utils.init();
    });

	return utils;
})();
