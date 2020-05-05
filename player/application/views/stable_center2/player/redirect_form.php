
<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?php echo $title; ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

	<div class="panel-body">

	<form action="<?= $reward_login_url ?>" id="login_form" method='POST'>
		<input type="hidden" name="username" value="<?= $username ?>">
		<input type="hidden" name="token" value="<?= $token ?>">
		<input type="hidden" name="from_host" value="<?= $from_host ?>">
	</form>
	<script type="text/javascript">
		var login_form = document.getElementById('login_form');
		login_form.submit();
	</script>


	</div>

</div>
