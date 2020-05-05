//iframe player js
define(['jquery','utils', 'variables','underscore','popup','web_push', 'call_api'],function($, utils, variables, _ , popup, webPush, callApi){

	var enterPTGame=function(params){
		var api_play_pt=variables.apiPlayPT;//  params['api_play_pt'];
		var gameCode=params['gameCode'];
		var lang=params['lang'];
		$.getScript(variables.assetBaseUrl+'/integrationpt.js', function(data, textStatus, jqxhr){
			utils.safelog('loaded pt js');
			// utils.safelog(data);
			iapiSetCallout('Login', function(response){
				if (response['errorCode']) {
					utils.safelog(response);
					if(response['errorCode']==48){
						utils.buildErrorMessage('login_pt_wrong_region');
					}else{
						utils.buildErrorMessage('login_pt_failed');// = 'Login failed. ' + response.playerMessage;
					}
				} else {
					// self.ptLogged=true;
					utils.safelog('logged pt');
					utils.buildErrorMessage('login_successfully');// = 'Login failed. ' + response.playerMessage;

					window.location = api_play_pt +"?language="+lang+"&affiliates=1&nolobby=1&game="+gameCode;
				}
			});

			utils.getJSONP(utils.getApiUrl('get_user_info'), null, function(data){
				if(data && data['key'] && data['secret']){
					iapiLogin(data['key'].toUpperCase(), data['secret'], '1', data['lang']);
				}
			}, function(){
				//get error
				utils.buildErrorMessage('login_pt_failed');
			});

		});

	};

	var init=function(){
		var self=this;
		//init pt games
		$('[data-autostart]').on('click',function(e){
			//call api
			var funcName=$(this).data('autostart');
			var params=$(this).data('autostartParams');
			self[funcName](params);
		});

		if(window['autostart']){
			var autostart=window['autostart'];
			$.each(autostart,function(k,v){
				utils.safelog(k);
				utils.safelog(v);
				self[k](v);
			});
		}

		// $("[data-autostart]").delay(100).trigger('click');
	};

	return {
		init: init,
		enterPTGame: enterPTGame
	};

});