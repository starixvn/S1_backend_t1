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
			<li class="active">Step 3</li>
			<li>Step 4</li>
			<li>Step 5</li>
			<li>Step 6</li>
		</ol>

		<div class="panel panel-primary">
			<div class="panel-heading"><h2 class="panel-title">Step 3</h2></div>
			<div class="panel-body">

				<form action="<?= BASEURL.'marketing_management/add_promo/4' ?>" method="post">

					<fieldset>
						<legend>Promo Period</legend>

						<div class="form-group">
							
							<?php if ($form['promoType'] == 0): # ONE TIME PROMO ?>
								<input type="hidden" name="promoStartTimestamp" id="promoStartTimestamp" value="<?= isset($form['promoStartTimestamp']) ? $form['promoStartTimestamp'] : '' ?>"/>
								<input type="hidden" name="promoEndTimestamp" id="promoEndTimestamp" value="<?= isset($form['promoEndTimestamp']) ? $form['promoEndTimestamp'] : '' ?>"/>
								<input type="text" name="promoPeriodTimestamp" id="promoPeriodTimestamp" class="form-control input-sm" placeholder="Required" required="required" autocomplete="off" value="<?= isset($form['promoPeriodTimestamp']) ? $form['promoPeriodTimestamp'] : '' ?>"/>
							<?php elseif ($form['promoType'] == 1): # RECURRING PROMO ?>
								<div class="form-inline">
									<label class="control-label">Starts </label>
									<input type="text" name="promoStartTimestamp" id="promoStartTimestamp" class="form-control input-sm" placeholder="Required" required="required" autocomplete="off" value="<?= isset($form['promoStartTimestamp']) ? $form['promoStartTimestamp'] : '' ?>" />
								
									<label class="control-label">Ends </label>
									<input type="number" name="promoLength" id="promoLength" class="form-control input-sm" min="0" placeholder="Optional" autocomplete="off" value="<?= isset($form['promoLength']) ? $form['promoLength'] : '' ?>"/>

									<?php echo form_dropdown('promoPeriod', $promoPeriods, isset($form['promoPeriod']) ? $form['promoPeriod'] : '', 'id="promoPeriod" class="form-control input-sm"') ?>

									<input type="text" name="promoEndTimestamp" id="promoEndTimestamp" class="form-control input-sm" placeholder="Please select a Start Date" readonly="readonly" value="<?= isset($form['promoEndTimestamp']) ? $form['promoEndTimestamp'] : '' ?>"/>
								</div>
							<?php endif ?>

						</div>

						<div class="form-group">
							<div class="form-inline">

								<label class="control-label">Player's<?php if ($form['promoType'] == 1): ?> <span class="promoPeriod">Daily</span><?php endif ?> </label>

								<input type="number" name="promoConditionValue" id="promoNth" class="form-control input-sm" min="1" step="1" placeholder="Required" required="required" autocomplete="off" <?php if (isset($form['promoConditionType']) && $form['promoConditionType'] == 1): ?> disabled="disabled" style="display:none"<?php endif ?> value="<?= isset($form['promoConditionValue']) ? $form['promoConditionValue'] : '' ?>"/>
								
								<?php echo form_dropdown('promoConditionType', $promoConditionTypes, isset($form['promoConditionType']) ? $form['promoConditionType'] : '', 'id="promoConditionType" class="form-control input-sm"') ?>

								<?php echo form_dropdown('promoRequiredType', $promoRequiredTypes, isset($form['promoRequiredType']) ? $form['promoRequiredType'] : '', 'id="promoRequiredType" class="form-control input-sm"') ?>
								
								<?php if ($form['promoType'] == 0): ?>
									<input type="hidden" name="promoConditionStartTimestamp" id="promoConditionStartTimestamp" value="<?= isset($form['promoConditionStartTimestamp']) ? $form['promoConditionStartTimestamp'] : '' ?>"/>
									<input type="hidden" name="promoConditionEndTimestamp" id="promoConditionEndTimestamp" value="<?= isset($form['promoConditionEndTimestamp']) ? $form['promoConditionEndTimestamp'] : '' ?>"/>
									<label class="control-label">From </label>
									<input type="text" name="promoConditionPeriodTimestamp" id="promoConditionPeriodTimestamp" class="form-control input-sm" placeholder="Required" required="required" autocomplete="off" value="<?= isset($form['promoConditionPeriodTimestamp']) ? $form['promoConditionPeriodTimestamp'] : '' ?>"/>
								<?php endif ?>

							</div>
						</div>

					</fieldset>

					<fieldset>
						<legend>Promo Limits</legend>
						<div class="row">

							<div class="col-xs-offset-2 col-xs-5">
								<label class="control-label">Minimum Bonus Amount:</label>
							</div>

							<div class="col-xs-5">
								<label class="control-label">Maximum Bonus Amount:</label>
							</div>

							<div class="col-xs-2">Default</div>

							<div class="col-xs-5">
								<div class="form-group">
									<input type="number" name="promoMinimumBonus" class="form-control input-sm" min="0" max="<?= isset($form['promoMaximumBonus']) ? $form['promoMaximumBonus'] : '' ?>" step="any" placeholder="Required" required="required" autocomplete="off" value="<?= isset($form['promoMinimumBonus']) ? $form['promoMinimumBonus'] : '' ?>"/>
								</div>
							</div>

							<div class="col-xs-5">
								<div class="form-group">
									<input type="number" name="promoMaximumBonus" class="form-control input-sm" min="<?= isset($form['promoMinimumBonus']) ? $form['promoMinimumBonus'] : '' ?>" step="any" placeholder="Required" required="required" autocomplete="off" value="<?= isset($form['promoMaximumBonus']) ? $form['promoMaximumBonus'] : '' ?>"/>
								</div>
							</div>

							<?php foreach ($levels as $levelId => $levelName): ?>
								<?php if (in_array($levelId, $form['promoLevels'])): ?>
									<div class="col-xs-2"><?php echo $levelName ?></div>
									<div class="col-xs-5">
										<div class="form-group">
											<input type="number" name="promoLimits[<?= $levelId ?>][min]" class="form-control input-sm promolimitsmin" data-level="<?= $levelId ?>" min="0" max="<?= isset($form['promoLimits'][$levelId]['max']) ? $form['promoLimits'][$levelId]['max'] : '0' ?>" step="any" placeholder="Optional" autocomplete="off" value="<?= isset($form['promoLimits'][$levelId]['min']) ? $form['promoLimits'][$levelId]['min'] : '' ?>"/>
										</div>
									</div>

									<div class="col-xs-5">
										<div class="form-group">
											<input type="number" name="promoLimits[<?= $levelId ?>][max]" class="form-control input-sm promolimitsmax" data-level="<?= $levelId ?>" min="<?= isset($form['promoLimits'][$levelId]['min']) ? $form['promoLimits'][$levelId]['min'] : '0' ?>" step="any" placeholder="Optional" autocomplete="off" value="<?= isset($form['promoLimits'][$levelId]['max']) ? $form['promoLimits'][$levelId]['max'] : '' ?>"/>
										</div>
									</div>
								<?php endif ?>
							<?php endforeach ?>
						</div>
					</fieldset>

					<fieldset>
						<legend>Promo Rules</legend>

						<div class="row">

							<div class="col-xs-4" id="formGroup-promoRuleInValue">
								<label class="control-label promoRequiredType">Deposit</label>
								<?php for ($i = 0; $i < (isset($form['promoRules']) ? sizeof($form['promoRules']) : 1) || $i < 1; $i++) : ?> 
									<div class="form-group"<?php if ($i != 0): ?> id="promoRuleInValue-<?= $i ?>"<?php endif ?>>
										<input type="number" step="any" name="promoRules[<?= $i ?>][promoRuleInValue]" class="form-control input-sm" min="1" placeholder="Required" required="required" autocomplete="off" value="<?= isset($form['promoRules'][$i]['promoRuleInValue']) ? $form['promoRules'][$i]['promoRuleInValue'] : '' ?>"/>
									</div>
								<?php endfor ?>
							</div>

							<div class="col-xs-3" id="formGroup-promoRuleOutValue">
								<label class="control-label">Bonus</label>
								<?php for ($i = 0; $i < (isset($form['promoRules']) ? sizeof($form['promoRules']) : 1) || $i < 1; $i++) : ?> 
									<div class="form-group"<?php if ($i != 0): ?> id="promoRuleOutValue-<?= $i ?>"<?php endif ?>>
										<input type="text" name="promoRules[<?= $i ?>][promoRuleOutValue]" class="form-control input-sm" placeholder="Optional" autocomplete="off" value="<?= isset($form['promoRules'][$i]['promoRuleOutValue']) ? $form['promoRules'][$i]['promoRuleOutValue'] : '' ?>"/>
									</div>
								<?php endfor ?>
							</div>

							<div class="col-xs-3" id="formGroup-promoRulePoints">
								<label class="control-label">Points</label>
								<?php for ($i = 0; $i < (isset($form['promoRules']) ? sizeof($form['promoRules']) : 1) || $i < 1; $i++) : ?> 
									<div class="form-group"<?php if ($i != 0): ?> id="promoRulePoints-<?= $i ?>"<?php endif ?>>
										<input type="text" name="promoRules[<?= $i ?>][promoRulePoints]" class="form-control input-sm" placeholder="Optional" autocomplete="off" value="<?= isset($form['promoRules'][$i]['promoRulePoints']) ? $form['promoRules'][$i]['promoRulePoints'] : '' ?>"/>
									</div>
								<?php endfor ?>
							</div>

							<div class="col-xs-2" id="formGroup-removePromoRule">
								<label class="control-label">Remove:</label>
								<?php for ($i = 0; $i < (isset($form['promoRules']) ? sizeof($form['promoRules']) : 1) || $i < 1; $i++) : ?> 
									<?php if ($i == 0): ?>
										<div class="form-group">
											<button type="button" class="btn btn-danger btn-block removePromoRule" disabled="disabled" style="height: 30px"><i class="glyphicon glyphicon-remove"></i></button>
										</div>
									<?php else: ?>
										<div class="form-group" id="removePromoRule-<?= $i ?>">
											<button type="button" class="btn btn-danger btn-block removePromoRule" data-id="<?= $i ?>" style="height: 30px"><i class="glyphicon glyphicon-remove"></i></button>
										</div>
									<?php endif ?>
								<?php endfor ?>
							</div>

							<div class="col-xs-2 col-xs-offset-10">
								<button type="button" id="button-add-rule" class="btn btn-primary btn-sm btn-block"><i class="glyphicon glyphicon-plus"></i> Add Rule</button>
							</div>

						</div>
					</fieldset>

					<hr/>

					<button type="submit" class="btn btn-primary btn-block">Proceed to Step 4</button>
				
				</form>

			</div> <!-- end of inner panel-body -->
		</div> <!-- end of inner panel -->
	</div> <!-- end of outer panel-body -->

	<div class="panel-footer"></div>
