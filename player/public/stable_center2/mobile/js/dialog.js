var Dialog = {
	
	show: function(dom){

		var self = this;

		var dialog = new DialogEl(document.getElementById(dom), {
	        mainElement : {
	            minscale : 0.6,
	            minopacity : 0.5,
	            rY : 40
	        },
	        innerElements : {tx : 100, ty : 100},
	        outofbounds: {x : 0, y: 0}
	    });
	    dialog.open();

	},

	hide: function(dom){

		var self = this;

		var dialog = new DialogEl(document.getElementById(dom), {
	        mainElement : {
	            minscale : 0.6,
	            minopacity : 0.5,
	            rY : 40
	        },
	        innerElements : {tx : 100, ty : 100},
	        outofbounds: {x : 0, y: 0}
	    });
	    dialog.close();

	},

}