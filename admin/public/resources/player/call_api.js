//async call api
var callApi = (function(){

	var query_balance=function(params, state){
		// utils.safelog(params);
		params['_state']=state;
		//call async
		utils.safelog('systemId:'+params['systemId']);
		$.post(
			utils.getApiUrl('query_balance/'+variables.role),
			params,
			'json'
		).done(
			function(data){
				utils.safelog(data);
			}
		);
	};

	var init=function(){
		var self=this;

		$(function(){
			//init button by data
			$('[data-callapi]').on('click',function(e){
				//call api
				var funcName=$(this).data('callapi');
				var params=$(this).data('callapiParams');
				var state=$(this).attr('data-callapi-state');
				self[funcName](params, state);
			});

			$("[data-callapi][data-callapi-autostart='true']").delay(1000).trigger('click');

			// utils.safelog(self['query_balance']);
			// self['query_balance'](1);
		});
	};

	return {
		query_balance: query_balance,
		init: init
	};
})();
