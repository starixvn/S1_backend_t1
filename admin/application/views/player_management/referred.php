<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Referral Lists </h4>
		<a href="#" class="btn btn-primary btn-sm pull-right" id="chat_history" onclick="closeDetails()"><span class="glyphicon glyphicon-remove"></span></a>
		<div class="clearfix"></div>
	</div>
	<div class="panel panel-body" id="details_panel_body">
		<div class="panel panel-info">
			<div class="panel-heading">
				<center>Player Name: <b><?= $player['username'] ?></b></center>
			</div>
		</div>

		<table class="table table-striped table-hover table-responsive" style="margin: 30px 0 0 0;" id="myTable">
			<thead>
				<tr>
					<th>Name</th>
					<th>Email</th>
					<th>Date</th>
				</tr>
			</thead>

			<tbody>
				<?php
						if(!empty($referred)) {
							$ctr = 1;
							foreach($referred as $referred) {
								$refer = $this->player_manager->getReferredPlayer($referred['invitedPlayerId']);
				?>
										<tr>
											<td><?= $refer['lastName'] . " " . $refer['firstName'] ?></td>
											<td><?= $refer['email'] ?></td>
											<td><?= $referred['referredOn']?></td>
										</tr>
				<?php 		 $ctr++; ?>
				<?php 		}
						} else {
				 ?>

					<tr>
						<td colspan="3" style="text-align:center"><span class="help-block">No Records Found</span></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>