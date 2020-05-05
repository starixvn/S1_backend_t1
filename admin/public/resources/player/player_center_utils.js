
_player_center_utils={};

_player_center_utils.initRefreshSaleOrder=function ($, orderId){
	if(orderId){
		setTimeout( function(){
			_player_center_utils.refreshSaleOrder($, orderId)
		}, 30000);
	}
}

_player_center_utils.refreshSaleOrder=function ($, orderId){
	//call
	$.post('/pub/query_order_status/'+orderId, function(data){
		if(data['success']){
			//check status
			if(data['order_status']=='settled'){
				//stop
				window.location.href=data['return_url'];
				return;
			}
			_player_center_utils.initRefreshSaleOrder($, orderId);
		}else{
			window.location.href='/';
		}

	}, 'json').fail(function(){
		_player_center_utils.initRefreshSaleOrder($, orderId);
	});

}
