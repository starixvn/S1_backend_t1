var Transactions = {

	transctionURL: '',
	loadedList: [],
	dateRangeValueStart: '',
	dateRangeValueEnd: '',

	init: function(){

		var self = this;

		self.pageEvent();

	},

	pageEvent: function(){

		var self = this;

		$('.menu_jyclass').click(function(){
            var vr=$(this).attr('vr');
            $(".table_main").hide();
            $("#main_"+vr).fadeIn();
            $('.menu_jyclass').removeClass('jy_cur');
            $(this).addClass('jy_cur');

            var trans_type = $(this).data('trans_type');
            var trans_type_value = $(this).data('trans_type_value');
            var url = ( $(this).data('target_url') != undefined ) ? $(this).data('target_url') : '';

            if( $.inArray(trans_type, self.loadedList) > -1 ) return false;

            self.getList(trans_type, trans_type_value, url);

        });

        $('body').on('click', "#transaction_deposit ul li a", function(e){
			e.preventDefault();
			$('.loading').removeClass('hide');
			self.getList('deposit', $('li[vr="deposit"]').data('trans_type_value'), $(this).attr('href'));
		});

        $('body').on('click', "#transaction_withdrawal ul li a", function(e){
			e.preventDefault();
			$('.loading').removeClass('hide');
			self.getList('withdrawal', $('li[vr="withdrawal"]').data('trans_type_value'), $(this).attr('href'));
		});

        $('body').on('click', "#transaction_transfer ul li a", function(e){
			e.preventDefault();
			$('.loading').removeClass('hide');
			self.getList('transfer', $('li[vr="transfer"]').data('trans_type_value'), $(this).attr('href'));
		});

		$('body').on('click', "#transaction_game ul li a", function(e){
			e.preventDefault();
			$('.loading').removeClass('hide');
			self.getList('game', '', $(this).attr('href'));
		});

		$('body').on('click', "#transaction_deposit_list ul li a", function(e){
			e.preventDefault();
			$('.loading').removeClass('hide');
			self.getList('deposit_list', '', $(this).attr('href'));
		});

		$('body').on('click', "#btn-submit", function(e){
			e.preventDefault();
			$('.loading').removeClass('hide');

			var trans_type = $(this).data('trans_type');

			var targetURL = '';

			if( $(this).data('target_url') != undefined ) targetURL = $(this).data('target_url');

			self.getList(trans_type, $('li[vr="' + trans_type + '"]').data('trans_type_value'), targetURL);
		});

	},

	getList: function(transaction_type, transaction_type_value, url, targetContainer){

		var self = this;

		// console.log(transaction_type);
		// console.log(transaction_type_value);
		// console.log(url);

		var targetURL = self.transctionURL;
		if( url != "undefined" && url != undefined && url != "" ) targetURL = url;

		self.loadedList.push(transaction_type);

		if( targetContainer == undefined ) {
			Loading.show('tbl_' + transaction_type);
		}else{
			$('#loading').removeClass('hide');
		}

		var dateRangeValueStart = $('#' + transaction_type + '_date_start').val(),
			dateRangeValueEnd = $('#' + transaction_type + '_date_end').val();

		if( dateRangeValueStart == undefined && dateRangeValueEnd == undefined ){
			dateRangeValueStart = self.dateRangeValueStart,
			dateRangeValueEnd = self.dateRangeValueEnd;
		}

		var by_game_platform_id = '';
		if( $('select[name="by_game_platform_id"]').val() != undefined || $('select[name="by_game_platform_id"]').val() != "undefined" ){

			by_game_platform_id = $('select[name="by_game_platform_id"]').val();

		}

        // console.log("Transaction Type: " + transaction_type);
        // console.log("Transaction Type Value: " + transaction_type_value);
        // console.log("Template: " + transaction_type);
        // console.log("Date Range Value Start: " + dateRangeValueStart);
        // console.log("Date Range Value End: " + dateRangeValueEnd);
        // console.log("By Game Platform ID: " + by_game_platform_id);
        // console.log("Target Container: " + targetContainer);
        // console.log("Target URL: " + targetURL);

		$.ajax({
			url: targetURL,
			type: 'POST',
			data: {
				trans_type: transaction_type,
				trans_type_value: transaction_type_value,
				template: transaction_type,
				dateRangeValueStart: dateRangeValueStart,
				dateRangeValueEnd: dateRangeValueEnd,
				by_game_platform_id: by_game_platform_id
			},
			success: function(data){
				if( targetContainer != undefined ){
					targetContainer.html(data);
					$('#loading').addClass('hide');
				}else{
					$('.main_' + transaction_type).html(data);
				}

			}
		});

	},

}