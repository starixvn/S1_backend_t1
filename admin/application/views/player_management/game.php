<script type="text/javascript">
setInterval(function() {
    var currentTime = new Date ( );
    var currentHours = currentTime.getHours ( );
    var currentMinutes = currentTime.getMinutes ( );
    var currentSeconds = currentTime.getSeconds ( );
    currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
    currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;
    var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";
    currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
    currentHours = ( currentHours == 0 ) ? 12 : currentHours;
    var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
    document.getElementById("time").innerHTML = currentTimeString;
}, 1000);
</script>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">WhatTa-Game</h4>
			</div>
			<div class="panel panel-body" id="details_panel_body">
				<div class="row">
					<div class="col-md-6">

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div class="row">
											<div class="col-md-6">
												Player Name: <b><?= $player['username'] ?></b>
													<input type="text" name="player_name" value="<?= $player['username'] ?>" style="display: none;">
											</div>

											<div class="col-md-6">
												Date: <b><?= date("F j, Y"); ?></b>
													<input type="text" name="date" value="<?= date("F j, Y"); ?>" style="display: none;">
											</div>
										</div>

										<br/>

										<div class="row">
											<div class="col-md-6">
												Wallet: <b><?= $player['totalBalance'] ?></b>
													<input type="text" name="player_name" value="<?= $player['totalBalance'] ?>" style="display: none;">
											</div>

											<div class="col-md-6">
												Time: <b><span id="time"></span></b>
													<input type="text" name="time" style="display: none;">
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>

						<form method="post" action="<?= BASEURL . 'game_controller/typeOfAction/' . $player['playerId']?>" role="form" autocomplete="off">
							<input type="submit" name="action" value="" style="display: none;">
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-info">
										<div class="panel-heading">
											<div class="row">
												<div class="col-md-12 form-inline">
													<label for="bet">Place Bet: </label> <input type="text" name="bet" placeholder="Numbers only" class="form-control">
														<?php echo form_error('bet', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
												</div>
											</div>

											<br/>

											<div class="row">
												<div class="col-md-12">
													<div class="jumbotron">
														<div class="container">
															<div class="row">
																<div class="col-md-6">
																	<center>
																		<h4>Result</h4>
																		<h2><strong><?= empty($result) ? '' : $result ?></strong></h2>
																	</center>
																</div>

																<div class="col-md-6">
																	<center>
																		<h2><strong><?= empty($decision) ? '' : $decision ?></strong></h2>
																	</center>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

											Win: <?= $this->session->userdata('win') ?>
											Loss: <?= $this->session->userdata('loss') ?>
											<br/>

											<div class="row">
												<div class="col-md-6 col-md-offset-3">
													<input type="submit" name="action" value="Start" class="btn btn-default btn-lg">
													<?php if($this->session->userdata('game_begin') != '' ) {?>
														<input type="submit" name="action" value="End" class="btn btn-danger btn-lg">
													<?php } ?>
												</div>
											</div>

										</div>
									</div>
								</div>
							</div>
						</div>
					</form>

					<div class="col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">GAME LOG</h4>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<table class="table table-striped table-hover">
											<thead>
												<tr>
													<td>Result</td>
													<td>Bet</td>
													<td>Time</td>
													<td>Status</td>
													<td>Profit Loss</td>
												</tr>
											</thead>

											<tbody>
												<?php $game_log = $this->session->userdata('game_log'); ?>
												<?php
													if(!empty($game_log)) {
														foreach($game_log as $row) { ?>
															<tr>
																<td><?= $row['result'] ?></td>
																<td><?= $row['bet'] ?></td>
																<td><?= $row['time'] ?></td>
																<td><?= $row['decision'] ?></td>
																<td>
																	<span class="help-block" style="<?= $row['decision'] == 'Win' ? 'color:#66cc66;' : 'color:#ff6666;' ?>">
																		<?= $row['profit_loss'] ?>
																	</span>
																</td>
															</tr>
												<?php 	}
													}
												 ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>
