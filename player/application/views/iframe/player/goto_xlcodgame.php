<!DOCTYPE html>
<html lang="en">
<head>
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
<body onload="document.xlc_form.submit()">
<form id="xlc_form" name="xlc_form" method="POST" action="<?php echo $url;?>" target="xlc_iframe">
    <input type="hidden" name="uno" value="<?=$userno?>">
    <input name="pw" type="hidden" value="<?=$passwd?>">
    <input type="hidden" value="<?=$signstr?>" name="sign">
</form>
<iframe id="xlc_iframe" name="xlc_iframe" width="100%" height="100%" src="about:blank"<?php if ($redirect_url): ?> sandbox="allow-scripts allow-forms"<?php endif ?>></iframe>
<script src="/resources/js/jquery-2.1.4.min.js"></script>
<?php if ($redirect_url): ?>
    <script type="text/javascript">
        var submitted = false;
        $(function() {
            $('#xlc_iframe').load(function() {
                if ( ! submitted) {
                    $('#xlc_iframe').replaceWith('<iframe id="xlc_iframe" name="xlc_iframe" width="100%" height="100%" src="<?=$redirect_url?>"></iframe>');
                }
                submitted = true;
            });
            $('#xlc_form').submit();
        });
    </script>
<?php endif ?>
</body>
</html>