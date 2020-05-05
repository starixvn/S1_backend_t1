<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">Batch Balance Adjustment Result</h4>
			</div>
			<div class="panel-body">
				<h4 class="text-info"><strong>Total: <span class="pull-right"><?=$total_count?></span></strong></h4>
				<h4 class="text-success"><strong>Success: <span class="pull-right"><?=count($success_users)?></span></strong></h4>
				<div class="text-danger">
					<h4><strong>Failed: <span class="pull-right"><?=$failed_count?></span></strong></h4>
					<ul>
						<?php foreach ($failed as $reason => $usernames): ?>
							<li><?=$reason?>: <span class="pull-right"><?=count($usernames)?></span>
								<ul>
									<?php foreach ($usernames as $username): ?>
										<li><?=$username?></li>
									<?php endforeach ?>
								</ul>
							</li>
						<?php endforeach ?>
					</ul>
				</div>
				<hr/>
				<a href="/marketing_management/batchBalanceAdjustment" class="btn btn-primary btn-block">Return to Batch Balance Adjustment</a>
			</div>
		</div>
	</div>
</div>