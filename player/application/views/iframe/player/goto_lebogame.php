<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $platformName;?></title>
</head>
<body onload="document.lebo_form.submit()">
<form method="post" action="<?=$url;?>" name="lebo_form" >
<INPUT type="hidden" name="uno" value="<?php echo $uno; ?>" >
<INPUT type="hidden" name="pw"  value="<?php echo $pw; ?>" >
<INPUT type="hidden" name="refurl"  value="<?php echo $refurl; ?>" >
<input type="hidden" value="<?php echo $signstr; ?>" name="signstr" /><br />
<input type="submit" value="login"/>
</form>
</body>
</html>