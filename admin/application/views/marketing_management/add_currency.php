<div class="panel panel-primary">

	<div class="panel-heading">
		<h1 class="panel-title pull-left" style="line-height:24px">Create Currency</h1>
		<a href="<?= BASEURL . 'marketing_management/currency_list' ?>" class="btn btn-default btn-sm pull-right" title="Close"><i class="glyphicon glyphicon-remove"></i></a>
		<div class="clearfix"></div>
	</div>

	<div class="panel-body">
		<form action="<?= BASEURL . 'marketing_management/add_currency'?>" method="post">
			
			<div class="form-group">
				<label class="control-label input-sm">Currency Code:</label>
				<input type="text" class="form-control input-sm" name="currencyCode" maxlength="3" value="<?php echo set_value('currencyCode') ?>"/>
			</div>

			<div class="form-group">
				<label class="control-label input-sm">Currency Name:</label>
				<input type="text" class="form-control input-sm" name="currencyName" value="<?php echo set_value('currencyName') ?>"/>
			</div>

			<button type="submit" class="btn btn-primary btn-block">Submit</button>
		</form>
	</div>
</div>