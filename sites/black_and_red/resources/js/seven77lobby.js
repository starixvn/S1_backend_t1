var SevenLobby = {

	getGameTypes: function(){

		var self = this;

		$.post(host + '/game_description/gameTypes/70', function(data) {
           	
           	var tabs = $('ul[id="game_type"]')
           		content = $('div[class="tab-content"]'),
           		gameTypes = data.list,
           		ctr = 1;

           	$.each(gameTypes, function(idx, val){

           		var active_tag = '';

           		if( ctr <= 1 ){
           			 active_tag = 'class="active"';
           			 self.getData(val.id);
           		}

           		var li = '<li ' + active_tag + '>';
           			li += '<a data-toggle="tab" href="#' + val.game_type_lang + '" id="btn_' + val.game_type_lang + '" data-game_type_id="' + val.id + '">' + val.game_type + '</a>';
           			li += '</li>';

				tabs.append(li);

				ctr++;

           	});

           	self.tabEvent();

        });

	},

	tabEvent: function(){

		var self = this;

		$('a[id^="btn_"]').on('click', function(){

			var gameTypeID = $(this).data('game_type_id');
			self.getData(gameTypeID);

		});

	},

	getData: function( gameType ){

		var self = this;

		$('#game-list').html('Processing...');

		$.getJSON(host + '/game_description/allGames/70/' + gameType, function(data) {
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list').html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });

	}

}