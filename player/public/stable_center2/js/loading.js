var Loading = {
	
	show: function(element){

		var self = this,
			html = '';

		html += '<div class="loading">';
		html += '<span>' + Language.cmsLang['text.loading'] + '</span>';
		html += '</div>';

        $(html).appendTo("." + element);

	},

	hide: function(element){

		var self = this;

		$('.loading').remove();

	},

}