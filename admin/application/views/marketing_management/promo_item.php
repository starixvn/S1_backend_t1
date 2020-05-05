<div class="panel panel-primary">
	<div class="panel-heading">
		<h1 class="panel-title">Promo Item</i>
		<a href="../promo_list" class="btn btn-default btn-sm pull-right" title="Close"><i class="glyphicon glyphicon-remove"></i></a>
	</div>
	<div class="panel-body">
		<?php # var_dump($this->_ci_cached_vars) ?>
		<label class="control-label"><i class="glyphicon glyphicon-tags"></i> Promo Details</label>
		<div class="well well-sm" style="background-color: #fff">
			<dl class="dl-horizontal">
						
						<dt>Name</dt>
						<dd><?= $name ?></dd>
						
						<dt>Description</dt>
						<dd><?= $description['short'] ?></dd>

						<dt>Levels</dt>
						<dd>
							<?php foreach ($levels as $level): ?>
								<span class="label label-primary"><?= $level['name'] ?></span>
							<?php endforeach ?>
						</dd>

						<dt>Games</dt>
						<dd>
							<?php foreach ($games as $game): ?>
								<span class="label label-primary"><?= $game['name'] ?></span>
							<?php endforeach ?>
						</dd>

						<dt>Timezone</dt>
						<dd><?= $timezone['name'] ?></dd>

						<dt>Currency</dt>
						<dd><?= $currency['code'].' - '.$currency['name'] ?></dd>
						
						<?php if ($type == 1): ?> 
							<dt>Recurring</dt>
							<dd><?= $period['name'] ?></dd>
						<?php endif ?>

						<dt>Period</dt>
						<dd><?= $period['start'].' - '.$period['end'] ?></dd>

						<dt>Limit</dt>
						<dd>
							<div class="row">
								<div class="col-xs-4"><strong>Level</strong></div>
								<div class="col-xs-4"><strong>Min</strong></div>
								<div class="col-xs-4"><strong>Max</strong></div>
								<div class="col-xs-4">Default</div>
								<div class="col-xs-4"><?= $bonus['minimum'] ?></div>
								<div class="col-xs-4"><?= $bonus['maximum'] ?></div>
								<?php foreach ($levels as $level): ?>
									<div class="col-xs-4"><?= $level['name'] ?></div>
									<div class="col-xs-4"><?= $level['min'] ?></div>
									<div class="col-xs-4"><?= $level['max'] ?></div>
								<?php endforeach ?>
							</div>
						</dd>

						<dt>Definition</dt>
						<dd>
							<var><?= $requirements['name'] ?></var> = 
							<strong>
								<?php if ($condition['code'] == 0): ?>
									<?php 
										$out = '';
										if ( ! in_array(($condition['value'] % 100), array(11,12,13))) {
											switch ($condition['value'] % 10) {
											 	case 1: $out = $condition['value'].'st'; break;
											 	case 2: $out = $condition['value'].'nd'; break;
											 	case 3: $out = $condition['value'].'rd'; break;
											}
										}
										echo $out ? : ($condition['value'].'th');
									?>
								<?php elseif ($condition['code'] == 1): ?>
									Total 
								<?php endif ?>
								<?php if ($type['code'] == 1): ?>
									<?= $period['name'] ?>
								<?php endif ?> 
								<?= $requirements['name'] ?>

							</strong> 
							<?php if ($type['code'] == 0): ?>
								from <strong><?= $condition['start'].' - '.$condition['end'] ?></strong>
							<?php endif ?> 
						</dd>


						<dt>Rules</dt>
						<?php foreach ($rules as $rule): ?>
							<dd>
								if (
									<var><?= $requirements['name'] ?></var>
									 >= 
									<strong><?= $rule['in'] ?></strong>
								) then (
									<var>Bonus</var> = <strong><?= $rule['out'] ?><?= $rule['isOutPercent'] ? '%' : '' ?></strong> 
									and
									<var>Points</var> = <strong><?= $rule['points'] ?><?= $rule['isPointsPercent'] ? '%' : '' ?></strong> 
								);</dd>
						<?php endforeach ?>

						<dt>Withdrawable</dt>
						<dd>
							<?= $bonus['release']['name'] ?><strong><?= $bonus['release']['value'] ?></strong>
						</dd>

						<dt>Expiration</dt>
						<dd>
							<strong><?= $bonus['expiration']['value'] ?></strong> days after <strong><?= $bonus['expiration']['name'] ?></strong>
						</dd>

					</dl>
		</div>

		<label class="control-label"><i class="glyphicon glyphicon-eye-open"></i> Promo Preview</label>
		<div class="well well-sm" style="background-color: #fff">
			<?= $description['html'] ?>
		</div>
		
	</div>
</div>