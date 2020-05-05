<div class="panel panel-primary">

	<div class="panel-heading">
		<h1 class="panel-title pull-left">Step 5</h1>
		<a href="<?= BASEURL.'marketing_management/promo_list' ?>" class="btn btn-danger btn-sm pull-right"><i class="glyphicon glyphicon-remove"></i></a>
		<div class="clearfix"></div>
	</div> <!-- end of outer panel-heading -->

	<div class="panel-body">

		<ol class="breadcrumb">
			<li><a href="<?= BASEURL.'marketing_management/add_promo/1' ?>">Step 1</a></li>
			<li><a href="<?= BASEURL.'marketing_management/add_promo/2' ?>">Step 2</a></li>
			<li><a href="<?= BASEURL.'marketing_management/add_promo/3' ?>">Step 3</a></li>
			<li><a href="<?= BASEURL.'marketing_management/add_promo/4' ?>">Step 4</a></li>
			<li class="active">Step 5</li>
			<li>Step 6</li>
		</ol>

		<div class="panel panel-primary">
			<div class="panel-heading"><h2 class="panel-title">Step 5</h2></div>
			<div class="panel-body">
				<form action="<?= BASEURL.'marketing_management/add_promo/6' ?>" method="post" id="form-promoHtmlDescription">
					<input name="promoHtmlDescription" type="hidden" id="promoHtmlDescription" value=""/>
					
					<div id="summernote"><?= isset($form['promoHtmlDescription']) ? $form['promoHtmlDescription'] : '' ?></div>

					<hr/>

					<button type="submit" class="btn btn-primary btn-block">Proceed to Step 6</button>

				</form>

			</div> <!-- end of inner panel-body -->
		</div> <!-- end of inner panel -->
	</div> <!-- end of outer panel-body -->

	<div class="panel-footer"></div>
</div>