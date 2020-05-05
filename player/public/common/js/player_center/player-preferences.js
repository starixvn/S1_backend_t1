var PlayerPreferences = {
	site_url : document.location.origin,

	initPlayerPreference: function(){
		var self = this;

		$('input[class^="pref-data-"]').on('switchChange.bootstrapSwitch', function(event, state) {

			$(this).prop('disabled',true);

			var value = "false";
			if(state){
				value = "true";
			}

			var field_name = $(this).attr('name');
			var post_data = {};

			post_data[field_name] = value;

			$.post(self.site_url+"/player_center2/player_preference/updatePreference", post_data, function(data){
				$('input[class^="pref-data-"]').prop('disabled',false);

				if(data.status != "success"){
					alert(data.message);

					$(this).prop('checked',true);

					if(value == "true")
						$(this).prop('checked',false);

					return false;
				}

				alert(data.message);
			});
		});
	}
};