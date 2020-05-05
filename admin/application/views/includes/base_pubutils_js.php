	if(typeof _pubutils == "undefined"){
	  _pubutils={
	    variables: {
	      debugLog: true
	    },
	    safelog: function(msg){
	        //check exists console.log
	        if(variables.debugLog && typeof(console)!='undefined' && console.log){
	            console.log(msg);
	        }
	    }
	  };
	}
