var Language = {

	cmsLang: '',

	getLanguage: function(module) {

		var self = this;

		$.ajax({
			url: base_url + 'api/json_lang',
			dataType: 'json',
			success: function(response) {
				if ( ! self.cmsLang) self.cmsLang = response;
				else self.cmsLang.concat(response);
			},
			async: true
		});
	},

	lang: function(ndx, s, t) {
		s = s || '';
		t = t || '';

		if ( ! this.cmsLang) this.getLanguage();

		if (s == undefined || s == '') return this.cmsLang[ndx];

		return $.sprintf(this.cmsLang[ndx], s, t);
	},

}