var Register = {
	
	$form: '',

	initForm: function(){

		var self = this;

		self.$form.validate({
			ignore: '.ignore',
			messages: {
	            username: {
	                required: '<?php printf(lang("gen.error.required"), lang("reg.03"))?>',
	                username: '<?php printf(lang("gen.error.character"), lang("reg.03"))?>',
	                remote: '<?php printf(lang("gen.error.exist"), lang("reg.03"))?>',
	                maxlength: '<?php printf(lang("gen.error.between"), lang("reg.03"), $min_username_length, $max_username_length)?>',
	                minlength: '<?php printf(lang("gen.error.between"), lang("reg.03"), $min_username_length, $max_username_length)?>'
	            },
	            password: {
	                required: '<?php printf(lang("gen.error.required"), lang("reg.05"))?>', 
	                password: '<?=$note?>'
	            },
	            cpassword: {
	                required: '<?php printf(lang("gen.error.required"), lang("reg.07"))?>', 
	                equalTo: '<?php printf(lang("gen.error.mismatch"), lang("reg.05"), lang("reg.07"))?>',
	            }
	        }
		});

	},

	rules: function(){

		var self = this;

		var rules: {
            username: {
                required: true, 
                username: true, 
                remote: {
                    url: '/iframe/auth/validate/username',
                    type: "GET",
                    dataFilter: function (data) {

                        var json = JSON.parse(data);

                        if( json.result == true ) return 'true';

                        return 'false';
                    }
                }
            },
            password: {required: true, password: true},
            cpassword: {required: true, equalTo: '#password'}
        }

        return rules;

	}

}