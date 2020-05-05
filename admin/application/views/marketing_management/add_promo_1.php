<style>
	.chosen-container-multi .chosen-choices .search-choice {
		font-size: 9px;
	}

</style>
<div class="panel panel-primary">

	<div class="panel-heading">
		<h1 class="panel-title pull-left">Add Promo</h1>
		<a href="<?= BASEURL.'marketing_management/promo_list' ?>" class="btn btn-danger btn-sm pull-right"><i class="glyphicon glyphicon-remove"></i></a>
		<div class="clearfix"></div>
	</div> <!-- end of outer panel-heading -->

	<div class="panel-body">

		<ol class="breadcrumb">
			<li class="active">Step 1</li>
			<li>Step 2</li>
			<li>Step 3</li>
			<li>Step 4</li>
			<li>Step 5</li>
			<li>Step 6</li>
		</ol>

		<div class="panel panel-primary">
			<div class="panel-heading"><h2 class="panel-title">Step 1</h2></div>
			<div class="panel-body">

				<form action="<?= BASEURL.'marketing_management/add_promo/2' ?>" method="post">

					<div class="form-group">
						<label class="control-label">Promo Type:</label>
						<?php echo form_dropdown('promoType', $promoTypes, $form['promoType'], 'id="promoType" class="form-control"') ?>
					</div>

					<div class="row">

						<div class="col-xs-6">

							<div class="form-group">
								<label class="control-label">Timezone:</label>
								<?= timezone_menu( isset($form['promoTimezone']) ? $form['promoTimezone'] : 'UP8', 'form-control', 'promoTimezone" id="promoTimezone') ?>
							</div>

							<div class="form-group">
								<label class="control-label">Applicable Games:</label>
								<?php echo form_multiselect('promoGames[]', $games, $form['promoGames'], 'id="promoGames" class="form-control chosen-select" data-placeholder="Select Games" data-untoggle="checkbox" data-target="#toggle-checkbox-1" required') ?>
								<p class="help-block pull-left">Applicable Games for the promo</p>
								<div class="checkbox pull-right" style="margin-top: 5px">
									<label><input type="checkbox" id="toggle-checkbox-1" data-toggle="checkbox" data-target="#promoGames option"<?= isset($form['promoGames']) && sizeof($form['promoGames']) == sizeof($games) ? ' checked' : '' ?>> Select all</label>
								</div>
							</div>

						</div>

						<div class="col-xs-6">

							<div class="form-group">
								<label class="control-label">Currency:</label>
								<?php echo form_dropdown('promoCurrency', $currencies, $form['promoCurrency'], 'id="promoCurrency" class="form-control"') ?>
							</div>

							<div class="form-group">
								<label class="control-label">Applicable Levels:</label>
								<?php echo form_multiselect('promoLevels[]', $levels, $form['promoLevels'], 'id="promoLevels" class="form-control chosen-select" data-placeholder="Select Levels" data-untoggle="checkbox" data-target="#toggle-checkbox-2" required') ?>
								<p class="help-block pull-left">Applicable Levels for the promo</p>
								<div class="checkbox pull-right" style="margin-top: 5px">
									<label><input type="checkbox" id="toggle-checkbox-2" data-toggle="checkbox" data-target="#promoLevels option"<?= isset($form['promoLevels']) && sizeof($form['promoLevels']) == sizeof($levels) ? 'checked' : '' ?>> Select all</label>
								</div>
							</div>

						</div>

					</div>

					<hr/>

					<button type="submit" class="btn btn-primary btn-block">Proceed to Step 2</button>

				</form>

			</div> <!-- end of inner panel-body -->
		</div> <!-- end of inner panel -->
	</div> <!-- end of outer panel-body -->

	<div class="panel-footer"></div>
</div>
<script type="text/javascript">

	$(function() {

		$('#promoStartTimestamp').daterangepicker({
			minDate: moment().startOf('month'),
			format: 'MMMM DD, YYYY',
			showWeekNumbers: true,
			singleDatePicker: true,
			showDropdowns: true,
		}, setEndDate);
		$('#promoPeriod,#promoLength,#promoStartTimestamp').change(setEndDate);

	});

	function setEndDate() {
		var date 	 = $('#promoStartTimestamp').val();
		var start 	 = moment(date, 'MMMM DD, YYYY');
		var promoPeriod 	 = $('#promoPeriod option:selected').text();
		var promoLength = $('#promoLength').val();

		if (date.trim().length == 0) {
			$('#promoEndTimestamp').val('Please select a Start Date');
		} else if ( ! start.isValid()) {
			$('#promoEndTimestamp').val('Invalid Date');
		} else if (promoLength == 0) {
			$('#promoEndTimestamp').val('Never');
		} else {
			$('#promoEndTimestamp').val(start.add(promoLength,promoPeriod).format('MMMM DD, YYYY'));
		}
	}

</script>
