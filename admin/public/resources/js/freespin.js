var FreeSpin = {
	
	invalidCoin: '',

	init: function(){

		var self = this;


        $('#start_in_five_min').on('click', function(){

            if( $(this).is(':checked') ){
                $('#start_date').prop('disabled', true);
                $('#start_time').prop('disabled', true);
                return;
            }

            $('#start_date').prop('disabled', false);
            $('#start_time').prop('disabled', false);

        });

        $('#has_end_date').on('click', function(){
            
            if( $(this).is(':checked') ){
                $('#end_date').prop('disabled', false);
                $('#end_time').prop('disabled', false);
                $('#relative_duration').prop('disabled', true);
                return;
            }

            $('#end_date').prop('disabled', true);
            $('#end_time').prop('disabled', true);
            $('#relative_duration').prop('disabled', false);

        });

		$(".chosen-select").chosen({
            disable_search: false,
        });

		$('.chosen-toggle').on('click', function(){
            if( $(this).is(':checked') ){
                $('#games').find('option').prop('selected', $(this).hasClass('select')).parent().trigger('chosen:updated');
            }else{
                $('#games').find('option').prop('selected', false).parent().trigger('chosen:updated');
            }
        });


        $('body').on('change', 'select[id^="linebet_"]', function(){

        	var game_code = $(this).data('game_code');
            var store_lineBet = $('input[name="' + game_code + '_line_bet_stored_value"]').val();

            store_lineBet = store_lineBet.split(',');

            console.log(store_lineBet);

            if( $(this).val() == 'maximum' ){
                $('input[id="line_bet_' + game_code + '"]').val(Math.max.apply(Math, store_lineBet));
                return;
            }

            $('input[id="line_bet_' + game_code + '"]').val(Math.min.apply(Math, store_lineBet));

        });

        $('body').on('change', 'select[id^="coins_"]', function(e){
            e.preventDefault();
            var currency = $(this).val(),
            	game_code = $(this).data('game_code'),
                coin_value = $('input[name="' + game_code + '_' + currency + '_coin_available"]').val();

            $('.lbl_coins_' + game_code).html(coin_value);

        });

        $('body').on('change', '#game_id', function(e){
            e.preventDefault();

            var game_code = $(this).val();

            if( $(this).val() == "" ){
                $('.coin').html('');
                return;
            }

            $.ajax({
                url: base_url + 'api/add_coin',
                type: 'POST',
                data: {
                    games: game_code
                },
                success: function(data){
                    $('.coin').html(data);
                }
            });

        });

        $('body').on('blur', 'input[id^="coin_value_"]', function(e){
            e.preventDefault();
            var value = $(this).val(),
            	gameCode = $(this).data('game_code'),
                currency = $('select[id="coins_' + gameCode + '"]').val(),
                coin_value = $('input[name="' + gameCode + '_' + currency + '_coin_available"]').val();

            if( $('#coin_value_' + gameCode).val() == "" ) return;

            coin_value = coin_value.split(',');

            if( $.inArray(value, coin_value) == -1 ){
                alert(self.invalidCoin);
                $('#coin_value_' + gameCode).val('');
                return;
            }


        });

        $('body').on('click', 'a[id^="add_coin_"]', function(e){
            e.preventDefault();
            
            var gameCode = $(this).data('game_code'),
            	currency = $('#coins_' + gameCode).val(),
                coin_value = $('#coin_value_' + gameCode).val(),
                html = '',
                currency_label = $('.coins_cnt_' + gameCode).find('div').length;

            if( coin_value == "" ) return false;

            html += '<div>';
            html += '<input type="hidden" name="coins[' + gameCode + '][' + currency + '][coin_value]" value="' + coin_value + '">';
            html += '<input type="hidden" name="coins[' + gameCode + '][' + currency + '][currency]" value="' + currency + '">';
            html += '<span class="coin_val">';
            html += coin_value;
            html += '</span>';
            html += '/';
            html += '<span class="currency">';
            html += currency;
            html += '</span>';
            html += '</div>';

            $('#coin_value_' + gameCode).val('');
            $('#coins_' + gameCode + ' option[value="' + currency + '"]').remove();
            $('.coins_cnt_' + gameCode).append(html);

            var currency = $('#coins_' + gameCode).val(),
                coin_value = $('input[name="' + gameCode + '_' + currency + '_coin_available"]').val();

            $('.lbl_coins_' + gameCode).html(coin_value);

        });

	},

}