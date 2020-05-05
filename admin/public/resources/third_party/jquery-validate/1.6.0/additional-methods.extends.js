jQuery.validator.addMethod('afterDate', function(value, element, params) {
	if ( ! value) return true;
	
	var to = new Date(value);
	var from = new Date(params);
	
	if (to.getTime() < from.getTime()) return false;
	
	return true;
});

jQuery.validator.addMethod('dateMDY', function(value, element, param) {
	var check = false;
	var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
	if( re.test(value)){
		var adata = value.split('/');
		var gg, mm;
		gg = parseInt(adata[1],10);
		mm = parseInt(adata[0],10);
		
		var aaaa = parseInt(adata[2],10);
		var xdata = new Date(aaaa,mm-1,gg);
		if ( ( xdata.getFullYear() == aaaa ) && ( xdata.getMonth () == mm - 1 ) && ( xdata.getDate() == gg ) )
			check = true;
		else
			check = false;
	} else
		check = false;
	return this.optional(element) || check;
}, 'Please enter a correct date');
