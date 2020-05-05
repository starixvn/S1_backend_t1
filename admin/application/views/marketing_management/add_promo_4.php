<div class="panel panel-primary">

	<div class="panel-heading">
		<h1 class="panel-title pull-left">Add Promo</h1>
		<a href="<?= BASEURL.'marketing_management/promo_list' ?>" class="btn btn-danger btn-sm pull-right"><i class="glyphicon glyphicon-remove"></i></a>
		<div class="clearfix"></div>
	</div> <!-- end of outer panel-heading -->

	<div class="panel-body">

		<ol class="breadcrumb">
			<li><a href="<?= BASEURL.'marketing_management/add_promo/1' ?>">Step 1</a></li>
			<li><a href="<?= BASEURL.'marketing_management/add_promo/2' ?>">Step 2</a></li>
			<li><a href="<?= BASEURL.'marketing_management/add_promo/3' ?>">Step 3</a></li>
			<li class="active">Step 4</li>
			<li>Step 5</li>
			<li>Step 6</li>
		</ol>

		<div class="panel panel-primary">
			<div class="panel-heading"><h2 class="panel-title">Step 4</h2></div>
			<div class="panel-body">
				<div align="left">
				<?php # var_dump($this->_ci_cached_vars['form']) ?>
				</div>
				<form action="<?= BASEURL.'marketing_management/add_promo/5' ?>" method="post" class="form-inline">
					
					<fieldset>
						<legend>Bonus Availability</legend>
						<label class="control-label">Available for withdrawal when </label>
						<?php echo form_dropdown('promoBonusReleaseType', $promoBonusReleaseTypes, isset($form['promoBonusReleaseType']) ? $form['promoBonusReleaseType'] : '', 'id="promoBonusReleaseType" class="form-control"') ?>
						<input type="number" name="promoBonusReleaseValue" class="form-control" min="0" step="any" placeholder="Required" required="required" value="<?= isset($form['promoBonusReleaseValue']) ? $form['promoBonusReleaseValue'] : '' ?>"/>
					</fieldset>
					<br>
					<fieldset>
						<legend>Bonus Expiration</legend>
						<label class="control-label">Expires </label>
						<input type="number" name="promoExpirationValue" class="form-control" min="0" step="any" placeholder="Required" required="required" value="<?= isset($form['promoExpirationValue']) ? $form['promoExpirationValue'] : '' ?>"/>
						<label class="control-label"> days after </label>
						<?php echo form_dropdown('promoExpirationType', $promoExpirationTypes, isset($form['promoExpirationType']) ? $form['promoExpirationType'] : '', 'id="promoExpirationType" class="form-control"') ?>
					</fieldset>

					<hr/>

					<button type="submit" class="btn btn-primary btn-block">Procced to Step 5</button>

				</form>

			</div> <!-- end of inner panel-body -->
		</div> <!-- end of inner panel -->
	</div> <!-- end of outer panel-body -->

	<div class="panel-footer"></div>
</div>