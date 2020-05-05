<?php

if (isset($full_html)) {
	echo $full_html;
} else {

	?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $platformName; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<style>
*{padding:0;margin:0;}
html , body {height:100%;}
iframe{border:none;}
</style>
</head>
<body>
<?php

	if (isset($url) && !empty($url)) {

		?>
<iframe width="100%" height="100%" src="<?php echo $url; ?>" id="main_iframe"></iframe>
<?php

	} else if (isset($call_js) && !empty($call_js)) {

		?>
<div class="loading"><?php echo lang('text.loading'); ?></div>
<iframe width="100%" height="100%" src="/empty.html" id="main_iframe"></iframe>
<script type="text/javascript" src='<?php echo $this->utils->jsUrl("jquery-1.11.1.min.js"); ?>'></script>
<script type="text/javascript">
var variables={
    debugLog: true
};
var utils={
    safelog:function(msg){
        //check exists console.log
        if(variables.debugLog && typeof(console)!='undefined' && console.log){
            console.log(msg);
        }
    },
	getJSONP:function(url,data, success, error){
		$.ajax({
			url: url,
			type: 'GET',
			data: data,
			dataType: 'jsonp',
			cache: false
		}).done(success)
		.fail(error);
	},
	getJSON:function(url,data, success, error){
		this.callJSON(url,'GET',data,success,error);
	},
	postJSON:function(url,data, success, error){
		this.callJSON(url,'POST',data,success,error);
	},
	callJSON:function(url,type,data, success, error){
		$.ajax({
			url: url,
			type: type,
			data: data,
			dataType: 'json',
			cache: false,
			xhrFields: {
			    withCredentials: true
			},
			success:success,
			error: error
		});
	}
};

	//load from url
	<?php echo $call_js; ?>
</script>
<?php

	} else if (isset($error_message)) {
		echo $error_message;
	} else if (isset($error_message_lang)) {
		echo lang($error_message);
	} else {
		echo lang('goto_game.error');
	}

	?>
</body>
</html>

<?php
}
?>