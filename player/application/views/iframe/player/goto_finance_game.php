<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,minimum-scale=1,maximum-scale=1" content="user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $platformName;?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <style>
        *{padding:0;margin:0;}
        html , body {height:100%;}
        iframe{border:none;}
    </style>
</head>


<iframe name="finance_frame" width="100%" height="100%" marginwidth="0" marginheight="0" align="middle" scrolling="no" id="finance_frame"></iframe>

<script>
    function urlTimezone(url) {
        if (url.indexOf("?")==-1) {
            return url
        }
        var d = new Date();
        var timezone = d.getTimezoneOffset();
        return url+"&timeZone="+timezone
    }

    var url = "<?php echo $url ?>";
    document.getElementById('finance_frame').src = urlTimezone(url);
</script>
</html>