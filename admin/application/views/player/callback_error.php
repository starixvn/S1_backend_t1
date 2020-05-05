<html>
<head>
<title><?php echo lang('error.payment.failed.title');?></title>
    <link rel="stylesheet" type="text/css" href="<?=CSSPATH?>bootstrap.css">
</head>
<body>
	<div class="container">
		<h2><?php echo lang('error.payment.failed.title');?></h2>
		<p><?php echo $message?></p>
		<ul class="list-inline">
			<!-- <li><a href="<?php echo site_url($next_url)?>" class="btn btn-primary"><?php echo lang('button.retry');?></a></li> -->
			<li><a href="<?php echo site_url($home_url)?>" class="btn btn-danger"><?php echo lang('button.go_home');?></a></li>
		</ul>
	</div>
</body>
</html>