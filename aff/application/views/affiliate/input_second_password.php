<div class="container">
	<br/>

	<!-- Input Second Password -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
					<i class="glyphicon glyphicon-cog"></i> <?= lang('Secondary Password'); ?> 
					<a href="javascript:;" onclick="goBack()" class="close">&times;</a>
				</h4>
			</div>

			<div class="panel panel-body" id="change_password_panel_body">
				<form method="POST" action="<?php echo site_url('affiliate/verifySecondPassword'); ?>" accept-charset="utf-8">
					<input type="hidden" name="next_uri" value="<?php echo $next_uri; ?>">
					<div class="row">
						<div class="col-md-6">
							<label for="second_password"><?= lang('Secondary Password'); ?>: </label>
							<input type="password" name="second_password" id="second_password" class="form-control" value="<?= set_value('second_password') ?>" placeholder="<?= lang('Secondary Password'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('second_password'); ?></label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<input type="submit" name="submit" id="submit" class="btn btn-primary" value="<?= lang('lang.submit'); ?>">
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- End of Input Second Password -->
</div>

<script type="text/javascript">
	function goBack(){
		window.history.back();
	}
</script>