</div>

<script type="text/javascript">

	// Add/Remove Rule
	var buttonId = <?= (isset($form['promoRules']) ? sizeof($form['promoRules']) : 1) ?>;
	$('#button-add-rule').click(function() {

		var promoRuleInValue 	= '<div class="form-group" id="promoRuleInValue-'+buttonId+'"><input type="number" step="any" name="promoRules['+buttonId+'][promoRuleInValue]" class="form-control input-sm" placeholder="Required" required="required" autocomplete="off"/></div>';
		var promoRuleOutValue 	= '<div class="form-group" id="promoRuleOutValue-'+buttonId+'"><input type="text" name="promoRules['+buttonId+'][promoRuleOutValue]" class="form-control input-sm" placeholder="Optional" autocomplete="off"/></div>';
		var promoRulePoints 	= '<div class="form-group" id="promoRulePoints-'+buttonId+'"><input type="text" name="promoRules['+buttonId+'][promoRulePoints]" class="form-control input-sm" placeholder="Optional" autocomplete="off"/></div>';
		var removePromoRule 	= '<div class="form-group" id="removePromoRule-'+buttonId+'"><button type="button" class="btn btn-danger btn-block removePromoRule" data-id="'+buttonId+'" style="height: 30px"><i class="glyphicon glyphicon-remove"></i></button></div>';

		$('#formGroup-promoRuleInValue').append(promoRuleInValue);
		$('#formGroup-promoRuleOutValue').append(promoRuleOutValue);
		$('#formGroup-promoRulePoints').append(promoRulePoints);
		$('#formGroup-removePromoRule').append(removePromoRule);

		buttonId++;
	});

	$('#formGroup-removePromoRule').on('click', '.removePromoRule', function() {

		var button = $(this);
		var id = button.data('id');

		$('#promoRuleInValue-'+id).remove();
		$('#promoRuleOutValue-'+id).remove();
		$('#promoRulePoints-'+id).remove();
		$('#removePromoRule-'+id).remove();

	});

	$('input[name="promoMinimumBonus"]').change( function() {
		$('input[name="promoMaximumBonus"]').attr('min',$(this).val());

	});

	$('input[name="promoMaximumBonus"]').change( function() {
		$('input[name="promoMinimumBonus"]').attr('max',$(this).val());
	});

	$('.promolimitsmin').change( function() {
		var level = $(this).data('level');
		$('.promolimitsmax[data-level="'+level+'"]').attr('min',$(this).val());

	});

	$('.promolimitsmin').change( function() {
		var level = $(this).data('level');
		$('.promolimitsmin[data-level="'+level+'"]').attr('max',$(this).val());
	});

</script>