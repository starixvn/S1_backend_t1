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
			<li><a href="<?= BASEURL.'marketing_management/add_promo/4' ?>">Step 4</a></li>
			<li><a href="<?= BASEURL.'marketing_management/add_promo/5' ?>">Step 5</a></li>
			<li class="active">Step 6</li>
		</ol>

		<div class="panel panel-primary">
			<div class="panel-heading"><h2 class="panel-title">Step 6</h2></div>
			<div class="panel-body">
				<form action="<?= BASEURL.'marketing_management/add_promo/7' ?>" method="post">
					<input name="promoCreatedBy" type="hidden" value="<?= $this->authentication->getUserId() ?>"/>
					
					<dl class="dl-horizontal">
						
						<dt>Name</dt>
						<dd><?= $form['promoName'] ?></dd>
						
						<dt>Description</dt>
						<dd><?= $form['promoShortDescription'] ?></dd>

						<dt>Levels</dt>
						<dd>
							<?php foreach ($levels as $levelId => $levelName): ?>
								<?php if (in_array($levelId,$form['promoLevels'])): ?>
									<span class="label label-primary"><?php echo $levelName ?></span>
								<?php endif ?>
							<?php endforeach ?>
						</dd>

						<dt>Games</dt>
						<dd>
							<?php foreach ($games as $gameId => $gameName): ?>
								<?php if (in_array($gameId,$form['promoGames'])): ?>
									<span class="label label-primary"><?php echo $gameName ?></span>
								<?php endif ?>
							<?php endforeach ?>
						</dd>

						<dt>Timezone</dt>
						<dd><?= $this->lang->line($form['promoTimezone']) ?></dd>

						<dt>Currency</dt>
						<?php foreach ($currencies as $currencyId => $currencyName): ?>
							<?php if ($currencyId == $form['promoCurrency']): ?>
								<dd><?php echo $currencyName ?></dd>
							<?php endif ?>
						<?php endforeach ?>
						
						<?php if ($form['promoType'] == 1): ?> 
							<dt>Recurring</dt>
							<dd><?= $this->lang->line('promoPeriod_'.$form['promoPeriod']) ?></dd>
						<?php endif ?>

						<dt>Period</dt>
						<dd>
							<?php if ($form['promoType'] == 0): ?>
								<?= $form['promoPeriodTimestamp'] ?>
							<?php elseif ($form['promoType'] == 1): ?> 
								<?= $form['promoStartTimestamp'] ?> - <?= $form['promoEndTimestamp'] ?>
							<?php endif ?>
						</dd>

						<dt>Limit</dt>
						<dd>
							<div class="row">
								<div class="col-xs-4"><strong>Level</strong></div>
								<div class="col-xs-4"><strong>Min</strong></div>
								<div class="col-xs-4"><strong>Max</strong></div>
								<div class="col-xs-4">Default</div>
								<div class="col-xs-4"><?= ' '.number_format($form['promoMinimumBonus'],2) ?></div>
								<div class="col-xs-4"><?= ' '.number_format($form['promoMaximumBonus'],2) ?></div>
								<?php foreach ($levels as $levelId => $levelName): ?>
									<?php if (in_array($levelId,$form['promoLevels'])): ?>
										<div class="col-xs-4"><?php echo $levelName ?></div>
										<div class="col-xs-4"><?= $form['promoLimits'][$levelId]['min'] ? number_format($form['promoLimits'][$levelId]['min'],2) : number_format($form['promoMinimumBonus'],2) ?></div>
										<div class="col-xs-4"><?= $form['promoLimits'][$levelId]['max'] ? number_format($form['promoLimits'][$levelId]['max'],2) : number_format($form['promoMaximumBonus'],2)  ?></div>
									<?php endif ?>
								<?php endforeach ?>
							</div>
						</dd>

						<dt>Definition</dt>
						<dd><var><?= $this->lang->line('promoRequiredType_'.$form['promoRequiredType']) ?></var> = 
						<strong>
							<?php if ($form['promoConditionType'] == 0): ?>
								<?php 
									$out = '';
									if ( ! in_array(($form['promoConditionValue'] % 100), array(11,12,13))) {
										switch ($form['promoConditionValue'] % 10) {
										 	case 1: $out = $form['promoConditionValue'].'st'; break;
										 	case 2: $out = $form['promoConditionValue'].'nd'; break;
										 	case 3: $out = $form['promoConditionValue'].'rd'; break;
										}
									}
									echo $out ? : ($form['promoConditionValue'].'th');
								?>
							<?php elseif ($form['promoConditionType'] == 1): ?>
								Total 
							<?php endif ?>
							<?php if ($form['promoType'] == 1): ?>
								<?= $this->lang->line('promoPeriod_'.$form['promoPeriod']) ?>
							<?php endif ?> 
							<?= $this->lang->line('promoRequiredType_'.$form['promoRequiredType']) ?>

						</strong> 
						<?php if ($form['promoType'] == 0): ?>
							from <strong><?php echo $form['promoConditionPeriodTimestamp'] ?></strong>
						<?php endif ?> 
						</dd>


						<dt>Rules</dt>
						<?php foreach ($form['promoRules'] as $rule): ?>
							<dd>
								if (
									<var><?= $this->lang->line('promoRequiredType_'.$form['promoRequiredType']) ?></var>
									 >= 
									<strong><?= number_format($rule['promoRuleInValue'],2) ?></strong>
								) then (
									<var>Bonus</var> = <strong><?= strpos($rule['promoRuleOutValue'], '%') ? $rule['promoRuleOutValue'] : number_format($rule['promoRuleOutValue']?:0,2) ?></strong> 
									and
									<var>Points</var> = <strong><?= strpos($rule['promoRulePoints'], '%') ? $rule['promoRulePoints'] : number_format($rule['promoRulePoints']?:0,2) ?></strong> 
								);</dd>
						<?php endforeach ?>

						<dt>Release</dt>
						<dd>
							<?= $this->lang->line('promoBonusReleaseType_'.$form['promoBonusReleaseType']) ?> <strong><?= number_format($form['promoBonusReleaseValue'],2) ?></strong>
						</dd>

						<dt>Promo Page</dt>
						<dd>
							<a data-toggle="modal" href="../preview" data-target="#modal">View Promo Page</a>
						</dd>

					</dl>

					<hr/>

					<button type="submit" class="btn btn-primary btn-block">Submit</button>

				</form>
			</div> <!-- end of inner panel-body -->
		</div> <!-- end of inner panel -->
	</div> <!-- end of outer panel-body -->

	<div class="panel-footer"></div>
</div>
<div class="modal fade" id="modal">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<style>
	var {
		font-weight: bold;
	}
</style>