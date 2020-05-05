<style type="text/css">
	body {
		background-color:#FF0000;
	}
	.bonus{
	    text-align: center;
	    padding-top: 50px;
	}
</style>
<div class="row bonus">

	<div class="col-md-12">
	<?php if (isset($logo_url) && !empty($logo_url)) {?>
	<img src='<?php echo $logo_url; ?>' width="400" height="400">
	<?php }
?>

	<h3><?php echo urldecode($msg); ?></h3>
	</div>

</div>
