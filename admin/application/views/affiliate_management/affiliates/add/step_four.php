<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Add Affiliate </h4>
				<a href="<?= BASEURL . 'affiliate_management/viewAffiliates'?>" class="btn btn-primary btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="affiliate_panel_body">
				<ol class="breadcrumb">
					<!-- <li class="active">Step 1: Sign Up Information</li> -->
					<li class="active"><a href="<?= BASEURL . 'affiliate_management/stepTwo/back'?>">Step 2: Personal Information</a></li>
					<!-- <li class="active"><a href="<?= BASEURL . 'affiliate_management/stepThree/back'?>">Step 3: Payment Information</a></li> -->
					<li class="active"><b>Step 3: Affiliate Game Options</b></li>
					<!-- <li class="active">Step 5: Affiliate Game Platform</li> -->
					<li class="active">Step 4: Affiliate Payout Options</li>
					<li class="active">Step 5: Account Information</li>
					<!-- <li class="active">Step 7: Finish</li> -->
				</ol>
				
				<!-- Content Info -->
				<form method="POST" action="<?= BASEURL . 'affiliate_management/stepFour'?>" accept-charset="utf-8">
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"> Affiliate Game Options </h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<?php
								$games = explode(',', $this->session->userdata('aff_game'));
								$percentage = $this->session->userdata('aff_percentage');
							?>

							<div class="row">
								<div class="col-md-6 col-md-offset-0">
									<table class="table">
										<thead>
											<th>Game Name</th>
											<th><input type="checkbox" name="check_percentage" id="check_percentage" <?= ($this->session->userdata('aff_check_percentage') == true || set_value('check_percentage') == "on") ? 'checked':''?> onclick="percentage();" > Percentage</th>
										</thead>
										<tbody>
											<?php foreach ($game as $key => $value_game) { ?>
											<tr>
												<td>
													<input type="checkbox" name="game[]"
														<?php
															foreach ($games as $value) {
																if($value == $value_game['gameId'] || set_value('game[]') == $value_game['gameId']) {
																	echo "checked";
																	break;
																}
															}
														?>
													value="<?= $value_game['gameId'] ?>" id="check_game_<?= $value_game['gameId'] ?>" onclick="gamePercentage(<?= $value_game['gameId'] ?>);"> <?= $value_game['game'] ?>
												</td>

												<td>
													<?php 
														if(!empty($percentage)) {
															foreach($percentage as $percentage_value) { 
																if($percentage_value['gameId'] == $value_game['gameId']) {
													?>
																	<input type="text" name="percentage_<?= $value_game['gameId'] ?>" id="percentage_<?= $value_game['gameId'] ?>" class="form-control percentage" value="<?= set_value('percentage') == null ? $percentage_value['percentage']:set_value('percentage') ?>" <?php if(set_value('check_percentage') == null) { echo "readonly"; } ?> style="width: 20%;">
													<?php 
																} else {
													?>
																	<input type="text" name="percentage_<?= $value_game['gameId'] ?>" id="percentage_<?= $value_game['gameId'] ?>" class="form-control percentage" value="<?= set_value('percentage') == null ? 0:set_value('percentage') ?>" <?php if(set_value('check_percentage') == null) { echo "readonly"; } ?> style="width: 20%;">
													<?php
																}
															}	 
														} else {
													?>
															<input type="text" name="percentage_<?= $value_game['gameId'] ?>" id="percentage_<?= $value_game['gameId'] ?>" class="form-control percentage" value="<?= set_value('percentage') == null ? 0:set_value('percentage') ?>" <?php if(set_value('check_percentage') == null) { echo "readonly"; } ?> style="width: 20%;">
													<?php } ?>
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-6 col-md-offset-0">
									<label style="color: red; font-size: 12px;"><?php echo form_error('game[]'); ?></label>
								</div>
							</div>

							<div class="row">
								<!-- <div class="col-md-2 col-md-offset-0">
									<input type="checkbox" name="check_percentage" id="check_percentage" <?= ($this->session->userdata('aff_check_percentage') == true || set_value('check_percentage') == "on") ? 'checked':''?> > Percentage
								</div> -->

								<div class="col-md-3 col-md-offset-0">
									<input type="checkbox" name="check_active_player" id="check_active_player" <?= ($this->session->userdata('aff_check_active_player') == true || set_value('check_active_player') == "on") ? 'checked':''?> > Pay per Active Player
								</div>

								<!-- <div class="col-md-1 col-md-offset-0">
									<input type="checkbox" name="check_cpa" id="check_cpa" <?= ($this->session->userdata('aff_check_cpa') == true || set_value('check_cpa') == "on") ? 'checked':''?> > CPA
								</div>

								<div class="col-md-1 col-md-offset-0">
									<input type="text" name="cpa" id="cpa" class="form-control" value="<?= ($this->session->userdata('aff_cpa') != null) ? $this->session->userdata('aff_cpa') : set_value('cpa') ?>">
								</div> -->
							</div>

							<br/>

							<div class="row">
								<div class="col-md-6 col-md-offset-0">
									<label style="color: red; font-size: 12px;"><?php echo form_error('check_active_player'); ?></label>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-2 col-md-offset-5">
							<input type="submit" name="submit" id="submit" class="form-control btn btn-primary" value="Next">
						</div>
					</div>		
				</form>
				<!-- End of Content Info -->
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>