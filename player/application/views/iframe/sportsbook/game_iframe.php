<?php if (isset($gameApiSessionToken)) {
	$date_of_expiry = time() + 60;
	setcookie("g", $gameApiSessionToken, $date_of_expiry, "", 'sbtest.webet88.com');
	setcookie("g", $gameApiSessionToken, $date_of_expiry, "", 'webet88.com');
	header("Location: http://sbtest.webet88.com/Deposit_ProcessLogin.aspx?lang=en");
} else {
	?>
<iframe width="100%" height="100%" frameborder="0" marginwidth="0" marginheight="0" src="<?php echo $url; ?>"></iframe>
<?php }?>