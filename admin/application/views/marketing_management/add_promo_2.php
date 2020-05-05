<div class="panel panel-primary">

	<div class="panel-heading">
		<h1 class="panel-title pull-left">Add Promo</h1>
		<a href="<?= BASEURL.'marketing_management/promo_list' ?>" class="btn btn-danger btn-sm pull-right"><i class="glyphicon glyphicon-remove"></i></a>
		<div class="clearfix"></div>
	</div> <!-- end of outer panel-heading -->

	<div class="panel-body">

		<ol class="breadcrumb">
			<li><a href="<?= BASEURL.'marketing_management/add_promo/1' ?>">Step 1</a></li>
			<li class="active">Step 2</li>
			<li>Step 3</li>
			<li>Step 4</li>
			<li>Step 5</li>
			<li>Step 6</li>
		</ol>

		<div class="panel panel-primary">
			<div class="panel-heading"><h2 class="panel-title">Step 2</h2></div>
			<div class="panel-body">
				<?php // var_dump($this->_ci_cached_vars['form']) ?>

				<form action="<?= BASEURL.'marketing_management/add_promo/3' ?>" method="post">
					
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label">Promo Name:</label>
								<input type="text" name="promoName" class="form-control input-sm" placeholder="Required" required="required" autocomplete="off" value="<?= isset($form['promoName']) ? $form['promoName'] : '' ?>"/>
							</div>

							<div class="form-group">
								<label class="control-label">Short Description:</label>
								<textarea name="promoShortDescription" class="form-control input-sm" rows="3" placeholder="Required" required="required"><?= isset($form['promoShortDescription']) ? $form['promoShortDescription'] : '' ?></textarea>
							</div>
						</div>

						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label">Long Description:</label>
								<textarea name="promoLongDescription" class="form-control input-sm" rows="7" placeholder="Optional"><?= isset($form['promoLongDescription']) ? $form['promoLongDescription'] : '' ?></textarea>
							</div>
						</div>
					</div>

					<hr/>

					<button type="submit" class="btn btn-primary btn-block">Proceed to Step 3</button>

				</form>

			</div> <!-- end of inner panel-body -->
		</div> <!-- end of inner panel -->
	</div> <!-- end of outer panel-body -->

	<div class="panel-footer"></div>
</div>